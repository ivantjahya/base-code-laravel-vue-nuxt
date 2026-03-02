<script setup lang="ts">
import { ref, computed, watch, getCurrentInstance, h, resolveComponent } from 'vue'
import axios from 'axios'
import { useI18n } from '../../composables/useI18n'
import { useApiStore } from '../../AppState'
import CmpCustomTable from '../../Components/CmpCustomTable.vue'
import CmpAccordionFilter from '../../Components/CmpAccordionFilter.vue'
import FormFilterFuncProfile from './FormFilterFuncProfile.vue'

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
    }
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const api = useApiStore()
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
        sortable: true,
    },
    {
        key: 'start_date',
        label: t('text.table-column.column-start-date'),
        sortable: true,
    },
    {
        key: 'end_date',
        label: t('text.table-column.column-end-date'),
        sortable: true,
    },
    {
        key: 'status',
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
const isSubmitting = ref(false)
const profileName = ref<string | null>(null)

// Validation error states
const errors = ref({
    profileName: '',
})

const resetForm = () => {
    profileName.value = null

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
        console.log('Dialog opened with initial data:', props.initialData)
        console.log('Edit mode:', props.editMode)
        console.log('Editing ID:', props.editingId)
        console.log('Company options:', props.companyOptions)
        console.log('Limit options:', props.limitOptions)
        console.log('Division options:', props.divisionOptions)

        if (props.editMode && props.initialData) {
            profileName.value = props.initialData.profileName || ''
        } else {
            resetForm()
        }
    }
})

// ========================= ACTION =========================
const handlePageChange = (page: number) => {
    currentPage.value = page
}

const handlePageSizeChange = (size: number) => {
    itemPerPage.value = size
    currentPage.value = 1 // Reset to first page when changing page size
}

const handleSearch = (query: string) => { // For global search, server-side
    globalSearchQuery.value = query
    currentPage.value = 1 // Reset to first page when searching
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
        class="text-[16px] font-semibold"
        :ui="{
            content: 'max-w-6xl',
            footer: 'justify-end'
        }"
    >
        <template #body>
            <!-- PROFILE -->
             <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                    {{ t('text.functional-profile-management-pg.input-new-profile') || 'Profile' }}
                </div>
                <div class="flex w-full text-sm">
                    <UInput
                        v-model="profileName"
                        size="md"
                        disabled
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>
            </div>

            <div class="mt-2">
                <!-- Filters Section with Accordion -->
                <!-- <CmpAccordionFilter>
                    <FormFilterFuncProfile
                        v-model:funcProfileCode="functionalProfileCodeFilter"
                        v-model:funcProfileName="functionalProfileNameFilter"
                        v-model:profile="profileFilter"
                        v-model:division="divisionFilter"
                        v-model:limit="limitFilter"
                        v-model:status="statusFilter"
                        :limit-options="limitOptions"
                        :profile-options="profileOptions"
                        :status-options="statusOptions"
                        :division-options="divisionOptions"
                        :loading="loadingTable"
                        @clear="resetFilter"
                        @find="onClickFindButton"
                    />
                </CmpAccordionFilter> -->
    
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
                @click="postSubmitFuncProfile"
            >
                {{ t('text.button.submit') || 'Submit' }}
            </UButton>
        </template>
    </UModal>
</template>
