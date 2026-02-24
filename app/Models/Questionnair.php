<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Questionnair extends Model
{
    use Notifiable;

    protected $fillable = [
        'client_id',
        'order_id',
        'service_name',
        'meta',
        'brief_token',
        'brief_token_expires_at',
        'status',
    ];


    protected static function booted()
    {
        static::creating(function ($q) {
            $q->brief_token = Str::uuid();
            $q->brief_token_expires_at = now()->addDays(14);
        });
    }


    protected $casts = [
        'meta' => 'array',
        'brief_token_expires_at' => 'datetime',
    ];

    // 🔹 Relationship with Client
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    // 🔹 Relationship with Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 🔹 Helper: get single meta field
    public function getMetaValue($key, $default = null)
    {
        return $this->meta[$key] ?? $default;
    }
}
