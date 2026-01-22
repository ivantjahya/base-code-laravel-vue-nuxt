<?php

use App\Http\Controllers\AppConstController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TranslationController;
use App\Http\Middleware\XssProtection;
use Illuminate\Support\Facades\Route;

Route::middleware([XssProtection::class])->group(function () {
    Route::post('/post-token', [AuthController::class, 'postToken'])->name('post-token')->middleware(['throttle:api-secure']);
    Route::post('/post-token-revoke', [AuthController::class, 'postTokenRevoke'])->name('post-token-revoke')->middleware(['throttle:api-secure']);
    Route::get('/translations/{locale}', [TranslationController::class, 'index'])->name('translations');
});

/** Main v1 API */
Route::prefix('v1')->middleware([XssProtection::class])->group(function () {
    Route::post('/post-app-const', [AppConstController::class, 'mainConst'])->name('app-const');
});
