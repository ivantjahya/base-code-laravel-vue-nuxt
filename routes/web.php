<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Master\MasterDataFunctionalProfileController;
use App\Http\Controllers\Master\MasterDataLimitController;
use App\Http\Controllers\Master\MasterDataProfileController;
use App\Http\Controllers\Master\MasterDataUserController;
use App\Http\Controllers\Master\MasterDataUserGuideController;
use App\Http\Controllers\Master\MasterDataRegionalSiteController;
use App\Http\Controllers\MasterDataApprovalFlowController;
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

        /** Profile */
        Route::get('/profile-management', [MasterDataProfileController::class, 'profileManagementPage'])->name('profile-management');

        /** Functional Profile */
        Route::get('/functional-profile-management', [MasterDataFunctionalProfileController::class, 'functionalProfileManagementPage'])->name('functional-profile-management');

        /** User */
        Route::get('/user-management', [MasterDataUserController::class, 'userManagementPage'])->name('user-management');

        /** Approval Flow */
        Route::get('/approval-flow-management', [MasterDataApprovalFlowController::class, 'approvalFlowManagementPage'])->name('approval-flow-management');

        /** Regional Site */
        Route::get('/regional-site', [MasterDataRegionalSiteController::class, 'regionalSitePage'])->name('regional-site');

        /** User Guide */
        Route::get('/user-guide-management', [MasterDataUserGuideController::class, 'userGuideManagementPage'])->name('user-guide-management');
    });
});

/** Route for explore nuxt component */
Route::get('/explore-nuxt', [HomeController::class, 'exploreNuxtPage'])->name('explore-nuxt');
