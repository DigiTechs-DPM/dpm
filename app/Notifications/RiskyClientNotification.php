<?php

namespace App\Notifications;

use App\Models\RiskyClient;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RiskyClientNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public RiskyClient $risk,
        public string $title = 'High Risk Client Alert'
    ) {}

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $client = $this->risk->client;
        $features = json_decode($this->risk->features, true);

        return (new MailMessage)
            ->subject("⚠️ {$this->title} — {$client->name}")
            ->markdown('emails.tickets.risky-clients', [
                'title' => $this->title,
                'body'  => "
                    Hello,<br><br>

                    A client has been flagged as <strong>{$this->risk->risk_level}</strong> churn risk.<br><br>

                    <strong>Client:</strong> {$client->name}<br>
                    <strong>Email:</strong> {$client->email}<br>
                    <strong>Risk Score:</strong> {$this->risk->risk_score}<br><br>

                    <strong>Risk Factors:</strong><br>
                    - Days Since Last Order: {$features['days_since_last_order']}<br>
                    - Total Orders: {$features['total_orders']}<br>
                    - Unpaid Orders: {$features['unpaid_orders']}<br>
                    - Lead Responses: {$features['lead_responses']}<br><br>

                    Please review and take required action.<br><br>
                "
            ]);
    }
}
