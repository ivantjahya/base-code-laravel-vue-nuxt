<?php

namespace App\Policies;

use App\Interfaces\InterfaceClass;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

class MenuCtrlPolicy
{
    /**
     * Walk the user's cached accessMenuList and find the item whose `code`
     * matches $menuCode. The search covers both top-level items and one level
     * of submenu, which matches the current data structure.
     */
    private function findMenuItem(mixed $user, int $menuCode): ?array
    {
        $menuList = Cache::tags([InterfaceClass::TAG_MENUPERM])
            ->get(InterfaceClass::KEY_MENUPERM.'-'.$user?->id, []);

        foreach ($menuList as $item) {
            if (($item['code'] ?? null) === $menuCode) {
                return $item;
            }

            foreach ($item['submenu'] ?? [] as $submenu) {
                if (($submenu['code'] ?? null) === $menuCode) {
                    return $submenu;
                }
            }
        }

        return null;
    }

    /**
     * Check whether the user's cached menu item (matched by $menuCode) contains
     * an acc_controls entry with the given $ctrlCode.
     */
    private function hasMenuCtrl(mixed $user, int $menuCode, string $ctrlCode): bool
    {
        $item = $this->findMenuItem($user, $menuCode);

        if ($item === null) {
            return false;
        }

        foreach ($item['acc_controls'] ?? [] as $ctrl) {
            if (($ctrl['code'] ?? null) === $ctrlCode) {
                return true;
            }
        }

        return false;
    }

    public function register(): void
    {
        /**
         * Generic gates — useful when the menu code is only known at runtime.
         *
         * Gate::allows('has-submenu-perm', [InterfaceClass::SUBMENU_LIMITS])
         * Gate::allows('has-menu-ctrl-perm', [InterfaceClass::SUBMENU_LIMITS, InterfaceClass::MENU_CTRL_CREATE])
         */
        Gate::define('has-submenu-perm', function ($user, int $menuCode): bool {
            return $this->findMenuItem($user, $menuCode) !== null;
        });

        Gate::define('has-menu-ctrl-perm', function ($user, int $menuCode, string $ctrlCode): bool {
            return $this->hasMenuCtrl($user, $menuCode, $ctrlCode);
        });

        // # ------ Limits ------
        Gate::define('has-limit-create-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_LIMITS, InterfaceClass::MENU_CTRL_CREATE);
        });

        Gate::define('has-limit-update-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_LIMITS, InterfaceClass::MENU_CTRL_UPDATE);
        });

        // # ------ Profiles ------
        Gate::define('has-profile-create-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_PROFILES, InterfaceClass::MENU_CTRL_CREATE);
        });

        Gate::define('has-profile-update-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_PROFILES, InterfaceClass::MENU_CTRL_UPDATE);
        });

        // # ------ Functional Profiles ------
        Gate::define('has-func-profile-create-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_FUNC_PROFILES, InterfaceClass::MENU_CTRL_CREATE);
        });

        Gate::define('has-func-profile-update-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_FUNC_PROFILES, InterfaceClass::MENU_CTRL_UPDATE);
        });

        // # ------ Users ------
        Gate::define('has-user-create-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_USERS, InterfaceClass::MENU_CTRL_CREATE);
        });

        Gate::define('has-user-update-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_USERS, InterfaceClass::MENU_CTRL_UPDATE);
        });

        // # ------ Approval Flows ------
        Gate::define('has-approval-flow-create-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_APPROVAL_FLOW, InterfaceClass::MENU_CTRL_CREATE);
        });

        Gate::define('has-approval-flow-update-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_APPROVAL_FLOW, InterfaceClass::MENU_CTRL_UPDATE);
        });

        // # ------ Regional Site ------
        Gate::define('has-regional-site-viewdetail-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_REGIONAL_SITE, InterfaceClass::MENU_CTRL_VIEW_DETAIL);
        });

        // # ------ User Guide ------
        Gate::define('has-user-guide-create-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_USER_GUIDE, InterfaceClass::MENU_CTRL_CREATE);
        });

        Gate::define('has-user-guide-update-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_USER_GUIDE, InterfaceClass::MENU_CTRL_UPDATE);
        });

        Gate::define('has-user-guide-delete-perm', function ($user): bool {
            return $this->hasMenuCtrl($user, InterfaceClass::SUBMENU_USER_GUIDE, InterfaceClass::MENU_CTRL_DELETE);
        });
    }
}
