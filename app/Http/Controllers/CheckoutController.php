<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Seller;
use App\Models\AccountKey;
use App\Models\PaymentLink;
use Illuminate\Http\Request;
use App\Services\PayPalGateway;
use App\Services\StripeGateway;
use Illuminate\Support\Facades\Log;
use App\Services\PaymentLinkService;
use Illuminate\Support\Facades\Crypt;
use App\Services\PaymentGatewayFactory;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentLinkNotification;

class CheckoutController extends Controller
{
    public function generateLinkForm(Request $request, Brand $brand, Lead $lead, ?Order $order = null)
    {
        $seller  = auth('seller')->user();
        $admin   = auth('admin')->user();

        $orderType = $request->get('type'); // "renewal" or null
        $renewedOrder = Order::where('order_type', $orderType)->where('parent_order_id', $order)->first();
        // ✅ Admin always allowed
        if ($admin) {
            $canGenerate = true;
        } else {
            // ✅ Only front sellers of this brand
            $canGenerate = $seller
                && (($seller->role ?? $seller->is_seller) === 'front_seller')
                && ((int)$seller->brand_id === (int)$brand->id);
        }
        // Lead must belong to brand
        abort_unless((int)$lead->brand_id === (int)$brand->id, 404, 'Lead/brand mismatch.');
        // Check if allowed
        abort_unless($canGenerate, 403, 'Only admins or front sellers can generate a link.');

        // If order is given, validate
        if ($order) {
            if (
                (int)$order->brand_id !== (int)$brand->id ||
                (int)$order->client_id !== (int)$lead->client_id
            ) {
                return redirect()
                    ->route('admin.orders.get')
                    ->with('error', 'Order does not belong to this lead/brand.');
            }

            // if ((int)$order->balance_due <= 0 || $order->paymentLinks()->where('is_active_link', false)->exists()) {
            //     // Check if the user is a seller or an admin
            //     if ($seller) {
            //         // Seller redirected
            //         return redirect()
            //             ->route('seller.orders.get')
            //             ->with('info', 'Order is already fully paid.');
            //     }

            //     // Admin redirected
            //     return redirect()
            //         ->route('admin.orders.get')
            //         ->with('info', 'Order is already fully paid.');
            // }

            if ((int)$order->balance_due <= 0) {
                if ($seller) {
                    return redirect()
                        ->route('seller.orders.get')
                        ->with('info', 'Order is already fully paid.');
                }
                return redirect()
                    ->route('admin.orders.get')
                    ->with('info', 'Order is already fully paid.');
            }
        }

        if ($seller) {
            return view('sellers.pages.generate-payment-link', compact('brand', 'lead', 'order'));
        }
        return view('admin.pages.generate-payment-link', compact('brand', 'lead', 'order'));
    }

    public function renewOrderLink(Brand $brand, Lead $lead, ?Order $order = null, $type = null)
    {
        $seller  = auth('seller')->user();
        $admin   = auth('admin')->user();

        $orderType = $type;
        // dd($orderType);
        // ✅ Admin always allowed
        if ($admin) {
            $canGenerate = true;
        } else {
            // ✅ Only front sellers of this brand
            $canGenerate = $seller
                && (($seller->role ?? $seller->is_seller) === 'front_seller')
                && ((int)$seller->brand_id === (int)$brand->id);
        }
        // Lead must belong to brand
        abort_unless((int)$lead->brand_id === (int)$brand->id, 404, 'Lead/brand mismatch.');
        // Check if allowed
        abort_unless($canGenerate, 403, 'Only admins or front sellers of this brand can generate a link.');

        // If order is given, validate
        if ($order) {
            if (
                (int)$order->brand_id !== (int)$brand->id ||
                (int)$order->client_id !== (int)$lead->client_id
            ) {
                return redirect()
                    ->route('seller.orders.get')
                    ->with('error', 'Order does not belong to this lead/brand.');
            }
        }
        return view('sellers.pages.renew-payment-link', compact('brand', 'lead', 'order', 'orderType'));
    }

