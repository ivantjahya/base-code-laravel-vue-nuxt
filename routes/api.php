<?php

use App\Http\Controllers\AppConstController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Master\MasterDataFunctionalProfileController;
use App\Http\Controllers\Master\MasterDataLimitController;
use App\Http\Controllers\Master\MasterDataMenuController;
use App\Http\Controllers\Master\MasterDataMerchStructController;
use App\Http\Controllers\Master\MasterDataProfileController;
use App\Http\Controllers\Master\MasterDataRegionalSiteController;
use App\Http\Controllers\Master\MasterDataUserController;
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
            /** Merch Struct */
            Route::prefix('merch-struct')->group(function () {
                Route::get('/list-merch-struct-div-cat', [MasterDataMerchStructController::class, 'getMerchStructDivCatList'])->name('get-merch-struct-div-cat-list');
            });

            /** Limit */
            Route::prefix('limit')->group(function () {
                Route::get('/list', [MasterDataLimitController::class, 'getLimitList'])->name('get-limit-list');
                Route::get('/detail/{id}', [MasterDataLimitController::class, 'getLimitDetail'])->name('get-limit-detail');
                Route::middleware(['can:has-limit-create-perm'])->group(function () {
                    Route::post('/create', [MasterDataLimitController::class, 'postLimitCreate'])->name('post-limit-create');
                });
                Route::middleware(['can:has-limit-update-perm'])->group(function () {
                    Route::put('/update/{id}', [MasterDataLimitController::class, 'postLimitUpdate'])->name('post-limit-update');
                    Route::put('/extend/{id}', [MasterDataLimitController::class, 'postLimitExtend'])->name('post-limit-extend');
                });
            });

            /** Menu */
            Route::prefix('menu')->group(function () {
                Route::get('/list', [MasterDataMenuController::class, 'getMenuList'])->name('get-menu-list');
                Route::get('/list-menu-acc-control', [MasterDataMenuController::class, 'getMenuAccControlList'])->name('get-menu-acc-control-list');
            });

            /** Profile */
            Route::prefix('profile')->group(function () {
                Route::get('/list', [MasterDataProfileController::class, 'getProfileList'])->name('get-profile-list');
                Route::get('/detail/{id}', [MasterDataProfileController::class, 'getProfileDetail'])->name('get-profile-detail');
                Route::get('/get-menu-access/{id}', [MasterDataProfileController::class, 'getProfileMenuAccess'])->name('get-profile-menu-access');
                Route::middleware(['can:has-profile-create-perm'])->group(function () {
                    Route::post('/create', [MasterDataProfileController::class, 'postProfileCreate'])->name('post-profile-create');
                });
                Route::middleware(['can:has-profile-update-perm'])->group(function () {
                    Route::put('/update/{id}', [MasterDataProfileController::class, 'postProfileUpdate'])->name('post-profile-update');
                });
            });

            /** Functional Profile */
            Route::prefix('func-profile')->group(function () {
                Route::get('/list', [MasterDataFunctionalProfileController::class, 'getFuncProfileList'])->name('get-func-profile-list');
                Route::get('/detail/{id}', [MasterDataFunctionalProfileController::class, 'getFuncProfileDetail'])->name('get-func-profile-detail');
                Route::middleware(['can:has-func-profile-create-perm'])->group(function () {
                    Route::post('/create', [MasterDataFunctionalProfileController::class, 'postFuncProfileCreate'])->name('post-func-profile-create');
                });
                Route::middleware(['can:has-func-profile-update-perm'])->group(function () {
                    Route::put('/update/{id}', [MasterDataFunctionalProfileController::class, 'postFuncProfileUpdate'])->name('post-func-profile-update');
                });
            });

            /** User */
            Route::prefix('user')->group(function () {
                Route::get('/list', [MasterDataUserController::class, 'getUserList'])->name('get-user-list');
            });

            /** Regional Site */
            Route::prefix('site')->group(function () {
                Route::get('/list', [MasterDataRegionalSiteController::class, 'getSiteList'])->name('get-site-list');
                Route::get('/detail/{id}', [MasterDataRegionalSiteController::class, 'getSiteDetail'])->name('get-site-detail');
            });
        });
    });
});
