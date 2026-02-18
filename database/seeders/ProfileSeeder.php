<?php

namespace Database\Seeders;

use App\Models\Master\Profile;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class ProfileSeeder extends Seeder
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

        /** Initial profile for login to application */
        Profile::firstOrCreate([
            'code' => 'P0001',
        ], [
            'name' => 'Super Admin',
            'description' => 'Profile with full access rights for ITS',
            'is_internal' => 1,
            'status' => 1,
        ]);
    }
}
