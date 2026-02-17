<?php

namespace App\Services\Upwork;

use App\Models\Brand;
use Illuminate\Support\Str;
use App\Models\Upwork\UpworkOrder;
use Illuminate\Support\Facades\DB;
use App\Models\Upwork\UpworkClient;
use Illuminate\Support\Facades\Log;
use App\Models\Upwork\UpworkPaymentLink;

class UpworkLinkGenerator
{
    /**
     * Create an original order and its first payment link
     */
    public function createOriginalOrderAndFirstLink(
        Brand $brand,
        array $clientData,
        string $sellType,
        string $serviceName,
        string $currency,
        int $totalCents,
        int $payNowCents,
        string $provider,
        int $expiresInHours = 168,
        $generatedBy = null
    ): UpworkPaymentLink {
        return DB::transaction(function () use (
            $brand,
            $clientData,
            $sellType,
            $serviceName,
            $currency,
            $totalCents,
            $payNowCents,
            $provider,
            $expiresInHours,
            $generatedBy
        ) {
            try {
                // Guard against invalid amounts
                abort_unless($totalCents > 0, 422, 'Invalid total amount.');
                abort_unless($payNowCents > 0, 422, 'Invalid pay now amount.');
                abort_unless($payNowCents <= $totalCents, 422, 'Pay now exceeds total amount.');

                // 1) Client creation or reuse
                $email = strtolower(trim($clientData['email'] ?? ''));
                abort_unless($email, 422, 'Client email required.');

                try {
                    $client = UpworkClient::create([
                        'name'   => $clientData['name'] ?? 'Unknown',
                        'email'  => $email,
                        'phone'  => $clientData['phone'] ?? null,
                        'status' => 'Active',
                    ]);
                } catch (\Illuminate\Database\QueryException $e) {
                    // If another request created it first, fetch it
                    $client = UpworkClient::whereRaw('LOWER(email)=?', [$email])
                        ->lockForUpdate()
                        ->firstOrFail();
                }

                Log::info('Upwork client created/reused', ['client' => $client->toArray()]);

                // dd($client);

                // 2) Check for existing unpaid orders
                $serviceName = $this->normalizeServiceName($serviceName);

                $order = UpworkOrder::query()
                    ->where('client_id', $client->id)
                    ->where('brand_id', $brand->id)
                    ->where('service_name', $serviceName)
                    ->where('order_type', 'original')
                    ->where('balance_due', '>', 0)
                    ->lockForUpdate()
                    ->first();

                if (!$order) {
                    // Create a new original order if no existing unpaid order
                    $order = UpworkOrder::create([
                        'client_id'    => $client->id,
                        'brand_id'     => $brand->id,
                        'order_type'   => 'original',
                        'sell_type'    => $sellType,
                        'service_name' => $serviceName,
                        'currency'     => strtoupper($currency),
                        'unit_amount'  => $totalCents,
                        'amount_paid'  => 0,
                        'balance_due'  => $totalCents,
                        'status'       => 'pending',
                    ]);
                } else {
                    // Reuse existing open order
                    abort_unless($order->currency === strtoupper($currency), 422, 'Currency mismatch.');
                    if ($totalCents > (int)$order->unit_amount) {
                        $order->unit_amount = $totalCents;
                        $order->balance_due = max(0, $totalCents - (int)$order->amount_paid);
                        $order->save();
                    }
                }

                // Ensure payNowCents does not exceed balance due
                abort_unless($payNowCents <= (int)$order->balance_due, 422, 'Pay now exceeds remaining balance.');

                // 3) Expire any existing active links for this order
                $this->expireActiveLinksForOrder($order->id);

                // 4) Create the payment link
                return UpworkPaymentLink::create([
                    'order_id'             => $order->id,
                    'brand_id'             => $brand->id,
                    'client_id'            => $client->id,
                    'service_name'         => $order->service_name,
                    'currency'             => $order->currency,
                    'provider'             => $provider,
                    'unit_amount'          => $payNowCents,
                    'order_total_snapshot' => (int)$order->unit_amount,
                    'token'                => Str::random(48),
                    'status'               => 'active',
                    'expires_at'           => now()->addHours($this->capHours($expiresInHours)),
                    'is_active_link'       => true,
                    'generated_by_id'      => $generatedBy?->id,
                    'generated_by_type'    => $generatedBy ? class_basename($generatedBy) : null,
                ]);
            } catch (\Exception $e) {
                Log::error('Error generating original order and first payment link', ['error' => $e->getMessage()]);
                throw new \RuntimeException('An error occurred while processing the payment link generation.');
            }
        });
    }

    /**
     * Create an installment link for a given order
     */
    public function createInstallmentLinkForOrder(
        UpworkOrder $order,
        int $payNowCents,
        string $provider,
        int $expiresInHours = 168,
        $generatedBy = null
    ): UpworkPaymentLink {
        return DB::transaction(function () use ($order, $payNowCents, $provider, $expiresInHours, $generatedBy) {

            try {
                $order = UpworkOrder::lockForUpdate()->findOrFail($order->id);

                abort_unless((int)$order->balance_due > 0, 422, 'Order already paid.');
                abort_unless($payNowCents > 0, 422, 'Invalid amount.');
                abort_unless($payNowCents <= (int)$order->balance_due, 422, 'Pay now exceeds remaining due.');

                // 3) Expire any active links for this order
                $this->expireActiveLinksForOrder($order->id);

                // 4) Create installment payment link
                return UpworkPaymentLink::create([
                    'order_id'             => $order->id,
                    'brand_id'             => $order->brand_id,
                    'client_id'            => $order->client_id,
                    'service_name'         => $order->service_name,
                    'currency'             => $order->currency,
                    'provider'             => $provider,
                    'unit_amount'          => $payNowCents,
                    'order_total_snapshot' => (int)$order->unit_amount,
                    'token'                => Str::random(48),
                    'status'               => 'active',
                    'expires_at'           => now()->addHours($this->capHours($expiresInHours)),
                    'is_active_link'       => true,
                    'generated_by_id'      => $generatedBy?->id,
                    'generated_by_type'    => $generatedBy ? class_basename($generatedBy) : null,
                ]);
            } catch (\Exception $e) {
                Log::error('Error generating installment payment link', ['error' => $e->getMessage()]);
                throw new \RuntimeException('An error occurred while processing the installment payment link generation.');
            }
        });
    }

    /**
     * Expire any active payment links for a given order.
     */
    private function expireActiveLinksForOrder(int $orderId): void
    {
        UpworkPaymentLink::where('order_id', $orderId)
            ->where('is_active_link', true)
            ->update([
                'is_active_link' => false,
                'status'         => 'expired',
                'expires_at'     => now(),
            ]);
    }

    /**
     * Ensure the expiration hours are within acceptable bounds.
     */
    private function capHours(int $hours): int
    {
        return max(1, min(720, $hours));
    }

    /**
     * Normalize the service name (trim spaces, collapse multiple spaces).
     */
    private function normalizeServiceName(string $serviceName): string
    {
        $s = trim($serviceName);
        $s = preg_replace('/\s+/', ' ', $s); // collapse multiple spaces
        return $s;
    }
}
