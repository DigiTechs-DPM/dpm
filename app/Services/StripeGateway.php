<?php

namespace App\Services;

use App\Models\Seller;
use Stripe\PaymentIntent;
use App\Models\AccountKey;
use Stripe\Checkout\Session;
use App\Services\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use App\Models\{Order, Payment, PaymentLink};
use App\Notifications\PaymentFailedNotification;
use App\Notifications\InitialPaymentNotification;

class StripeGateway implements PaymentGateway
{

    protected string $secret;

    public function __construct(?string $secret = null)
    {
        $this->secret = $secret ?? config('services.stripe.secret');

        if (!$this->secret) {
            throw new \InvalidArgumentException('Stripe secret key is missing.');
        }

        Log::info('StripeGateway initialized with key: ' . substr($this->secret, 0, 6));

        // dd('Dynamic StripeGateway constructed with key', $this->secret); // 🧨 Should hit here!

        \Stripe\Stripe::setApiKey($this->secret);
    }

    public function createCheckout(PaymentLink $link, array $buyer): array
    {
        // Minimal fields from your form:
        $email = $buyer['email'] ?? null;

        Log::info('Stripe: create checkout', ['link_id' => $link->id, 'order_id' => $link->order_id]);

        $session = Session::create([
            'mode'        => 'payment',
            'success_url' => route('paylinks.success', $link->token) . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'  => route('paylinks.cancel', $link->token) . '?canceled=1',
            'line_items'  => [[
                'price_data' => [
                    'currency'     => strtolower($link->currency),
                    'product_data' => ['name' => $link->service_name],
                    'unit_amount'  => $link->unit_amount,
                ],
                'quantity' => 1,
            ]],
            'customer_email' => $email,
            'metadata' => [
                'brand_id'          => (string) $link->brand->id,
                'payment_link_id'   => (string) $link->id,
                'order_id'          => (string) $link->order_id,
                'lead_id'           => (string) $link->lead_id,
                'payment_link_token' => $link->token,
            ],
            // 'metadata' => [
            //     'payment_link_id'    => (string)$link->id,
            //     'order_id'           => (string)$link->order_id,
            //     'lead_id'            => (string)$link->lead_id,
            //     'payment_link_token' => $link->token,
            // ],
            'billing_address_collection' => 'required',
        ], [
            'idempotency_key' => 'paylink_' . $link->token,
        ]);

        // dd($link, $session);

        $link->update(['provider_session_id' => $session->id]);
        $link->order?->update(['provider_session_id' => $session->id]);

        return ['id' => $session->id, 'url' => $session->url];
    }

    public function handleCheckoutSuccess(PaymentLink $link, ?string $sessionId): void
    {
        if (!$sessionId) return;

        $session = \Stripe\Checkout\Session::retrieve($sessionId);
        // if (($session->payment_status ?? null) !== 'paid') return; // old payment failed return
        if (($session->payment_status ?? null) !== 'paid') {

            Notification::route('mail', $link->order->client->email)
                ->notify(new PaymentFailedNotification(
                    order: $link->order,
                    provider: 'stripe',
                    reason: 'Your payment attempt was declined or not completed.',
                    retryUrl: $link->last_issued_url
                ));

            Log::warning('Stripe payment failed during checkout.session.return', [
                'order_id' => $link->order_id,
                'session' => $session->id ?? null
            ]);

            return;
        }

        $piId   = $session->payment_intent ?? null;
        $amount = (int) ($session->amount_total ?? 0);
        $currency = $session->currency ?? $link->currency;

        // ⭐ RETURN PAYMENT + ORDER
        [$payment, $order] = $this->recordPaymentFromGateway(
            link: $link,
            provider: 'stripe',
            providerPaymentIntentId: $piId,
            sessionId: $session->id,
            reportedAmount: $amount,     // Stripe gives amount_total in cents in most modes
            reportedCurrency: $currency,
            payloadArray: $session->toArray()
        );

        // $this->sendInitialPaymentNotifications($payment, $order);

        Log::info('Stripe Initial Payment Notifications sent');
    }

