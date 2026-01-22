<?php

namespace App\Providers;

use App\Policies\MenuCtrlPolicy;
use App\Policies\MenuPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (app()->environment('production')) {
            URL::forceScheme('https');
        }

        /**
         * Rate Limiter
         */
        RateLimiter::for('api-secure', function (Request $request) {
            return Limit::perMinute(10)->by($request->user()?->id ?: $request->ip());
        });

        /**
         * Custom policies
         */
        (new MenuPolicy)->register();
        (new MenuCtrlPolicy)->register();
    }
}
