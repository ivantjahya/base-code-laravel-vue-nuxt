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
import DialogFormLimit from './Components/DialogFormLimit.vue'
import FormFilterLimit from './Components/FormFilterLimit.vue'

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')

// ========================= FILTER =========================
const limitCodeFilter = ref('')
const minFilter = ref<number | null>(null)
const maxFilter = ref<number | null>(null)
const ModelStartDateFilter = shallowRef<CalendarDate>()
const ModelEndDateFilter = shallowRef<CalendarDate>()

const resetFilter = () => {
    limitCodeFilter.value = ''
    minFilter.value = null
    maxFilter.value = null
    ModelStartDateFilter.value = undefined
    ModelEndDateFilter.value = undefined
}

// ========================= TABLE =========================
const limitData = ref([])
const currentPage = ref(1)
const itemPerPage = ref(10)
const countTotalData = ref(0)
const loadingTable = ref(false)
const showLoadingOverlay = ref(true)
const globalSearchQuery = ref('') // For global search, server-side

const columns = computed(() => [
    {
        key: 'code',
        label: t('text.table-column.column-limit-code'),
        sortable: true
    },
    {
        key: 'min_value',
        label: t('text.table-column.column-minimum-amount'),
        sortable: true,
        formatter: (value: number) => formatCurrency(value),
        alignBody: 'right'
    },
    {
        key: 'max_value',
        label: t('text.table-column.column-maximum-amount'),
        sortable: true,
        formatter: (value: number) => formatCurrency(value),
        alignBody: 'right'
    },
    {
        key: 'start_date',
        label: t('text.table-column.column-start-date'),
        sortable: true,
        formatter: (value: string) => formatDate(value)
    },
    {
        key: 'end_date',
        label: t('text.table-column.column-end-date'),
        sortable: true,
        formatter: (value: string) => formatDate(value)
    },
    {
        key: 'is_active',
        label: t('text.table-column.column-status'),
        sortable: false,
        cellRenderer: (value: any, row: any) => {
            const statusText = value ? 'Active' : 'Not Active'
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
            show: (row: any) => row.is_active === true,
            onSelect: (row: any) => handleEdit(row)
        },
        {
            label: t('text.button.extend' as any) || 'Extend',
            icon: 'i-lucide-clock',
            show: (row: any) => row.is_active === false,
            onSelect: (row: any) => handleExtend(row)
        }
    ]
])

// ========================= MODAL INPUT =========================
const modalTitle = ref('')
const modalSubmitOpen = ref(false)
const editMode = ref(false)
const extendMode = ref(false)
const editingId = ref<string | null>(null)
const editData = ref({})

const showModal = () => {
    modalTitle.value = t('text.limit-management-pg.add-new-limit' as any) || 'Create New Limit'
    editMode.value = false
    extendMode.value = false
    editingId.value = null
    editData.value = {}
    modalSubmitOpen.value = true
}

const closeModal = () => {
    modalSubmitOpen.value = false
}

const onSubmitted = async () => {
    await getLimitList()
}

// ========================= ACTION =========================
const getLimitList = async () => {
    // limitData.value = showLoadingOverlay.value ? [] : limitData.value; // Clear data only when showing loading overlay (use table template #loading)
    loadingTable.value = true;

    try {
        const params = {
            limit_code: limitCodeFilter.value,
            min_value: minFilter.value,
            max_value: maxFilter.value,
            start_date: getDateString(ModelStartDateFilter.value),
            end_date: getDateString(ModelEndDateFilter.value),
            skip: (currentPage.value - 1) * itemPerPage.value,
            limit: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
            sort_by: 'code',
        }
        const response = await axios.get(api.getLimitList, { params });

        limitData.value = response.data.data?.items;
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
    getLimitList()
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getLimitList()
}

const handlePageSizeChange = (size: number) => {
    itemPerPage.value = size
    currentPage.value = 1 // Reset to first page when changing page size
    getLimitList()
}

const handleSearch = (query: string) => { // For global search, server-side
    globalSearchQuery.value = query
    currentPage.value = 1 // Reset to first page when searching
    getLimitList()
}

const handleEdit = (data: any) => {
    modalTitle.value = t('text.limit-management-pg.edit-limit' as any) || 'Edit Limit'
    editMode.value = true
    extendMode.value = false
    editingId.value = data.id
    editData.value = data
    modalSubmitOpen.value = true
}

const handleExtend = (data: any) => {
    modalTitle.value = t('text.limit-management-pg.extend-limit' as any) || 'Extend Limit'
    editMode.value = false
    extendMode.value = true
    editingId.value = data.id
    editData.value = data
    modalSubmitOpen.value = true
}

// Fetch initial data on component mount
onMounted(() => {
    getLimitList()
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
                            {{ t('text.limit-management-pg.list') || 'List of Limits' }}
                        </h1>

                    </div>
                </div>
            </UCard>

            <!-- MODAL -->
            <DialogFormLimit
                :open="modalSubmitOpen"
                :title="modalTitle"
                :edit-mode="editMode"
                :extend-mode="extendMode"
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
                    <FormFilterLimit
                        v-model:limit-code="limitCodeFilter"
                        v-model:min-amount="minFilter"
                        v-model:max-amount="maxFilter"
                        v-model:start-date="ModelStartDateFilter"
                        v-model:end-date="ModelEndDateFilter"
                        :loading="loadingTable"
                        @clear="resetFilter"
                        @find="onClickFindButton"
                    />
                </CmpAccordionFilter>

                <!-- Nuxt UI Table -->
                <CmpCustomTable
                    :data="limitData"
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

<style scoped>
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
</style>
