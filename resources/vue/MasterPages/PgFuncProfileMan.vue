<script setup lang="ts">
import { ref, computed, h, resolveComponent, onMounted } from 'vue'
import { useI18n } from '../composables/useI18n'
import { useApiStore } from '../AppState'
import axios from 'axios'
import { getCurrentInstance } from 'vue'
import { useGlobalOptions } from '../composables/useGlobalOptions'
import { useFormatters } from '../composables/useFormatters'
import CmpLayout from '../Components/CmpLayout.vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'
import CmpAccordionFilter from '../Components/CmpAccordionFilter.vue'
import DialogFormFuncProfile from './Components/DialogFormFuncProfile.vue'
import FormFilterFuncProfile from './Components/FormFilterFuncProfile.vue'

const { t } = useI18n()
const { statusOptions } = useGlobalOptions()
const { formatDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')
const UBadge = resolveComponent('UBadge')

// ========================= FILTER =========================
const profileFilter = ref<string | null>(null)
const companyFilter = ref<string | null>(null)
const divisionFilter = ref<string | null>(null)
const limitFilter = ref<string | null>(null)
const statusFilter = ref<string | null>(null)

// For select options
const profileOptions = ref<{ label: string; value: string }[]>([])
const companyOptions = ref<{ label: string; value: string }[]>([])
const limitOptions = ref<{ label: string; value: string }[]>([])
const divisionOptions = ref<{ label: string; value: string }[]>([])
const divisionOptionsLoading = ref(false)

const resetFilter = () => {
    profileFilter.value = null
    companyFilter.value = null
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
        key: 'profile',
        label: t('text.table-column.column-profile'),
        sortable: true,
        cellRenderer: (_value: any, row: any) => row.profile ? row.profile?.name : '-'
    },
    {
        key: 'company',
        label: t('text.table-column.column-company'),
        sortable: true,
        cellRenderer: (_value: any, row: any) => row.company ? row.company?.code + ' - ' + row.company?.name : '-'
    },
    {
        key: 'division',
        label: t('text.table-column.column-division'),
        sortable: true,
        cellRenderer: (_value: any, row: any) => row.merch_struct ? row.merch_struct?.code + ' - ' + row.merch_struct?.name : '-'
    },
    {
        key: 'limit',
        label: t('text.table-column.column-limit'),
        sortable: true,
        cellRenderer: (_value: any, row: any) => row.limit ? row.limit?.code : '-'
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
            profile: profileFilter.value,
            company: companyFilter.value,
            division: divisionFilter.value,
            limit_code: limitFilter.value,
            status: statusFilter.value,
            skip: (currentPage.value - 1) * itemPerPage.value,
            limiit: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
            sort_by: 'profile.name',
            sort_order: 'asc',
        }
        const response = await axios.get(api.getFuncProfileList, { params });

        functionalProfileData.value = response.data.data?.items || [];
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

const getCompanyOptions = async () => {
    companyOptions.value = [] // Clear options before fetching new data
    try {
        const params = {
            skip: 0,
            limit: 1000,
            sort_by: 'code',
            sort_order: 'asc',
        }

        const response = await axios.get(api.getCompanyList, { params })
        const sourceItems = response?.data?.items || []
        const sourceArray = Array.isArray(sourceItems) ? sourceItems : []
        const activeData = sourceArray.filter((item: any) => {
            const rawActive = item?.status
            return rawActive === true || rawActive === 1 || rawActive === '1'
        })

        const uniqueOptions = new Map<string, { label: string; value: string }>()
        activeData.forEach((item: any) => {
            const label = String(item?.code).trim() + ' - ' + String(item?.name).trim()
            const value = String(item?.id).trim()

            if (!value) return
            uniqueOptions.set(value, {
                label: label,
                value: value,
            })
        })

        companyOptions.value = Array.from(uniqueOptions.values())
    } catch (error) {
        console.error('Error fetching company options:', error)
        companyOptions.value = []
    }
}

const getLimitOptions = async () => {
    limitOptions.value = [] // Clear options before fetching new data
    try {
        const params = {
            skip: 0,
            limit: 1000,
            sort_by: 'code',
            sort_order: 'asc',
        }

        const response = await axios.get(api.getLimitList, { params })
        const sourceItems = response?.data?.data?.items || response?.data?.data || response?.data || []
        const sourceArray = Array.isArray(sourceItems) ? sourceItems : []
        const activeData = sourceArray.filter((item: any) => {
            const rawActive = item?.is_active
            return rawActive === true || rawActive === 1 || rawActive === '1'
        })

        const uniqueOptions = new Map<string, { label: string; value: string }>()
        activeData.forEach((item: any) => {
            const label = String(item?.code).trim()
            const value = String(item?.code).trim()

            if (!value) return
            uniqueOptions.set(value, {
                label: label,
                value: value,
            })
        })

        limitOptions.value = Array.from(uniqueOptions.values())
    } catch (error) {
        console.error('Error fetching limit options:', error)
        limitOptions.value = []
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

// Fetch initial data on component mount
onMounted(async () => {
    await Promise.all([
        getProfileOptions(),
        getCompanyOptions(),
        getLimitOptions(),
        getDivisionOptions(),
        getFuncProfileList()
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
                        <!-- <UButton type="button" @click="showModal" class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[16px] px-5">
                            {{ t('text.button.new').toUpperCase() || 'NEW' }}
                        </UButton> -->

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
                :profile-options="profileOptions"
                :company-options="companyOptions"
                :limit-options="limitOptions"
                :division-options="divisionOptions"
                :division-loading="divisionOptionsLoading"
                @update:open="modalSubmitOpen = $event"
                @submitted="onSubmitted"
                @close="closeModal"
            />

            <!-- MAIN CONTENT -->
            <UCard>
                <!-- Filters Section with Accordion -->
                <CmpAccordionFilter>
                    <FormFilterFuncProfile
                        v-model:profile="profileFilter"
                        v-model:company="companyFilter"
                        v-model:division="divisionFilter"
                        v-model:limit="limitFilter"
                        v-model:status="statusFilter"
                        :profile-options="profileOptions"
                        :company-options="companyOptions"
                        :limit-options="limitOptions"
                        :division-options="divisionOptions"
                        :status-options="statusOptions"
                        :loading="loadingTable"
                        @clear="resetFilter"
                        @find="onClickFindButton"
                    />
                </CmpAccordionFilter>

                <!-- Nuxt UI Table -->
                <CmpCustomTable
                    :data="functionalProfileData"
                    :columns="columns"
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
