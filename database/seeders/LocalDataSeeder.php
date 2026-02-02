<?php

namespace Database\Seeders;

use App\Interfaces\InterfaceClass;
use App\Models\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class LocalDataSeeder extends Seeder
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

        // Create local site for testing

        // Create local merchendise structure for testing

        // Create local profile for testing
        $profile = Profile::firstOrCreate([
            'code' => 'P0000',
        ], [
            'name' => 'Testing Profile',
            'description' => 'Profile for testing purpose',
            'is_internal' => 1,
            'status' => 1,
        ]);

        // Create local user for testing
        User::firstOrCreate([
            'username' => 'test',
        ], [
            'name' => 'Test User',
            'password' => Hash::make(env('DEFAULT_PASSWORD_USER')),
            'profile_id' => $profile->id,
            'valid_date' => Carbon::parse(InterfaceClass::DEFAULT_VALID_DATE),
        ]);
    }
}
