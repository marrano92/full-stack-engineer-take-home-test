<?php

namespace App\Actions\Auth;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class VerifyOtpAndLoginAction
{
    /**
     * @throws ValidationException
     */
    public function execute(string $email, string $otpCode, Request $request): array
    {

        $this->checkRateLimit($email);

        $validOtp = OtpCode::findValidOtp($email, $otpCode);

        if ( ! $validOtp) {
            $this->recordFailedAttempt($email);
            throw ValidationException::withMessages([
                'otp_code' => 'Invalid or expired OTP code.'
            ]);
        }

        $validOtp->markAsUsed();

        $user = User::where('email', $email)->first();

        if ( ! $user) {
            throw ValidationException::withMessages([
                'email' => 'User not found.'
            ]);
        }

        Auth::login($user);

        $this->cleanupAfterSuccess($email, $request);

        return [
            'user'    => $user,
            'message' => 'Login successful'
        ];
    }

    /**
     * @throws ValidationException
     */
    private function checkRateLimit(string $email): void
    {
        $key = 'otp-verify:'.$email;

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            throw ValidationException::withMessages([
                'otp_code' => "Too many attempts. Try again in {$seconds} seconds."
            ]);
        }
    }

    private function recordFailedAttempt(string $email): void
    {
        $key = 'otp-verify:'.$email;
        RateLimiter::hit($key, 300);
    }

    private function cleanupAfterSuccess(string $email, Request $request): void
    {
        RateLimiter::clear('otp-verify:'.$email);
        RateLimiter::clear('otp-send:'.$email);

        $request->session()->forget('otp_email');
        $request->session()->regenerate();
    }
}
