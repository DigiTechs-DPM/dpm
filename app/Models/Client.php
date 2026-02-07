<?php

namespace App\Models;


use Laravel\Sanctum\HasApiTokens;

use Illuminate\Support\Facades\Hash;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Client extends Authenticatable
{
    use SoftDeletes, Notifiable, HasApiTokens, HasFactory;

    protected $fillable = ['name', 'email', 'password', 'phone', 'meta', 'status', 'last_seen'];

    protected $casts = [
        'meta' => 'array',
    ];

    public function isOnline()
    {
        return $this->last_seen && $this->last_seen->gt(now()->subMinutes(5));
    }


    // Auto-hash password when setting it
    public function setPasswordAttribute($value)
    {
        // Always hash password before saving
        $this->attributes['password'] = Hash::make($value);

        // Also store plain password in meta JSON for internal recovery use
        $meta = is_array($this->meta) ? $this->meta : json_decode($this->meta ?? '{}', true);
        $meta['plain_password'] = $value;
        $this->attributes['meta'] = json_encode($meta);
    }


    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function paymentLinks()
    {
        return $this->hasMany(PaymentLink::class);
    }
}
