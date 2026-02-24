<?php

// app/Services/PayPalGateway.php
namespace App\Services;

use App\Models\Order;
use App\Models\Seller;
use App\Models\Payment;
use App\Models\PaymentLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaymentFailedNotification;
use App\Notifications\InitialPaymentNotification;

/**
 * Production-ready PayPal gateway:
 * - Uses PayPal Orders API (create order -> approve -> capture)
 * - Idempotent DB recording (provider + txn id)
 * - Safe with return + webhook both firing (no duplicates, no double emails)
 * - No sleep() in requests
 * - Optional webhook signature verification (recommended)
 * - Validates currency + amount against PaymentLink and remaining balance
 *
 * REQUIREMENTS / ASSUMPTIONS:
 * - PaymentLink has: id, token, currency, unit_amount (cents), order_id, provider_session_id, provider_payment_intent_id, last_issued_url, status, is_active_link
 * - Order has: id, unit_amount (cents), amount_paid (cents), balance_due accessor, status, first_paid_at, provider_session_id, provider_payment_intent_id
 * - Payment has: provider, provider_payment_intent_id, order_id, payment_link_id, amount, currency, status, payload, credit_to_seller_id, etc.
 * - Notifications implement ShouldQueue and $afterCommit=true if you want strict “only after commit”.
 */
class PayPalGateway implements PaymentGateway
{
    protected string $clientId;
    protected string $secret;
    protected string $base;
    protected ?string $webhookId;

    public function __construct(array $config)
    {
        $this->clientId  = (string)($config['client_id'] ?? '');
        $this->secret    = (string)($config['secret'] ?? '');
        $this->base      = rtrim((string)($config['base'] ?? 'https://api.paypal.com'), '/');
        $this->webhookId = $config['webhook_id'] ?? null;

        if (!$this->clientId || !$this->secret) {
            throw new \InvalidArgumentException('PayPal client_id/secret missing.');
        }
    }

