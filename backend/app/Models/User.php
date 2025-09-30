<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;  // Add this

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;  // Add HasApiTokens

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relationships
    public function business()
    {
        return $this->hasOne(Business::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    public function isWasher()
    {
        return $this->role === 'washer';
    }

    public function isCustomer()
    {
        return $this->role === 'customer';
    }
}