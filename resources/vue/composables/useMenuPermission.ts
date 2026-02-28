import { computed } from 'vue'
import { useMainStore } from '../AppState'

/**
 * Menu permission composable.
 *
 * MENU_CODE and CTRL_CODE are no longer duplicated here — they are served by
 * the backend (MenuConst.php / MenuCtrlConst.php) via the /api/v1/post-app-const
 * endpoint and stored in the Pinia main store.  When a new menu or control is
 * added on the backend, the frontend picks it up automatically without any
 * changes to this file.
 *
 * Usage examples:
 *   const { hasMenu, hasMenuCtrl, MENU_CODE, CTRL_CODE } = useMenuPermission()
 *
 *   hasMenu(MENU_CODE.SUBMENU_LIMITS)
 *   hasMenuCtrl(MENU_CODE.SUBMENU_LIMITS, CTRL_CODE.MENU_CTRL_CREATE)
 */
export function useMenuPermission() {
    const mainStore = useMainStore()

    /** All menu numeric codes keyed by PHP constant name (e.g. SUBMENU_LIMITS: 1010) */
    const MENU_CODE = computed(() => mainStore.menuCodes)

    /** All control string codes keyed by PHP constant name (e.g. MENU_CTRL_CREATE: 'create') */
    const CTRL_CODE = computed(() => mainStore.ctrlCodes)

    /**
     * Find a menu item (top-level or one level of submenu) by its numeric code.
     */
    function findMenuItem(menuCode: number) {
        for (const item of mainStore.accessMenuList) {
            if (item.code === menuCode) return item
            for (const sub of item.submenu ?? []) {
                if (sub.code === menuCode) return sub
            }
        }
        return null
    }

    /**
     * Returns true if the user has the given menu/page in their access list.
     */
    function hasMenu(menuCode: number): boolean {
        return findMenuItem(menuCode) !== null
    }

    /**
     * Returns true if the user has a specific acc_controls action on the menu item.
     *
     * Example:
     *   hasMenuCtrl(MENU_CODE.value.SUBMENU_LIMITS, CTRL_CODE.value.MENU_CTRL_CREATE)
     */
    function hasMenuCtrl(menuCode: number, ctrlCode: string): boolean {
        const item = findMenuItem(menuCode)
        if (!item) return false
        return (item.acc_controls ?? []).some((ctrl) => ctrl.code === ctrlCode)
    }

    return { hasMenu, hasMenuCtrl, MENU_CODE, CTRL_CODE }
}

