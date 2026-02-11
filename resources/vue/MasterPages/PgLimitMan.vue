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

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')

// ========================= STATE FOR FILTER =========================
const moreFilterItems = computed(() => [
  {
    label: t('text.input-field.more-filters'),
    slot: 'panelMoreFilter',
    icon: 'i-lucide-filter',
    defaultOpen: false
  }
])
const limitCodeFilter = ref('')
const minFilter = ref<number | null>(null)
const maxFilter = ref<number | null>(null)
const StartDateFilter = ref<any>(null)
const ModelStartDateFilter = shallowRef<CalendarDate>()
const EndDateFilter = ref<any>(null)
const ModelEndDateFilter = shallowRef<CalendarDate>()
const isPopoverFilterStartDateOpen = ref(false)
const isPopoverFilterEndDateOpen = ref(false)

const resetFilter = () => {
    limitCodeFilter.value = ''
    minFilter.value = null
    maxFilter.value = null
    StartDateFilter.value = null
    ModelStartDateFilter.value = undefined
    EndDateFilter.value = null
    ModelEndDateFilter.value = undefined
}

// ========================= STATE FOR TABLE =========================
const limitData = ref([])
const currentPage = ref(1)
const itemPerPage = ref(10)
const countTotalData = ref(0)
const loadingTable = ref(false)
const showLoadingOverlay = ref(false)

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
const isSubmitting = ref(false)
const editMode = ref(false)
const editingId = ref<string | null>(null)
const globalSearchQuery = ref('') // For global search, server-side

const valueMin = ref<number | null>(null)
const valueMax = ref<number | null>(null)

const inputStartDate = ref<any>(null)
const modelValueStart = shallowRef<CalendarDate>()
const inputEndDate = ref<any>(null)
const modelValueEnd = shallowRef<CalendarDate>()
const isPopoverFormStartDateOpen = ref(false)
const isPopoverFormEndDateOpen = ref(false)

const valueSwitch = ref(true)

const resetForm = () => {
    valueMin.value = null
    valueMax.value = null
    modelValueStart.value = undefined
    modelValueEnd.value = undefined
    valueSwitch.value = true
    editMode.value = false
    editingId.value = null
}

const showModal = () => {
    modalTitle.value = t('text.limit-management-pg.add-new-limit' as any) || 'Create New Limit'
    resetForm()
    modalSubmitOpen.value = true
}

const closeModal = () => {
    modalTitle.value = ''
    modalSubmitOpen.value = false
    resetForm()
}

// Submit create/update limit
const postSubmitLimit = async () => {
    // Validation
    if (valueMin.value == null || valueMax.value == null || !getDateString(modelValueStart.value) || !getDateString(modelValueEnd.value)) {
        console.error('Please fill all required fields')
        return
    }

    if (valueMax.value < valueMin.value) {
        console.error('Maximum must be greater than or equal to minimum')
        return
    }

    isSubmitting.value = true
    try {
        // Convert CalendarDate to YYYY-MM-DD format
        const startDate = getDateString(modelValueStart.value)
        const endDate = getDateString(modelValueEnd.value)

        const payload = {
            min_value: valueMin.value,
            max_value: valueMax.value,
            start_date: startDate,
            end_date: endDate,
        }

        let response
        if (editMode.value && editingId.value) {
            // Update existing limit
            response = await axios.put(`${api.postLimitUpdate}${editingId.value}`, payload)
        } else {
            // Create new limit
            response = await axios.post(api.postLimitCreate, payload)
        }

        Swal?.fire({
            icon: 'success',
            title: t('text.message.success' as any) || 'Success!',
            text: editMode.value ? t('text.message.data-updated-msg' as any) || 'The data has been updated successfully!' : t('text.message.data-saved-msg' as any) || 'The data has been saved successfully!',
            confirmButtonText: 'OK'
        })

        closeModal()
        await getLimitList()
    } catch (error: any) {
        console.error('Error submitting limit:', error.response?.data || error.message)
        Swal?.fire({
            icon: 'error',
            title: t('text.message.failed-to-save-data' as any) || 'Failed to save data!',
            text: t('text.message.failed-to-save-data-msg' as any) || 'Failed to save the data. An error occurred.',
            confirmButtonText: 'OK'
        })
    } finally {
        isSubmitting.value = false
    }
}

