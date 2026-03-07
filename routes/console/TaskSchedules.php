<?php

use App\Services\PythonTaskMasterDataService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

/** ---------- Packages Cron ---------- */
// Schedule::command('model:prune')->dailyAt('03:00');

/** ---------- Task Master Data ---------- */
/** Merchandise Structure */
Schedule::call(function () {
    try {
        app(PythonTaskMasterDataService::class)->processMiddlewareMerchStruct();
        Log::info('Scheduled middleware:master:merch-struct completed successfully');
    } catch (\Throwable $e) {
        Log::error('Scheduled middleware:master:merch-struct failed', [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
})->dailyAt('00:00')->name('middleware:master:merch-struct');

/** Site - Gold */
Schedule::call(function () {
    try {
        app(PythonTaskMasterDataService::class)->processMiddlewareSiteGold();
        Log::info('Scheduled middleware:master:site-gold completed successfully');
    } catch (\Throwable $e) {
        Log::error('Scheduled middleware:master:site-gold failed', [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
})->dailyAt('00:00')->name('middleware:master:site-gold');

/** Site - Regional Kontrabon */
Schedule::call(function () {
    try {
        app(PythonTaskMasterDataService::class)->processMiddlewareVendKontrabonRegional();
        Log::info('Scheduled middleware:master:site-kontrabon-regional completed successfully');
    } catch (\Throwable $e) {
        Log::error('Scheduled middleware:master:site-kontrabon-regional failed', [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
})->dailyAt('00:00')->name('middleware:master:site-kontrabon-regional');

/** Site - EBS & Company */
Schedule::call(function () {
    try {
        app(PythonTaskMasterDataService::class)->processMiddlewareSiteEbs();
        Log::info('Scheduled middleware:master:site-ebs completed successfully');
    } catch (\Throwable $e) {
        Log::error('Scheduled middleware:master:site-ebs failed', [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
})->dailyAt('00:00')->name('middleware:master:site-ebs');

/** User - Reset login attempt */
Schedule::call(function () {
    try {
        app(PythonTaskMasterDataService::class)->processUserResetLoginAttempt();
        Log::info('Scheduled middleware:master:user-reset-login-attempt completed successfully');
    } catch (\Throwable $e) {
        Log::error('Scheduled middleware:master:user-reset-login-attempt failed', [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
})->dailyAt('00:00')->name('middleware:master:user-reset-login-attempt');

/** User - Inactive User */
Schedule::call(function () {
    try {
        app(PythonTaskMasterDataService::class)->processInactiveUser();
        Log::info('Scheduled middleware:master:user-inactive completed successfully');
    } catch (\Throwable $e) {
        Log::error('Scheduled middleware:master:user-inactive failed', [
            'error' => $e->getMessage(),
        ]);

        throw $e;
    }
})->dailyAt('00:00')->name('middleware:master:user-inactive');
