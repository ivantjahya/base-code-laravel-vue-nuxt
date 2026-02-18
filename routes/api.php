<?php

use App\Http\Controllers\AppConstController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Master\MasterDataLimitController;
use App\Http\Middleware\XssProtection;
use Illuminate\Support\Facades\Route;

Route::middleware([XssProtection::class])->group(function () {
    /** Authentication */
    Route::post('/post-token', [AuthController::class, 'postToken'])->name('post-token')->middleware(['throttle:api-secure']);
    Route::post('/post-token-revoke', [AuthController::class, 'postTokenRevoke'])->name('post-token-revoke')->middleware(['throttle:api-secure']);
});

/** Main v1 API */
Route::prefix('v1')->middleware([XssProtection::class])->group(function () {
    /** App constants */
    Route::post('/post-app-const', [AppConstController::class, 'mainConst'])->name('app-const');

    Route::middleware(['auth:api'])->group(function () {
        Route::prefix('masterdata')->group(function () {
            /** Limit */
            Route::prefix('limit')->group(function () {
                Route::get('/list', [MasterDataLimitController::class, 'getLimitList'])->name('get-limit-list');
                Route::get('/detail/{id}', [MasterDataLimitController::class, 'getLimitDetail'])->name('get-limit-detail');
                Route::post('/create', [MasterDataLimitController::class, 'postLimitCreate'])->name('post-limit-create');
                Route::put('/update/{id}', [MasterDataLimitController::class, 'postLimitUpdate'])->name('post-limit-update');
                Route::put('/extend/{id}', [MasterDataLimitController::class, 'postLimitExtend'])->name('post-limit-extend');
            });
        });
    });
});
