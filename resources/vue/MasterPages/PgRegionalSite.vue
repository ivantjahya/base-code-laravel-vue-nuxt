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
import FormFilterRegionalSite from './Components/FormFilterRegionalSite.vue'

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')

// ========================= STATE FOR FILTER =========================
const siteCodeFilter = ref('')
const siteNameFilter = ref('')
const regionalCodeFilter = ref('')
const regionalNameFilter = ref('')
const statusFilter = ref<string | null>(null)

const resetFilter = () => {
    siteCodeFilter.value = ''
    siteNameFilter.value = ''
    regionalCodeFilter.value = ''
    regionalNameFilter.value = ''
    statusFilter.value = null
}

// ========================= TABLE =========================
const regionalSiteData = ref([])
const currentPage = ref(1)
const itemPerPage = ref(10)
const countTotalData = ref(0)
const loadingTable = ref(false)
const showLoadingOverlay = ref(true)
const globalSearchQuery = ref('') // For global search, server-side

const columns = computed(() => [
    {
        key: 'site_code',
        label: t('text.table-column.column-site-code'),
        sortable: true
    },
    {
        key: 'site_initial',
        label: t('text.table-column.column-site-initial'),
        sortable: true
    },
    {
        key: 'site_name',
        label: t('text.table-column.column-site-name'),
        sortable: true
    },
    {
        key: 'site_address',
        label: t('text.table-column.column-site-address'),
        sortable: true
    },
    {
        key: 'site_city',
        label: t('text.table-column.column-site-city'),
        sortable: true
    },
    {
        key: 'site_region',
        label: t('text.table-column.column-site-region'),
        sortable: true
    },
    {
        key: 'regional_code',
        label: t('text.table-column.column-regional-code'),
        sortable: true
    },
    {
        key: 'regional_initial',
        label: t('text.table-column.column-regional-initial'),
        sortable: true
    },
    {
        key: 'regional_name',
        label: t('text.table-column.column-regional-name'),
        sortable: true
    },
    {
        key: 'regional_address',
        label: t('text.table-column.column-regional-address'),
        sortable: true
    },
    {
        key: 'regional_city',
        label: t('text.table-column.column-regional-city'),
        sortable: true
    },
    {
        key: 'regional_region',
        label: t('text.table-column.column-regional-region'),
        sortable: true
    },
    {
        key: 'status',
        label: t('text.table-column.column-status'),
        sortable: true,
        formatter: (value: number) => value === 1 ? 'Active' : 'Inactive'
    },
    {
        key: 'actions',
        label: '',
        sortable: false,
    }
])

const actions = computed(() => [
    [
        {
            label: t('text.button.edit' as any) || 'Edit',
            icon: 'i-lucide-pencil',
            onSelect: (row) => handleEdit(row)
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
    modalTitle.value = t('text.regional-site-management-pg.add-new-regional-site' as any) || 'Create New Regional Site'
    editMode.value = false
    editingId.value = null
    editData.value = {}
    modalSubmitOpen.value = true
}

const closeModal = () => {
    modalSubmitOpen.value = false
}

const onSubmitted = async () => {
    await getRegionalSiteList()
}

// ========================= ACTION =========================
const getRegionalSiteList = async () => {
    // regionalSiteData.value = showLoadingOverlay.value ? [] : regionalSiteData.value; // Clear data only when showing loading overlay (use table template #loading)
    loadingTable.value = true;

    try {
        const params = {
            site_code: siteCodeFilter.value,
            site_name: siteNameFilter.value,
            regional_code: regionalCodeFilter.value,
            regional_name: regionalNameFilter.value,
            status: statusFilter.value,
            skip: (currentPage.value - 1) * itemPerPage.value,
            profile: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
        }
        const response = await axios.get(api.getRegionalSiteList, { params });

        regionalSiteData.value = response.data.data?.items.map((item: any) => ({
            ...item,
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

const onClickFindButton = () => {
    currentPage.value = 1
    getRegionalSiteList()
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getRegionalSiteList()
}

const handlePageSizeChange = (size: number) => {
    itemPerPage.value = size
    currentPage.value = 1 // Reset to first page when changing page size
    getRegionalSiteList()
}

const handleSearch = (query: string) => { // For global search, server-side
    globalSearchQuery.value = query
    currentPage.value = 1 // Reset to first page when searching
    getRegionalSiteList()
}

const handleEdit = (data: any) => {
    modalTitle.value = t('text.regional-site-management-pg.edit-regional-site' as any) || 'Edit regional site'
    editMode.value = true
    editingId.value = data.id
    editData.value = data
    modalSubmitOpen.value = true
}

// Fetch initial data on component mount
onMounted(() => {
    getRegionalSiteList()
})

</script>

<template>
    <CmpLayout :title="t('menu.master-data') || 'Master Data'">

        <!-- Content -->
        <div class="flex-1 overflow-auto p-3">

            <!-- Title Section -->
            <UCard class="mb-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-4">

                        <!-- TITLE -->
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ t('text.regional-site-management-pg.list') || 'List of Regional Sites' }}
                        </h1>

                    </div>
                </div>
            </UCard>

            <!-- MAIN CONTENT -->
            <UCard>
                <!-- Filters Section with Accordion -->
                <CmpAccordionFilter>
                    <FormFilterRegionalSite
                        v-model:regional-site-code="regionalSiteCodeFilter"
                        v-model:regional-site-name="regionalSiteNameFilter"
                        v-model:regional-site-source="regionalSiteSourceFilter"
                        v-model:status="statusFilter"
                        :loading="loadingTable"
                        @clear="resetFilter"
                        @find="onClickFindButton"
                    />
                </CmpAccordionFilter>

                <!-- Nuxt UI Table -->
                <CmpCustomTable
                    :data="regionalSiteData"
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