    protected function sellerOwnsLead(Seller $seller, Lead $lead, Brand $brand): bool
    {
        return (int) $seller->id === (int) $lead->seller_id
            && (int) $seller->brand_id === (int) $brand->id;
    }

    public function generatePayLink(Request $request, Brand $brand, Lead $lead, PaymentLinkService $links)
    {
        $seller = auth('seller')->user();
        $admin  = auth('admin')->user();

        // // Admin can always generate; seller must own lead and be in same brand
        // $canGenerate = $admin
        //     ? true
        //     : ($seller && ($seller->id === $lead->seller_id && $seller->brand_id === $brand->id));
        $canGenerate = $admin ? true : ($seller && $this->sellerOwnsLead($seller, $lead, $brand));

        abort_unless($canGenerate, 403, 'Seller must belong to and own the lead.');

        $data = $request->validate([
            'service_name'     => ['required', 'string', 'max:255'],
            'currency'         => ['required', 'string', 'size:3'],
            'total_amount'     => ['required', 'numeric', 'gt:0'],
            'payable_amount'   => ['required', 'numeric', 'gt:0'],
            'expires_in_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            'provider'         => ['nullable', 'in:stripe,paypal'],
            'order_type'       => ['nullable', 'in:original,renewal'],
            'parent_order_id'  => ['nullable', 'integer', 'exists:orders,id'], // for renewals
            'order'            => ['nullable', 'integer'], // you were passing as $request->order
        ]);

        if ((float)$data['payable_amount'] > (float)$data['total_amount']) {
            return back()->with('error', 'Payable amount cannot exceed total amount.');
        }

        $actor = $admin ?: $seller;
        abort_unless($actor, 403);

        $link = $links->createInstallmentLink(
            brand: $brand,
            lead: $lead,
            sellerIdWhoGenerated: $seller->id ?? $lead->seller_id,
            serviceName: $data['service_name'],
            currency: strtoupper($data['currency']),
            totalCents: (int) round($data['total_amount'] * 100),
            payNowCents: (int) round($data['payable_amount'] * 100),
            expiresInHours: $data['expires_in_hours'] ?? null,
            provider: $data['provider'] ?? null,
            orderType: $data['order_type'] ?? 'original',
            parentOrderId: (int) ($data['parent_order_id'] ?? $request->order ?? 0) ?: null,
            meta: [
                'generated_by_id'   => $actor->id,
                'generated_by_type' => $actor instanceof \App\Models\Admin ? 'admin' : 'seller',
            ],
        );

        $url = $link->signedUrl();
        $link->update([
            'last_issued_url'        => $url,
            'last_issued_at'         => now(),
            'last_issued_expires_at' => $link->expires_at ?? now()->addDays(7),
            'generated_by_id'        => $actor->id,
            'generated_by_type'      => $actor instanceof \App\Models\Admin ? 'admin' : 'seller',
        ]);

        Notification::route('mail', $link->client?->email ?? $lead->email)
            ->notify(new PaymentLinkNotification($link, $url));

        return back()->with('success', 'Payment link created.')->with('payment_link_url', $url);
    }

