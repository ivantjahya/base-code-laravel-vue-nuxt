<?php

namespace Database\Seeders;

use App\Interfaces\InterfaceClass;
use App\Models\Master\Profile;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /** Only run in production environment */
        if (! App::environment('production')) {
            return;
        }

        /** Initial user for login to application */
        $profileSuperAdmin = Profile::where('code', 'P0001')->first();

        User::firstOrCreate([
            'username' => 'superadmin',
        ], [
            'name' => 'Super Admin',
            'password' => Hash::make(env('DEFAULT_PASSWORD_USER')),
            'profile_id' => $profileSuperAdmin->id,
            'valid_date' => Carbon::parse(InterfaceClass::DEFAULT_VALID_DATE),
        ]);
    }
}
