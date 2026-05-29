<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'telegram_id',
        'phone',
        'name',
        'email',
        'password',
        'tier',
        'avatar_url',
        'metadata',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'metadata' => 'array',
        ];
    }

    public function inspections(): HasMany
    {
        return $this->hasMany(Inspection::class);
    }

    public function isFree(): bool
    {
        return $this->tier === 'free';
    }

    public function isStarter(): bool
    {
        return $this->tier === 'starter';
    }

    public function isPro(): bool
    {
        return $this->tier === 'pro';
    }

    public function isPremium(): bool
    {
        return $this->tier === 'premium';
    }

    public function canCreateInspection(): bool
    {
        return true; // Free tier has 1/month, checked in controller
    }

    public function getMaxInspectionsPerMonth(): int
    {
        return match ($this->tier) {
            'free' => 1,
            'starter' => 5,
            'pro' => -1, // unlimited
            'premium' => -1,
            default => 0,
        };
    }
}
