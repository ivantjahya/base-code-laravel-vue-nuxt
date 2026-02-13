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
import DialogFormUser from './Components/DialogFormUser.vue'
import FormFilterUser from './Components/FormFilterUser.vue'

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')

// ========================= FILTER =========================
const usernameFilter = ref('')
const nameFilter = ref('')
const profileFilter = ref<string | null>(null)
const categoryFilter = ref<string | null>(null)
const ModelValidityDateFilter = shallowRef<CalendarDate>()
const statusFilter = ref<string | null>(null)

const resetFilter = () => {
    usernameFilter.value = ''
    nameFilter.value = ''
    profileFilter.value = null
    categoryFilter.value = null
    ModelValidityDateFilter.value = undefined
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
        sortable: true
    },
    {
        key: 'category',
        label: t('text.table-column.column-category'),
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

// ========================= MODAL INPUT =========================
const modalTitle = ref('')
const modalSubmitOpen = ref(false)
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
            profile_code: profileFilter.value,
            category: categoryFilter.value,
            status: statusFilter.value,
            validity_date: getDateString(ModelValidityDateFilter.value),
            skip: (currentPage.value - 1) * itemPerPage.value,
            user: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
        }
        const response = await axios.get(api.getUserList, { params });

        userData.value = response.data.data?.items.map((item: any) => ({
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

const handleEdit = (data: any) => {
    modalTitle.value = t('text.user-management-pg.edit-user' as any) || 'Edit User'
    editMode.value = true
    editingId.value = data.id
    editData.value = data
    modalSubmitOpen.value = true
}

// Fetch initial data on component mount
onMounted(() => {
    getUserList()
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
                            {{ t('text.user-management-pg.list') || 'List of Users' }}
                        </h1>

                    </div>
                </div>
            </UCard>

            <!-- MODAL -->
            <DialogFormUser
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
                    <FormFilterUser
                        v-model:username="usernameFilter"
                        v-model:name="nameFilter"
                        v-model:profile="profileFilter"
                        v-model:category="categoryFilter"
                        v-model:site="siteFilter"
                        v-model:validity-date="validityDateFilter"
                        v-model:status="statusFilter"
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
