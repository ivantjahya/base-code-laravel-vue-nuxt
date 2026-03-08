<script setup lang="ts">
import { ref, computed, h, resolveComponent, onMounted } from 'vue'
import { useI18n } from '../composables/useI18n'
import { useMenuPermission } from '../composables/useMenuPermission'
import { useApiStore } from '../AppState'
import axios from 'axios'
import { getCurrentInstance } from 'vue'
import { useGlobalOptions } from '../composables/useGlobalOptions'
import CmpLayout from '../Components/CmpLayout.vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'
import CmpAccordionFilter from '../Components/CmpAccordionFilter.vue'
import DialogFormUser from './Components/DialogFormUser.vue'
import FormFilterUser from './Components/FormFilterUser.vue'
import { TEXT_SIZE_CLASS, TEXT_TITLE_SIZE_CLASS, TITLE_TEXT_CLASS, TABLE_TEXT_STATUS_SIZE_CLASS, BUTTON_PRIMARY_CLASS } from '../constants'

const { t } = useI18n()
const { hasMenuCtrl, MENU_CODE, CTRL_CODE } = useMenuPermission()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')

type MerchStructData = {
    id: string
    code: string
    name: string
    parent_id: string | null
}

type SiteData = {
    id: string
    code: string
    initial: string
    name: string
    address: string
    city: string
    region: string
    im_auto_flag: number
    dc_flag: number
    start_date: string
    end_date: string
}

// ========================= PERMISSIONS =========================
const canCreateUser = computed(() => hasMenuCtrl(MENU_CODE.value.SUBMENU_USERS, CTRL_CODE.value.MENU_CTRL_CREATE))
const canUpdateUser = computed(() => hasMenuCtrl(MENU_CODE.value.SUBMENU_USERS, CTRL_CODE.value.MENU_CTRL_UPDATE))

// ========================= FILTER =========================
const usernameFilter = ref('')
const nameFilter = ref('')
const profileFilter = ref<string | null>(null)
const categoryFilter = ref<string | null>(null)
const statusFilter = ref<string | null>(null)

// For select options
const profileOptions = ref<{ label: string; value: string }[]>([])
const siteOptions = ref<{ label: string; value: string }[]>([])
const categoryOptions = ref<{ label: string; value: string }[]>([])
const categoryOptionsLoading = ref(false)

const resetFilter = () => {
    usernameFilter.value = ''
    nameFilter.value = ''
    profileFilter.value = null
    categoryFilter.value = null
    statusFilter.value = null
}

// ========================= TABLE =========================
const userData = ref([])
const currentPage = ref(1)
const itemPerPage = ref(10)
const countTotalData = ref(0)
const loadingTable = ref(false)
const showLoadingOverlay = ref(true)
const globalSearchQuery = ref('') // For global search, server-side

const columns = computed(() => [
    {
        key: 'username',
        label: t('text.table-column.column-username'),
        sortable: true
    },
    {
        key: 'name',
        label: t('text.table-column.column-name'),
        sortable: true
    },
    {
        key: 'profile',
        label: t('text.table-column.column-profile'),
        sortable: true,
        cellRenderer: (_value: any, row: any) => row.profile ? row.profile?.name : '-'
    },
    {
        key: 'category',
        label: t('text.table-column.column-category'),
        sortable: true,
        cellRenderer: (_value: any, row: any) => row.merch_struct ? row.merch_struct?.code + ' - ' + row.merch_struct?.name : '-'
    },
    {
        key: 'status',
        label: t('text.table-column.column-status'),
        sortable: false,
        cellRenderer: (value: any, row: any) => {
            const statusText = value ? t('text.message.active' as any) || 'Active' : t('text.message.not-active' as any) || 'Not Active'
            const badgeColor = value ? 'success' : 'primary'

            return h(UBadge, {
                variant: 'subtle',
                color: badgeColor,
                class: TABLE_TEXT_STATUS_SIZE_CLASS
            }, () => statusText)
        }
    }
])

const actions = computed(() => [
    [
        {
            label: t('text.button.edit' as any) || 'Edit',
            icon: 'i-lucide-pencil',
            onSelect: (row : any) => handleEdit(row)
        },
        {
            label: t('text.button.reset-password' as any) || 'Reset Password',
            icon: 'i-lucide-key-round',
            onSelect: (row : any) => handleResetPassword(row)
        },
        {
            label: t('text.button.unlock-user' as any) || 'Unlock User',
            icon: 'i-lucide-lock-open',
            onSelect: (row : any) => handleUnlockUser(row)
        }
    ]
])

