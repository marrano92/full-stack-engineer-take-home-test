<?php

use App\Http\Controllers\AssetController;
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

         Route::get('/assets', [AssetController::class, 'index'])->name('assets.index');
         Route::get('/assets/add', [AssetController::class, 'pageNew'])->name('assets.create');
         Route::get('/assets/{asset}/edit', [AssetController::class, 'pageEdit'])->name('assets.edit');
     });
