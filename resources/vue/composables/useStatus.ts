import { computed } from 'vue'
import { useMainStore } from '../AppState'

/**
 * Status codes composable.
 *
 * STATUS_CODE values are served by the backend (StatusConst.php) via the
 * /api/v1/post-app-const endpoint and stored in the Pinia main store.
 * When a new status code is added on the backend, the frontend picks it up
 * automatically without any changes to this file.
 *
 * Usage example:
 *   const { STATUS_CODE } = useStatus()
 *
 *   statusCode === STATUS_CODE.value.STATUS_USER_ACTIVE
 */
export function useStatus() {
    const mainStore = useMainStore()

    /** All status string codes keyed by PHP constant name (e.g. STATUS_USER_ACTIVE: 'USR_ACTIVE') */
    const STATUS_CODE = computed(() => mainStore.statusCodes)

    return { STATUS_CODE }
}
