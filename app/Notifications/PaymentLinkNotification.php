<?php

namespace App\Notifications;

use App\Models\PaymentLink;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;      // remove this if you don't want to queue
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentLinkNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public PaymentLink $link,
        public string $url
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $amount = number_format($this->link->unit_amount / 100, 2) . ' ' . $this->link->currency;

        return (new MailMessage)
            ->subject('Your Secure Payment Link')
            ->markdown('emails.payment_link_created', [
                'brandName' => $this->link->brand->brand_name ?? 'The DPM Team',
                'service'   => $this->link->service_name,
                'amount'    => $amount,
                'url'       => $this->url,
                'expiresAt' => $this->link->expires_at,
                'lead'      => $this->link->lead,
            ]);
    }

    // public function toMail(object $notifiable): MailMessage
    // {
    //     $amount = number_format($this->link->unit_amount / 100, 2) . ' ' . $this->link->currency;

    //     return (new MailMessage)
    //         ->subject('Your secure payment link')
    //         ->greeting('Hi!')
    //         ->line("Service: {$this->link->service_name}")
    //         ->line("Amount: {$amount}")
    //         ->when(
    //             $this->link->expires_at,
    //             fn($msg) =>
    //             $msg->line('Expires: ' . $this->link->expires_at->toDayDateTimeString())
    //         )
    //         ->action('Pay Now', $this->url)
    //         ->line('If the button does not work, copy and paste this link into your browser:')
    //         ->line($this->url);
    // }

    // Optional: for database channel etc.
    public function toArray(object $notifiable): array
    {
        return [
            'payment_link_id' => $this->link->id,
            'url'             => $this->url,
        ];
    }
}
