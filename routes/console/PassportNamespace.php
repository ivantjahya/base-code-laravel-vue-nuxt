<?php

use App\Models\Passport\PassportClient;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

Artisan::command('passport:client:delete {id}', function () {
    $client = PassportClient::where('id', $this->argument('id'))->first();
    if ($client !== null) {
        $client->delete();
        $this->info('Client deleted');
        Log::alert('Console passport:client:delete executed', ['appName' => config('app.name')]);
    } else {
        $this->info('Client not found');
    }
})->purpose('Delete passport client');

Artisan::command('passport:client:env', function () {
    $this->info('Generating personal access client from .env');

    if (config('passport.personal_access_client.id') === null) {
        $this->error('Please set PASSPORT_PERSONAL_ACCESS_CLIENT_ID in .env');

        return;
    }

    $passportClient = PassportClient::where('name', 'Personal Access Client Env')->first();
    if (! is_null($passportClient)) {
        $this->info('Existing Personal Access Client Env found, deleting...', ['id' => $passportClient->id]);
        $passportClient->delete();
    }

    $client = collect();

    DB::transaction(function () use (&$client) {
        $client = PassportClient::create([
            'name' => 'Personal Access Client Env',
            'secret' => 'secret',
            'provider' => 'users',
            'redirect_uris' => [],
            'grant_types' => ['personal_access'],
            'revoked' => false,
        ]);

        $client->id = config('passport.personal_access_client.id');
        $client->secret = config('passport.personal_access_client.secret', Str::random(40));
        $client->save();
    });

    $this->info('Client id: '.$client?->id);
    $this->info('Client Secret: '.$client?->secret);
    $this->info('Client id and secret generated from .env');

    Log::debug('Console passport:client:env executed', ['appName' => config('app.name')]);
})->purpose('Generate personal access client from .env');

Artisan::command('passport:client:grant:env', function () {
    if (config('passport.client_credentials_grant_client.id') === null) {
        $this->error('Please set PASSPORT_CLIENT_CREDENTIALS_GRANT_CLIENT_ID in .env');

        return;
    }

    $passportClient = PassportClient::where('name', 'Client Credentials Client Env')->first();
    if (! is_null($passportClient)) {
        $passportClient->delete();
    }

    $client = collect();

    DB::transaction(function () use (&$client) {
        $client = PassportClient::create([
            'name' => 'Client Credentials Client Env',
            'secret' => 'secret',
            'provider' => 'users',
            'redirect_uris' => [],
            'grant_types' => ['client_credentials'],
            'revoked' => false,
        ]);

        $client->id = config('passport.client_credentials_grant_client.id');
        $client->secret = config('passport.client_credentials_grant_client.secret', Str::random(40));
        $client->save();
    });

    $this->info('Client id: '.$client?->id);
    $this->info('Client secret: '.$client?->secret);
    $this->info('Client id and secret generated from .env');

    Log::debug('Console passport:client:grant:env executed', ['appName' => config('app.name')]);
})->purpose('Generate client credentials access client from .env');