const columnPinning = ref({
    right: ['actions']
})

// ========================= STATE FOR MODAL =========================
const modalTitle = ref('')
const modalSubmitOpen = ref(false)
const viewOnlyMode = ref(false)
const editMode = ref(false)
const editingId = ref<string | null>(null)
const editData = ref({})

const showModal = () => {
    modalTitle.value = t('text.user-management-pg.add-new-user' as any) || 'Create New User'
    editMode.value = false
    editingId.value = null
    editData.value = {}
    modalSubmitOpen.value = true
}

const closeModal = () => {
    modalSubmitOpen.value = false
}

const onSubmitted = async () => {
    await getUserList()
}

// ========================= ACTION =========================
const getUserList = async () => {
    // userData.value = showLoadingOverlay.value ? [] : userData.value; // Clear data only when showing loading overlay (use table template #loading)
    loadingTable.value = true;

    try {
        const params = {
            username: usernameFilter.value,
            name: nameFilter.value,
            profile: profileFilter.value,
            category: categoryFilter.value,
            status: statusFilter.value,
            skip: (currentPage.value - 1) * itemPerPage.value,
            limit: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
            sort_by: 'username',
        }
        const response = await axios.get(api.getUserList, { params });

        userData.value = response.data.data?.items.map((item: any) => ({
            ...item,
            // profile: item.profile?.name || '-',
            status: item.status === 1 ? 'Active' : 'Inactive'
        }));
        countTotalData.value = response.data.data?.total || 0;
    } catch (error) {
        console.error('Error fetching data:', error);
        Swal?.fire({
            icon: 'error',
            title: t('text.message.error' as any) || 'Error!',
            text: t('text.message.failed-to-load-data-msg' as any) || 'Failed to load data.',
            confirmButtonText: 'OK'
        });
    } finally {
        loadingTable.value = false;
    }
}

const getProfileOptions = async () => {
    profileOptions.value = [] // Clear options before fetching new data
    try {
        const params = {
            skip: 0,
            limit: 1000,
            sort_by: 'code',
            sort_order: 'asc',
        }

        const response = await axios.get(api.getProfileList, { params })
        const sourceItems = response?.data?.data?.items || response?.data?.data || response?.data || []
        const sourceArray = Array.isArray(sourceItems) ? sourceItems : []

        const activeData = sourceArray.filter((item: any) => {
            const rawActive = item?.status
            return rawActive === true || rawActive === 1 || rawActive === '1'
        })

        const uniqueOptions = new Map<string, { label: string; value: string }>()
        activeData.forEach((item: any) => {
            const label = String(item?.name).trim()
            const value = String(item?.id).trim()

            if (!value) return
            uniqueOptions.set(value, {
                label: label,
                value: value,
            })
        })

        profileOptions.value = Array.from(uniqueOptions.values())
    } catch (error) {
        console.error('Error fetching profile options:', error)
        profileOptions.value = []
    }
}

const getSiteOptions = async () => {
    siteOptions.value = [] // Clear options before fetching new data
    try {
        const params = {
            skip: 0,
            limit: 1000,
            sort_by: 'code',
            sort_order: 'asc',
        }

        const response = await axios.get(api.getSiteList, { params })
        const sourceItems = response?.data?.items || response?.data || response?.data || []
        const sourceArray = Array.isArray(sourceItems) ? sourceItems : []

        // const activeData = sourceArray.filter((item: any) => {
        //     const rawActive = item?.status
        //     console.log(rawActive);
        //     return rawActive === true || rawActive === 1 || rawActive === '1'
        // })

        const uniqueOptions = new Map<string, { label: string; value: string }>()
        sourceArray.forEach((item: any) => {
            const label = String(item?.site).trim() + ' - ' + String(item?.initial).trim()
            const value = String(item?.id).trim()

            if (!value) return
            uniqueOptions.set(value, {
                label: label,
                value: value,
            })
        })

        siteOptions.value = Array.from(uniqueOptions.values())
    } catch (error) {
        console.error('Error fetching site options:', error)
        siteOptions.value = []
    }
}

