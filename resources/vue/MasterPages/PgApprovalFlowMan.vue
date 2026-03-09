<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef, onMounted } from 'vue'
import { useI18n } from '../composables/useI18n'
import { useMenuPermission } from '../composables/useMenuPermission'
import { useApiStore } from '../AppState'
import axios from 'axios'
import { getCurrentInstance } from 'vue'
import { useGlobalOptions } from '../composables/useGlobalOptions'
import CmpLayout from '../Components/CmpLayout.vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'
import CmpAccordionFilter from '../Components/CmpAccordionFilter.vue'
import DialogFormApprovalFlow from './Components/DialogFormApprovalFlow.vue'
import FormFilterApprovalFlow from './Components/FormFilterApprovalFlow.vue'
import { TEXT_SIZE_CLASS, TEXT_TITLE_SIZE_CLASS, TITLE_TEXT_CLASS, TABLE_TEXT_STATUS_SIZE_CLASS, BUTTON_PRIMARY_CLASS } from '../constants'

const { t } = useI18n()
const { hasMenuCtrl, MENU_CODE, CTRL_CODE } = useMenuPermission()
const { statusOptions } = useGlobalOptions()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')

// ========================= PERMISSIONS =========================
const canCreateApprovalFlow = computed(() => hasMenuCtrl(MENU_CODE.value.SUBMENU_APPROVAL_FLOW, CTRL_CODE.value.MENU_CTRL_CREATE))
const canUpdateApprovalFlow = computed(() => hasMenuCtrl(MENU_CODE.value.SUBMENU_APPROVAL_FLOW, CTRL_CODE.value.MENU_CTRL_UPDATE))

// ========================= STATE FOR FILTER =========================
const approvalFlowCodeFilter = ref('')
const profileFilter = ref<number | null>(null)
const divisionFilter = ref<number | null>(null)
const statusFilter = ref<number | null>(null)

// For select options
const profileOptions = ref<{ label: string; value: string }[]>([])
const divisionOptions = ref<{ label: string; value: string }[]>([])
const divisionOptionsLoading = ref(false)
const poStatusOptions = ref<{ label: string; value: string }[]>([])
const poStatusOptionsLoading = ref(false)

const resetFilter = () => {
    approvalFlowCodeFilter.value = ''
    profileFilter.value = null
    divisionFilter.value = null
    statusFilter.value = null
}

// ========================= TABLE =========================
const approvalFlowData = ref([])
const currentPage = ref(1)
const itemPerPage = ref(10)
const countTotalData = ref(0)
const loadingTable = ref(false)
const showLoadingOverlay = ref(true)
const globalSearchQuery = ref('') // For global search, server-side

const columns = computed(() => [
    {
        key: 'code',
        label: t('text.table-column.column-approval-flow-code'),
        sortable: true
    },
    {
        key: 'profile',
        label: t('text.table-column.column-profile'),
        sortable: true,
        cellRenderer: (_value: any, row: any) => row.profile ? row.profile?.name : '-'
    },
    {
        key: 'division',
        label: t('text.table-column.column-division'),
        sortable: true,
        cellRenderer: (_value: any, row: any) => row.merch_struct ? row.merch_struct?.name : '-'
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
            onSelect: (row : any) => handleEdit(row)
        }
    ]
])

// ========================= STATE FOR MODAL =========================
const modalTitle = ref('')
const modalSubmitOpen = ref(false)
const viewOnlyMode = ref(false)
const editMode = ref(false)
const editingId = ref<string | null>(null)
const editData = ref({})

const showModal = () => {
    modalTitle.value = t('text.approval-flow-management-pg.add-new-approval-flow' as any) || 'Create New Approval Flow'
    viewOnlyMode.value = false
    editMode.value = false
    editingId.value = null
    editData.value = {}
    modalSubmitOpen.value = true
}

const closeModal = () => {
    modalSubmitOpen.value = false
}

const onSubmitted = async () => {
    await getApprovalFlowList()
}

