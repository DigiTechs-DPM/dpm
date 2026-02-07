<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class RiskyClient extends Model
{
    use Notifiable;

    protected $fillable = ['client_id', 'risk_level', 'risk_score', 'features', 'status'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
