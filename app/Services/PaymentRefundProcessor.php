<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentLink;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\NotifyStakeholders;

class PaymentRefundProcessor
{
    /* *****************************
     *    STRIPE REFUND HANDLER
     ***************************** */
    public function processStripeRefund(\Stripe\Event $event): void
    {
        $charge = $event->data->object;

        $paymentIntentId = $charge->payment_intent ?? null;
        if (!$paymentIntentId) return;

        $payment = Payment::where('provider', 'stripe')
            ->where('provider_payment_intent_id', $paymentIntentId)
            ->first();

        if (!$payment) return;

        $refundAmount = (int) $charge->amount_refunded;
        $this->applyRefund($payment, $refundAmount, 'stripe', $event->toArray());
    }


    /* *****************************
     *   STRIPE CHARGEBACK HANDLER
     ***************************** */
    public function processStripeChargeback(\Stripe\Event $event): void
    {
        $object = $event->data->object;
        $chargeId = $object->charge ?? null;

        if (!$chargeId) return;

        $charge = \Stripe\Charge::retrieve($chargeId);
        $paymentIntentId = $charge->payment_intent ?? null;

        $payment = Payment::where('provider', 'stripe')
            ->where('provider_payment_intent_id', $paymentIntentId)
            ->first();

        if (!$payment) return;

        $refund = (int)$charge->amount;
        $this->applyChargeback($payment, $refund, 'stripe', $event->toArray());
    }


    /* *****************************
     *   PAYPAL REFUND HANDLER
     ***************************** */
    public function processPaypalRefund(array $webhook): void
    {
        $capture = $webhook['resource'] ?? [];
        $txnId   = $capture['id'] ?? null;
        $refunds = $capture['seller_receivable_breakdown']['gross_refund_amount']['value'] ?? null;

        if (!$txnId || !$refunds) return;

        $refundCents = (int)round($refunds * 100);

        $payment = Payment::where('provider', 'paypal')
            ->where('provider_payment_intent_id', $txnId)
            ->first();

        if (!$payment) return;

        $this->applyRefund($payment, $refundCents, 'paypal', $webhook);
    }


    /* *****************************
     *   PAYPAL CHARGEBACK HANDLER
     ***************************** */
    public function processPaypalChargeback(array $webhook): void
    {
        $txn = $webhook['resource']['disputed_transactions'][0]['seller_transaction_id'] ?? null;

        if (!$txn) return;

        $payment = Payment::where('provider', 'paypal')
            ->where('provider_payment_intent_id', $txn)
            ->first();

        if (!$payment) return;

        $refund = (int)$payment->amount;

        $this->applyChargeback($payment, $refund, 'paypal', $webhook);
    }


    /* ===================================
     *   CORE REFUND LOGIC (SHARED)
     * =================================== */
    private function applyRefund(Payment $payment, int $refundCents, string $provider, array $raw)
    {
        DB::transaction(function () use ($payment, $refundCents, $raw, $provider) {

            $order = $payment->order;

            $delta = max(0, $refundCents - (int)$payment->refunded_amount);
            if ($delta <= 0) return;

            // Update payment
            $payment->refunded_amount = $refundCents;
            $payment->refund_status = $refundCents >= $payment->amount ? 'full' : 'partial';
            $payment->status = 'refunded';
            $payment->refund_payload = $raw;
            $payment->save();

            // Update order (NO amount_paid change!)
            $order->refunded_amount += $delta;
            $order->refund_status = $order->refunded_amount >= $order->unit_amount ? 'full' : 'partial';
            $order->status = 'refunded';
            $order->save();

            // Disable any active payment links
            PaymentLink::where('order_id', $order->id)
                ->update([
                    'is_active_link' => false,
                ]);

            // Notify stakeholders
            NotifyStakeholders::refund($payment, $order, $provider, null);

            Log::info("Refund applied safely", [
                'payment_id' => $payment->id,
                'order_id'   => $order->id,
                'delta'      => $delta
            ]);
        });
    }

    // private function applyRefund(Payment $payment, int $refundCents, string $provider, array $raw)
    // {
    //     DB::transaction(function () use ($payment, $refundCents, $raw, $provider) {

    //         $order = $payment->order;

    //         // Prevent double refunding
    //         $delta = max(0, $refundCents - (int)$payment->refunded_amount);
    //         if ($delta <= 0) return;

    //         // Update payment
    //         $payment->refunded_amount = $refundCents;
    //         $payment->refund_status = $refundCents >= $payment->amount ? 'full' : 'partial';
    //         $payment->status = 'refunded';
    //         $payment->refund_payload = $raw;
    //         $payment->save();

    //         // Update order
    //         $order->refunded_amount += $delta;

    //         // old refund amount paid logic
    //         // $order->amount_paid = max(0, $order->amount_paid - $delta);

    //         $order->refund_status = $order->refunded_amount >= $order->unit_amount ? 'full' : 'partial';
    //         $order->status = 'refunded';
    //         $order->save();

    //         // 🔥 SEND NOTIFICATION — refund occurred
    //         NotifyStakeholders::refund($payment, $order, $provider, null);

    //         Log::info("Refund applied", [
    //             'payment_id' => $payment->id,
    //             'order_id'   => $order->id,
    //             'delta'      => $delta
    //         ]);
    //     });
    // }


    private function applyChargeback(Payment $payment, int $disputeAmount, string $provider, array $raw)
    {
        DB::transaction(function () use ($payment, $disputeAmount, $raw, $provider) {

            $order = $payment->order;

            // Update payment
            $payment->refund_status = 'chargeback';
            $payment->status = 'chargeback';
            $payment->refund_payload = $raw;
            $payment->save();

            // Update order
            $order->refund_status = 'chargeback';
            $order->status = 'chargeback';
            // old charge back logic
            // $order->amount_paid = max(0, $order->amount_paid - $disputeAmount);
            $order->save();

            PaymentLink::where('order_id', $order->id)
                ->update([
                    'is_active_link' => false,
                ]);

            // 🔥 SEND NOTIFICATION — chargeback/dispute
            \App\Services\NotifyStakeholders::dispute(
                payment: $payment,
                order: $order,
                provider: $provider,
                stage: 'created',
                reason: 'A dispute/chargeback was filed by the customer.'
            );

            Log::warning("Chargeback applied", [
                'payment_id' => $payment->id,
                'order_id'   => $order->id,
            ]);
        });
    }
}
