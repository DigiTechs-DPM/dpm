<?php

namespace App\Models;


use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class Admin extends Authenticatable
{
    //
    use Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password', // Add all required fields
        'role',
        'last_seen'
    ];

    protected $hidden = [
        'password',
    ];

    public function isOnline()
    {
        return $this->last_seen && $this->last_seen->gt(now()->subMinutes(5));
    }

    // Auto-hash password when setting it
    public function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::make($value);
        }
    }

}
