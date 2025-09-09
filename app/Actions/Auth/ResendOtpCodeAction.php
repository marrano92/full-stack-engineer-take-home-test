<?php

namespace App\Actions\Auth;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class ResendOtpCodeAction
{
    /**
     * Resend OTP
     *
     * @throws ValidationException
     */
    public function execute(string $email): array
    {
        if ( ! User::where('email', $email)->exists()) {
            throw ValidationException::withMessages([
                'email' => 'User not found with this email address.'
            ]);
        }

        $this->checkRateLimit($email);

        $otpCode = OtpCode::createForEmail($email);

        try {
            $this->sendOtpEmail($email, $otpCode->otp_code);
        } catch (\Exception $e) {
            $otpCode->delete();
            throw ValidationException::withMessages([
                'email' => 'Error sending email. Please try again.'
            ]);
        }

        return [
            'message'            => 'OTP code sent via email',
            'expires_in_minutes' => 5
        ];
    }

    /**
     * @throws ValidationException
     */
    private function checkRateLimit(string $email): void
    {
        $key = 'otp-send:'.$email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'email' => "Too many requests. Try again in {$seconds} seconds."
            ]);
        }

        RateLimiter::hit($key, 300);
    }

    private function sendOtpEmail(string $email, string $otpCode): void
    {
        Mail::raw(
            "Your access code is: {$otpCode}\n\nThis code expires in 5 minutes.\nDo not share this code with anyone.\n\nIf you did not request this code, please ignore this email.",
            function ($message) use ($email) {
                $message->to($email)
                        ->subject('Your access code - '.config('app.name'));
            }
        );
    }
}
