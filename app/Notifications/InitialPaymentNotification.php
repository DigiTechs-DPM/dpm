<?php

namespace App\Notifications;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Client;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InitialPaymentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public $payment,  // Accept both PPC and Upwork Payment models
        public $order,    // Accept both PPC and Upwork Order models
        public $client    // Accept both PPC and Upwork Client models
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail']; // Email notification channel
    }

    public function toMail(object $notifiable): MailMessage
    {
        // Optional check for PPC and Upwork models - ensures compatibility for both modules
        $paymentAmount = optional($this->payment)->amount ?? 0;
        $orderService = optional($this->order)->service_name ?? 'N/A';
        $clientName = optional($this->client)->name ?? 'N/A';

        return (new MailMessage)
            ->subject('Your Payment Was Successful')
            ->view('emails.initial-payment', [
                'payment' => $this->payment,
                'order'   => $this->order,
                'client'  => $this->client,
                'amount'  => $paymentAmount,
                'service' => $orderService,
                'clientName' => $clientName
            ]);
    }
}

// class InitialPaymentNotification extends Notification implements ShouldQueue
// {
//     use Queueable;

//     public function __construct(
//         public Payment $payment,
//         public Order $order,
//         public Client $client
//     ) {}

//     public function via(object $notifiable): array
//     {
//         return ['mail']; // Email notification channel
//     }

//     public function toMail(object $notifiable): MailMessage
//     {
//         return (new MailMessage)
//             ->subject('Your Payment Was Successful')
//             ->view('emails.initial-payment', [
//                 'payment' => $this->payment,
//                 'order'   => $this->order,
//                 'client'  => $this->client,
//             ]);
//     }
// }
