<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;
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

    /** Dashboard */
    Route::get('/dashboard', [DashController::class, 'dashboardPage'])->name('dashboard');
});

/** Route for explore nuxt component */
Route::get('/explore-nuxt', [DashController::class, 'exploreNuxtPage'])->name('explore-nuxt');
