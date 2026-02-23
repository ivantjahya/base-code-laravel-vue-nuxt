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
import DialogFormFuncProfile from './Components/DialogFormFuncProfile.vue'
import FormFilterFuncProfile from './Components/FormFilterFuncProfile.vue'
import type { TreeItemSelectEvent } from 'reka-ui'
import type { TreeItem } from '@nuxt/ui'

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')
// const UDropdownMenu = resolveComponent('UDropdownMenu')

// State
const moreFilterItems = computed(() => [
  {
    label: t('text.input-field.more-filters'),
    slot: 'panelMoreFilter',
    icon: 'i-lucide-filter',
    defaultOpen: false
  }
])

// ========================= FILTER =========================
const functionalProfileCodeFilter = ref('')
const functionalProfileNameFilter = ref('')
const profileFilter = ref<string | null>(null)
const divisionValueFilter = ref(['FASHION', 'NON FOOD', 'FRESH'])
const divisionFilter = ref<string | null>(null)
const limitValueFilter = ref(['L001', 'L002', 'L003'])
const limitFilter = ref<string | null>(null)
const statusValueFilter = ref(['Active', 'Not Active'])
const statusFilter = ref<string | null>(null)

const resetFilter = () => {
    functionalProfileCodeFilter.value = ''
    functionalProfileNameFilter.value = ''
    profileFilter.value = null
    divisionFilter.value = null
    limitFilter.value = null
    statusFilter.value = null
}

// ========================= TABLE =========================
const functionalProfileData = ref([])
const currentPage = ref(1)
const itemPerPage = ref(10)
const countTotalData = ref(0)
const loadingTable = ref(false)
const showLoadingOverlay = ref(true)
const globalSearchQuery = ref('') // For global search, server-side

const columns = computed(() => [
    {
        key: 'code',
        label: t('text.table-column.column-functional-profile-code'),
        sortable: true
    },
    {
        key: 'name',
        label: t('text.table-column.column-functional-profile-name'),
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
        key: 'limit',
        label: t('text.table-column.column-limit'),
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
    modalTitle.value = t('text.functional-profile-management-pg.add-new-functional-profile' as any) || 'Create New Profile'
    editMode.value = false
    editingId.value = null
    editData.value = {}
    modalSubmitOpen.value = true
}

const closeModal = () => {
    modalSubmitOpen.value = false
}

const onSubmitted = async () => {
    await getFuncProfileList()
}

// ========================= ACTION =========================
const getFuncProfileList = async () => {
    // functionalProfileData.value = showLoadingOverlay.value ? [] : functionalProfileData.value; // Clear data only when showing loading overlay (use table template #loading)
    loadingTable.value = true;

    try {
        const params = {
            code: functionalProfileCodeFilter.value,
            name: functionalProfileNameFilter.value,
            profile: profileFilter.value,
            division: divisionFilter.value,
            limit: limitFilter.value,
            status: statusFilter.value,
            skip: (currentPage.value - 1) * itemPerPage.value,
            functionalProfile: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
        }
        const response = await axios.get(api.getFuncProfileList, { params });

        functionalProfileData.value = response.data.data?.items.map((item: any) => ({
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
    getFuncProfileList()
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getFuncProfileList()
}

const handlePageSizeChange = (size: number) => {
    itemPerPage.value = size
    currentPage.value = 1 // Reset to first page when changing page size
    getFuncProfileList()
}

const handleSearch = (query: string) => { // For global search, server-side
    globalSearchQuery.value = query
    currentPage.value = 1 // Reset to first page when searching
    getFuncProfileList()
}

const handleEdit = (data: any) => {
    modalTitle.value = t('text.profile-management-pg.edit-profile' as any) || 'Edit profile'
    editMode.value = true
    editingId.value = data.id
    editData.value = data
    modalSubmitOpen.value = true
}

// Fetch initial data on component mount
onMounted(() => {
    getFuncProfileList()
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
                            {{ t('text.functional-profile-management-pg.list') || 'List of Functional Profiles' }}
                        </h1>

                    </div>
                </div>
            </UCard>

            <!-- MODAL -->
            <DialogFormFuncProfile
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
                    <FormFilterFuncProfile
                        v-model:funcProfileCode="functionalProfileCodeFilter"
                        v-model:funcProfileName="functionalProfileNameFilter"
                        v-model:profile="profileFilter"
                        v-model:division="divisionFilter"
                        v-model:limit="limitFilter"
                        v-model:status="statusFilter"
                        :loading="loadingTable"
                        @clear="resetFilter"
                        @find="onClickFindButton"
                    />
                </CmpAccordionFilter>

                <!-- Nuxt UI Table -->
                <CmpCustomTable
                    :data="functionalProfileData"
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
