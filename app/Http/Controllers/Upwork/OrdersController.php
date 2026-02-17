<?php

namespace App\Http\Controllers\Upwork;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Upwork\UpworkOrder;
use App\Notifications\PaymentLinkNotification;
use App\Services\Upwork\UpworkLinkGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class OrdersController extends Controller
{
    public function upworklinkGenerator()
    {
        // $admin = auth('admin')->user();
        // abort_unless($admin && $admin->role === 'up_admin', 403, 'Unauthorized');
        $domains = Brand::where('module', 'upwork')->get();
        return view('upwork.pages.generate-payment-link', compact('domains'));
    }

    public function upworklinkGeneratorFinal(UpworkOrder $order)
    {
        $order->loadMissing(['brand', 'client']); // needed in blade
        $domains = Brand::all();
        return view('upwork.pages.generate-final-link', compact('domains', 'order'));
    }

    // optimized logic
    public function upworkOrders(Request $request)
    {
        $isAdmin = auth('admin')->check();
        if (!$isAdmin) {
            return redirect()->route('upwork.login.get')->with('error', 'You must be logged in.');
        }

        $query = UpworkOrder::with([
            'brand:id,brand_name',
            'client:id,name,email',
            'latestPaymentLink:id,order_id,is_active_link,last_issued_url'
        ]);
        // --- Filters ---
        $query
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('brand_id'), fn($q) => $q->where('brand_id', (int) $request->brand_id))
            ->when($request->filled('q'), function ($q) use ($request) {
                $term = trim($request->q);
                $q->where(
                    fn($w) =>
                    $w->where('service_name', 'like', "%{$term}%")
                        ->orWhere('buyer_name', 'like', "%{$term}%")
                        ->orWhere('buyer_email', 'like', "%{$term}%")
                );
            });

        $orders = $query->where('order_type', 'original')->paginate(20)->withQueryString();

        // dd($query,$orders);
        return view('upwork.pages.orders', compact('orders'));
    }

    public function generatePayLinkFirst(Request $request, UpworkLinkGenerator $links)
    {
        $admin = auth('admin')->user();
        abort_unless($admin && $admin->role === 'up_admin', 403, 'Unauthorized');

        // Validate input data
        $data = $request->validate([
            'client_name'      => ['required', 'string', 'max:255'],
            'client_email'     => ['required', 'email', 'max:255'],
            'client_phone'     => ['nullable', 'string', 'max:50'],
            'brandId'          => ['required', 'integer', 'exists:brands,id'],
            'service'          => ['required', 'string', 'max:255'],
            'currency'         => ['required', 'string', 'size:3'],
            'unit_amount'      => ['required', 'numeric', 'gt:0'], // dollars
            'payable_amount'   => ['required', 'numeric', 'gt:0'], // dollars
            'sell_type'        => ['required', 'in:front,upsell'],
            'provider'         => ['required', 'in:stripe'],
            'expires_in_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
        ]);

        // If payable amount is greater than total amount, return error
        if ((float)$data['payable_amount'] > (float)$data['unit_amount']) {
            return back()->withErrors(['payable_amount' => 'Payable amount cannot exceed total amount.'])->withInput();
        }

        // dd($request->all(), $data);

        try {
            $brand = Brand::findOrFail((int)$data['brandId']);

            // Generate payment link
            $link = $links->createOriginalOrderAndFirstLink(
                brand: $brand,
                clientData: [
                    'name'  => $data['client_name'],
                    'email' => strtolower(trim($data['client_email'])),
                    'phone' => $data['client_phone'] ?? null,
                ],
                sellType: $data['sell_type'],
                serviceName: $data['service'],
                currency: strtoupper($data['currency']),
                totalCents: (int) round(((float)$data['unit_amount']) * 100),
                payNowCents: (int) round(((float)$data['payable_amount']) * 100),
                provider: $data['provider'],
                expiresInHours: (int)($data['expires_in_hours'] ?? 168),
                generatedBy: $admin
            );

            $url = $link->signedUrl();

            // Update link with the generated URL
            $link->update([
                'last_issued_url'        => $url,
                'last_issued_at'         => now(),
                'last_issued_expires_at' => $link->expires_at,
            ]);

            $clientEmail = $link->client?->email ?? $data['client_email'];
            Notification::route('mail', $clientEmail)
                ->notify(
                    (new PaymentLinkNotification($link, $url, 'upwork'))
                        ->delay(now()->addSeconds(5))
                );

            return back()->with('success', 'Payment link created.')->with('payment_link_url', $url);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error generating payment link', ['error' => $e->getMessage()]);

            // Return a friendly error message
            return back()->withErrors('An error occurred while generating the payment link. Please try again later.');
        }
    }

    public function generatePayLinkInstallment(Request $request, UpworkLinkGenerator $links, UpworkOrder $order)
    {
        $admin = auth('admin')->user();
        abort_unless($admin && $admin->role === 'up_admin', 403, 'Unauthorized');

        // Refresh order to ensure it's up-to-date
        $order->refresh();

        // Guard against already fully paid orders
        if ((int)$order->balance_due <= 0 || $order->status === 'paid') {
            return back()->with('info', 'Order is already fully paid.');
        }

        try {
            // Validate the request data
            $data = $request->validate([
                'provider'         => ['required', 'in:stripe,paypal'],
                'payable_amount'   => ['required', 'numeric', 'gt:0'], // dollars
                'expires_in_hours' => ['nullable', 'integer', 'min:1', 'max:720'],
            ]);

            $payNowCents = (int) round(((float)$data['payable_amount']) * 100);

            // Guard against exceeding the remaining balance
            if ($payNowCents > (int)$order->balance_due) {
                return back()->withErrors(['payable_amount' => 'Pay Now cannot exceed remaining due.'])->withInput();
            }

            // Generate the payment link for the installment
            $link = $links->createInstallmentLinkForOrder(
                order: $order,
                payNowCents: $payNowCents,
                provider: $data['provider'],
                expiresInHours: (int)($data['expires_in_hours'] ?? 168),
                generatedBy: $admin
            );

            $url = $link->signedUrl();

            // Update the payment link with the generated URL
            $link->update([
                'last_issued_url'        => $url,
                'last_issued_at'         => now(),
                'last_issued_expires_at' => $link->expires_at,
            ]);

            return back()->with('success', 'Installment link created.')->with('payment_link_url', $url);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error generating installment payment link', ['error' => $e->getMessage()]);

            // Return a friendly error message
            return back()->withErrors('An error occurred while generating the installment payment link. Please try again later.');
        }
    }

    public function upworkPayments(Request $request)
    {
        $admin = auth('admin')->user();
        abort_unless($admin && $admin->role === 'up_admin', 403, 'Unauthorized');

        $query = UpworkOrder::with([
            'brand:id,brand_name',
            'client:id,name,email',
        ]);

        // optional filters
        if ($request->filled('status'))   $query->where('status', $request->string('status'));
        if ($request->filled('brand_id')) $query->where('brand_id', (int) $request->brand_id);
        if ($request->filled('q')) {
            $q = trim($request->q);
            $query->where(function ($w) use ($q) {
                $w->where('service_name', 'like', "%{$q}%")
                    ->orWhere('buyer_name', 'like', "%{$q}%")
                    ->orWhere('buyer_email', 'like', "%{$q}%");
            });
        }

        $orders = $query->paginate(20)->withQueryString();
        return view('upwork.pages.payment-data', compact('orders'));
    }
}
