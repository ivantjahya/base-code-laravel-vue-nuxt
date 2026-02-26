<?php

namespace App\Policies;

use App\Interfaces\InterfaceClass;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class MenuPolicy
{
    public function register(): void
    {
        /**
         * List policy of parent menu
         */
        Gate::define('has-master-menu-perm', function ($user) {
            return true;

            // $menuIds = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, Carbon::now()->addYear(), function () use ($user) {
            //     $menu = $this->callGetMenuPermissionList(request(), $user, 0);
            //     return array_column($menu['data'], 'menu_id');
            // });

            // return in_array(InterfaceClass::MENU_MASTER_DATA, $menuIds);
        });

        Gate::define('has-new-registration-menu-perm', function ($user) {
            return true;

            // $menuIds = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, Carbon::now()->addYear(), function () use ($user) {
            //     $menu = $this->callGetMenuPermissionList(request(), $user, 0);
            //     return array_column($menu['data'], 'menu_id');
            // });

            // return in_array(InterfaceClass::MENU_NEW_REGISTRATION, $menuIds);
        });
        
        Gate::define('has-purchase-order-menu-perm', function ($user) {
            return true;

            // $menuIds = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, Carbon::now()->addYear(), function () use ($user) {
            //     $menu = $this->callGetMenuPermissionList(request(), $user, 0);
            //     return array_column($menu['data'], 'menu_id');
            // });

            // return in_array(InterfaceClass::MENU_PURCHASE_ORDER, $menuIds);
        });

        Gate::define('has-consignment-menu-perm', function ($user) {
            return true;

            // $menuIds = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, Carbon::now()->addYear(), function () use ($user) {
            //     $menu = $this->callGetMenuPermissionList(request(), $user, 0);
            //     return array_column($menu['data'], 'menu_id');
            // });

            // return in_array(InterfaceClass::MENU_CONSIGNMENT, $menuIds);
        });

        Gate::define('has-principal-report-menu-perm', function ($user) {
            return true;

            // $menuIds = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, Carbon::now()->addYear(), function () use ($user) {
            //     $menu = $this->callGetMenuPermissionList(request(), $user, 0);
            //     return array_column($menu['data'], 'menu_id');
            // });

            // return in_array(InterfaceClass::MENU_PRINCIPAL_REPORT, $menuIds);
        });

        Gate::define('has-finance-menu-perm', function ($user) {
            return true;

            // $menuIds = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, Carbon::now()->addYear(), function () use ($user) {
            //     $menu = $this->callGetMenuPermissionList(request(), $user, 0);
            //     return array_column($menu['data'], 'menu_id');
            // });

            // return in_array(InterfaceClass::MENU_FINANCE, $menuIds);
        });

        Gate::define('has-dc-fee-menu-perm', function ($user) {
            return true;

            // $menuIds = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, Carbon::now()->addYear(), function () use ($user) {
            //     $menu = $this->callGetMenuPermissionList(request(), $user, 0);
            //     return array_column($menu['data'], 'menu_id');
            // });

            // return in_array(InterfaceClass::MENU_DC_FEE, $menuIds);
        });

        Gate::define('has-tax-supplier-data-menu-perm', function ($user) {
            return true;

            // $menuIds = Cache::tags([InterfaceClass::TAG_MENUPERM])->remember(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, Carbon::now()->addYear(), function () use ($user) {
            //     $menu = $this->callGetMenuPermissionList(request(), $user, 0);
            //     return array_column($menu['data'], 'menu_id');
            // });

            // return in_array(InterfaceClass::MENU_TAX_SUPPLIER_DATA, $menuIds);
        });
    }
}