    // webhook logic
    public function handleWebhook(string $payload, array $headers): bool
    {
        Log::info('Stripe Webhook Handler invoked', [
            'payload_raw' => $payload,
            'headers' => $headers,
        ]);
        // Case-insensitive signature header
        $sigHeader = $headers['stripe-signature'][0]
            ?? $headers['Stripe-Signature'][0]
            ?? null;

        if (!$sigHeader) {
            Log::error('Stripe Webhook missing signature header');
            return false;
        }
        // Decode to extract brand_id
        $temp = json_decode($payload, true);
        $brandId = $temp['data']['object']['metadata']['brand_id'] ?? null;

        if (!$brandId) {
            Log::warning('Stripe webhook missing brand_id');
            return false;
        }
        // Load webhook secret for this brand
        $webhookSecret = AccountKey::where('brand_id', $brandId)->value('stripe_webhook_secret');
        if (!$webhookSecret) {
            Log::error("No Stripe webhook secret for brand {$brandId}");
            return false;
        }
        // Verify Stripe signature
        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
        } catch (\Throwable $e) {
            Log::error('Stripe Webhook signature verification failed', [
                'error' => $e->getMessage(),
            ]);
            return false;
        }
        Log::info('Stripe Webhook signature verified', ['event_type' => $event->type]);
        // Handle successful Stripe checkout
        if (in_array($event->type, [
            'checkout.session.completed',
            'checkout.session.async_payment_succeeded'
        ])) {

            $session = $event->data->object;

            $linkId = (int) $session->metadata->payment_link_id;
            $link = PaymentLink::find($linkId);

            if (!$link) {
                Log::warning('PaymentLink not found', ['link_id' => $linkId]);
                return true;
            }
            // Extract amounts
            $piId = $session->payment_intent ?? null;
            $amount = (int) ($session->amount_total ?? 0);
            $currency = $session->currency;

            Log::info("Stripe: Recording payment", [
                'payment_intent' => $piId,
                'amount' => $amount,
                'currency' => $currency
            ]);

            [$payment, $order] = $this->recordPaymentFromGateway(
                link: $link,
                provider: 'stripe',
                providerPaymentIntentId: $piId,
                sessionId: $session->id,
                reportedAmount: $amount,
                reportedCurrency: $currency,
                payloadArray: (array) $session
            );

            // $this->sendInitialPaymentNotifications($payment, $order); // if you want via webhook too

            Log::info('Stripe Initial Payment Notifications sent');
        } else {
            Log::info('Stripe Webhook event ignored', ['type' => $event->type]);
        }

        // payment failed notification
        if ($event->type === 'payment_intent.payment_failed') {
            $pi = $event->data->object;
            $intentId = $pi->id;
            $order = Order::where('provider_payment_intent_id', $intentId)->first();

            if ($order) {
                Notification::route('mail', $order->client->email)
                    ->notify(new PaymentFailedNotification(
                        order: $order,
                        provider: 'stripe',
                        reason: $pi->last_payment_error->message ?? 'Your card was declined.',
                        retryUrl: $order->latestPaymentLink->last_issued_url
                    ));
            }

            Log::info('Stripe payment_intent.payment_failed processed');
            return true;
        }

