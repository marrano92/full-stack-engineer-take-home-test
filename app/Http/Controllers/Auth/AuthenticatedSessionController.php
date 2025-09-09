<?php

namespace App\Http\Controllers\Auth;

use App\Actions\Auth\SendLoginOtpAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login page.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status'           => $request->session()->get('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     * @throws ValidationException
     */
    public function store(LoginRequest $request, SendLoginOtpAction $sendLoginOtpAction): JsonResponse|Response
    {
        $result = $sendLoginOtpAction->execute($request->email, $request->password);

        $request->session()->put('otp_email', $request->email);

        if ($request->expectsJson()) {
            return response()->json([
                'otp_required' => true,
                'message'      => $result['message']
            ]);
        }

        return Inertia::render('Auth/VerifyOtp', [
            'email' => $request->email
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
