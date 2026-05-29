<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpCode extends Model
{
    protected $fillable = [
        'phone',
        'code',
        'ip_address',
        'attempts',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
        ];
    }

    /**
     * Generate a new 6-digit OTP code.
     */
    public static function generateCode(): string
    {
        return str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Create a new OTP code for a phone number.
     */
    public static function createForPhone(string $phone, string $ipAddress = null): ?self
    {
        // Check rate limit BEFORE creating new OTP
        if (static::isRateLimited($phone)) {
            return null; // Rate limited, caller should handle
        }

        // Increment rate limit counter
        static::incrementRateLimit($phone);

        // Invalidate any existing OTP codes for this phone
        static::where('phone', $phone)->where('expires_at', '>', now())->delete();

        return static::create([
            'phone' => $phone,
            'code' => static::generateCode(),
            'ip_address' => $ipAddress,
            'attempts' => 0,
            'expires_at' => now()->addMinutes(5),
        ]);
    }

    /**
     * Check if the OTP code is valid (not expired and within attempts).
     */
    public function isValid(): bool
    {
        return $this->expires_at->isFuture() && $this->attempts < 3;
    }

    /**
     * Verify the provided code.
     */
    public function verify(string $code): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $valid = hash_equals($this->code, $code);
        
        if (!$valid) {
            $this->increment('attempts');
        }

        return $valid;
    }

    /**
     * Check if rate limit is exceeded for this phone.
     * Uses cache to track request count per phone per minute.
     */
    public static function isRateLimited(string $phone): bool
    {
        $key = "otp_rate_limit:{$phone}";
        $count = Cache::get($key, 0);
        
        return $count >= 3;
    }

    /**
     * Increment the rate limit counter for this phone.
     */
    public static function incrementRateLimit(string $phone): void
    {
        $key = "otp_rate_limit:{$phone}";
        $count = Cache::get($key, 0);
        
        if ($count === 0) {
            // First request - set expiry to 1 minute
            Cache::put($key, 1, now()->addMinute());
        } else {
            // Increment existing counter
            Cache::increment($key);
        }
    }

    /**
     * Clean up expired OTP codes.
     */
    public static function cleanup(): int
    {
        return static::where('expires_at', '<', now())->delete();
    }
}