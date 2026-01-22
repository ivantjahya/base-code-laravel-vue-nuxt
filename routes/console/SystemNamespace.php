<?php

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

Artisan::command('system:refresh', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    $this->info('Clear all');

    Artisan::call('config:cache');
    $this->info('Re-cached config');

    Log::alert('Console system:refresh executed', ['appName' => config('app.name')]);
})->purpose('Refresh system - clear all and re-cache config');

Artisan::command('system:start', function () {
    if (! App::environment('local')) {
        $this->call('migrate', ['--force' => true]);
        $this->info('Migrated');
    }

    $this->call('cache:clear');

    $this->call('storage:link');

    Cache::flush(); /** Cache */
    $this->call('view:clear');
    $this->info('Cache cleared');

    $this->info('System startup scripts executed');

    Log::alert('Console system:start executed', ['appName' => config('app.name')]);
})->purpose('Start system');
