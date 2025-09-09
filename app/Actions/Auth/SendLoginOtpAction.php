<?php

namespace App\Actions\Auth;

use App\Models\OtpCode;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

class SendLoginOtpAction
{
    /**
     * @throws ValidationException
     */
    public function execute(string $email, string $password): array
    {
        $user = User::where('email', $email)->first();

        if ( ! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

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
            'user'     => $user,
            'otp_sent' => true,
            'message'  => 'OTP sent to your email'
        ];
    }

    private function sendOtpEmail(string $email, string $otpCode): void
    {
        Mail::raw(
            "Your access code is: {$otpCode}\n\nThis code expires in 5 minutes.\nDo not share this code with anyone.",
            function ($message) use ($email) {
                $message->to($email)
                        ->subject('Your access code - '.config('app.name'));
            }
        );
    }
}
