<script setup lang="ts">
import { ref, computed, watch, getCurrentInstance, h, resolveComponent } from 'vue'
import axios from 'axios'
import { useI18n } from '../../composables/useI18n'
import { useApiStore } from '../../AppState'
import { useFormatters } from '../../composables/useFormatters'
import CmpCustomTable from '../../Components/CmpCustomTable.vue'
import CmpAccordionFilter from '../../Components/CmpAccordionFilter.vue'
import FormFilterFuncProfile from './FormFilterFuncProfile.vue'
import DialogFormFuncProfileSubmit from './DialogFormFuncProfileSubmit.vue'
import { TEXT_SIZE_CLASS, TEXT_TITLE_SIZE_CLASS, TITLE_MODAL_TEXT_CLASS, INPUT_FIELD_WARN_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS, TABLE_TEXT_STATUS_SIZE_CLASS } from '../../constants'

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    editMode: {
        type: Boolean,
        default: false
    },
    editingId: {
        type: String,
        default: null
    },
    initialData: {
        type: Object,
        default: () => ({})
    },
    companyOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    limitOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    divisionOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    canCreateFuncProfile: {
        type: Boolean,
        default: false
    },
    canUpdateFuncProfile: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const api = useApiStore()
const { formatDate } = useFormatters()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const UBadge = resolveComponent('UBadge')

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
                class: TABLE_TEXT_STATUS_SIZE_CLASS
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

const columnPinning = ref({
    right: ['actions']
})

// ========================= STATE FOR MODAL =========================
const isSubmitting = ref(false)
const profileId = ref<string | null>(null)
const profileName = ref<string | null>(null)

// Validation error states
const errors = ref({
    profileName: '',
})

// ========================= STATE FOR MODAL SUBMIT FUNCTIONAL PROFILE =========================
const modalSubmitFuncProfileTitle = ref('')
const modalSubmitFuncProfileOpen = ref(false)
const editModeFuncProfile = ref(false)
const editingIdFuncProfile = ref<string | null>(null)
const editDataFuncProfile = ref({})

const showModalSubmitFuncProfile = () => {
    modalSubmitFuncProfileTitle.value = t('text.functional-profile-management-pg.add-new-functional-profile' as any) || 'Create New Functional Profile'
    editModeFuncProfile.value = false
    editingIdFuncProfile.value = null
    editDataFuncProfile.value = {}
    modalSubmitFuncProfileOpen.value = true
}

const closeModalSubmitFuncProfile = () => {
    modalSubmitFuncProfileOpen.value = false
}

const onSubmittedFuncProfile = async () => {
    await getFuncProfileList()
}

