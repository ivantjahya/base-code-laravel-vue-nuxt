import { computed } from 'vue'
import { useMainStore } from '../AppState'

/**
 * Menu path composable.
 *
 * MENU_PATH values are served by the backend (MenuPathConst.php) via the
 * /api/v1/post-app-const endpoint and stored in the Pinia main store.
 * When a new menu path is added on the backend, the frontend picks it up
 * automatically without any changes to this file.
 *
 * Usage example:
 *   const { MENU_PATH } = useMenuPath()
 *
 *   menuPath === MENU_PATH.value.MENU_HOME
 */
export function useMenuPath() {
    const mainStore = useMainStore()

    /** All menu path string codes keyed by PHP constant name (e.g. MENU_HOME: 'HOME') */
    const MENU_PATH = computed(() => mainStore.menuPaths)

    return { MENU_PATH }
}
