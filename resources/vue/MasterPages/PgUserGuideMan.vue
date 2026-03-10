<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef, onMounted } from 'vue'
import { useI18n } from '../composables/useI18n'
import { useMenuPermission } from '../composables/useMenuPermission'
import { useApiStore } from '../AppState'
import axios from 'axios'
import { getCurrentInstance } from 'vue'
import CmpLayout from '../Components/CmpLayout.vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'
import CmpAccordionFilter from '../Components/CmpAccordionFilter.vue'
import DialogFormUserGuide from './Components/DialogFormUserGuide.vue'
import FormFilterUserGuide from './Components/FormFilterUserGuide.vue'
import { TEXT_SIZE_CLASS, TEXT_TITLE_SIZE_CLASS, TITLE_TEXT_CLASS, TABLE_TEXT_STATUS_SIZE_CLASS, BUTTON_PRIMARY_CLASS } from '../constants'

const { t } = useI18n()
const { hasMenuCtrl, MENU_CODE, CTRL_CODE } = useMenuPermission()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')

// ========================= PERMISSIONS =========================
const canCreateUserGuide = computed(() => hasMenuCtrl(MENU_CODE.value.SUBMENU_USER_GUIDE, CTRL_CODE.value.MENU_CTRL_CREATE))
const canUpdateUserGuide = computed(() => hasMenuCtrl(MENU_CODE.value.SUBMENU_USER_GUIDE, CTRL_CODE.value.MENU_CTRL_UPDATE))
const canDeleteUserGuide = computed(() => hasMenuCtrl(MENU_CODE.value.SUBMENU_USER_GUIDE, CTRL_CODE.value.MENU_CTRL_DELETE))

// ========================= FILTER =========================
const nameFilter = ref('')
const menuFilter = ref<string | null>(null)
const statusFilter = ref<string | null>(null)

// For select options
const menuOptions = ref<{ label: string; value: string }[]>([])
const menuOptionsLoading = ref(false)

const resetFilter = () => {
    nameFilter.value = ''
    menuFilter.value = null
    statusFilter.value = null
}

// ========================= TABLE =========================
const userGuideData = ref([])
const currentPage = ref(1)
const itemPerPage = ref(10)
const countTotalData = ref(0)
const loadingTable = ref(false)
const showLoadingOverlay = ref(true)
const globalSearchQuery = ref('') // For global search, server-side

const columns = computed(() => [
    {
        key: 'name',
        label: t('text.table-column.column-user-guide-name'),
        sortable: true
    },
    {
        key: 'file_name',
        label: t('text.table-column.column-user-guide-file-name'),
        sortable: true
    },
    {
        key: 'menu',
        label: t('text.table-column.column-user-guide-menu'),
        sortable: true,
        cellRenderer: (_value: any, row: any) => row.menu ? row.menu?.name : '-'
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
    },
])

const actions = computed(() => [
    [
        {
            label: t('text.button.edit' as any) || 'Edit',
            icon: 'i-lucide-pencil',
            onSelect: (row: any) => handleEdit(row)
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
    modalTitle.value = t('text.user-guide-management-pg.add-new-user-guide' as any) || 'Create New User Guide'
    editMode.value = false
    editingId.value = null
    editData.value = {}
    modalSubmitOpen.value = true
}

const closeModal = () => {
    modalSubmitOpen.value = false
}

const onSubmitted = async () => {
    await getUserGuideList()
}

// ========================= ACTION =========================
const getUserGuideList = async () => {
    // userGuideData.value = showLoadingOverlay.value ? [] : userGuideData.value; // Clear data only when showing loading overlay (use table template #loading)
    loadingTable.value = true;

    try {
        const params = {
            user_guide_name: nameFilter.value,
            user_guide_menu: menuFilter.value,
            status: statusFilter.value,
            skip: (currentPage.value - 1) * itemPerPage.value,
            limit: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
            sort_by: 'name',
        }
        const response = await axios.get(api.getUserGuideList, { params });

        userGuideData.value = response.data.data?.data || [];
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

const getMenuOptions = async () => {
    menuOptions.value = [] // Clear options before fetching new data
    try {
        const params = {
            skip: 0,
            limit: 1000,
            sort_by: 'code',
            sort_order: 'asc',
        }

        const response = await axios.get(api.getMenuList, { params })
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

        menuOptions.value = Array.from(uniqueOptions.values())
    } catch (error) {
        console.error('Error fetching menu options:', error)
        menuOptions.value = []
    }
}

const onClickFindButton = () => {
    currentPage.value = 1
    getUserGuideList()
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getUserGuideList()
}

const handlePageSizeChange = (size: number) => {
    itemPerPage.value = size
    currentPage.value = 1 // Reset to first page when changing page size
    getUserGuideList()
}

const handleSearch = (query: string) => { // For global search, server-side
    globalSearchQuery.value = query
    currentPage.value = 1 // Reset to first page when searching
    getUserGuideList()
}

const handleEdit = (data: any) => {
    modalTitle.value = t('text.user-guide-management-pg.edit-user-guide' as any) || 'Edit user guide'
    editMode.value = true
    editingId.value = data.id
    editData.value = data
    modalSubmitOpen.value = true
}

// Fetch initial data on component mount
onMounted(async () => {
    await Promise.all([
        getMenuOptions(),
        getUserGuideList()
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
                        <UButton v-if="canCreateUserGuide" type="button" @click="showModal" :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`">
                            {{ t('text.button.new').toUpperCase() || 'NEW' }}
                        </UButton>

                        <h1 :class="`${TITLE_TEXT_CLASS} ${TEXT_TITLE_SIZE_CLASS}`">
                            {{ t('text.user-guide-management-pg.list') || 'List of User Guides' }}
                        </h1>
                    </div>
                </div>
            </UCard>

            <!-- MODAL -->
            <DialogFormUserGuide
                :open="modalSubmitOpen"
                :title="modalTitle"
                :view-only="viewOnlyMode"
                :edit-mode="editMode"
                :editing-id="editingId"
                :initial-data="editData"
                @update:open="modalSubmitOpen = $event"
                @submitted="onSubmitted"
                @close="closeModal"
            />

            <!-- MAIN CONTENT -->
            <UCard>
                <!-- Filters Section with Accordion -->
                <CmpAccordionFilter>
                    <FormFilterUserGuide
                        v-model:userGuideName="nameFilter"
                        v-model:menu="menuFilter"
                        v-model:status="statusFilter"
                        :loading="loadingTable"
                        @clear="resetFilter"
                        @find="onClickFindButton"
                    />
                </CmpAccordionFilter>

                <!-- Nuxt UI Table -->
                <CmpCustomTable
                    :data="userGuideData"
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