    // Create checkout with single payment account
    public function createCheckout(Request $request, string $token, PaymentGatewayFactory $factory)
    {
        // 1. Fetch link
        $link = PaymentLink::with(['order', 'lead', 'brand'])->where('token', $token)->first();
        if (!$link) {
            return back()->with('error', 'Link not found', $token);
        }
        // 1.5 Dump link & relationships
        // dd('Link loaded', [
        //     'link_id' => $link->id,
        //     'link->order' => $link->order ? $link->order->toArray() : null,
        //     'link->brand' => $link->brand ? $link->brand->toArray() : null,
        // ]);

        abort_if(! $link->isActiveLink(), 410, 'This payment link is no longer active.');
        // dd($link->order, $link->order->brand);

        $buyer = $request->validate([
            'first_name' => ['nullable', 'string', 'max:255'],
            'last_name'  => ['nullable', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255'],
            'phone'      => ['nullable', 'string', 'max:30'],
            'address'    => ['nullable', 'string', 'max:255'],
            'city'       => ['nullable', 'string', 'max:255'],
            'state'      => ['nullable', 'string', 'max:255'],
            'zip'        => ['nullable', 'string', 'max:30'],
            'country'    => ['nullable', 'string', 'max:255'],
        ]);
        $brand = $link->brand ?? $link->order->brand ?? null;
        abort_if(!$brand, 500, 'Missing brand information.');

        // ✅ Load the keys from DB
        $keys = AccountKey::where('brand_id', $brand->id)
            ->where('status', 'active')
            ->first();
        if (!$keys || !$keys->stripe_secret_key) {
            return response()->json(['error' => 'Stripe keys are missing for this brand'], 500);
        }
        // ✅ Optional: log or preview keys
        Log::info('Stripe keys loaded for brand', [
            'brand_id' => $brand->id,
            'stripe_key' => substr($keys->stripe_secret_key, 0, 6) . '****'
        ]);

        $gateway = $factory->forProviderWithBrand($link->provider, $brand);   // 'stripe' or 'paypal'
        $checkout = $gateway->createCheckout($link, $buyer); // returns ['id'=>..., 'url'=>...]

        // (optional) persist gateway session ID if you want:
        if (!empty($checkout['id'])) {
            if ($link->provider === 'stripe') {
                $link->update(['provider_session_id' => $checkout['id']]);
                $link->order?->update(['provider_session_id' => $checkout['id']]);
            } else {
                $link->update(['provider_session_id' => $checkout['id']]); // add column if you want
            }
        }

        return redirect()->away($checkout['url']);
        // $session = $stripe->createCheckout($link, $buyer);
        // return redirect()->away($session['url']);
    }

    public function checkoutSuccess(Request $request, string $token)
    {
        $link = PaymentLink::with('order')->where('token', $token)->firstOrFail();

        $provider = $link->provider ?? 'stripe';
        $gateway = $provider === 'paypal'
            ? app(PayPalGateway::class)
            : app(StripeGateway::class);

        // Stripe: pass session_id; PayPal: service will read ?token=
        $gateway->handleCheckoutSuccess($link, $request->query('session_id'));

        return view('paid-success', ['link' => $link->fresh('order'), 'order' => $link->order]);
    }

    public function showPaymentPage(Request $request, string $token)
    {
        // Signature / expiry
        if (! $request->hasValidSignature()) {
            abort(403, 'Invalid or expired link.');
        }
        // Load DB row (revocation/expiry lives here)
        $link = PaymentLink::with(['brand', 'lead', 'order'])
            ->where('token', $token)->firstOrFail();
        if (! $link->isActiveLink()) {
            return response()->view('errors.payLink-error', [
                'message' => 'This payment link is not active.'
            ], 410);
            // abort(410, 'This payment link is not active.');
        }
        // Decrypt + validate payload (optional but nice for prefill)
        $p = $request->query('p');
        try {
            $data = $p ? json_decode(Crypt::decryptString($p), true, 512, JSON_THROW_ON_ERROR) : [];
        } catch (\Throwable $e) {
            abort(404, 'Invalid payload.');
        }
        // Cross-check critical fields so URL can’t lie
        if (($data['t'] ?? null) !== $link->token ||
            (int)($data['a'] ?? -1) !== (int)$link->unit_amount ||
            strtoupper($data['c'] ?? '') !== $link->currency
        ) {
            abort(400, 'Payload mismatch.');
        }
        // Render page (or create Stripe checkout here)
        // dd($link);
        return view('generated-link-page', [
            'brand'   => $link->brand,
            'service' => $link->service_name,
            'amount'  => $link->unit_amount,
            'currency' => $link->currency,
            'order'   => $link->order,
            'lead'    => $link->lead,
            'token'   => $link->token,
            'link'   => $link,
        ]);
    }
}
