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
import DialogFormProfile from './Components/DialogFormProfile.vue'
import FormFilterProfile from './Components/FormFilterProfile.vue'

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')

// ========================= STATE FOR FILTER =========================
const profileCodeFilter = ref('')
const profileNameFilter = ref('')
const profileSourceFilter = ref<string | null>(null)
const statusFilter = ref<string | null>(null)

const resetFilter = () => {
    profileCodeFilter.value = ''
    profileNameFilter.value = ''
    profileSourceFilter.value = null
    statusFilter.value = null
}

// ========================= TABLE =========================
const profileData = ref([])
const currentPage = ref(1)
const itemPerPage = ref(10)
const countTotalData = ref(0)
const loadingTable = ref(false)
const showLoadingOverlay = ref(true)
const globalSearchQuery = ref('') // For global search, server-side

const columns = computed(() => [
    {
        key: 'code',
        label: t('text.table-column.column-profile-code'),
        sortable: true
    },
    {
        key: 'name',
        label: t('text.table-column.column-profile-name'),
        sortable: true
    },
    {
        key: 'source',
        label: t('text.table-column.column-profile-source'),
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
    modalTitle.value = t('text.profile-management-pg.add-new-profile' as any) || 'Create New Profile'
    editMode.value = false
    editingId.value = null
    editData.value = {}
    modalSubmitOpen.value = true
}

const closeModal = () => {
    modalSubmitOpen.value = false
}

const onSubmitted = async () => {
    await getProfileList()
}

// ========================= ACTION =========================
const getProfileList = async () => {
    // profileData.value = showLoadingOverlay.value ? [] : profileData.value; // Clear data only when showing loading overlay (use table template #loading)
    loadingTable.value = true;

    try {
        const params = {
            profile_code: profileCodeFilter.value,
            profile_name: profileNameFilter.value,
            profile_source: profileSourceFilter.value,
            status: statusFilter.value,
            skip: (currentPage.value - 1) * itemPerPage.value,
            profile: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
        }
        const response = await axios.get(api.getProfileList, { params });

        profileData.value = response.data.data?.items.map((item: any) => ({
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
    getProfileList()
}

const handlePageChange = (page: number) => {
    currentPage.value = page
    getProfileList()
}

const handlePageSizeChange = (size: number) => {
    itemPerPage.value = size
    currentPage.value = 1 // Reset to first page when changing page size
    getProfileList()
}

const handleSearch = (query: string) => { // For global search, server-side
    globalSearchQuery.value = query
    currentPage.value = 1 // Reset to first page when searching
    getProfileList()
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
    getProfileList()
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

                        <!-- BUTTON NEW -->
                        <UButton type="button" @click="showModal" class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[16px] px-5">
                            {{ t('text.button.new').toUpperCase() || 'NEW' }}
                        </UButton>

                        <!-- TITLE -->
                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ t('text.profile-management-pg.list') || 'List of Profiles' }}
                        </h1>

                    </div>
                </div>
            </UCard>

            <!-- MODAL -->
            <DialogFormProfile
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
                    <FormFilterProfile
                        v-model:profile-code="profileCodeFilter"
                        v-model:profile-name="profileNameFilter"
                        v-model:profile-source="profileSourceFilter"
                        v-model:status="statusFilter"
                        :loading="loadingTable"
                        @clear="resetFilter"
                        @find="onClickFindButton"
                    />
                </CmpAccordionFilter>

                <!-- Nuxt UI Table -->
                <CmpCustomTable
                    :data="profileData"
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
