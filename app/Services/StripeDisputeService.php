<?php

namespace App\Services;

use App\Services\PaymentRefundProcessor;
use Illuminate\Support\Facades\Log;

class StripeDisputeService
{
    public function __construct(
        protected PaymentRefundProcessor $processor
    ) {}

    /**
     * "charge.dispute.created"
     */
    public function created(array $payload): void
    {
        $this->handle($payload, 'charge.dispute.created');
    }

    /**
     * "charge.dispute.updated"
     */
    public function updated(array $payload): void
    {
        $this->handle($payload, 'charge.dispute.updated');
    }

    /**
     * "charge.dispute.closed"
     */
    public function closed(array $payload): void
    {
        $this->handle($payload, 'charge.dispute.closed');
    }

    protected function handle(array $payload, string $label): void
    {
        try {
            $event = \Stripe\Event::constructFrom($payload);

            Log::info("StripeDisputeService handling {$label}", [
                'event_id' => $event->id ?? null,
                'type'     => $event->type ?? null,
            ]);

            // Same core logic for created / updated / closed
            $this->processor->processStripeChargeback($event);
        } catch (\Throwable $e) {
            Log::error("StripeDisputeService {$label} failed", [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
        }
    }
}
