<?php

namespace Database\Seeders;

use App\Models\Master\KontrabonRegional;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Cache::flush();

        $this->call([
            PassportInitSeeder::class,
            /** For master data */
            AccessControlSeeder::class,
            MenuSeeder::class,
            StatusSeeder::class,
            MerchStructSeeder::class,
            KontrabonRegionalSeeder::class,
            ProfileSeeder::class,
            UserSeeder::class,
            /** For local testing */
            LocalDataSeeder::class,
        ]);
    }
}
