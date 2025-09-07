<?php

use App\Http\Controllers\AssetController;
use App\Http\Controllers\OwnerController;
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
         Route::post('/assets', [AssetController::class, 'store'])->name('assets.store');
         Route::get('/assets/{asset}/edit', [AssetController::class, 'pageEdit'])->name('assets.edit');
         Route::put('/assets/{asset}', [AssetController::class, 'update'])->name('assets.update');
         Route::delete('/assets/{asset}', [AssetController::class, 'destroy'])->name('assets.destroy');
         
         Route::get('/api/owners/search', [OwnerController::class, 'search'])->name('owners.search');
         Route::post('/api/owners/find-or-create', [OwnerController::class, 'findOrCreate'])->name('owners.findOrCreate');
     });
