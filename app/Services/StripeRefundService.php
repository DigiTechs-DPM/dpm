<?php

namespace App\Services;

use App\Services\PaymentRefundProcessor;
use Illuminate\Support\Facades\Log;

class StripeRefundService
{
    public function __construct(
        protected PaymentRefundProcessor $processor
    ) {}

    /**
     * Handle "charge.refunded"
     */
    public function refunded(array $payload): void
    {
        $this->handle($payload, 'charge.refunded');
    }

    /**
     * Handle "charge.refund.updated"
     */
    public function updated(array $payload): void
    {
        $this->handle($payload, 'charge.refund.updated');
    }

    /**
     * Common handler – convert payload to Stripe\Event and delegate
     */
    protected function handle(array $payload, string $label): void
    {
        try {
            // Create a Stripe\Event object from raw array
            $event = \Stripe\Event::constructFrom($payload);

            Log::info("StripeRefundService handling {$label}", [
                'event_id' => $event->id ?? null,
                'type'     => $event->type ?? null,
            ]);

            $this->processor->processStripeRefund($event);
        } catch (\Throwable $e) {
            Log::error("StripeRefundService {$label} failed", [
                'error'   => $e->getMessage(),
                'payload' => $payload,
            ]);
        }
    }
}
