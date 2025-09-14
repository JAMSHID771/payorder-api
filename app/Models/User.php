<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'last_name',
        'phone',
        'email',
        'password',
        'avatar',
        'phone_verified_at',
        'phone_verification_code',
        'phone_verification_expires_at',
        'email_verified_at',
        'is_verified',
        'role',
    ];

    protected $hidden = [
        'password',
        'phone_verification_code',
        'phone_verification_expires_at',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'phone_verified_at' => 'datetime',
            'phone_verification_expires_at' => 'datetime',
            'is_verified' => 'boolean',
            'password' => 'hashed',
        ];
    }

    // Role validation
    public function setRoleAttribute($value)
    {
        if (!in_array($value, ['user', 'admin'])) {
            throw new \InvalidArgumentException('Role must be either "user" or "admin"');
        }
        $this->attributes['role'] = $value;
    }

    public function isVerified(): bool
    {
        return $this->is_verified && $this->phone_verified_at !== null;
    }

    public function isVerificationCodeExpired(): bool
    {
        return $this->phone_verification_expires_at && $this->phone_verification_expires_at->isPast();
    }


    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . $this->last_name);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}