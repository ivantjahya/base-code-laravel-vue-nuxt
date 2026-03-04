<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Master\MasterDataApprovalFlowController;
use App\Http\Controllers\Master\MasterDataFunctionalProfileController;
use App\Http\Controllers\Master\MasterDataLimitController;
use App\Http\Controllers\Master\MasterDataProfileController;
use App\Http\Controllers\Master\MasterDataRegionalSiteController;
use App\Http\Controllers\Master\MasterDataUserController;
use App\Http\Controllers\Master\MasterDataUserGuideController;
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
        Route::middleware(['can:has-master-limit-menu-perm'])->group(function () {
            Route::get('/limit-management', [MasterDataLimitController::class, 'limitManagementPage'])->name('limit-management');
        });

        /** Profile */
        Route::middleware(['can:has-master-profile-menu-perm'])->group(function () {
            Route::get('/profile-management', [MasterDataProfileController::class, 'profileManagementPage'])->name('profile-management');
        });

        /** Functional Profile */
        Route::middleware(['can:has-master-func-profile-menu-perm'])->group(function () {
            Route::get('/functional-profile-management', [MasterDataFunctionalProfileController::class, 'functionalProfileManagementPage'])->name('functional-profile-management');
        });

        /** User */
        Route::middleware(['can:has-master-user-menu-perm'])->group(function () {
            Route::get('/user-management', [MasterDataUserController::class, 'userManagementPage'])->name('user-management');
        });

        /** Approval Flow */
        Route::middleware(['can:has-master-approval-flow-menu-perm'])->group(function () {
            Route::get('/approval-flow-management', [MasterDataApprovalFlowController::class, 'approvalFlowManagementPage'])->name('approval-flow-management');
        });

        /** Regional Site */
        Route::middleware(['can:has-master-regional-site-menu-perm'])->group(function () {
            Route::get('/regional-site', [MasterDataRegionalSiteController::class, 'regionalSitePage'])->name('regional-site');
        });

        /** User Guide */
        Route::middleware(['can:has-master-user-guide-menu-perm'])->group(function () {
            Route::get('/user-guide-management', [MasterDataUserGuideController::class, 'userGuideManagementPage'])->name('user-guide-management');
        });
    });

    Route::middleware(['can:has-tax-supplier-data-menu-perm'])->group(function () {
        /** Tax Supplier Data */
        Route::get('/tax-supplier-data', [HomeController::class, 'exploreNuxtPage'])->name('tax-supplier-data');
    });
});

/** Route for explore nuxt component */
Route::get('/explore-nuxt', [HomeController::class, 'exploreNuxtPage'])->name('explore-nuxt');
