<?php

namespace App\Providers;

use App\Interfaces\InterfaceClass;
use App\Models\PassportAuthCode;
use App\Models\PassportClient;
use App\Models\PassportRefreshToken;
use App\Models\PassportToken;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        /**
         * Passport Configuration
         */
        Passport::cookie('api_token_cookie');

        Passport::useTokenModel(PassportToken::class);
        Passport::useRefreshTokenModel(PassportRefreshToken::class);
        Passport::useAuthCodeModel(PassportAuthCode::class);
        Passport::useClientModel(PassportClient::class);

        // Set token expiration times
        Passport::tokensExpireIn(InterfaceClass::getPassportTokenLifetime());
        Passport::refreshTokensExpireIn(InterfaceClass::getPassportRefreshTokenLifetime());
    }
}
