<?php

use App\Http\Controllers\AssetsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

require __DIR__.'/auth.php';

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('assets')
        : redirect()->route('login');
});

Route::middleware(['auth'])
     ->group(function () {
         Route::get('/dashboard', function () {
             return to_route('assets.index');
         })->name('dashboard');

         Route::get('/assets', [AssetsController::class, 'index'])->name('assets.index');
         Route::get('/assets/add', [AssetsController::class, 'pageNew'])->name('assets.add');
         Route::get('/assets/{asset}/edit', [AssetsController::class, 'pageEdit'])->name('assets.edit');
     });
