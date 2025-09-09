<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OtpCode extends Model
{

    protected $fillable = [
        'email',
        'otp_code',
        'expires_at',
        'used_at',
    ];


    protected $casts = [
        'expires_at' => 'datetime',
        'used_at'    => 'datetime',
    ];

    public static function generateCode(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    public static function createForEmail(string $email): self
    {
        return self::create([
            'email'      => $email,
            'otp_code'   => self::generateCode(),
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);
    }

    public function isExpired(): bool
    {
        return Carbon::now()->isAfter($this->expires_at);
    }


    public function isUsed(): bool
    {
        return ! is_null($this->used_at);
    }


    public function markAsUsed(): void
    {
        $this->update(['used_at' => Carbon::now()]);
    }

    public static function findValidOtp(string $email, string $code): ?self
    {
        return self::where('email', $email)
                   ->where('otp_code', $code)
                   ->whereNull('used_at')
                   ->where('expires_at', '>', Carbon::now())
                   ->first();
    }

}
