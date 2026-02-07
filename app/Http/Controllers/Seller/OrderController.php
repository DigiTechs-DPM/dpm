<?php

namespace App\Http\Controllers\Seller;

use App\Models\Lead;
use Illuminate\Http\Request;
use App\Services\StripeGateway;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{

    public function sellerLeadFinish(Lead $lead)
    {
        // allow admins, otherwise only the owning front_seller in same brand
        if (!auth('admin')->check()) {
            $seller = auth('seller')->user();
            abort_unless(
                $seller
                    && $seller->id === $lead->seller_id
                    && $seller->brand_id === $lead->brand_id
                    && (($seller->role ?? $seller->is_seller) === 'front_seller'),
                403
            );
        }
        // require payment before finishing (optional but sensible)
        $paid = $lead->paymentLinks()->where('status', 'paid')->exists()
            || optional($lead->client)->orders()->where('status', 'paid')->exists();
        if (!$paid) {
            return back()->with('error', 'Cannot mark as finished until payment is received.');
        }
        $next = (int)! (bool)$lead->is_finish;
        $lead->update([
            'is_finish'   => $next,
            'finished_at' => $next ? now() : null,   // if you track timestamp
        ]);
        return back()->with('success', $next ? 'Lead marked as finished.' : 'Lead reopened.');
    }


    // public function checkoutSuccess(Request $request, string $token, StripeGateway $stripe)
    // {
    //     $link = PaymentLink::with('order')->where('token', $token)->firstOrFail();
    //     $sessionId = $request->query('session_id');
    //     $stripe->handleCheckoutSuccess($link, $sessionId);

    //     return view('paid-success', ['link' => $link->fresh('order'), 'order' => $link->order]);
    // }

    public function handle(Request $request, StripeGateway $stripe)
    {
        $ok = $stripe->handleWebhook($request->getContent(), $request->headers->all());
        return response()->json(['ok' => $ok]);
    }
}
