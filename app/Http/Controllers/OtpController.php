<?php

namespace App\Http\Controllers;

use App\Actions\Auth\ResendOtpCodeAction;
use App\Actions\Auth\VerifyOtpAndLoginAction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    /**
     *
     * @throws ValidationException
     */
    public function sendOtp(Request $request, ResendOtpCodeAction $resendOtpCodeAction): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $result = $resendOtpCodeAction->execute($request->email);

        return response()->json($result);
    }

    /**
     *
     * @throws ValidationException
     */
    public function verifyOtp(
        Request $request,
        VerifyOtpAndLoginAction $verifyOtpAndLoginAction
    ): JsonResponse|RedirectResponse {
        $request->validate([
            'email' => 'required|email',
            'otp_code' => 'required|string|size:6'
        ]);

        $result = $verifyOtpAndLoginAction->execute(
            $request->email,
            $request->otp_code,
            $request
        );

        if ($request->expectsJson()) {
            return response()->json([
                'message' => $result['message'],
                'user'    => $result['user']->only(['id', 'name', 'email'])
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }
}
