<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AccountKey extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'module',
        'brand_id',
        'brand_url',

        // // Super keys
        // 'super_publishable_key',
        // 'super_secret_key',

        // Stripe
        'stripe_publishable_key',
        'stripe_secret_key',
        'stripe_webhook_secret',

        // PayPal
        'paypal_client_id',
        'paypal_secret',
        'paypal_webhook_id',
        'paypal_base_url',

        // Status
        'status',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