        return true;
    }

    // In StripeGateway (or a shared payment service)
    private function recordPaymentFromGateway(
        PaymentLink $link,
        string      $provider,
        ?string     $providerPaymentIntentId,
        ?string     $sessionId,
        int         $reportedAmount,
        string      $reportedCurrency,
        array       $payloadArray
    ): array {
        if (!$providerPaymentIntentId) {
            return [null, null];
        }

        return DB::transaction(function () use (
            $link,
            $provider,
            $providerPaymentIntentId,
            $sessionId,
            $reportedAmount,
            $reportedCurrency,
            $payloadArray,
        ) {
            /** @var \App\Models\Order $order */
            $order = Order::lockForUpdate()->findOrFail($link->order_id);

            // 🔐 Idempotency
            $existing = Payment::where('provider', $provider)
                ->where('provider_payment_intent_id', $providerPaymentIntentId)
                ->first();

            if ($existing) {
                // Already processed – return the existing payment + order
                return [$existing, $order];
            }

            // Remaining balance on order
            $remaining    = max(0, (int)$order->unit_amount - (int)$order->amount_paid);
            $creditCents  = min((int)$reportedAmount, $remaining);

            // Commission attribution
            $decider      = app(\App\Services\CommissionDecider::class);
            $creditedToId = $link->credit_to_seller_id ?: $decider->creditSellerIdFor($order);

            /** @var \App\Models\Payment $payment */
            $payment = Payment::create([
                'order_id'                   => $order->id,
                'payment_link_id'            => $link->id,
                'amount'                     => $creditCents,
                'currency'                   => strtoupper($reportedCurrency),
                'status'                     => 'succeeded',
                'provider'                   => $provider,
                'provider_payment_intent_id' => $providerPaymentIntentId,
                'payload'                    => $payloadArray,

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

            $order->provider_session_id        = $sessionId;
            $order->provider_payment_intent_id = $providerPaymentIntentId;
            $order->save();

            Log::info('Stripe payment recorded', [
                'order_id' => $order->id,
                'link_id'  => $link->id,
                'txn_id'   => $providerPaymentIntentId,
                'amount'   => $order->amount_paid,
            ]);

            // Mark link
            if ($link->status !== 'paid') {
                $link->update([
                    'status'                     => 'paid',
                    'expires_at'                     => now(),
                    'is_active_link'                     => false,
                    'paid_at'                    => now(),
                    'provider_session_id'        => $sessionId,
                    'provider_payment_intent_id' => $providerPaymentIntentId,
                ]);
            }

            // Optional counters for FS
            if ($creditedToId === (int)$order->front_seller_id) {
                // $decider->updateCountersAfterCredit($order, $creditCents);
                $decider->updateCountersAfterCredit($order, $creditCents, $creditedToId);
            }

            DB::afterCommit(function () use ($order) {
                if ($order->order_type === 'original' && (int)$order->balance_due === 0) {
                    app(\App\Services\BriefService::class)->dispatchBriefEmail($order->id);
                }
            });

            return [$payment, $order];
        });
    }

    private function sendInitialPaymentNotifications(Payment $payment, Order $order)
    {
        $client = $order->client;
        Notification::route('mail', $client->email)
            ->notify(
                (new InitialPaymentNotification($payment, $order, $client))
                    ->delay(now()->addSeconds(3))
            );
        $creditedSeller = Seller::find($payment->credit_to_seller_id);
        if ($creditedSeller && $creditedSeller->email) {
            Notification::route('mail', $creditedSeller->email)
                ->notify(
                    (new InitialPaymentNotification($payment, $order, $client))
                        ->delay(now()->addSeconds(6))
                );
        }
        if ($order->owner_seller_id !== $order->front_seller_id) {
            $pm = Seller::find($order->owner_seller_id);
            if ($pm && $pm->email) {
                Notification::route('mail', $pm->email)
                    ->notify(
                        (new InitialPaymentNotification($payment, $order, $client))
                            ->delay(now()->addSeconds(9))
                    );
            }
        }
    }

    public function getRevenue($from = null, $to = null)
    {
        $params = [
            'limit' => 100,
        ];

        if ($from && $to) {
            $params['created'] = [
                'gte' => strtotime($from),
                'lte' => strtotime($to),
            ];
        }

        $payments = PaymentIntent::all($params);
        $total = 0;
        foreach ($payments->data as $payment) {
            if ($payment->status === 'succeeded') {
                $total += $payment->amount_received; // in cents
            }
        }
        return $total / 100; // convert to currency
    }
}