// ========================= ACTION =========================
const getApprovalFlowList = async () => {
    // approvalFlowData.value = showLoadingOverlay.value ? [] : approvalFlowData.value; // Clear data only when showing loading overlay (use table template #loading)
    loadingTable.value = true;

    try {
        const params = {
            approval_flow_code: approvalFlowCodeFilter.value,
            profile: profileFilter.value,
            division: divisionFilter.value,
            status: statusFilter.value,
            skip: (currentPage.value - 1) * itemPerPage.value,
            limit: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
            sort_by: 'code',
        }
        const response = await axios.get(api.getApprovalFlowList, { params });

        approvalFlowData.value = response.data.data?.items || [];
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

const getDivisionOptions = async () => {
    divisionOptions.value = [] // Clear options before fetching new data
    divisionOptionsLoading.value = true
    try {
        const response = await axios.get(api.getMerchStructDivCatList)

        const sourceItems = response?.data?.data?.items || response?.data?.data || response?.data || []
        const sourceArray = Array.isArray(sourceItems) ? sourceItems : []
        const divisionData = sourceArray.filter((item: any) => {
            return item.parent_id === null
        })

        const uniqueOptions = new Map<string, { label: string; value: string }>()
        divisionData.forEach((item: any) => {
            const label = String(item?.code).trim() + ' - ' + String(item?.name).trim()
            const value = String(item?.id).trim()

            if (!value) return
            uniqueOptions.set(value, {
                label: label,
                value: value,
            })
        })

        divisionOptions.value = Array.from(uniqueOptions.values())
    } catch (error) {
        console.error('Error fetching division options:', error)
        divisionOptions.value = []
    } finally {
        divisionOptionsLoading.value = false
    }
}

const getPoStatusOptions = async () => {
    poStatusOptions.value = [] // Clear options before fetching new data
    poStatusOptionsLoading.value = true
    try {
        const response = await axios.get(api.getPoStatusList)

        const sourceItems = response?.data?.items || response?.data || response || []
        const sourceArray = Array.isArray(sourceItems) ? sourceItems : []

        const uniqueOptions = new Map<string, { label: string; value: string }>()
        sourceArray.forEach((item: any) => {
            const label = String(item?.code).trim() + ' - ' + String(item?.description).trim()
            const value = String(item?.id).trim()

            if (!value) return
            uniqueOptions.set(value, {
                label: label,
                value: value,
            })
        })

        poStatusOptions.value = Array.from(uniqueOptions.values())
    } catch (error) {
        console.error('Error fetching po status options:', error)
        poStatusOptions.value = []
    } finally {
        poStatusOptionsLoading.value = false
    }
}

const onClickFindButton = () => {
    currentPage.value = 1
    getApprovalFlowList()
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getApprovalFlowList()
}

const handlePageSizeChange = (size: number) => {
    itemPerPage.value = size
    currentPage.value = 1 // Reset to first page when changing page size
    getApprovalFlowList()
}

const handleSearch = (query: string) => { // For global search, server-side
    globalSearchQuery.value = query
    currentPage.value = 1 // Reset to first page when searching
    getApprovalFlowList()
}

const handleEdit = async (data: any) => {
    const profileId = String(data?.id || '')
    if (!profileId) return

    loadingTable.value = true
    try {
        const response = await axios.get(`${api.getApprovalFlowDetail}${profileId}`)
        const detail = response?.data?.data || response?.data || {}
        console.log(divisionOptions.value);

        modalTitle.value = t('text.approval-flow-management-pg.edit-approval-flow' as any) || 'Edit Approval Flow'
        viewOnlyMode.value = !canUpdateApprovalFlow.value
        editMode.value = true
        editingId.value = profileId
        const rawProfileSource = detail?.is_internal ?? data?.is_internal ?? null
        editData.value = {
            profile: detail?.profile || data?.profile || null,
            division: detail?.merch_struct || data?.merch_struct || null,
            poStatus: detail?.po_status || data?.po_status || null,
            request_to: detail?.next_profile || data?.next_profile || null,
            nextPoStatus: detail?.next_po_status || data?.next_po_status || null,
            description: detail?.description || data?.description || '',
            status: Boolean(detail?.status ?? data?.status ?? true)
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

// Fetch initial data on component mount
onMounted(async () => {
    await Promise.all([
        getProfileOptions(),
        getDivisionOptions(),
        getPoStatusOptions(),
        getApprovalFlowList()
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
                        <UButton v-if="canCreateApprovalFlow" type="button" @click="showModal" :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`">
                            {{ t('text.button.new').toUpperCase() || 'NEW' }}
                        </UButton>

                        <!-- TITLE -->
                        <h1 :class="`${TITLE_TEXT_CLASS} ${TEXT_TITLE_SIZE_CLASS}`">
                            {{ t('text.approval-flow-management-pg.list') || 'List of Approval Flows' }}
                        </h1>
                    </div>
                </div>
            </UCard>

            <!-- MODAL -->
            <DialogFormApprovalFlow
                :open="modalSubmitOpen"
                :title="modalTitle"
                :view-only="viewOnlyMode"
                :edit-mode="editMode"
                :editing-id="editingId"
                :initial-data="editData"
                :profile-options="profileOptions"
                :division-options="divisionOptions"
                :division-loading="divisionOptionsLoading"
                :po-status-options="poStatusOptions"
                :po-status-loading="poStatusOptionsLoading"
                @update:open="modalSubmitOpen = $event"
                @submitted="onSubmitted"
                @close="closeModal"
            />

            <!-- MAIN CONTENT -->
            <UCard>
                <!-- Filters Section with Accordion -->
                <CmpAccordionFilter>
                    <FormFilterApprovalFlow
                        v-model:approvalCode="approvalFlowCodeFilter"
                        v-model:profile="profileFilter"
                        v-model:division="divisionFilter"
                        v-model:status="statusFilter"
                        :profile-options="profileOptions"
                        :status-options="statusOptions"
                        :division-options="divisionOptions"
                        :loading="loadingTable"
                        @clear="resetFilter"
                        @find="onClickFindButton"
                    />
                </CmpAccordionFilter>

                <!-- Nuxt UI Table -->
                <CmpCustomTable
                    :data="approvalFlowData"
                    :columns="columns"
                    :actions="actions"
                    :showNumberColumn="false"
                    :showFilters="true"
                    :loading="loadingTable"
                    :showLoadingOverlay="showLoadingOverlay"
                    :page-size="itemPerPage"
                    :current-page="currentPage"
                    :count-total-data="countTotalData"
                    @update:currentPage="handlePageChange"
                    @update:pageSize="handlePageSizeChange"
                    @search="handleSearch"
                />
            </UCard>

        </div>

    </CmpLayout>
</template>
