<?php

namespace App\Services\Upwork;

use App\Models\Upwork\UpworkOrder;
use App\Models\Upwork\UpworkPayment;
use App\Models\Upwork\UpworkPaymentLink;
use Illuminate\Support\Facades\DB;

class UpworkPaymentRecorder
{
    /**
     * Idempotent: safe to call multiple times for the same provider intent.
     */
    public function recordSucceededPayment(
        UpworkPaymentLink $link,
        string $provider,
        string $providerPaymentIntentId,
        int $reportedAmountCents,
        string $reportedCurrency,
        array $payload = [],
        ?string $sessionId = null
    ): array {
        return DB::transaction(function () use (
            $link,
            $provider,
            $providerPaymentIntentId,
            $reportedAmountCents,
            $reportedCurrency,
            $payload,
            $sessionId
        ) {
            $order = UpworkOrder::lockForUpdate()->findOrFail($link->order_id);

            // ✅ Idempotency guard
            $existing = UpworkPayment::where('provider', $provider)
                ->where('provider_payment_intent_id', $providerPaymentIntentId)
                ->first();

            if ($existing) {
                return [$existing, $order];
            }

            // Cap payment to remaining due (never over-credit)
            $remaining = max(0, (int)$order->balance_due);
            $credit = min($reportedAmountCents, $remaining);

            $payment = UpworkPayment::create([
                'order_id'                   => $order->id,
                'payment_link_id'            => $link->id,
                'amount'                     => $credit,
                'currency'                   => strtoupper($reportedCurrency),
                'status'                     => 'succeeded',
                'provider'                   => $provider,
                'provider_payment_intent_id' => $providerPaymentIntentId,
                'payload'                    => $payload,
            ]);

            // Roll up order totals
            $order->amount_paid = (int)$order->amount_paid + $credit;
            $order->provider_session_id = $sessionId ?: $order->provider_session_id;
            $order->provider_payment_intent_id = $providerPaymentIntentId;
            $order->recomputeAndPersistStatus();
            $order->save();

            // Mark link used
            $link->update([
                'status'                     => 'paid',
                'is_active_link'             => false,
                'paid_at'                    => now(),
                'expires_at'                 => now(),
                'provider_session_id'        => $sessionId,
                'provider_payment_intent_id' => $providerPaymentIntentId,
            ]);

            return [$payment, $order];
        });
    }
}