// ========================= ACTION =========================
const getFuncProfileList = async () => {
    loadingTable.value = true;

    try {
        const params = {
            profile: profileId.value,
            skip: (currentPage.value - 1) * itemPerPage.value,
            limit: itemPerPage.value,
            search: globalSearchQuery.value, // For global search, server-side
            sort_by: 'merch_struct.code', // Example sort field, adjust as needed
            sort_order: 'asc', // or 'desc'
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

const resetForm = () => {
    profileId.value = null
    profileName.value = null
    functionalProfileData.value = []
    globalSearchQuery.value = ''

    errors.value = {
        profileName: '',
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, (newVal) => {
    if (newVal) {
        if (props.editMode && props.initialData) {
            profileId.value = props.editingId || null
            profileName.value = props.initialData.profileName || ''
        } else {
            resetForm()
        }
        getFuncProfileList()
    }
})

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

const handleEdit = async (data: any) => {
    const funcProfileId = String(data?.id || '')
    if (!funcProfileId) return

    loadingTable.value = true
    try {
        const response = await axios.get(`${api.getFuncProfileDetail}${funcProfileId}`)
        const detail = response?.data?.data || response?.data || {}

        modalSubmitFuncProfileTitle.value = t('text.functional-profile-management-pg.edit-functional-profile' as any) || 'Edit Functional Profile'
        editModeFuncProfile.value = true
        editingIdFuncProfile.value = funcProfileId
        editDataFuncProfile.value = {
            profile: detail?.profile || data?.profile || null,
            company: detail?.company || data?.company || null,
            merchStruct: detail?.merch_struct || data?.merch_struct || null,
            limit: detail?.limit || data?.limit || null,
            startDate: detail?.start_date || data?.start_date || null,
            endDate: detail?.end_date || data?.end_date || null,
            isActive: detail?.is_active || data?.is_active || false,
        }
        modalSubmitFuncProfileOpen.value = true
    } catch (error: any) {
        console.error('Error fetching functional profile detail:', error?.response?.data || error?.message)
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

// Submit create/update profile
const postSubmitFuncProfile = async () => {
    // Reset errors
    errors.value = {
        profileName: ''
    }

    // Validation
    let hasError = false

    if (!profileName.value) {
        errors.value.profileName = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {

        const payload = {
            profileName: profileName.value,
        }

        if (props.editMode && props.editingId) {
            await axios.put(`${api.postFuncProfileUpdate}${props.editingId}`, payload)
        } else {
            await axios.post(api.postFuncProfileCreate, payload)
        }

        Swal?.fire({
            icon: 'success',
            title: t('text.message.success' as any) || 'Success!',
            text: props.editMode ? t('text.message.data-updated-msg' as any) || 'The data has been updated successfully!' : t('text.message.data-saved-msg' as any) || 'The data has been saved successfully!',
            confirmButtonText: 'OK'
        })

        emit('submitted')
        closeModal()
    } catch (error: any) {
        console.error('Error submitting functional profile:', error.response?.data || error.message)
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

const isOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value)
})
</script>

<template>
    <UModal
        v-model:open="isOpen"
        :title="title"
        :dismissible="false"
        :class="TITLE_MODAL_TEXT_CLASS"
        :ui="{
            content: `w-full mx-auto sm:max-w-md md:max-w-2xl lg:max-w-5xl`,
            body: 'sm:pt-2 sm:px-6',
            footer: 'justify-end'
        }"
    >
        <template #body>
            <div class="overflow-y-auto">
                <!-- MODAL -->
                <DialogFormFuncProfileSubmit
                    :open="modalSubmitFuncProfileOpen"
                    :title="modalSubmitFuncProfileTitle"
                    :edit-mode="editModeFuncProfile"
                    :editing-id="editingIdFuncProfile"
                    :profile-data="props.initialData"
                    :company-options="props.companyOptions"
                    :limit-options="props.limitOptions"
                    :division-options="props.divisionOptions"
                    :initial-data="editDataFuncProfile"
                    :can-update-func-profile="props.canUpdateFuncProfile"
                    @update:open="modalSubmitFuncProfileOpen = $event"
                    @submitted="onSubmittedFuncProfile"
                    @close="closeModalSubmitFuncProfile"
                />
    
                <!-- PROFILE -->
                <div class="flex flex-col md:flex-row w-full gap-2">
                    <!-- PROFILE -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto font-semibold" :class="TEXT_SIZE_CLASS">
                            {{ t('text.functional-profile-management-pg.input-new-profile') || 'Profile' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="profileName"
                                size="md"
                                disabled
                                class="w-full font-light"
                                :ui="{
                                    base: TEXT_SIZE_CLASS
                                }"
                            />
                        </div>
                    </div>
    
                    <div class="px-2"></div>
    
                    <!-- BUTTON NEW -->
                    <div class="flex w-full justify-end">
                        <UButton
                            v-if="props.canCreateFuncProfile"
                            type="button"
                            :disabled="isSubmitting"
                            @click="showModalSubmitFuncProfile"
                            :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`"
                        >
                            {{ t('text.button.new').toUpperCase() || 'NEW' }}
                        </UButton>
                    </div>
                </div>
    
                <div class="mt-2">    
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
                        :column-pinning="columnPinning"
                        @update:currentPage="handlePageChange"
                        @update:pageSize="handlePageSizeChange"
                        @search="handleSearch"
                    />
                </div>
            </div>
        </template>

        <template #footer>
            <UButton
                :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`"
                :loading="isSubmitting"
                :disabled="isSubmitting"
                @click="closeModal"
            >
                {{ t('text.button.close') || 'Close' }}
            </UButton>
        </template>
    </UModal>
</template>