const getCategoryOptions = async () => {
    categoryOptions.value = []
    categoryOptionsLoading.value = true

    try {
        const response = await axios.get(api.getMerchStructDivCatList)

        const sourceItems =
            response?.data?.data?.items ||
            response?.data?.data ||
            response?.data ||
            []

        const sourceArray = Array.isArray(sourceItems) ? sourceItems : []

        // 🔹 Ambil parent
        const parentData = sourceArray.filter((item: any) => item.parent_id === null)

        // 🔹 Ambil semua categories dari setiap parent
        const allCategories = parentData.flatMap((parent: any) => parent.categories || [])

        // 🔹 Mapping ke options
        const uniqueOptions = new Map<string, { label: string; value: string }>()

        allCategories.forEach((cat: any) => {
            const label = `${cat.code} - ${cat.name}`
            const value = cat.id

            if (!value) return

            uniqueOptions.set(value, {
                label,
                value,
            })
        })

        categoryOptions.value = Array.from(uniqueOptions.values())
    } catch (error) {
        console.error('Error fetching category options:', error)
        categoryOptions.value = []
    } finally {
        categoryOptionsLoading.value = false
    }
}

const onClickFindButton = () => {
    currentPage.value = 1
    getUserList()
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getUserList()
}

const handlePageSizeChange = (size: number) => {
    itemPerPage.value = size
    currentPage.value = 1 // Reset to first page when changing page size
    getUserList()
}

const handleSearch = (query: string) => { // For global search, server-side
    globalSearchQuery.value = query
    currentPage.value = 1 // Reset to first page when searching
    getUserList()
}

const handleEdit = async (data: any) => {
    const userId = String(data?.id || '')
    if (!userId) return

    loadingTable.value = true
    try {
        const response = await axios.get(`${api.getUserDetail}${userId}`)
        const detail = response?.data?.data || response?.data || {}

        // Prepare for merch struct
        const rawMerchStructs: MerchStructData[] = Array.isArray(detail?.merch_structs) ? detail.merch_structs : []
        const merchStructs = rawMerchStructs.map(data => { return { value: data.id, label: data.name } }) || []

        // Prepare for sites
        const rawSites: SiteData[] = Array.isArray(detail?.sites) ? detail.sites : []
        const sites = rawSites.map(data => { return { value: data.id, label: data.name } }) || []

        modalTitle.value = t('text.user-management-pg.edit-user' as any) || 'Edit User'
        viewOnlyMode.value = !canUpdateUser.value
        editMode.value = true
        editingId.value = userId
        editData.value = {
            username: detail?.username || data?.username || '',
            name: detail?.name || data?.name || '',
            profile: detail?.profile || data?.profile || null,
            category: merchStructs,
            site: sites,
            valid_date: detail?.valid_date || data?.valid_date || null,
        }
        modalSubmitOpen.value = true
    } catch (error: any) {
        console.error('Error fetching profile detail:', error?.response?.data || error?.message)
        Swal?.fire({
            icon: 'error',
            title: t('text.message.error' as any) || 'Error!',
            text: t('text.message.failed-to-load-data-msg' as any) || 'Failed to load data.',
            confirmButtonText: 'OK'
        })
    } finally {
        loadingTable.value = false
    }
}

const handleResetPassword = (data: any) => {
    loadingTable.value = true

    Swal?.fire({
        title: t('text.user-management-pg.reset-password' as any) || 'Reset Password',
        text: t('text.user-management-pg.reset-password-confirm-msg' as any) || 'Are you sure you want to reset the password for this user?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('text.button.yes' as any).toUpperCase() || 'YES',
        cancelButtonText: t('text.button.no' as any).toUpperCase() || 'NO',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.put(`${api.postResetPassword}${data.id}`)
                
                Swal?.fire({
                    icon: 'success',
                    title: t('text.message.success' as any) || 'Success!',
                    text: t('text.user-management-pg.reset-password-success-msg' as any) || 'Password has been reset successfully.',
                    confirmButtonText: t('text.button.ok' as any) || 'OK'
                })
                getUserList() // Refresh the user list after resetting password
            } catch (error) {
                loadingTable.value = false
                console.error('Error resetting password:', error)
                Swal?.fire({
                    icon: 'error',
                    title: t('text.message.error' as any) || 'Error!',
                    text: t('text.user-management-pg.reset-password-failed-msg' as any) || 'Failed to reset password.',
                    confirmButtonText: t('text.button.ok' as any) || 'OK'
                })
            }
        }
    })
}

