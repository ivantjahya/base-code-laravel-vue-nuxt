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
import DialogFormUserGuide from './Components/DialogFormUserGuide.vue'
import FormFilterUserGuide from './Components/FormFilterUserGuide.vue'

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')

// ========================= FILTER =========================
const codeFilter = ref('')
const nameFilter = ref('')
const menuFilter = ref<string | null>(null)
const statusFilter = ref<string | null>(null)

const resetFilter = () => {
    codeFilter.value = ''
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
        key: 'code',
        label: t('text.table-column.column-user-guide-code'),
        sortable: true
    },
    {
        key: 'name',
        label: t('text.table-column.column-user-guide-name'),
        sortable: true
    },
    {
        key: 'menu',
        label: t('text.table-column.column-user-guide-menu'),
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

// ========================= MODAL =========================
const modalTitle = ref('')
const modalSubmitOpen = ref(false)
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
            user_guide_code: codeFilter.value,
            user_guide_name: nameFilter.value,
            user_guide_menu: menuFilter.value,
            status: statusFilter.value,
            skip: (currentPage.value - 1) * itemPerPage.value,
            userGuide: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
        }
        const response = await axios.get(api.getUserGuideList, { params });

        userGuideData.value = response.data.data?.items.map((item: any) => ({
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
onMounted(() => {
    getUserGuideList()
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

                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ t('text.user-guide-management-pg.list') || 'List of User Guides' }}
                        </h1>

                    </div>

                </div>

            </UCard>

            <!-- MODAL -->
            <DialogFormUserGuide
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
                    <FormFilterUserGuide
                        v-model:userGuideCode="codeFilter"
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
                    @update:currentPage="handlePageChange"
                    @update:pageSize="handlePageSizeChange"
                    @search="handleSearch"
                />
            </UCard>

        </div>

    </CmpLayout>
</template>
