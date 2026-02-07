<?php

namespace App\Http\Controllers\Compliances;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PaypalRefundService;
use App\Services\StripeRefundService;
use App\Services\PaypalDisputeService;
use App\Services\StripeDisputeService;

class RefundWebhookController extends Controller
{
    public function stripeDisputeHandle(Request $request, StripeDisputeService $service)
    {
        $payload = $request->json()->all();
        $event   = $payload['type'] ?? null;

        switch ($event) {
            case 'charge.dispute.created':
                $service->created($payload);
                break;

            case 'charge.dispute.updated':
                $service->updated($payload);
                break;

            case 'charge.dispute.closed':
                $service->closed($payload);
                break;

            default:
                // ignore unrelated events
                return response()->json(['status' => 'ignored'], Response::HTTP_OK);
        }

        return response()->json(['status' => 'processed'], Response::HTTP_OK);
    }

    public function stripeRefundHandle(Request $request, StripeRefundService $service)
    {
        $payload = $request->json()->all();
        $event   = $payload['type'] ?? null;

        switch ($event) {
            case 'charge.refunded':
                $service->refunded($payload);
                break;

            case 'charge.refund.updated':
                $service->updated($payload);
                break;

            default:
                return response()->json(['status' => 'ignored'], Response::HTTP_OK);
        }

        return response()->json(['status' => 'processed'], Response::HTTP_OK);
    }

    public function paypalDisputeHandle(Request $request, PaypalDisputeService $service)
    {
        $event = $request->get('event_type');

        switch ($event) {
            case 'CUSTOMER.DISPUTE.CREATED':
                $service->created($request->all());
                break;

            case 'CUSTOMER.DISPUTE.UPDATED':
                $service->updated($request->all());
                break;

            case 'CUSTOMER.DISPUTE.RESOLVED':
                $service->closed($request->all());
                break;

            default:
                return response()->json(['status' => 'ignored']);
        }

        return response()->json(['status' => 'processed']);
    }

    public function paypalRefundHandle(Request $request, PaypalRefundService $service)
    {
        $event = $request->get('event_type');

        switch ($event) {
            case 'PAYMENT.CAPTURE.REFUNDED':
            case 'PAYMENT.SALE.REFUNDED':
                $service->refunded($request->all());
                break;

            default:
                return response()->json(['status' => 'ignored']);
        }

        return response()->json(['status' => 'processed']);
    }

    // public function processPaypalRefund(array $webhook)
    // {
    //     try {
    //         $paymentId = $webhook['resource']['id'];
    //         $refundedAmount = $webhook['resource']['amount']['total']; // Assuming it's in the format 'total' => value

    //         // Retrieve payment record
    //         $payment = Payment::findOrFail($paymentId);
    //         // Adjust seller's revenue by subtracting the refunded amount
    //         $seller = $payment->order->seller;
    //         $seller->revenue -= $refundedAmount;

    //         // Update the payment record as refunded
    //         $payment->status = 'refunded';
    //         $payment->amount_refunded = $refundedAmount;
    //         $payment->save();

    //         // Adjust order's balance due (optional, if needed)
    //         $order = $payment->order;
    //         $order->balance_due += $refundedAmount;
    //         $order->save();

    //         // Adjust lead revenue (if needed, based on your business logic)
    //         $lead = $order->lead;
    //         if ($lead) {
    //             $lead->revenue -= $refundedAmount;
    //             $lead->save();
    //         }

    //         // Log successful refund processing
    //         Log::info("Paypal refund processed for payment {$paymentId}. Refunded: {$refundedAmount}");
    //     } catch (\Exception $e) {
    //         // Handle any exceptions (e.g., failed database updates)
    //         Log::error("Error processing PayPal refund", [
    //             'error' => $e->getMessage(),
    //             'webhook' => $webhook
    //         ]);
    //     }
    // }

    // public function processPaypalChargeback(array $webhook)
    // {
    //     try {
    //         $disputeId = $webhook['resource']['dispute_id'];
    //         $chargebackAmount = $webhook['resource']['amount']['total'];

    //         // Find payment related to this dispute
    //         $payment = Payment::where('dispute_id', $disputeId)->firstOrFail();

    //         // Adjust seller's revenue due to chargeback
    //         $seller = $payment->order->seller;
    //         $seller->revenue -= $chargebackAmount;

    //         // Flag payment as chargeback
    //         $payment->status = 'chargeback';
    //         $payment->save();

    //         // Log successful chargeback processing
    //         Log::info("Paypal chargeback processed for dispute {$disputeId}. Amount: {$chargebackAmount}");
    //     } catch (\Exception $e) {
    //         // Handle errors during chargeback processing
    //         Log::error("Error processing PayPal chargeback", [
    //             'error' => $e->getMessage(),
    //             'webhook' => $webhook
    //         ]);
    //     }
    // }
}