const handleUnlockUser = (data: any) => {
    loadingTable.value = true

    Swal?.fire({
        title: t('text.user-management-pg.unlock-user' as any) || 'Unlock User',
        text: t('text.user-management-pg.unlock-user-confirm-msg' as any) || 'Are you sure you want to unlock this user?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('text.button.yes' as any).toUpperCase() || 'YES',
        cancelButtonText: t('text.button.no' as any).toUpperCase() || 'NO',
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                await axios.put(`${api.postUnlockUser}${data.id}`)

                Swal?.fire({
                    icon: 'success',
                    title: t('text.message.success' as any) || 'Success!',
                    text: t('text.user-management-pg.unlock-user-success-msg' as any) || 'User has been unlocked successfully.',
                    confirmButtonText: t('text.button.ok' as any) || 'OK'
                })
                getUserList() // Refresh the user list after unlocking
            } catch (error) {
                loadingTable.value = false
                console.error('Error unlocking user:', error)
                Swal?.fire({
                    icon: 'error',
                    title: t('text.message.error' as any) || 'Error!',
                    text: t('text.user-management-pg.unlock-user-failed-msg' as any) || 'Failed to unlock user.',
                    confirmButtonText: t('text.button.ok' as any) || 'OK'
                })
            }
        }
    })
}

// Fetch initial data on component mount
onMounted(async () => {
    await Promise.all([
        getProfileOptions(),
        getSiteOptions(),
        getCategoryOptions(),
        getUserList()
    ]).catch((error) => {
        console.error('Error during initial data fetch:', error)
        Swal?.fire({
            icon: 'error',
            title: t('text.message.error' as any) || 'Error!',
            text: t('text.message.failed-to-load-data-msg' as any) || 'Failed to load data.',
            confirmButtonText: 'OK'
        });
    })
})
</script>

<template>
    <CmpLayout :title="t('menu.master-data') || 'Master Data'">

        <!-- Content -->
        <div class="flex-1 overflow-auto p-3">

            <!-- Title Section -->
            <UCard class="mb-3" :ui="{ body: 'sm:py-3 sm:px-6' }">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">

                        <!-- BUTTON NEW -->
                        <UButton v-if="canCreateUser" type="button" @click="showModal" :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`">
                            {{ t('text.button.new').toUpperCase() || 'NEW' }}
                        </UButton>

                        <!-- TITLE -->
                        <h1 :class="`${TITLE_TEXT_CLASS} ${TEXT_TITLE_SIZE_CLASS}`">
                            {{ t('text.user-management-pg.list') || 'List of Users' }}
                        </h1>

                    </div>
                </div>
            </UCard>

            <!-- MODAL -->
            <DialogFormUser
                :open="modalSubmitOpen"
                :title="modalTitle"
                :view-only="viewOnlyMode"
                :edit-mode="editMode"
                :editing-id="editingId"
                :initial-data="editData"
                :profile-options="profileOptions"
                :category-options="categoryOptions"
                :site-options="siteOptions"
                @update:open="modalSubmitOpen = $event"
                @submitted="onSubmitted"
                @close="closeModal"
            />

            <!-- MAIN CONTENT -->
            <UCard>
                <!-- Filters Section with Accordion -->
                <CmpAccordionFilter>
                    <FormFilterUser
                        v-model:username="usernameFilter"
                        v-model:name="nameFilter"
                        v-model:profile="profileFilter"
                        v-model:category="categoryFilter"
                        v-model:status="statusFilter"
                        :profile-options="profileOptions"
                        :category-options="categoryOptions"
                        :loading="loadingTable"
                        @clear="resetFilter"
                        @find="onClickFindButton"
                    />
                </CmpAccordionFilter>

                <!-- Nuxt UI Table -->
                <CmpCustomTable
                    :data="userData"
                    :columns="columns"
                    :actions="actions"
                    :showNumberColumn="false"
                    :showFilters="true"
                    :loading="loadingTable"
                    :showLoadingOverlay="showLoadingOverlay"
                    :page-size="itemPerPage"
                    :current-page="currentPage"
                    :count-total-data="countTotalData"
                    :column-pinning="columnPinning"
                    @update:currentPage="handlePageChange"
                    @update:pageSize="handlePageSizeChange"
                    @search="handleSearch"
                />
            </UCard>
        </div>
    </CmpLayout>
</template>

<!-- <style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.2s ease-out;
}
</style> -->
