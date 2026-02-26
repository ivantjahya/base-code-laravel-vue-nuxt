<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef, onMounted } from 'vue'
import { useI18n } from '../composables/useI18n'
import { useApiStore } from '../AppState'
import axios from 'axios'
import { getCurrentInstance } from 'vue'
import { useGlobalOptions } from '../composables/useGlobalOptions'
import CmpLayout from '../Components/CmpLayout.vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'
import CmpAccordionFilter from '../Components/CmpAccordionFilter.vue'
import DialogFormRegionalSite from './Components/DialogFormRegionalSite.vue'
import FormFilterRegionalSite from './Components/FormFilterRegionalSite.vue'

const { t } = useI18n()
const { statusOptions } = useGlobalOptions()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')

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
        key: 'site',
        label: t('text.table-column.column-site-code'),
        sortable: true,
        size: 136,
    },
    {
        key: 'initial',
        label: t('text.table-column.column-site-initial'),
        sortable: true,
        size: 110
    },
    {
        key: 'name',
        label: t('text.table-column.column-site-name'),
        sortable: true
    },
    {
        key: 'address',
        label: t('text.table-column.column-site-address'),
        sortable: true
    },
    {
        key: 'city',
        label: t('text.table-column.column-site-city'),
        sortable: true
    },
    {
        key: 'region',
        label: t('text.table-column.column-site-region'),
        sortable: true
    },
    {
        key: 'regional_code_kontrabon',
        label: t('text.table-column.column-regional-code'),
        sortable: true
    },
    {
        key: 'regional_init_kontrabon',
        label: t('text.table-column.column-regional-initial'),
        sortable: true
    },
    {
        key: 'regional_name_kontrabon',
        label: t('text.table-column.column-regional-name'),
        sortable: true
    },
    {
        key: 'regional_address_kontrabon',
        label: t('text.table-column.column-regional-address'),
        sortable: true
    },
    {
        key: 'regional_city_kontrabon',
        label: t('text.table-column.column-regional-city'),
        sortable: true
    },
    {
        key: 'regional_region_kontrabon',
        label: t('text.table-column.column-regional-region'),
        sortable: true
    },
    {
        key: 'is_active',
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
            label: t('text.button.view' as any) || 'View',
            icon: 'i-lucide-eye',
            onSelect: (row: any) => handleView(row)
        }
    ]
])

const columnPinning = ref({
    left: ['site', 'initial'],
    right: ['actions']
})

// ========================= STATE FOR MODAL =========================
const modalTitle = ref('')
const modalSubmitOpen = ref(false)
const viewMode = ref(false)
const viewingId = ref<string | null>(null)
const viewData = ref({})

const showModal = () => {
    modalTitle.value = t('text.regional-site-management-pg.add-new-regional-site' as any) || 'Create New Regional Site'
    viewMode.value = false
    viewingId.value = null
    viewData.value = {}
    modalSubmitOpen.value = true
}

const closeModal = () => {
    modalSubmitOpen.value = false
}

// ========================= ACTION =========================
const getSiteList = async () => {
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
            limit: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
            sort_by: 'code',
            sort_order: 'asc'
        }
        const response = await axios.get(api.getSiteList, { params });

        regionalSiteData.value = response.data?.items;
        countTotalData.value = response.data?.total || 0;
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
    getSiteList()
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getSiteList()
}

const handlePageSizeChange = (size: number) => {
    itemPerPage.value = size
    currentPage.value = 1 // Reset to first page when changing page size
    getSiteList()
}

const handleSearch = (query: string) => { // For global search, server-side
    globalSearchQuery.value = query
    currentPage.value = 1 // Reset to first page when searching
    getSiteList()
}

const handleView = (data: any) => {
    modalTitle.value = t('text.regional-site-pg.view-regional-site' as any) || 'View Regional Site'
    viewMode.value = true
    viewData.value = data
    viewingId.value = data?.id || null
    modalSubmitOpen.value = true
}

// Fetch initial data on component mount
onMounted(() => {
    getSiteList()
})

</script>

<template>
    <CmpLayout :title="t('menu.master-data') || 'Master Data'">

        <!-- Content -->
        <div class="flex-1 overflow-auto p-3">

            <!-- Title Section -->
            <UCard class="mb-3" :ui="{ body: 'sm:py-3 sm:px-6' }">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">

                        <!-- TITLE -->
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ t('text.regional-site-pg.list') || 'List of Regional Sites' }}
                        </h1>

                    </div>
                </div>
            </UCard>

            <!-- MODAL -->
            <DialogFormRegionalSite
                :open="modalSubmitOpen"
                :title="modalTitle"
                :view-mode="viewMode"
                :viewing-id="viewingId"
                :initial-data="viewData"
                @update:open="modalSubmitOpen = $event"
                @close="closeModal"
            />

            <!-- MAIN CONTENT -->
            <UCard>
                <!-- Filters Section with Accordion -->
                <CmpAccordionFilter>
                    <FormFilterRegionalSite
                        v-model:siteCode="siteCodeFilter"
                        v-model:siteName="siteNameFilter"
                        v-model:regionalCode="regionalCodeFilter"
                        v-model:regionalName="regionalNameFilter"
                        v-model:status="statusFilter"
                        :status-options="statusOptions"
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
                    :column-pinning="columnPinning"
                    @update:currentPage="handlePageChange"
                    @update:pageSize="handlePageSizeChange"
                    @search="handleSearch"
                />
            </UCard>

        </div>

    </CmpLayout>
</template>
