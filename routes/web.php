<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MasterDataLimitController;
use Illuminate\Support\Facades\Route;

/** Route for login redirect */
Route::get('/login-redirect', function () {
    return redirect(route('landing-page'));
})->name('login');

Route::get('/login', function () {
    return redirect(route('landing-page'));
})->name('login');

/** Routes that do not need authentication */
Route::middleware(['guest'])->group(function () {
    /** Login */
    Route::get('/', [AuthController::class, 'loginPage'])->name('landing-page');
    Route::post('/post-login', [AuthController::class, 'postLogin'])->name('post-login');
});

/** Routes that need authentication first */
Route::middleware(['auth'])->group(function () {
    /** Logout */
    Route::post('/post-logout', [AuthController::class, 'postLogout'])->name('post-logout');
    Route::get('/get-logout', [AuthController::class, 'getLogout'])->name('get-logout');

    /** Home */
    Route::get('/home', [HomeController::class, 'homePage'])->name('home');

    Route::middleware(['can:has-master-menu-perm'])->group(function () {
        /** Limit */
        Route::get('/limit-management', [MasterDataLimitController::class, 'limitManagementPage'])->name('limit-management');
    });
});

/** Route for explore nuxt component */
Route::get('/explore-nuxt', [HomeController::class, 'exploreNuxtPage'])->name('explore-nuxt');
