<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Lead;
use App\Notifications\PaymentLinkNotification;
use App\Services\LeadClassifier;
use App\Services\PaymentGatewayFactory;
use App\Services\PaymentLinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class BrandingController extends Controller
{
    private function host(?string $url): ?string
    {
        if (!$url) return null;
        if (!preg_match('~^https?://~i', $url)) $url = 'https://' . $url;
        $h = parse_url($url, PHP_URL_HOST);
        return $h ? strtolower(preg_replace('/^www\./i', '', $h)) : null;
    }

    private function brandFromUrl(?string $url): ?Brand
    {
        $h = $this->host($url);
        if (!$h) return null;
        return Brand::where('brand_host', $h)
            ->orWhereJsonContains('allowed_origins', $h)
            ->orWhereJsonContains('allowed_origins', 'www.' . $h)
            ->first();
    }

    private function brandFromOrigin(Request $r): ?Brand
    {
        $origin = $r->headers->get('Origin') ?: $r->headers->get('Referer');
        return $this->brandFromUrl($origin);
    }

    public function storeLead(Request $req, LeadClassifier $classifier)
    {
        $incoming = $req->all();

        $brand = $this->brandFromUrl($incoming['url'] ?? null)
            ?? $this->brandFromOrigin($req);

        abort_unless($brand, 422, 'Unknown brand');

        $coreFields = ['name', 'email', 'phone', 'service', 'message'];
        $core = [];

        foreach ($coreFields as $field) {
            $core[$field] = isset($incoming[$field]) ? trim((string) $incoming[$field]) : null;
        }

        $validated = validator($core, [
            'name'    => 'required|string|max:255',
            'email'   => 'required|email|max:255',
            'phone'   => 'required|string|max:30',
            'message' => 'nullable|string|max:4000',
            'service' => 'nullable|string|max:255',
        ])->validate();

        $meta = $incoming;
        foreach ($coreFields as $field) unset($meta[$field]);

        $meta['ip']       = $req->ip();
        $meta['ua']       = substr((string) $req->userAgent(), 0, 255);
        $meta['url']      = $incoming['url'] ?? $req->headers->get('Referer');
        $meta['timezone'] = $incoming['timezone'] ?? now()->timezoneName();
        $meta['service']  = $validated['service'] ?? null;

        // Optional: if AI classification fails, don't kill lead creation
        try {
            $prediction = $classifier->classify($validated);
        } catch (\Throwable $e) {
            Log::warning('Lead classification failed', ['error' => $e->getMessage()]);
            $prediction = null;
        }

        // ✅ Make DB write atomic
        $result = DB::transaction(function () use ($brand, $validated, $meta, $prediction) {
            $client = Client::firstOrCreate(
                ['email' => strtolower(trim($validated['email']))],
                [
                    'name'  => $validated['name'],
                    'phone' => $validated['phone'] ?? null,
                ]
            );

            $seller = app(\App\Services\LeadAssigner::class)->assignNext($brand);
            abort_unless($seller, 422, 'No seller available for this brand.');

            $lead = Lead::create([
                'brand_id'   => $brand->id,
                'seller_id'  => $seller->id,
                'client_id'  => $client->id,
                'name'       => $validated['name'],
                'email'      => $validated['email'],
                'phone'      => $validated['phone'],
                'message'    => $validated['message'] ?? null,
                'status'     => 'new',
                'prediction' => $prediction ? json_encode($prediction) : null,
                'domain_url' => $this->host($meta['url']),
                'meta'       => $meta,
            ]);

            // ✅ Email ONLY after commit
            DB::afterCommit(function () use ($lead, $seller) {
                try {
                    Notification::route('mail', $lead->email)
                        ->notify(new \App\Notifications\LeadAutoReplyNotification($lead));

                    if ($seller && $seller->email) {
                        Notification::route('mail', $seller->email)
                            ->notify(new \App\Notifications\LeadCreatedFsNotification($lead, $seller));
                    }
                } catch (\Throwable $e) {
                    // IMPORTANT: don't break the request after commit; just log.
                    Log::error('Lead notifications failed', [
                        'lead_id' => $lead->id,
                        'error'   => $e->getMessage(),
                    ]);
                }
            });

            return [$lead, $client, $seller];
        });

        [$lead, $client, $seller] = $result;

        return response()->json([
            'ok'         => true,
            'lead_id'    => $lead->id,
            'data'       => $lead,
            'meta'       => $meta,
            'prediction' => $prediction,
        ], 201);
    }

    // public function storeLead(Request $req, LeadClassifier $classifier)
    // {
    //     $data = $req->validate([
    //         'name'   => 'required|string|max:255',
    //         'email'  => 'required|email|max:255',
    //         'phone'  => 'nullable|string|max:30',
    //         'service' => 'nullable|string|max:255',
    //         'price' => 'nullable|string',
    //         'message' => 'nullable|string|max:4000',
    //         'url'    => 'nullable|url',
    //         'utm_source' => 'nullable|string|max:100',
    //         'utm_medium' => 'nullable|string|max:100',
    //         'utm_campaign' => 'nullable|string|max:150',
    //         'referrer' => 'nullable|string|max:2048',
    //         'session_id' => 'nullable|string|max:64',
    //     ]);
    //     // dd($req->all());

    //     // Run classification
    //     $prediction = $classifier->classify($data);       // "real" or "fake"
    //     // if ($prediction['score'] < 50 || $prediction['status'] === 'spam') {
    //     //     return response()->json([
    //     //         'ok' => false,
    //     //         'rejected' => true,
    //     //         'reason' => 'Low quality or spam lead',
    //     //         'prediction' => $prediction,
    //     //     ], 200); // not an error, just a silent reject
    //     // }

    //     // Brand resolution (unchanged)
    //     $brand = $this->brandFromUrl($data['url'] ?? null) ?? $this->brandFromOrigin($req);
    //     abort_unless($brand, 422, 'Unknown brand');
    //     $idem = $req->header('Idempotency-Key');
    //     if ($idem && Lead::where('brand_id', $brand->id)->where('meta->idem', $idem)->exists()) {
    //         return response()->json(['ok' => true, 'duplicate' => true], 200);
    //     }
    //     $client = Client::firstOrCreate(
    //         ['email' => strtolower(trim($data['email']))],
    //         [
    //             'name'  => $data['name'] ?? null,
    //             'phone' => $data['phone'] ?? null,
    //         ]
    //     );

    //     $seller = app(LeadAssigner::class)->assignNext($brand);

    //     $lead = Lead::create([
    //         'brand_id'   => $brand->id,
    //         'seller_id'  => $seller->id,
    //         'client_id'  => $client->id,
    //         'name'       => $client['name'],
    //         'email'      => $client['email'],
    //         'phone'      => $client['phone'] ?? null,
    //         'message'    => $data['message'] ?? null,
    //         'status'     => 'new',
    //         'prediction' => json_encode($prediction),
    //         'domain_url' => $this->host($data['url'] ?? ($req->headers->get('Referer') ?: '')),
    //         'meta' => array_filter([
    //             'utm_source'   => $data['utm_source'] ?? null,
    //             'utm_medium'   => $data['utm_medium'] ?? null,
    //             'utm_campaign' => $data['utm_campaign'] ?? null,
    //             'page_title'   => $data['page_title'] ?? null,
    //             'timezone'     => $data['timezone'] ?? null,
    //             'locale'       => $data['locale'] ?? null,
    //             'channel'      => $data['channel'] ?? null,
    //             'preferred_contact' => $data['preferred_contact'] ?? null,
    //             'contact_time'      => $data['contact_time'] ?? null,
    //             'company'      => $data['company'] ?? null,
    //             'currency'     => $data['currency'] ?? 'USD',
    //             'service'      => $data['service'] ?? null,
    //             'price'        => $data['price'] ?? null,
    //             'session_id'   => $data['session_id'] ?? null,
    //             'idem'         => $idem,
    //             'ip'           => $req->ip(),
    //             'ua'           => substr((string)$req->userAgent(), 255),
    //         ]),
    //     ]);

    //     return response()->json(['ok' => true, 'data' => $lead, 'lead_id' => $lead->id, 'prediction' => $prediction], 201);
    // }

    public function directOrder(
        Request $request,
        PaymentLinkService $links,
        PaymentGatewayFactory $factory,
        LeadClassifier $classifier
    ) {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|email|max:255',
            'phone'       => 'nullable|string|max:30',
            'service'     => 'required|string|max:255',   // service_name
            'price'       => 'required|string|max:50',    // allow "1,299.99"
            'provider'    => 'required|in:stripe,paypal',
            // optional
            'message'     => 'nullable|string|max:4000',
            'url'         => 'nullable|url',
            'utm_source'  => 'nullable|string|max:100',
            'utm_medium'  => 'nullable|string|max:100',
            'utm_campaign' => 'nullable|string|max:150',
            'referrer'    => 'nullable|string|max:2048',
            'session_id'  => 'nullable|string|max:64',
        ]);

        // AI heuristic (optional)
        $prediction = $classifier->classify($data);

        // Brand resolution (use your helpers)
        $brand = $this->brandFromUrl($data['url'] ?? null) ?? $this->brandFromOrigin($request);
        abort_unless($brand, 422, 'Unknown brand');

        // Idempotency (optional): caller should send an Idempotency-Key
        $idem = $request->header('Idempotency-Key');

        // Normalize price → cents
        $totalCents = $this->toCents($data['price']);
        $currency   = 'USD'; // or resolve from brand/site

        return DB::transaction(function () use ($data, $brand, $links, $factory, $prediction, $totalCents, $currency, $idem) {
            // 1) Find or create client
            $client = Client::firstOrCreate(
                ['email' => strtolower(trim($data['email']))],
                ['name' => $data['name'], 'phone' => $data['phone'] ?? null]
            );

            // 2) Auto-assign a seller for this brand
            [$brandId, $sellerId] = $this->determineBrandAndSeller($data, $brand);

            // 3) Create a lightweight “implicit” lead (so reporting stays consistent)
            $lead = Lead::create([
                'brand_id'     => $brandId,
                'seller_id'    => $sellerId,
                'client_id'    => $client->id,
                'name'         => $client->name,
                'email'        => $client->email,
                'phone'        => $client->phone,
                'message'      => $data['message'] ?? null,
                'status'       => 'client',         // or 'direct_order'
                'converted_at' => now(),
                'prediction'   => is_array($prediction) ? json_encode($prediction) : ($prediction ?? null),
                'domain_url'   => $this->host($data['url'] ?? ''),
                'meta'         => array_filter([
                    'source'        => 'direct',
                    'utm_source'    => $data['utm_source'] ?? null,
                    'utm_medium'    => $data['utm_medium'] ?? null,
                    'utm_campaign'  => $data['utm_campaign'] ?? null,
                    'referrer'      => $data['referrer'] ?? null,
                    'session_id'    => $data['session_id'] ?? null,
                    'idem'          => $idem,
                    'ip'            => request()->ip(),
                    'ua'            => substr((string)request()->userAgent(), 0, 255),
                    'service'       => $data['service'],
                    'currency'      => $currency,
                    'price_cents'   => $totalCents,
                ]),
            ]);

            // 4) AUTO-generate the payment link by reusing your Installment service
            // For a “pay in full now” direct order, total = payNow
            $link = $links->createInstallmentLink(
                brand: $brand,
                lead: $lead,
                sellerIdWhoGenerated: $sellerId,     // auto-assigned seller
                serviceName: $data['service'],
                currency: $currency,
                totalCents: $totalCents,
                payNowCents: $totalCents,           // full amount now
                expiresInHours: 24 * 7,
                description: 'Direct order checkout',
                provider: $data['provider'],        // 'stripe' | 'paypal'
            );

            // 5) Send the link to the client (email) – optional because we’ll also redirect
            $url = $link->signedUrl();
            $link->update([
                'last_issued_url'        => $url,
                'last_issued_at'         => now(),
                'last_issued_expires_at' => $link->expires_at ?? now()->addDays(7),
            ]);
            Notification::route('mail', $client->email)
                ->notify(new PaymentLinkNotification($link, $url));

            // 6) Immediately start gateway checkout (same page flow as seller generate)
            $gateway  = $factory->forProvider($link->provider);
            $checkout = $gateway->createCheckout($link, ['email' => $client->email]);

            // (optional) store provider session id if your gateways return it
            if (!empty($checkout['id'])) {
                // add a generic nullable column if available (recommended)
                $link->update(['provider_session_id' => $checkout['id']]);
                $link->order?->update(['provider_session_id' => $checkout['id']]);
            }

            // 7) Redirect the customer to Stripe/PayPal hosted checkout
            return redirect()->away($checkout['url']);
        });
    }

    private function toCents(string $amount): int
    {
        $norm = preg_replace('/[^\d.,]/', '', $amount);
        $norm = str_replace(',', '', $norm);
        return (int) round(((float)$norm) * 100);
    }

    private function determineBrandAndSeller(array $data, Brand $brandFromUrl): array
    {
        if (!empty($data['brand_id']) && !empty($data['seller_id'])) {
            return [(int)$data['brand_id'], (int)$data['seller_id']];
        }
        $brandId  = $brandFromUrl->id;
        $sellerId = $this->pickSellerForBrand($brandId);
        return [$brandId, $sellerId];
    }

    private function pickSellerForBrand(int $brandId): int
    {
        $default = \App\Models\Seller::where('brand_id', $brandId)
            ->whereIn('is_seller', ['project_manager', 'front_seller'])
            ->orderBy('id')
            ->value('id');

        return $default ?? (int)\App\Models\Seller::where('brand_id', $brandId)->value('id');
    }
}