    /**
     * Create PayPal Order and return approval URL.
     * Returns: ['id' => <paypal_order_id>, 'url' => <approve_url>]
     */
    public function createCheckout(PaymentLink $link, array $buyer): array
    {
        // Minimal safety
        if (!$link->order_id) {
            throw new \RuntimeException('PaymentLink missing order_id.');
        }
        if (!$link->currency || $link->unit_amount <= 0) {
            throw new \RuntimeException('PaymentLink currency/amount invalid.');
        }

        $paypalAmount = number_format($link->unit_amount / 100, 2, '.', '');

        // Embed ids in purchase_unit custom_id (works reliably)
        $customId = json_encode([
            'order_id'        => (int)$link->order_id,
            'payment_link_id' => (int)$link->id,
            'token'           => (string)$link->token,
        ], JSON_UNESCAPED_SLASHES);

        $payload = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => strtoupper($link->currency),
                    'value'         => $paypalAmount,
                ],
                'custom_id'    => $customId,
                'description'  => (string)$link->service_name,
            ]],
            'application_context' => [
                'brand_name'          => optional($link->brand)->brand_name ?: config('app.name'),
                // IMPORTANT: PayPal will redirect back here after approval.
                // You can capture here OR you can capture only via webhook (recommended).
                'return_url'          => route('paylinks.success', $link->token),
                'cancel_url'          => route('paylinks.cancel', $link->token) . '?canceled=1',
                'shipping_preference' => 'NO_SHIPPING',
                'user_action'         => 'PAY_NOW',
            ],
        ];

        Log::info('PayPal createCheckout', [
            'link_id' => $link->id,
            'order_id' => $link->order_id,
            'amount' => $paypalAmount,
            'currency' => strtoupper($link->currency),
            'base' => $this->base,
        ]);

        $res = $this->api('POST', '/v2/checkout/orders', $payload, [
            // PayPal idempotency
            'PayPal-Request-Id' => 'pp_create_' . $link->token,
        ]);

        $paypalOrderId = $res['id'] ?? null;
        $approveUrl = $this->extractApproveUrl($res);

        if (!$paypalOrderId || !$approveUrl) {
            Log::error('PayPal createCheckout missing id/approve', ['response' => $res]);
            throw new \RuntimeException('PayPal createCheckout failed (missing approve link).');
        }

        // Save provider session id (PayPal Order ID)
        $link->update(['provider_session_id' => $paypalOrderId]);
        $link->order?->update(['provider_session_id' => $paypalOrderId]);

        return ['id' => $paypalOrderId, 'url' => $approveUrl];
    }

    /**
     * Success handler called from return_url route.
     *
     * PRODUCTION RECOMMENDATION:
     * - Prefer WEBHOOK as source of truth.
     * - But if you still want to capture here (user returns), this will attempt capture once.
     * - If capture fails, it DOES NOT sleep() and DOES NOT spam; webhook can still finalize.
     */
    public function handleCheckoutSuccess(PaymentLink $link, ?string $sessionId): void
    {
        $paypalOrderId = $sessionId ?: $link->provider_session_id;
        if (!$paypalOrderId) {
            Log::warning('PayPal success called without paypalOrderId', ['link_id' => $link->id]);
            return;
        }

        // If you want strict webhook-only, uncomment this:
        // return;

        try {
            $cap = $this->api('POST', "/v2/checkout/orders/{$paypalOrderId}/capture", null, [
                'PayPal-Request-Id' => 'pp_capture_' . $paypalOrderId, // idempotency
            ]);
        } catch (\Throwable $e) {
            Log::error('PayPal capture failed on return', [
                'paypal_order_id' => $paypalOrderId,
                'link_id' => $link->id,
                'error' => $e->getMessage(),
            ]);

            // Optional: notify payment failed (ONLY if you want immediate feedback)
            // This can be noisy because sometimes PayPal capture is delayed.
            // $this->safeSendFailureEmail($link, 'PayPal could not capture your payment yet. Please try again.');

            return;
        }

        $this->recordFromCapturePayload($cap, $paypalOrderId);
    }

    /**
     * Webhook handler (recommended).
     * Handles PAYMENT.CAPTURE.COMPLETED and records payment idempotently.
     */
    public function handleWebhook(string $payload, array $headers): bool
    {
        $event = json_decode($payload, true);
        if (!is_array($event)) {
            Log::warning('PayPal webhook invalid JSON');
            return false;
        }

        // Verify signature if webhookId is configured
        if ($this->webhookId && !$this->verifySignature($payload, $headers)) {
            Log::warning('PayPal webhook signature invalid', [
                'event_type' => $event['event_type'] ?? null,
                'event_id' => $event['id'] ?? null,
            ]);
            return false;
        }

        $type = (string)($event['event_type'] ?? '');
        Log::info('PayPal webhook received', [
            'type' => $type,
            'event_id' => $event['id'] ?? null,
        ]);

        if ($type !== 'PAYMENT.CAPTURE.COMPLETED') {
            return true; // ignore other types
        }

        $resource  = $event['resource'] ?? [];
        $amountVal = $resource['amount']['value'] ?? null;
        $currency  = $resource['amount']['currency_code'] ?? null;
        $captureId = $resource['id'] ?? null;

        if (!$captureId || !$amountVal || !$currency) {
            Log::warning('PayPal webhook missing capture fields', ['resource' => $resource]);
            return true;
        }

        // Try extracting meta from custom_id (if present), else fetch PayPal order
        $meta = null;
        $custom = $resource['custom_id'] ?? null;
        if ($custom) {
            $tmp = json_decode($custom, true);
            if (is_array($tmp) && isset($tmp['order_id'], $tmp['payment_link_id'])) {
                $meta = $tmp;
            }
        }

        if (!$meta) {
            $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;
            if ($paypalOrderId) {
                $meta = $this->extractMetaFromPayPalOrder($paypalOrderId);
            }
        }

        if (!$meta) {
            Log::warning('PayPal webhook missing metadata (custom_id)', ['capture' => $captureId]);
            return true;
        }

        $orderId = (int)$meta['order_id'];
        $linkId  = (int)$meta['payment_link_id'];
        $cents   = (int)round(((float)$amountVal) * 100);

        // Record payment (idempotent)
        [$payment, $order, $isNew] = $this->recordPayment(
            provider: 'paypal',
            providerTxnId: $captureId,
            orderId: $orderId,
            linkId: $linkId,
            reportedAmountCents: $cents,
            currency: (string)$currency,
            rawPayload: $event
        );

        if ($isNew && $payment && $order) {
            DB::afterCommit(function () use ($payment, $order) {
                $this->sendInitialPaymentNotifications($payment, $order);
            });
        }

        return true;
    }

    /* ===========================================================
     * Internal recording + validation
     * =========================================================== */

    private function recordFromCapturePayload(array $cap, string $paypalOrderId): void
    {
        // Capture payload -> capture object
        $purchaseUnit = $cap['purchase_units'][0] ?? [];
        $capture = $purchaseUnit['payments']['captures'][0] ?? null;

        if (!$capture) {
            // Sometimes capture response is delayed/empty; fetch order details
            Log::warning('PayPal capture payload missing captures; fetching order', ['paypal_order_id' => $paypalOrderId]);
            $ord = $this->api('GET', "/v2/checkout/orders/{$paypalOrderId}");
            $purchaseUnit = $ord['purchase_units'][0] ?? [];
            $capture = $purchaseUnit['payments']['captures'][0] ?? null;
        }

        if (!$capture) {
            Log::error('PayPal capture still missing; cannot record', ['paypal_order_id' => $paypalOrderId]);
            return;
        }

        $captureId = $capture['id'] ?? $paypalOrderId;
        $amountVal = $capture['amount']['value'] ?? null;
        $currency  = $capture['amount']['currency_code'] ?? null;

        if (!$amountVal || !$currency) {
            Log::error('PayPal capture missing amount/currency', ['capture' => $capture]);
            return;
        }

        // Extract meta (prefer purchase_units custom_id)
        $meta = null;
        $custom = $purchaseUnit['custom_id'] ?? null;
        if ($custom) {
            $tmp = json_decode($custom, true);
            if (is_array($tmp) && isset($tmp['order_id'], $tmp['payment_link_id'])) {
                $meta = $tmp;
            }
        }
        if (!$meta) {
            // fallback: GET order and extract
            $meta = $this->extractMetaFromPayPalOrder($paypalOrderId);
        }

        if (!$meta) {
            Log::warning('PayPal capture missing metadata; skipping record', ['paypal_order_id' => $paypalOrderId]);
            return;
        }

        $orderId = (int)$meta['order_id'];
        $linkId  = (int)$meta['payment_link_id'];
        $cents   = (int)round(((float)$amountVal) * 100);

        [$payment, $order, $isNew] = $this->recordPayment(
            provider: 'paypal',
            providerTxnId: (string)$captureId,
            orderId: $orderId,
            linkId: $linkId,
            reportedAmountCents: $cents,
            currency: (string)$currency,
            rawPayload: $cap
        );

        if ($isNew && $payment && $order) {
            DB::afterCommit(function () use ($payment, $order) {
                $this->sendInitialPaymentNotifications($payment, $order);
            });
        }
    }

    /**
     * Idempotent payment recording with strong validation.
     * Returns: [Payment|null, Order|null, bool $isNew]
     */
    protected function recordPayment(
        string $provider,
        string $providerTxnId,
        int $orderId,
        int $linkId,
        int $reportedAmountCents,
        string $currency,
        array $rawPayload
    ): array {
        if (!$providerTxnId) {
            Log::warning('recordPayment called without providerTxnId');
            return [null, null, false];
        }

        return DB::transaction(function () use (
            $provider,
            $providerTxnId,
            $orderId,
            $linkId,
            $reportedAmountCents,
            $currency,
            $rawPayload
        ) {
            /** @var PaymentLink $link */
            $link  = PaymentLink::lockForUpdate()->findOrFail($linkId);
            /** @var Order $order */
            $order = Order::lockForUpdate()->findOrFail($orderId);

            // Hard cross-check: link must belong to order
            abort_unless((int)$link->order_id === (int)$order->id, 422, 'Payment link does not match order.');

            // Idempotency: already recorded?
            $existing = Payment::where('provider', $provider)
                ->where('provider_payment_intent_id', $providerTxnId)
                ->first();

            if ($existing) {
                return [$existing, $order, false];
            }

            // Currency must match link
            abort_unless(
                strtoupper($currency) === strtoupper($link->currency),
                422,
                'Currency mismatch from PayPal.'
            );

            // Validate amount against link expectation (strict)
            $expected = (int)$link->unit_amount; // cents
            abort_unless($reportedAmountCents > 0, 422, 'Invalid amount from PayPal.');
            abort_unless($reportedAmountCents === $expected, 422, 'Amount mismatch from PayPal.');

            // Never credit more than remaining
            $remaining   = max(0, (int)$order->unit_amount - (int)$order->amount_paid);
            $creditCents = min($reportedAmountCents, $remaining);

            abort_unless($creditCents > 0, 422, 'Order already fully paid.');

            // Commission attribution
            $decider      = app(CommissionDecider::class);
            $creditedToId = (int)($link->credit_to_seller_id ?: $decider->creditSellerIdFor($order));

            /** @var Payment $payment */
            $payment = Payment::create([
                'order_id'                   => $order->id,
                'payment_link_id'            => $link->id,
                'amount'                     => $creditCents,
                'currency'                   => strtoupper($currency),
                'status'                     => 'succeeded',
                'provider'                   => $provider,
                'provider_payment_intent_id' => $providerTxnId,
                'payload'                    => $rawPayload,

                // snapshots
                'seller_id'                  => (int)$order->seller_id,
                'owner_seller_id'            => (int)$order->owner_seller_id,
                'front_seller_id'            => (int)$order->front_seller_id,
                'credited_seller_id'         => (int)$creditedToId,
                'credit_to_seller_id'        => (int)$creditedToId,
            ]);

            // Roll-up order
            $order->amount_paid = (int)$order->amount_paid + $creditCents;
            if (!$order->first_paid_at) {
                $order->first_paid_at = now();
            }

            // provider ids
            $order->provider_session_id        = $link->provider_session_id; // PayPal Order ID
            $order->provider_payment_intent_id = $providerTxnId;             // Capture ID
            $order->save();

            // Mark link paid (single-use)
            if ($link->status !== 'paid') {
                $link->update([
                    'status'                     => 'paid',
                    'expires_at'                 => now(),
                    'is_active_link'             => false,
                    'paid_at'                    => now(),
                    'provider_payment_intent_id' => $providerTxnId,
                ]);
            }

            DB::afterCommit(function () use ($order) {
                app(\App\Services\BriefService::class)->dispatchBriefEmail($order->id);
            });

            return [$payment, $order, true];
        });
    }

    /* ===========================================================
     * API / Token / Webhook verification
     * =========================================================== */

    protected function api(string $method, string $path, ?array $json = null, array $extraHeaders = []): array
    {
        $token = $this->getAccessToken();

        $headers = array_merge([
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token,
        ], $this->formatHeaders($extraHeaders));

        $opts = [
            CURLOPT_URL            => $this->base . $path,
            CURLOPT_CUSTOMREQUEST  => strtoupper($method),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER     => $headers,

            // Production timeouts
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_TIMEOUT        => 30,
        ];

        if ($json !== null) {
            $opts[CURLOPT_POSTFIELDS] = json_encode($json, JSON_UNESCAPED_SLASHES);
        }

        $ch = curl_init();
        curl_setopt_array($ch, $opts);

        $resp = curl_exec($ch);
        $err  = curl_error($ch);
        $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_close($ch);

        if ($err || $code < 200 || $code >= 300) {
            Log::error("PayPal API error HTTP {$code}", [
                'error'    => $err,
                'resp'     => $resp,
                'method'   => $method,
                'path'     => $path,
                'body'     => $json,
            ]);
            throw new \RuntimeException("PayPal API Error ({$code}): " . ($err ?: (string)$resp));
        }

        $decoded = json_decode((string)$resp, true);
        if (!is_array($decoded)) {
            Log::error('PayPal API returned invalid JSON', ['resp' => $resp]);
            throw new \RuntimeException('Invalid PayPal API response JSON.');
        }

        return $decoded;
    }

    protected function getAccessToken(): string
    {
        $cacheKey = "paypal_token_" . sha1($this->clientId . '|' . $this->base);

        return Cache::remember($cacheKey, 300, function () {
            $auth = base64_encode("{$this->clientId}:{$this->secret}");

            $opts = [
                CURLOPT_URL            => $this->base . '/v1/oauth2/token',
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Basic ' . $auth,
                    'Content-Type: application/x-www-form-urlencoded',
                ],
                CURLOPT_POSTFIELDS     => 'grant_type=client_credentials',
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT        => 30,
            ];

            $ch = curl_init();
            curl_setopt_array($ch, $opts);

            $resp = curl_exec($ch);
            $err  = curl_error($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);

            curl_close($ch);

            if ($err || $code < 200 || $code >= 300) {
                Log::error("PayPal token fetch error HTTP {$code}", ['error' => $err, 'resp' => $resp]);
                throw new \RuntimeException("PayPal token error ({$code}): " . ($err ?: (string)$resp));
            }

            $json = json_decode((string)$resp, true);
            $tok = $json['access_token'] ?? null;

            if (!$tok) {
                Log::error('PayPal token missing in response', ['resp' => $resp]);
                throw new \RuntimeException('PayPal token missing.');
            }

            return (string)$tok;
        });
    }

    protected function verifySignature(string $payload, array $headers): bool
    {
        try {
            $h = $this->normalizeHeaders($headers);

            $body = [
                'auth_algo'         => $h['paypal-auth-algo'] ?? '',
                'cert_url'          => $h['paypal-cert-url'] ?? '',
                'transmission_id'   => $h['paypal-transmission-id'] ?? '',
                'transmission_sig'  => $h['paypal-transmission-sig'] ?? '',
                'transmission_time' => $h['paypal-transmission-time'] ?? '',
                'webhook_id'        => $this->webhookId,
                'webhook_event'     => json_decode($payload, true),
            ];

            $res = $this->api('POST', '/v1/notifications/verify-webhook-signature', $body);

            return (($res['verification_status'] ?? '') === 'SUCCESS');
        } catch (\Throwable $e) {
            Log::warning('PayPal webhook verification failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /* ===========================================================
     * Utilities
     * =========================================================== */

    private function extractApproveUrl(array $res): ?string
    {
        $links = $res['links'] ?? null;
        if (!is_array($links)) return null;

        foreach ($links as $l) {
            if (($l['rel'] ?? '') === 'approve' && !empty($l['href'])) {
                return (string)$l['href'];
            }
        }
        return null;
    }

    private function extractMetaFromPayPalOrder(string $paypalOrderId): ?array
    {
        try {
            $ord = $this->api('GET', "/v2/checkout/orders/{$paypalOrderId}");
            $custom = $ord['purchase_units'][0]['custom_id'] ?? null;
            if (!$custom) return null;

            $meta = json_decode((string)$custom, true);
            if (!is_array($meta)) return null;

            if (!isset($meta['order_id'], $meta['payment_link_id'])) return null;

            return $meta;
        } catch (\Throwable $e) {
            Log::warning('PayPal GET order failed for metadata', [
                'paypal_order_id' => $paypalOrderId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    private function normalizeHeaders(array $headers): array
    {
        // incoming $headers sometimes looks like: ['paypal-auth-algo' => ['...']]
        $flat = [];
        foreach ($headers as $k => $v) {
            $key = strtolower((string)$k);
            $flat[$key] = is_array($v) ? (string)($v[0] ?? '') : (string)$v;
        }
        return $flat;
    }

    private function formatHeaders(array $headers): array
    {
        $out = [];
        foreach ($headers as $k => $v) {
            $out[] = $k . ': ' . $v;
        }
        return $out;
    }

    /* ===========================================================
     * Email notifications (after commit recommended)
     * =========================================================== */

    private function sendInitialPaymentNotifications(Payment $payment, Order $order): void
    {
        $client = $order->client;

        // Client
        if ($client && $client->email) {
            Notification::route('mail', $client->email)
                ->notify((new InitialPaymentNotification($payment, $order, $client))->delay(now()->addSeconds(3)));
        }

        // Credited seller
        $creditedSeller = Seller::find($payment->credit_to_seller_id);
        if ($creditedSeller && $creditedSeller->email) {
            Notification::route('mail', $creditedSeller->email)
                ->notify((new InitialPaymentNotification($payment, $order, $client))->delay(now()->addSeconds(6)));
        }

        // PM if different
        if ((int)$order->owner_seller_id !== (int)$order->front_seller_id) {
            $pm = Seller::find($order->owner_seller_id);
            if ($pm && $pm->email) {
                Notification::route('mail', $pm->email)
                    ->notify((new InitialPaymentNotification($payment, $order, $client))->delay(now()->addSeconds(9)));
            }
        }
    }

    private function safeSendFailureEmail(PaymentLink $link, string $reason): void
    {
        try {
            $order = $link->order;
            if ($order && $order->client && $order->client->email) {
                Notification::route('mail', $order->client->email)
                    ->notify(new PaymentFailedNotification(
                        order: $order,
                        provider: 'paypal',
                        reason: $reason,
                        retryUrl: $link->last_issued_url
                    ));
            }
        } catch (\Throwable $e) {
            Log::error('Failed sending PayPal failure email', [
                'link_id' => $link->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}

// class PayPalGateway implements PaymentGateway
// {

//     protected string $clientId;
//     protected string $secret;
//     protected string $base;
//     protected ?string $webhookId;

//     public function __construct(array $config)
//     {
//         $this->clientId  = $config['client_id'];
//         $this->secret    = $config['secret'];
//         $this->base      = rtrim($config['base'] ?? 'https://api.paypal.com', '/');
//         $this->webhookId = $config['webhook_id'] ?? null;
//     }

//     public function createCheckout(PaymentLink $link, array $buyer): array
//     {
//         $payload = [
//             'intent' => 'CAPTURE',
//             'purchase_units' => [[
//                 'amount' => [
//                     'currency_code' => strtoupper($link->currency),
//                     'value'         => number_format($link->unit_amount / 100, 2, '.', ''),
//                 ],
//                 'custom_id' => json_encode([
//                     'order_id'        => (int)$link->order_id,
//                     'payment_link_id' => (int)$link->id,
//                     'token'           => $link->token,
//                 ]),
//                 'description' => $link->service_name,
//             ]],
//             'application_context' => [
//                 'brand_name'          => optional($link->brand)->brand_name ?: config('app.name'),
//                 // 'return_url'          => route('paypal.return', $link->token),
//                 'return_url'          => route('paylinks.success', $link->token),
//                 'cancel_url'  => route('paylinks.cancel', $link->token) . '?canceled=1',
//                 'shipping_preference' => 'NO_SHIPPING',
//                 'user_action'         => 'PAY_NOW',
//             ],
//         ];

//         Log::debug('PayPal createCheckout payload', [
//             'payload' => $payload,
//             'base_url' => $this->base,
//         ]);

//         $res = $this->api('POST', '/v2/checkout/orders', $payload);
//         Log::debug('PayPal API response', ['response' => $res]);

//         $paypalOrderId = $res['id'] ?? null;

//         // Find approve link
//         $approve = null;
//         if (isset($res['links']) && is_array($res['links'])) {
//             foreach ($res['links'] as $linkObj) {
//                 if (isset($linkObj['rel']) && $linkObj['rel'] === 'approve') {
//                     $approve = $linkObj;
//                     break;
//                 }
//             }
//         }

//         $url = $approve['href'] ?? null;

//         if (!$paypalOrderId || !$url) {
//             Log::error('PayPal createCheckout failed: missing id or approve link', [
//                 'res' => $res,
//                 'order_id' => $paypalOrderId,
//                 'approve' => $approve,
//             ]);
//             // Optionally throw an exception or return an error
//             return ['id' => null, 'url' => null];

//             Log::error('PayPal capture detailed failure', [
//                 'paypalOrderId' => $paypalOrderId,
//                 'response' => $e->getMessage(),
//             ]);
//         }

//         // Save session id
//         $link->update(['provider_session_id' => $paypalOrderId]);
//         $link->order?->update(['provider_session_id' => $paypalOrderId]);

//         Log::info('PayPal create checkout success', [
//             'link_id'         => $link->id,
//             'order_id'        => $link->order_id,
//             'paypal_order_id' => $paypalOrderId,
//             'url'              => $url,
//         ]);

//         return ['id' => $paypalOrderId, 'url' => $url];
//     }

//     public function handleCheckoutSuccess(PaymentLink $link, ?string $sessionId): void
//     {
//         $paypalOrderId = $sessionId ?: $link->provider_session_id;
//         if (!$paypalOrderId) return;

//         Log::debug('Calling capture', [
//             'order_id' => $paypalOrderId,
//             'link_details' => $link,
//         ]);


//         try {
//             $cap = $this->api('POST', "/v2/checkout/orders/{$paypalOrderId}/capture");
//             Log::info('PayPal: capture response', ['order' => $paypalOrderId, 'status' => $cap['status'] ?? null]);
//         } catch (\Throwable $e) {
//             Log::warning('PayPal capture failed, retrying in 3 seconds...');
//             sleep(3);

//             try {
//                 $cap = $this->api('POST', "/v2/checkout/orders/{$paypalOrderId}/capture");
//             } catch (\Throwable $e2) {
//                 Log::error('Second PayPal capture attempt failed', [
//                     'order_id' => $paypalOrderId,
//                     'error' => $e2->getMessage(),
//                 ]);
//                 // >>> SEND FAILURE NOTIFICATION <<<
//                 $order = $link->order;
//                 Notification::route('mail', $order->client->email)
//                     ->notify(new PaymentFailedNotification(
//                         order: $order,
//                         provider: 'paypal',
//                         reason: 'Your PayPal payment was declined or could not be processed.',
//                         retryUrl: $link->last_issued_url
//                     ));
//                 return;
//             }
//         }

//         // ✅ Capture object -> payments.captures[0]
//         $purchaseUnit = $cap['purchase_units'][0] ?? [];
//         $capture      = $purchaseUnit['payments']['captures'][0] ?? [];

//         $captureId   = $capture['id'] ?? null;
//         $amountValue = $capture['amount']['value'] ?? null;
//         $currency    = $capture['amount']['currency_code'] ?? null;

//         // ✅ READ custom_id FROM THE CAPTURE
//         $custom_id   = $capture['custom_id'] ?? null;
//         // fallback (rare): some responses include it at the unit level
//         if (!$custom_id) {
//             $custom_id = $purchaseUnit['custom_id'] ?? null;
//         }
//         $meta = $custom_id ? json_decode($custom_id, true) : null;

//         if (!$meta || !isset($meta['order_id'], $meta['payment_link_id'])) {
//             Log::warning('PayPal success missing custom_id metadata.', ['cap' => $cap]);
//             return;
//         }

//         $orderId = (int)$meta['order_id'];
//         $linkId  = (int)$meta['payment_link_id'];
//         $cents   = (int) round(((float)$amountValue) * 100);

//         [$payment, $order] = $this->recordPayment(
//             provider: 'paypal',
//             providerTxnId: $captureId ?: $paypalOrderId,
//             orderId: $orderId,
//             linkId: $linkId,
//             cents: $cents,
//             reportedAmount: $cents,
//             currency: $currency ?: 'USD',
//             rawPayload: $cap
//         );

//         /**
//          * 🔥 SEND INITIAL PAYMENT NOTIFICATIONS HERE
//          */
//         $this->sendInitialPaymentNotifications($payment, $order);
//     }

//     /** Webhook (optional but recommended). Safe to call even if return handled it. */
//     public function handleWebhook(string $payload, array $headers): bool
//     {
//         if ($this->webhookId && !$this->verifySignature($payload, $headers)) {
//             Log::warning('PayPal webhook signature invalid');
//             return false;
//         }
//         Log::info('PayPal webhook received', [
//             'event' => $event['event_type'] ?? 'unknown',
//             'id'    => $event['id'] ?? 'n/a'
//         ]);


//         $event = json_decode($payload, true);
//         if (!is_array($event)) return false;

//         $type = $event['event_type'] ?? '';
//         Log::info('PayPal webhook', ['type' => $type]);

//         if ($type === 'PAYMENT.CAPTURE.COMPLETED') {
//             $resource   = $event['resource'] ?? [];
//             $amount     = $resource['amount']['value'] ?? null;
//             $currency   = $resource['amount']['currency_code'] ?? null;
//             $captureId  = $resource['id'] ?? null;

//             // ✅ try reading custom_id directly off the capture resource
//             $custom_id  = $resource['custom_id'] ?? null;
//             $meta       = $custom_id ? json_decode($custom_id, true) : null;

//             Log::info('PayPal webhook received', ['headers' => $headers, 'payload' => $payload]);

//             // fallback: fetch the PP order and read purchase_units[0].custom_id
//             if (!$meta || !isset($meta['order_id'], $meta['payment_link_id'])) {
//                 $paypalOrderId = $resource['supplementary_data']['related_ids']['order_id'] ?? null;
//                 if ($paypalOrderId) {
//                     $ord        = $this->api('GET', "/v2/checkout/orders/{$paypalOrderId}");
//                     $unitCustom = $ord['purchase_units'][0]['custom_id'] ?? null;
//                     $meta       = $unitCustom ? json_decode($unitCustom, true) : null;
//                 }
//             }

//             if (!$meta || !isset($meta['order_id'], $meta['payment_link_id'])) {
//                 Log::warning('PayPal missing custom_id metadata; skipping.');
//                 return true;
//             }

//             $orderId = (int)$meta['order_id'];
//             $linkId  = (int)$meta['payment_link_id'];
//             $cents   = (int) round(((float)$amount) * 100);

//             [$payment, $order] = $this->recordPayment(
//                 provider: 'paypal',
//                 providerTxnId: $captureId ?: ($event['id'] ?? 'n/a'),
//                 orderId: $orderId,
//                 linkId: $linkId,
//                 cents: $cents,
//                 reportedAmount: $cents,
//                 currency: $currency ?: 'USD',
//                 rawPayload: $event
//             );

//             /**
//              * 🔥 SEND INITIAL PAYMENT NOTIFICATIONS HERE
//              */
//             $this->sendInitialPaymentNotifications($payment, $order);

//             return true;
//         }

//         if ($type === 'CHECKOUT.ORDER.APPROVED') {
//             // usually followed by CAPTURE; nothing to do here
//             return true;
//         }

//         return true;
//     }

//     protected function recordPayment(
//         string $provider,
//         string $providerTxnId,
//         int $orderId,
//         int $linkId,
//         int $cents,
//         int $reportedAmount,
//         string $currency,
//         array $rawPayload
//     ): array {

//         return DB::transaction(function () use (
//             $provider,
//             $providerTxnId,
//             $orderId,
//             $linkId,
//             $cents,
//             $reportedAmount,
//             $currency,
//             $rawPayload
//         ) {

//             $link  = PaymentLink::lockForUpdate()->findOrFail($linkId);
//             $order = Order::lockForUpdate()->findOrFail($orderId);

//             // Idempotency
//             $exists = Payment::where('provider', $provider)
//                 ->where('provider_payment_intent_id', $providerTxnId)
//                 ->exists();

//             if ($exists) {
//                 Log::info('PayPal payment already recorded; skipping', [
//                     'txn' => $providerTxnId
//                 ]);

//                 // ⭐ Return existing payment + order so notifications don't break
//                 $p = Payment::where('provider_payment_intent_id', $providerTxnId)->first();
//                 return [$p, $order];
//             }

//             // compute remaining
//             $remaining   = max(0, (int) $order->unit_amount - (int) $order->amount_paid);
//             $creditCents = min((int)$reportedAmount, $remaining);

//             $decider      = app(CommissionDecider::class);
//             $creditedToId = $link->credit_to_seller_id ?: $decider->creditSellerIdFor($order);

//             // Create payment
//             $payment = Payment::create([
//                 'order_id'        => $order->id,
//                 'payment_link_id' => $link->id,
//                 'amount'          => $creditCents,
//                 'currency'        => strtoupper($currency),
//                 'status'          => 'succeeded',
//                 'provider'        => $provider,
//                 'provider_payment_intent_id' => $providerTxnId,
//                 'payload'         => $rawPayload,

//                 // snapshots
//                 'seller_id'       => (int) $order->seller_id,
//                 'owner_seller_id' => (int) $order->owner_seller_id,
//                 'front_seller_id' => (int) $order->front_seller_id,
//                 'credited_seller_id' => (int) $creditedToId,
//                 'credit_to_seller_id' => (int) $creditedToId,
//             ]);

//             // Update order
//             $order->amount_paid += $creditCents;
//             if (!$order->first_paid_at) {
//                 $order->first_paid_at = now();
//             }
//             $order->provider_payment_intent_id = $providerTxnId;
//             $order->save();

//             if ($link->status !== 'paid') {
//                 $link->update([
//                     'status'                     => 'paid',
//                     'expires_at'                 => now(),
//                     'is_active_link'             => false,
//                     'paid_at'                    => now(),
//                     'provider_session_id'        => $link->provider_session_id, // we stored PP Order ID here
//                     'provider_payment_intent_id' => $providerTxnId,           // store PP Capture ID here
//                 ]);
//             }

//             // Return correctly!
//             return [$payment, $order];
//         });
//     }

//     protected function verifySignature(string $payload, array $headers): bool
//     {
//         try {
//             $body = [
//                 'auth_algo'         => $headers['paypal-auth-algo'][0]       ?? $headers['PayPal-Auth-Algo'][0]       ?? '',
//                 'cert_url'          => $headers['paypal-cert-url'][0]        ?? $headers['PayPal-Cert-Url'][0]        ?? '',
//                 'transmission_id'   => $headers['paypal-transmission-id'][0] ?? $headers['PayPal-Transmission-Id'][0] ?? '',
//                 'transmission_sig'  => $headers['paypal-transmission-sig'][0] ?? $headers['PayPal-Transmission-Sig'][0] ?? '',
//                 'transmission_time' => $headers['paypal-transmission-time'][0] ?? $headers['PayPal-Transmission-Time'][0] ?? '',
//                 'webhook_id'        => $this->webhookId,
//                 'webhook_event'     => json_decode($payload, true),
//             ];
//             $res = $this->api('POST', '/v1/notifications/verify-webhook-signature', $body);
//             return ($res['verification_status'] ?? '') === 'SUCCESS';
//         } catch (\Throwable $e) {
//             Log::warning('PayPal webhook verification failed: ' . $e->getMessage());
//             return false;
//         }
//     }

//     protected function api(string $method, string $path, ?array $json = null): array
//     {
//         $token = $this->getAccessToken();
//         if (!$token) {
//             Log::error('PayPal getAccessToken failed or returned null');
//             throw new \RuntimeException('PayPal access token is missing');
//         }

//         $opts = [
//             CURLOPT_URL            => $this->base . $path,
//             CURLOPT_CUSTOMREQUEST  => $method,
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_HTTPHEADER     => [
//                 'Content-Type: application/json',
//                 'Authorization: ' . "Bearer {$token}",
//             ],
//         ];
//         if ($json !== null) {
//             $opts[CURLOPT_POSTFIELDS] = json_encode($json);
//         }

//         $ch = curl_init();
//         curl_setopt_array($ch, $opts);
//         $resp = curl_exec($ch);
//         $err  = curl_error($ch);
//         $code = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
//         curl_close($ch);

//         if ($err || $code < 200 || $code >= 300) {
//             Log::error("PayPal API error — HTTP {$code}", [
//                 'error'    => $err,
//                 'response' => $resp,
//                 'method'   => $method,
//                 'path'     => $path,
//                 'body'     => $json,
//             ]);
//             throw new \RuntimeException("PayPal API Error ({$code}): " . ($err ?: $resp));
//         }

//         $decoded = json_decode($resp, true);
//         if (!is_array($decoded)) {
//             Log::error('PayPal API returned non-array JSON', ['resp' => $resp]);
//             throw new \RuntimeException("Invalid PayPal API response");
//         }

//         return $decoded;
//     }

//     protected function getAccessToken(): string
//     {
//         return Cache::remember("paypal_token_{$this->clientId}", 300, function () {
//             $auth = base64_encode("{$this->clientId}:{$this->secret}");

//             $opts = [
//                 CURLOPT_URL            => $this->base . '/v1/oauth2/token',
//                 CURLOPT_CUSTOMREQUEST  => 'POST',
//                 CURLOPT_RETURNTRANSFER => true,
//                 CURLOPT_HTTPHEADER     => [
//                     'Authorization: Basic ' . $auth,
//                     'Content-Type: application/x-www-form-urlencoded',
//                 ],
//                 CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
//             ];

//             $ch = curl_init();
//             curl_setopt_array($ch, $opts);
//             $resp = curl_exec($ch);
//             $err  = curl_error($ch);
//             $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
//             curl_close($ch);

//             if ($err || $code < 200 || $code >= 300) {
//                 Log::error("PayPal token fetch error — HTTP {$code}", ['error' => $err, 'resp' => $resp]);
//                 throw new \RuntimeException("PayPal token error ({$code}): " . ($err ?: $resp));
//             }

//             $json = json_decode($resp, true);
//             return $json['access_token'] ?? '';
//         });
//     }

//     /* ================= Email Notification helpers ================= */
//     private function sendInitialPaymentNotifications(Payment $payment, Order $order)
//     {
//         $client = $order->client;
//         Notification::route('mail', $client->email)
//             ->notify(
//                 (new InitialPaymentNotification($payment, $order, $client))
//                     ->delay(now()->addSeconds(3))
//             );
//         $creditedSeller = Seller::find($payment->credit_to_seller_id);
//         if ($creditedSeller && $creditedSeller->email) {
//             Notification::route('mail', $creditedSeller->email)
//                 ->notify(
//                     (new InitialPaymentNotification($payment, $order, $client))
//                         ->delay(now()->addSeconds(6))
//                 );
//         }
//         if ($order->owner_seller_id !== $order->front_seller_id) {
//             $pm = Seller::find($order->owner_seller_id);
//             if ($pm && $pm->email) {
//                 Notification::route('mail', $pm->email)
//                     ->notify(
//                         (new InitialPaymentNotification($payment, $order, $client))
//                             ->delay(now()->addSeconds(9))
//                     );
//             }
//         }
//     }
// }
