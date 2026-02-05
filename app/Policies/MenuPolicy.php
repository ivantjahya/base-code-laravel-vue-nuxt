<?php

namespace App\Policies;

use App\Interfaces\InterfaceClass;
// use App\Traits\PythonUserProfileMicroserviceFunction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class MenuPolicy
{
    // use PythonMasterDataMicroserviceFunction;

    public function register(): void
    {
        /**
         * List policy of parent menu
         */
        Gate::define('has-master-menu-perm', function ($user) {
            return true;

            // $menuIds = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->aususer, Carbon::now()->addYear(), function () use ($user) {
            //     $menu = $this->callGetMenuPermissionList(request(), $user, 0);

            //     return array_column($menu['data'], 'menu_id');
            // });

            // return in_array(InterfaceClass::PARENT_MENU_ADMINISTRATION, $menuIds);
        });
    }
}
