<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class LocalUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** Only run in local or staging environment */
        if (! App::environment('local') && ! App::environment('staging')) {
            return;
        }

        /** User Admin */
        User::firstOrCreate([
            'username' => 'admin',
        ], [
            'name' => 'Admin',
            'password' => Hash::make(env('DEFAULT_PASSWORD_USER')),
        ]);
    }
}
