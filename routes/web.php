<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashController;
use Illuminate\Support\Facades\Route;

Route::get('/welcome', function () {
    return view('welcome');
});

/** Route for explore nuxt component */
Route::get('/explore-nuxt', [DashController::class, 'exploreNuxtPage'])->name('explore-nuxt');

/** Route for login redirect */
Route::get('/login-redirect', function () {
    return redirect(route('landing-page'));
})->name('login');

Route::get('/login', function () {
    return redirect(route('landing-page'));
})->name('login');

Route::middleware(['guest'])->group(function () {
    /** Login */
    Route::get('/', [AuthController::class, 'loginPage'])->name('landing-page');
    Route::post('/post-login', [AuthController::class, 'postLogin'])->name('post-login');
});

Route::get('/dashboard', [DashController::class, 'dashboardPage'])->name('dashboard');
