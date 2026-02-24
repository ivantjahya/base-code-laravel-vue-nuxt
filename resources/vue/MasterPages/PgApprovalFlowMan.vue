<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef, onMounted } from 'vue'
import type { TableColumn } from '@nuxt/ui'
import { CalendarDate, DateFormatter, getLocalTimeZone, today } from '@internationalized/date'
import { useI18n } from '../composables/useI18n'
import { useFormatters } from '../composables/useFormatters'
import { useApiStore } from '../AppState'
import axios from 'axios'
import { getCurrentInstance } from 'vue'
import CmpLayout from '../Components/CmpLayout.vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'
import CmpAccordionFilter from '../Components/CmpAccordionFilter.vue'
import DialogFormApprovalFlow from './Components/DialogFormApprovalFlow.vue'
import FormFilterApprovalFlow from './Components/FormFilterApprovalFlow.vue'

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')

// ========================= STATE FOR FILTER =========================
const approvalFlowCodeFilter = ref('')
const profile = ref<number | null>(null)
const division = ref<number | null>(null)
const statusFilter = ref<number | null>(null)

const resetFilter = () => {
    approvalFlowCodeFilter.value = ''
    profile.value = null
    division.value = null
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
        sortable: true
    },
    {
        key: 'division',
        label: t('text.table-column.column-division'),
        sortable: true
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
                class: 'text-xs'
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
const editMode = ref(false)
const editingId = ref<string | null>(null)
const editData = ref({})

const showModal = () => {
    modalTitle.value = t('text.approval-flow-management-pg.add-new-approval-flow' as any) || 'Create New Approval Flow'
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
            profile: profile.value,
            division: division.value,
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
        const response = await axios.get(`${api.getProfileDetail}${profileId}`)
        const detail = response?.data?.data || response?.data || {}
        const rawMenuAccess = Array.isArray(detail?.menu_access) ? detail.menu_access : []

        modalTitle.value = t('text.approval-flow-management-pg.edit-approval-flow' as any) || 'Edit Approval Flow'
        editMode.value = true
        editingId.value = profileId
        const rawProfileSource = detail?.is_internal ?? data?.is_internal ?? null
        editData.value = {
            profileName: detail?.name || data?.name || '',
            description: detail?.description || data?.description || '',
            profileSource: rawProfileSource === null || rawProfileSource === undefined || rawProfileSource === '' ? null : Number(rawProfileSource),
            status: Boolean(detail?.status ?? data?.status ?? true),
            menuAccess: rawMenuAccess
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
onMounted(() => {
    getApprovalFlowList()
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
                        <UButton type="button" @click="showModal" class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[16px] px-5">
                            {{ t('text.button.new').toUpperCase() || 'NEW' }}
                        </UButton>

                        <!-- TITLE -->
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ t('text.approval-flow-management-pg.list') || 'List of Approval Flows' }}
                        </h1>

                    </div>
                </div>
            </UCard>

            <!-- MODAL -->
            <DialogFormApprovalFlow
                :open="modalSubmitOpen"
                :title="modalTitle"
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
                    <FormFilterApprovalFlow
                        v-model:approval-flow-code="approvalFlowCodeFilter"
                        v-model:profile="profileFilter"
                        v-model:division="divisionFilter"
                        v-model:status="statusFilter"
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
