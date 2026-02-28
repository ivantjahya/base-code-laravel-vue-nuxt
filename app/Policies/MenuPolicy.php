<?php

namespace App\Policies;

use App\Interfaces\InterfaceClass;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class MenuPolicy
{
    /**
     * Check whether the user's cached accessMenuList contains a menu item
     * with the given code, searching both top-level items and one level of
     * submenu (which matches the current data structure).
     *
     * The list is keyed by user ID (snapshot written at login) so it reflects
     * the profile access that was active when the user last logged in.
     */
    private function hasMenuCode(mixed $user, int $menuCode): bool
    {
        $menuList = Cache::tags([InterfaceClass::TAG_MENUPERM])->get(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, []);

        foreach ($menuList as $item) {
            // Check the top-level item (e.g. MENU_MASTER_DATA = 1000)
            if (($item['code'] ?? null) === $menuCode) {
                return true;
            }

            // Check one level of submenu (e.g. SUBMENU_LIMITS = 1010)
            foreach ($item['submenu'] ?? [] as $submenu) {
                if (($submenu['code'] ?? null) === $menuCode) {
                    return true;
                }
            }
        }

        return false;
    }

    public function register(): void
    {
        /**
         * List policy of parent menu.
         * Each Gate reads the user's cached menu snapshot and matches by `code`.
         */
        Gate::define('has-home-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::MENU_HOME);
        });

        Gate::define('has-master-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::MENU_MASTER_DATA);
        });

        Gate::define('has-new-registration-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::MENU_NEW_REGISTRATION);
        });

        Gate::define('has-purchase-order-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::MENU_PURCHASE_ORDER);
        });

        Gate::define('has-consignment-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::MENU_CONSIGNMENT);
        });

        Gate::define('has-principal-report-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::MENU_PRINCIPAL_REPORT);
        });

        Gate::define('has-finance-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::MENU_FINANCE);
        });

        Gate::define('has-dc-fee-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::MENU_DC_FEE);
        });

        Gate::define('has-tax-supplier-data-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::MENU_TAX_SUPPLIER_DATA);
        });

        /**
         * List policy of submenu / page access. Similar to above but also checks the `submenu` array for matches,
         * which is the current data structure.
         */
        Gate::define('has-master-limit-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::SUBMENU_LIMITS);
        });

        Gate::define('has-master-profile-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::SUBMENU_PROFILES);
        });

        Gate::define('has-master-func-profile-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::SUBMENU_FUNC_PROFILES);
        });

        Gate::define('has-master-user-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::SUBMENU_USERS);
        });

        Gate::define('has-master-approval-flow-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::SUBMENU_APPROVAL_FLOW);
        });

        Gate::define('has-master-regional-site-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::SUBMENU_REGIONAL_SITE);
        });

        Gate::define('has-master-user-guide-menu-perm', function ($user) {
            return $this->hasMenuCode($user, InterfaceClass::SUBMENU_USER_GUIDE);
        });
    }
}
