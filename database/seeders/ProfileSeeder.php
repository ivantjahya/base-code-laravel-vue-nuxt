<?php

namespace Database\Seeders;

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
    }
}