const getLimitList = async () => {
    limitData.value = showLoadingOverlay.value ? [] : limitData.value;
    loadingTable.value = true;

    try {
        const params = {
            limit_code: limitCodeFilter.value,
            min_amount: minFilter.value,
            max_amount: maxFilter.value,
            start_date: getDateString(ModelStartDateFilter.value),
            end_date: getDateString(ModelEndDateFilter.value),
            skip: (currentPage.value - 1) * itemPerPage.value,
            limit: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
        }
        const response = await axios.get(api.getLimitList, { params });
        
        limitData.value = response.data.data?.items.map((item: any) => ({
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

    // Populate form with existing data
    editMode.value = true
    editingId.value = data.id
    valueMin.value = data.min_value
    valueMax.value = data.max_value
    
    // Convert date strings to CalendarDate
    modelValueStart.value = stringToCalendarDate(data.start_date)
    modelValueEnd.value = stringToCalendarDate(data.end_date)
    
    valueSwitch.value = data.is_active
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
            <UCard class="mb-3">
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

                        <!-- MODAL -->
                        <UModal v-model:open="modalSubmitOpen" :title="modalTitle" :dismissible="false" class="text-[16px] font-semibold" :ui="{ footer: 'justify-end' }">
                            <template #body>
                                <!-- MINIMUM -->
                                <UFormField
                                    orientation="horizontal"
                                    class="mb-2"
                                >
                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.limit-management-pg.input-new-minimum') || 'Minimum' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInputNumber
                                        v-model="valueMin"
                                        required
                                        locale="id-ID"
                                        :format-options="{
                                            style: 'currency',
                                            currency: 'IDR'
                                        }"
                                        class="w-80 border-[#CAD5E2] font-reguler focus:border-[#F26524]"
                                    />
                                </UFormField>

                                <!-- MAXIMUM -->
                                <UFormField orientation="horizontal" class="mb-2" >
                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.limit-management-pg.input-new-maximum') || 'Maximum' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInputNumber
                                        v-model="valueMax"
                                        locale="id-ID"
                                        :format-options="{
                                            style: 'currency',
                                            currency: 'IDR'
                                        }"
                                        class="w-80 border-[#CAD5E2] font-reguler focus:border-[#F26524] "
                                    />
                                </UFormField>

                                <!-- START DATE -->
                                <UFormField orientation="horizontal" class="mb-2" >
                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.limit-management-pg.input-new-start-date') || 'Start Date' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInputDate
                                        ref="inputStartDate"
                                        v-model="modelValueStart"
                                        locale="id-ID" format="dd/mm/yyyy"
                                        :max-value="modelValueEnd"
                                        :disabled="editMode"
                                        class="w-80 border-[#CAD5E2] font-reguler focus:border-[#F26524]"
                                    >
                                        <template #trailing>
                                            <UPopover v-model:open="isPopoverFormStartDateOpen">
                                                <UButton
                                                    color="neutral"
                                                    variant="link"
                                                    size="sm"
                                                    icon="i-lucide-calendar"
                                                    aria-label="Select a date"
                                                    class="px-0"
                                                    :disabled="editMode"
                                                />

                                                <template #content>
                                                    <UCalendar
                                                        v-model="modelValueStart"
                                                        :max-value="modelValueEnd"
                                                        class="p-2"
                                                        @update:model-value="isPopoverFormStartDateOpen = false"
                                                    />
                                                </template>
                                            </UPopover>
                                        </template>
                                    </UInputDate>
                                </UFormField>

                                <!-- END DATE -->
                                <UFormField orientation="horizontal" class="mb-2" >
                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.limit-management-pg.input-new-end-date') || 'End Date' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInputDate
                                        ref="inputEndDate"
                                        v-model="modelValueEnd"
                                        locale="id-ID"
                                        format="dd/mm/yyyy"
                                        :min-value="modelValueStart"
                                        :disabled="editMode"
                                        class="w-80 border-[#CAD5E2] font-reguler focus:border-[#F26524]"
                                    >
                                        <template #trailing>
                                            <UPopover v-model:open="isPopoverFormEndDateOpen">
                                                <UButton
                                                    color="neutral"
                                                    variant="link"
                                                    size="sm"
                                                    icon="i-lucide-calendar"
                                                    aria-label="Select a date"
                                                    class="px-0"
                                                    :disabled="editMode"
                                                />

                                                <template #content>
                                                    <UCalendar
                                                        v-model="modelValueEnd"
                                                        :min-value="modelValueStart"
                                                        class="p-2"
                                                        @update:model-value="isPopoverFormEndDateOpen = false"
                                                    />
                                                </template>
                                            </UPopover>
                                        </template>
                                    </UInputDate>
                                </UFormField>

                                <!-- STATUS -->
                                <UFormField orientation="horizontal" class="mb-2" >
                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.limit-management-pg.input-new-status') || 'Status' }}
                                        </span>
                                    </template>

                                    <div class="flex justify-start w-80">
                                        <USwitch v-model="valueSwitch" />
                                    </div>
                                </UFormField>
                            </template>

                            <template #footer>
                                <UButton
                                    v-if="!editMode"
                                    class="bg-[#FEE9D6] text-[#F26524] hover:bg-[#FBD0AD] hover:text-[#E34613] active:bg-[#FBD0AD] active:text-[#E34613] text-[14px] px-5"
                                    :disabled="isSubmitting"
                                    @click="resetForm"
                                >{{ t('text.button.clear') || 'Clear' }}</UButton>

                                <UButton 
                                    class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[14px] px-5"
                                    :loading="isSubmitting"
                                    :disabled="isSubmitting"
                                    @click="postSubmitLimit"
                                >
                                    {{ t('text.button.submit') || 'Submit' }}
                                </UButton>
                            </template>
                        </UModal>
                    </div>
                </div>
            </UCard>

            <!-- MAIN CONTENT -->
            <UCard>
                <!-- Filters Section with Accordion -->
                <div class="mb-6">
                    <UAccordion
                        :items="moreFilterItems"
                        class="w-full border-1 border-gray-200 dark:border-gray-700 rounded-lg px-4"
                        :ui="{
                        wrapper: 'w-full',
                        item: {
                            base: 'border-1 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden',
                        }
                        }"
                    >
                        <template #panelMoreFilter>
                            <div class="py-2 space-y-4 bg-white dark:bg-gray-900">
                                <div class="grid grid-flow-row text-sm">
                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">

                                        <!-- LIMIT CODE -->
                                        <div class="flex w-full">
                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.limit-code') || 'Limit Code' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInput
                                                    v-model="limitCodeFilter"
                                                    :placeholder="t('text.input-field.limit-code-placeholder') || 'Enter limit code'"
                                                    size="md"
                                                    class="w-full font-light text-base md:text-sm"
                                                />
                                            </div>
                                        </div>

                                        <div class="px-2"></div>

                                        <!-- START DATE -->
                                        <div class="flex w-full">
                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.start-date') || 'Start Date' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInputDate
                                                    ref="StartDateFilter"
                                                    v-model="ModelStartDateFilter"
                                                    locale="id-ID" format="dd/mm/yyyy"
                                                    :max-value="ModelEndDateFilter"
                                                    class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524]"
                                                >
                                                    <template #trailing>
                                                        <UPopover v-model:open="isPopoverFilterStartDateOpen">
                                                            <UButton
                                                                color="neutral"
                                                                variant="link"
                                                                size="sm"
                                                                icon="i-lucide-calendar"
                                                                aria-label="Select a date"
                                                                class="px-0"
                                                            />

                                                            <template #content>
                                                                <UCalendar
                                                                    v-model="ModelStartDateFilter"
                                                                    :max-value="ModelEndDateFilter"
                                                                    class="p-2"
                                                                    @update:model-value="isPopoverFilterStartDateOpen = false"
                                                                />
                                                            </template>
                                                        </UPopover>
                                                    </template>
                                                </UInputDate>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">
                                        <!-- MINIMUM -->
                                        <div class="flex w-full">
                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.min-amount') || 'Minimum' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInputNumber
                                                    v-model="minFilter"
                                                    locale="id-ID"
                                                    :format-options="{
                                                        style: 'currency',
                                                        currency: 'IDR'
                                                    }"
                                                    class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524] "
                                                />
                                            </div>
                                        </div>

                                        <div class="px-2"></div>

                                        <!-- END DATE -->
                                        <div class="flex w-full">
                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.end-date') || 'End Date' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInputDate
                                                    ref="EndDateFilter"
                                                    v-model="ModelEndDateFilter"
                                                    locale="id-ID" format="dd/mm/yyyy"
                                                    :min-value="ModelStartDateFilter"
                                                    class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524]"
                                                >
                                                    <template #trailing>
                                                        <UPopover v-model:open="isPopoverFilterEndDateOpen">
                                                            <UButton
                                                                color="neutral"
                                                                variant="link"
                                                                size="sm"
                                                                icon="i-lucide-calendar"
                                                                aria-label="Select a date"
                                                                class="px-0"
                                                            />

                                                            <template #content>
                                                                <UCalendar
                                                                    v-model="ModelEndDateFilter"
                                                                    :min-value="ModelStartDateFilter"
                                                                    class="p-2"
                                                                    @update:model-value="isPopoverFilterEndDateOpen = false"
                                                                />
                                                            </template>
                                                        </UPopover>
                                                    </template>
                                                </UInputDate>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">
                                        <!-- MAXIMUM -->
                                        <div class="flex w-full">
                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.max-amount') || 'Maximum' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInputNumber
                                                    v-model="maxFilter"
                                                    locale="id-ID"
                                                    :format-options="{
                                                        style: 'currency',
                                                        currency: 'IDR'
                                                    }"
                                                    class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524] "
                                                />
                                            </div>
                                        </div>

                                        <div class="px-2"></div>

                                        <!-- BUTTON FIND -->
                                        <div class="flex w-full mb-1">
                                            <div class="w-full md:w-50 my-auto text-base md:text-sm"></div>
                                            <div class="flex w-full text-sm gap-2">
                                                <UButton
                                                    class="flex-1 w-full justify-center text-base md:text-sm text-[#F26524] hover:text-[#E34613] bg-[#FEE9D6] hover:bg-[#FBD0AD] active:bg-[#FBD0AD] active:text-[#E34613]"
                                                    :disabled="isSubmitting"
                                                    @click="resetFilter"
                                                >{{ t('text.button.clear') || 'Clear' }}</UButton>

                                                <UButton
                                                    class="flex-3 w-full justify-center text-base md:text-sm text-white bg-[#F26524] hover:bg-[#E34613] active:bg-[#E34613]"
                                                    :loading="loadingTable"
                                                    size="md"
                                                    icon="i-lucide-search"
                                                    @click="onClickFindButton"
                                                >
                                                    {{ t('text.button.find') || 'Find' }}
                                                </UButton>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </UAccordion>
                </div>

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



