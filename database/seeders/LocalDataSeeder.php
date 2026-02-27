<?php

namespace Database\Seeders;

use App\Interfaces\InterfaceClass;
use App\Models\Master\Menu;
use App\Models\Master\MenuAccControl;
use App\Models\Master\Profile;
use App\Models\Master\ProfileMenu;
use App\Models\Master\Status;
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

        // Create local profile for testing
        $profile = Profile::firstOrCreate([
            'code' => 'P0000',
        ], [
            'name' => 'Testing Profile',
            'description' => 'Profile for testing purpose',
            'is_internal' => 1,
            'status' => 1,
        ]);

        // Link profile with all menus and access control for testing
        Menu::chunkById(1000, function ($menus) use ($profile) {
            foreach ($menus as $menu) {
                $menuAccControls = MenuAccControl::where('menu_id', $menu->id)->get();

                if ($menuAccControls->isEmpty()) {
                    ProfileMenu::firstOrCreate([
                        'profile_id' => $profile->id,
                        'menu_id' => $menu->id,
                        'acc_control_id' => null
                    ], []);
                } else {
                    foreach ($menuAccControls as $menuAccControl) {
                        ProfileMenu::firstOrCreate([
                            'profile_id' => $profile->id,
                            'menu_id' => $menu->id,
                            'acc_control_id' => $menuAccControl->acc_control_id,
                        ], []);
                    }
                }
            }
        }, $column = 'id');

        // Create local user for testing
        $statusIdActive = Status::where('code', 'USR_ACTIVE')->value('id');

        User::firstOrCreate([
            'username' => 'test',
        ], [
            'name' => 'Test User',
            'password' => Hash::make(env('DEFAULT_PASSWORD_USER')),
            'profile_id' => $profile->id,
            'valid_date' => Carbon::parse(InterfaceClass::DEFAULT_VALID_DATE),
            'status_id' => $statusIdActive,
        ]);
    }
}
