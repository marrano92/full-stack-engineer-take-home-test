<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\OtpController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')
     ->group(function () {
         Route::get('login', [AuthenticatedSessionController::class, 'create'])
              ->name('login');

         Route::post('login', [AuthenticatedSessionController::class, 'store']);

         Route::post('otp/send', [OtpController::class, 'sendOtp'])
              ->name('otp.send');
         Route::post('otp/verify', [OtpController::class, 'verifyOtp'])
              ->name('otp.verify');
     });

Route::middleware('auth')
     ->group(function () {
         Route::any('logout', [AuthenticatedSessionController::class, 'destroy'])
              ->name('logout');
     });
