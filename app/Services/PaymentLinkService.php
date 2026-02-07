<?php

namespace App\Services;

use App\Models\{Brand, Lead, Client, LeadAssignment, Order, PaymentLink};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentLinkService
{
    // public function createInstallmentLink(
    //     Brand   $brand,
    //     Lead    $lead,
    //     int     $sellerIdWhoGenerated,
    //     string  $serviceName,
    //     string  $currency,
    //     int     $totalCents,
    //     int     $payNowCents,
    //     ?int    $expiresInHours = null,
    //     ?string $description = null,
    //     ?string $provider = null,
    //     ?string $orderType = 'original',      // 'original' | 'renewal'
    //     ?int    $parentOrderId = null,
    //     array   $meta = []
    // ): PaymentLink {
    //     return DB::transaction(function () use (
    //         $brand,
    //         $lead,
    //         $sellerIdWhoGenerated,
    //         $serviceName,
    //         $currency,
    //         $totalCents,
    //         $payNowCents,
    //         $expiresInHours,
    //         $description,
    //         $provider,
    //         $orderType,
    //         $parentOrderId,
    //         $meta
    //     ) {

    //         if ($lead->client_id) {
    //             $client = $lead->client;
    //         } else {
    //             $email  = $lead->email ? strtolower(trim($lead->email)) : null;

    //             $client = $email
    //                 ? Client::whereRaw('LOWER(email)=?', [$email])->first()
    //                 : null;

    //             if (!$client) {
    //                 $client = Client::create([
    //                     'name'  => $lead->name ?: 'Unknown',
    //                     'email' => $email,
    //                     'phone' => $lead->phone,
    //                 ]);
    //             }

    //             $lead->client()->associate($client);
    //             $lead->save();
    //         }

    //         if ($client && (!$client->name || !$client->phone)) {
    //             $client->fill([
    //                 'name'  => $client->name  ?: ($lead->name ?: $client->name),
    //                 'phone' => $client->phone ?: $lead->phone,
    //             ])->save();
    //         }

    //         $assignment = LeadAssignment::where('lead_id', $lead->id)
    //             ->latest('assigned_at')
    //             ->first();
    //         // ✅ FS is always the original lead owner
    //         $frontSellerId = (int) $lead->getOriginal('seller_id');
    //         // ✅ Current owner = PM if assigned, otherwise FS
    //         $currentOwnerId = $assignment
    //             ? (int) $assignment->assigned_to
    //             : (int) $lead->seller_id;

    //         $order = null;

    //         if ($orderType === 'renewal') {
    //             // 🔁 RENEWAL PATH
    //             abort_unless($parentOrderId, 422, 'Parent order required for renewal.');
    //             $parent = Order::lockForUpdate()->findOrFail($parentOrderId);
    //             abort_unless(
    //                 (int) $parent->lead_id === (int) $lead->id &&
    //                     (int) $parent->client_id === (int) $client->id &&
    //                     strcasecmp($parent->service_name, $serviceName) === 0,
    //                 422,
    //                 'Parent order mismatch for this renewal.'
    //             );
    //             // abort_unless(
    //             //     (int) $parent->balance_due === 0 &&
    //             //         in_array($parent->status, ['paid']),
    //             //     422,
    //             //     'Parent order must be fully paid before creating a renewal.'
    //             // );
    //             $order = Order::query()
    //                 ->where('order_type', 'renewal')
    //                 ->where('parent_order_id', $parent->id)
    //                 ->whereIn('status', ['pending'])
    //                 ->lockForUpdate()
    //                 ->first();

    //             if (!$order) {
    //                 // ✅ seller_id stays FS, owner_seller_id is current owner (PM or FS)
    //                 $order = Order::create([
    //                     'lead_id'         => $lead->id,
    //                     'brand_id'        => $brand->id,
    //                     'seller_id'       => $frontSellerId,     // acquisition owner
    //                     'front_seller_id' => $frontSellerId,     // FS snapshot
    //                     'owner_seller_id' => $currentOwnerId,    // PM (or FS if no PM)
    //                     'client_id'       => $client->id,
    //                     'service_name'    => $serviceName,
    //                     'currency'        => strtoupper($currency),
    //                     'unit_amount'     => $totalCents,
    //                     'amount_paid'     => 0,
    //                     'balance_due'     => $totalCents,
    //                     'status'          => 'pending',
    //                     'buyer_name'      => $lead->name,
    //                     'buyer_email'     => $lead->email,
    //                     'order_type'      => 'renewal',
    //                     'parent_order_id' => $parent->id,
    //                 ]);
    //             } else {
    //                 abort_unless($order->currency === strtoupper($currency), 422, 'Currency mismatch.');
    //                 if ($totalCents > (int) $order->unit_amount) {
    //                     $order->unit_amount = $totalCents;
    //                     $order->balance_due = max(0, $totalCents - (int) $order->amount_paid);
    //                     $order->save();
    //                 }
    //             }
    //         } else {
    //             // 🆕 ORIGINAL ORDER PATH
    //             $unpaidExists = Order::query()
    //                 ->where('lead_id', $lead->id)
    //                 ->where('brand_id', $brand->id)
    //                 ->where('client_id', $client->id)
    //                 ->where('service_name', $serviceName)
    //                 ->where('order_type', 'original')
    //                 ->whereIn('status', ['pending'])
    //                 ->exists();
    //             // abort_if($unpaidExists, 422, 'You already have an unpaid original order for this service.');
    //             $order = Order::query()
    //                 ->where('lead_id', $lead->id)
    //                 ->where('brand_id', $brand->id)
    //                 ->where('client_id', $client->id)
    //                 ->where('service_name', $serviceName)
    //                 ->where('order_type', 'original')
    //                 ->whereIn('status', ['pending'])
    //                 ->lockForUpdate()
    //                 ->first();

    //             if (!$order) {
    //                 // ✅ seller_id = FS, owner_seller_id = current owner (FS or PM)
    //                 $order = Order::create([
    //                     'lead_id'         => $lead->id,
    //                     'brand_id'        => $brand->id,
    //                     'seller_id'       => $frontSellerId,     // acquisition owner
    //                     'front_seller_id' => $frontSellerId,
    //                     'owner_seller_id' => $currentOwnerId,    // current owner
    //                     'client_id'       => $client->id,
    //                     'service_name'    => $serviceName,
    //                     'currency'        => strtoupper($currency),
    //                     'unit_amount'     => $totalCents,
    //                     'amount_paid'     => 0,
    //                     'balance_due'     => $totalCents,
    //                     'status'          => 'pending',
    //                     'buyer_name'      => $lead->name,
    //                     'buyer_email'     => $lead->email,
    //                     'order_type'      => 'original',
    //                     'parent_order_id' => null,
    //                 ]);
    //             } else {
    //                 abort_unless($order->currency === strtoupper($currency), 422, 'Currency mismatch.');
    //                 if ($totalCents > (int) $order->unit_amount) {
    //                     $order->unit_amount = $totalCents;
    //                     $order->balance_due = max(0, $totalCents - (int) $order->amount_paid);
    //                     $order->save();
    //                 }
    //             }
    //         }

    //         if ((int) $order->owner_seller_id !== $currentOwnerId) {
    //             $order->owner_seller_id = $currentOwnerId; // PM or FS
    //             $order->save();
    //         }


    //         abort_unless(
    //             $payNowCents <= (int) $order->balance_due,
    //             422,
    //             'Payable exceeds remaining balance.'
    //         );

    //         if ($order->order_type === 'renewal') {
    //             $creditSellerId = (int) $currentOwnerId;
    //         } else {
    //             $isFirstPayment = ((int) $order->amount_paid === 0);
    //             $creditSellerId = $isFirstPayment
    //                 ? (int) $frontSellerId   // FS always gets first payment
    //                 : (int) $currentOwnerId; // later goes to PM
    //         }

    //         $generatedById   = auth('admin')->id() ?? auth('seller')->id();
    //         $generatedByType = auth('admin')->check() ? 'admin' : 'seller';

    //         $hours = max(1, min(720, (int) ($expiresInHours ?? 168)));

    //         return PaymentLink::create([
    //             'lead_id'              => $lead->id,
    //             'brand_id'             => $brand->id,
    //             'client_id'            => $client->id,
    //             'order_id'             => $order->id,
    //             'service_name'         => $serviceName,
    //             'description'          => $description,
    //             'provider'             => $provider,
    //             'currency'             => strtoupper($currency),
    //             'unit_amount'          => $payNowCents,
    //             'order_total_snapshot' => (int) $order->unit_amount,
    //             'token'                => Str::random(48),
    //             'status'               => 'active',
    //             'expires_at'           => now()->addHours($hours),
    //             // ✅ Separate three roles clearly:
    //             'seller_id'           => $frontSellerId,    // acquisition (FS)
    //             'owner_seller_id'     => $currentOwnerId,   // current owner (PM/FS)
    //             'credit_to_seller_id' => $creditSellerId,   // who gets this payment

    //             'generated_by_id'   => $generatedById,
    //             'generated_by_type' => $generatedByType,
    //         ]);
    //     });
    // }


    // payment link generate
    public function createInstallmentLink(
        Brand   $brand,
        Lead    $lead,
        int     $sellerIdWhoGenerated,
        string  $serviceName,
        string  $currency,
        int     $totalCents,
        int     $payNowCents,
        ?int    $expiresInHours = null,
        ?string $description = null,
        ?string $provider = null,
        ?string $orderType = 'original',      // 'original' | 'renewal'
        ?int    $parentOrderId = null,
        array   $meta = []
    ): PaymentLink {
        return DB::transaction(function () use (
            $brand,
            $lead,
            $sellerIdWhoGenerated,
            $serviceName,
            $currency,
            $totalCents,
            $payNowCents,
            $expiresInHours,
            $description,
            $provider,
            $orderType,
            $parentOrderId,
            $meta
        ) {
            // 1) Resolve client (no duplicates)
            if ($lead->client_id) {
                $client = $lead->client;
            } else {
                $email  = $lead->email ? strtolower(trim($lead->email)) : null;
                $client = $email
                    ? Client::whereRaw('LOWER(email)=?', [$email])->first()
                    : null;
                if (!$client) {
                    $client = Client::create([
                        'name'  => $lead->name ?: 'Unknown',
                        'email' => $email,
                        'phone' => $lead->phone,
                    ]);
                }
                $lead->client()->associate($client);
                $lead->save();
            }
            if ($client && (!$client->name || !$client->phone)) {
                $client->fill([
                    'name'  => $client->name  ?: ($lead->name ?: $client->name),
                    'phone' => $client->phone ?: $lead->phone,
                ])->save();
            }

            // 2) Determine current owner vs front seller
            $assignment     = LeadAssignment::where('lead_id', $lead->id)->latest('assigned_at')->first();
            $frontSellerId  = (int) ($assignment?->assigned_by ?: $lead->getOriginal('seller_id'));
            $currentOwnerId = (int) ($assignment?->assigned_to ?: $lead->seller_id);

            // 3) ORIGINAL vs RENEWAL creation rules
            $order = null;

            if ($orderType === 'renewal') {
                // 3a) Enforce: parent must be fully paid (and ideally completed)
                abort_unless($parentOrderId, 422, 'Parent order required for renewal.');
                $parent = Order::lockForUpdate()->findOrFail($parentOrderId);

                abort_unless(
                    (int)$parent->lead_id === (int)$lead->id &&
                        (int)$parent->client_id === (int)$client->id &&
                        strcasecmp($parent->service_name, $serviceName) === 0,
                    422,
                    'Parent order mismatch for this renewal.'
                );

                // abort_unless(
                //     (int)$parent->balance_due === 0 && in_array($parent->status, ['paid', 'completed', 'delivered']),
                //     422,
                //     'Parent order must be fully paid before creating a renewal.'
                // );

                // 3b) Reuse an open renewal (if any) to prevent duplicates
                $order = Order::query()
                    ->where('order_type', 'renewal')
                    ->where('parent_order_id', $parent->parent_order_id)
                    ->whereIn('status', ['pending'])
                    ->lockForUpdate()
                    ->first();
                // dd($parent,$order);

                if (!$order) {
                    $order = Order::create([
                        'lead_id'         => $lead->id,
                        'brand_id'        => $brand->id,
                        'seller_id'       => $currentOwnerId,   // current owner for operational responsibility
                        'front_seller_id' => $frontSellerId,    // snapshot for attribution
                        'owner_seller_id' => $currentOwnerId,   // snapshot for attribution
                        'client_id'       => $client->id,
                        'service_name'    => $serviceName,
                        'currency'        => strtoupper($currency),
                        'unit_amount'     => $totalCents,
                        'status'          => 'pending',
                        'buyer_name'      => $lead->name,
                        'buyer_email'     => $lead->email,
                        'order_type'      => 'renewal',
                        'parent_order_id' => $parent->id,
                    ]);
                } else {
                    // allow only increases; currency must match
                    abort_unless($order->currency === strtoupper($currency), 422, 'Currency mismatch.');
                    if ($totalCents > (int)$order->unit_amount) {
                        $order->unit_amount = $totalCents;
                        $order->save();
                    }
                }
            } else {
                // ORIGINAL order path
                // 3c) HARD RULE: You cannot create a NEW original order if any unpaid order exists for same lead+service
                $unpaidExists = Order::query()
                    ->where('lead_id', $lead->id)
                    ->where('brand_id', $brand->id)
                    ->where('client_id', $client->id)
                    ->where('service_name', $serviceName)
                    ->whereIn('status', ['pending'])
                    ->exists();

                // abort_if($unpaidExists, 422, 'You already have an unpaid order for this service.');

                // Reuse an open original if somehow exists (safety), else create
                $order = Order::query()
                    ->where('lead_id', $lead->id)
                    ->where('brand_id', $brand->id)
                    ->where('client_id', $client->id)
                    ->where('service_name', $serviceName)
                    ->where('order_type', 'original')
                    ->whereIn('status', ['pending'])
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    $order = Order::create([
                        'lead_id'         => $lead->id,
                        'brand_id'        => $brand->id,
                        'seller_id'       => $currentOwnerId,
                        'front_seller_id' => $frontSellerId,
                        'owner_seller_id' => $currentOwnerId,
                        'client_id'       => $client->id,
                        'service_name'    => $serviceName,
                        'currency'        => strtoupper($currency),
                        'unit_amount'     => $totalCents,
                        'status'          => 'pending',
                        'buyer_name'      => $lead->name,
                        'buyer_email'     => $lead->email,
                        'order_type'      => 'original',
                        'parent_order_id' => null,
                    ]);
                } else {
                    abort_unless($order->currency === strtoupper($currency), 422, 'Currency mismatch.');
                    if ($totalCents > (int)$order->unit_amount) {
                        $order->unit_amount = $totalCents;
                        $order->save();
                    }
                }
            }

            // If owner changed after assignment, refresh on open order
            if ((int)$order->owner_seller_id !== $currentOwnerId) {
                $order->owner_seller_id = $currentOwnerId;
                $order->seller_id       = $currentOwnerId;
                $order->save();
            }

            // 4) Guard amounts and compute credit
            abort_unless($payNowCents <= (int)$order->balance_due, 422, 'Payable exceeds remaining balance.');
            // replaced with belower code
            // $isFirstPayment  = ((int)$order->amount_paid === 0);
            // $creditSellerId  = $isFirstPayment ? (int)$frontSellerId : (int)$currentOwnerId;

            if ($order->order_type === 'renewal') {
                // Always credit PM for renewal payments
                $creditSellerId = (int)$currentOwnerId;
            } else {
                // For original orders:
                //  - First payment → FS
                //  - Future payments → PM
                $isFirstPayment  = ((int)$order->amount_paid === 0);
                $creditSellerId  = $isFirstPayment ? (int)$frontSellerId : (int)$currentOwnerId;
            }

            $generatedById   = auth('admin')->id() ?? auth('seller')->id();
            $generatedByType = auth('admin')->check() ? 'admin' : 'seller';

            // 5) Create Payment Link
            return PaymentLink::create([
                'lead_id'              => $lead->id,
                'brand_id'             => $brand->id,
                'client_id'            => $client->id,
                'order_id'             => $order->id,
                'service_name'         => $serviceName,
                'description'          => $description,
                'provider'             => $provider,
                'currency'             => strtoupper($currency),
                'unit_amount'          => $payNowCents,
                'order_total_snapshot' => (int)$order->unit_amount,
                'token'                => Str::random(48),
                'status'               => 'active',
                'expires_at'           => now()->addHours(max(1, min(720, (int)($expiresInHours ?? 168)))),

                // ownership + attribution
                'seller_id'            => $currentOwnerId,   // current owner at issue time
                'owner_seller_id'      => $currentOwnerId,   // snapshot
                'credit_to_seller_id'  => $creditSellerId,   // front for first, owner for later
                'generated_by_id'      => $generatedById,
                'generated_by_type'    => $generatedByType,

                // keep metadata if you store JSON (optional)
                // 'meta' => $meta,
            ]);
        });
    }
}
