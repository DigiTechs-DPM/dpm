<?php

namespace App\Services;

use App\Services\PaymentRefundProcessor;
use Illuminate\Support\Facades\Log;

class PaypalDisputeService
{
    public function __construct(
        protected PaymentRefundProcessor $processor
    ) {}

    /**
     * CUSTOMER.DISPUTE.CREATED
     */
    public function created(array $webhook): void
    {
        $this->handle($webhook, 'CUSTOMER.DISPUTE.CREATED');
    }

    /**
     * CUSTOMER.DISPUTE.UPDATED
     */
    public function updated(array $webhook): void
    {
        $this->handle($webhook, 'CUSTOMER.DISPUTE.UPDATED');
    }

    /**
     * CUSTOMER.DISPUTE.RESOLVED
     */
    public function closed(array $webhook): void
    {
        $this->handle($webhook, 'CUSTOMER.DISPUTE.RESOLVED');
    }

    protected function handle(array $webhook, string $label): void
    {
        try {
            Log::info("PaypalDisputeService handling {$label}", [
                'event_type' => $webhook['event_type'] ?? null,
                'dispute_id' => $webhook['resource']['dispute_id'] ?? null,
            ]);

            $this->processor->processPaypalChargeback($webhook);
        } catch (\Throwable $e) {
            Log::error("PaypalDisputeService {$label} failed", [
                'error'   => $e->getMessage(),
                'payload' => $webhook,
            ]);
        }
    }
}
