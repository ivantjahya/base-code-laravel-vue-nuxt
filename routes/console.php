<?php

use Illuminate\Support\Facades\Artisan;

$consoleFiles = glob(__DIR__.'/console/*.php');
foreach ($consoleFiles as $consoleFile) {
    require $consoleFile;
}

Artisan::command('get-additional-config', function () {
    $data = (string) config('additional.default_password_user');
    $this->info($data);
})->purpose('Debug additional config');
