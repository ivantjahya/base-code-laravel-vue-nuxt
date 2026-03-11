<script setup lang="ts">
import { ref, computed, watch, getCurrentInstance, shallowRef } from 'vue'
import { CalendarDate } from '@internationalized/date'
import axios from 'axios'
import { useI18n } from '../../composables/useI18n'
import { useApiStore } from '../../AppState'
import { useFormatters } from '../../composables/useFormatters'
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
    profileData: {
        type: Object,
        default: () => ({})
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
    canUpdateFuncProfile: {
        type: Boolean,
        default: false
    },
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const { getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

// ========================= STATE FOR MODAL =========================
const isSubmitting = ref(false)

const profileIId = ref<string | null>(null)
const profileName = ref<string | null>(null)
const selectedCompany = ref<string | null>(null)
const selectedDivision = ref<string | null>(null)
const selectedDivisionArr = ref<string[]>([])
const selectedLimit = ref<string | null>(null)

const inputStartDate = ref<any>(null)
const modelValueStart = shallowRef<CalendarDate>()
const inputEndDate = ref<any>(null)
const modelValueEnd = shallowRef<CalendarDate>()
const isPopoverFormStartDateOpen = ref(false)
const isPopoverFormEndDateOpen = ref(false)

// Validation error states
const errors = ref({
    profileIId: '',
    profileName: '',
    company: '',
    division: '',
    limit: '',
    startDate: '',
    endDate: '',
})

// ========================= ACTION =========================
const disableForm = computed(() => {
    const today = new Date()
    const isActive = props.initialData.isActive
    const endDate = new Date(props.initialData.endDate)

    if (isActive) {
        return true
    }

    // Compare only the date part by setting time to 00:00:00
    today.setHours(0,0,0,0)
    endDate.setHours(0,0,0,0)

    return endDate < today
})

const disableEndDateForm = computed(() => {
    const today = new Date()
    const isActive = props.initialData.isActive
    const endDate = new Date(props.initialData.endDate)

    if (isActive) {
        return false
    }

    // Compare only the date part by setting time to 00:00:00
    today.setHours(0,0,0,0)
    endDate.setHours(0,0,0,0)

    return endDate < today
})

const allDivisionsChecked = computed({
    get: () => {
        const allNodes = props.divisionOptions.map(option => option.value)
        if (allNodes.length === 0) return false

        const selectedValues = selectedDivisionArr.value.map(String)
        return allNodes.every((node: any) => selectedValues.includes(String(node)))
    },
    set: (checked: boolean) => {
        const allNodes = props.divisionOptions.map(option => option.value)
        if (!checked || allNodes.length === 0) {
            selectedDivisionArr.value = []
            return
        }

        selectedDivisionArr.value = allNodes
    }
})

const resetFormfuncProfile = () => {
    selectedCompany.value = null
    selectedDivision.value = null
    selectedDivisionArr.value = []
    selectedLimit.value = null
    modelValueStart.value = undefined
    modelValueEnd.value = undefined

    errors.value = {
        profileIId: '',
        profileName: '',    
        company: '',
        division: '',
        limit: '',
        startDate: '',
        endDate: '',
    }
}

const resetForm = () => {
    profileIId.value = null
    profileName.value = null
    selectedCompany.value = null
    selectedDivision.value = null
    selectedDivisionArr.value = []
    selectedLimit.value = null
    modelValueStart.value = undefined
    modelValueEnd.value = undefined

    errors.value = {
        profileIId: '',
        profileName: '',
        company: '',
        division: '',
        limit: '',
        startDate: '',
        endDate: '',
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, (newVal) => {
    if (newVal) {
        if (props.profileData) {
            profileIId.value = props.profileData?.profileId || ''
            profileName.value = props.profileData?.profileName || ''
        }

        if (props.editMode && props.initialData) {
            selectedCompany.value = props.initialData.company?.id || ''
            selectedDivision.value = props.initialData.merchStruct?.id || ''
            selectedLimit.value = props.initialData.limit?.code || ''
            modelValueStart.value = stringToCalendarDate(props.initialData.startDate)
            modelValueEnd.value = stringToCalendarDate(props.initialData.endDate)
        } else {
            resetFormfuncProfile()
        }
    }
})

// Submit create/update profile
const postSubmitFuncProfile = async () => {
    // Reset errors
    errors.value = {
        profileIId: '',
        profileName: '',
        company: '',
        division: '',
        limit: '',
        startDate: '',
        endDate: '',
    }

    // Validation
    let hasError = false

    if (profileName.value?.trim() === '' || profileIId.value === '') {
        errors.value.profileName = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!selectedCompany.value) {
        errors.value.company = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!selectedDivision.value && props.editMode) {
        errors.value.division = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (selectedDivisionArr.value.length === 0 && !props.editMode) {
        errors.value.division = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!selectedLimit.value) {
        errors.value.limit = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!getDateString(modelValueStart.value)) {
        errors.value.startDate = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!getDateString(modelValueEnd.value)) {
        errors.value.endDate = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {
        // Convert CalendarDate to YYYY-MM-DD format
        const startDateFormatted = getDateString(modelValueStart.value)
        const endDateFormatted = getDateString(modelValueEnd.value)

        if (props.editMode && props.editingId) {
            const payload = {
                profile_id: profileIId.value,
                company: selectedCompany.value,
                merch_struct: selectedDivision.value,
                limit: selectedLimit.value,
                start_date: startDateFormatted,
                end_date: endDateFormatted,
            }

            await axios.put(`${api.postFuncProfileUpdate}${props.editingId}`, payload)
        } else {
            const divisionPayload = selectedDivisionArr.value.map(divisionId => ({ id: divisionId }))
            const payload = {
                profile_id: profileIId.value,
                company: selectedCompany.value,
                division: divisionPayload,
                limit: selectedLimit.value,
                start_date: startDateFormatted,
                end_date: endDateFormatted,
            }

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
        :ui="{ footer: 'justify-end' }"
    >
        <template #body>
            <!-- PROFILE NAME -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.profileName"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.profile-management-pg.input-new-profile-name') || 'Profile Name' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>
                <div class="w-80">
                    <UInput
                        v-model="profileName"
                        disabled
                        required
                        :placeholder="t('text.profile-management-pg.input-new-profile-name-placeholder') || 'Enter profile name'"
                        class="w-80 border-[#CAD5E2] font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.profileName
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`
                        }"
                    />
                    <p v-if="errors.profileName" :class="INPUT_FIELD_WARN_CLASS">{{ errors.profileName }}</p>
                </div>
            </UFormField>

            <!-- COMPANY -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.company"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.functional-profile-management-pg.input-company') || 'Company' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>
                <div class="w-80">
                    <USelectMenu
                        v-model="selectedCompany"
                        :items="companyOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.functional-profile-management-pg.input-company-placeholder') || 'Select company'"
                        class="w-80 font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.company
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="disableForm"
                    />
                    <p v-if="errors.company" :class="INPUT_FIELD_WARN_CLASS">{{ errors.company }}</p>
                </div>
            </UFormField>

            <!-- DIVISION -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.division"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.input-field.division') || 'Division' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>
                <div v-if="!editMode"
                    class="w-80 font-light max-h-36 overflow-y-auto border border-gray-300 rounded-md p-2 dark:border-gray-700"
                >
                    <!-- <p v-if="divisionLoading" class="text-xs md:text-sm text-gray-500">{{ t('text.message.loading') || 'Loading...' }}</p> -->
                    <div class="space-y-2">
                        <UCheckbox
                            v-for="division in divisionOptions"
                            :key="division.value"
                            :id="`division-checkbox-${division.value}`"
                            :model-value="selectedDivisionArr.includes(division.value)"
                            :label="division.label"
                            :ui="{
                                label: TEXT_SIZE_CLASS
                            }"
                            @update:model-value="(val) => {
                                if (val) {
                                    if (!selectedDivisionArr.includes(division.value)) selectedDivisionArr.push(division.value)
                                    if (props.divisionOptions.length > 0 && selectedDivisionArr.length === props.divisionOptions.length) {
                                        allDivisionsChecked = true
                                    }
                                } else {
                                    const idx = selectedDivisionArr.indexOf(division.value)
                                    if (idx !== -1) selectedDivisionArr.splice(idx, 1)
                                }
                            }"
                            :disabled="disableForm"
                       />
                    </div>
                </div>
                <div v-else class="w-80 font-light">
                    <USelectMenu
                        v-model="selectedDivision"
                        :items="divisionOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.functional-profile-management-pg.input-new-division-placeholder') || 'Select division'"
                        class="w-80 font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.division
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="disableForm"
                    />
                </div>
                <p v-if="errors.division" :class="INPUT_FIELD_WARN_CLASS">{{ errors.division }}</p>
            </UFormField>
            
            <!-- CHECK ALL -->
            <UFormField v-if="!editMode" orientation="horizontal" class="mb-2">
                <div class="flex items-center w-80 gap-2">
                    <UCheckbox
                        v-model="allDivisionsChecked"
                        :disabled="divisionOptions.length === 0 || disableForm"
                    />
                    <span class="font-light" :class="TEXT_SIZE_CLASS">
                        {{ t('text.button.check-all') || 'Check All' }}
                    </span>
                </div>
            </UFormField>

            <!-- LIMIT -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.limit"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.input-field.limit') || 'Limit' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>
                <div class="w-80">
                    <USelectMenu
                        v-model="selectedLimit"
                        :items="limitOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.functional-profile-management-pg.input-new-limit-placeholder') || 'Select limit'"
                        class="w-80 font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.limit
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="disableForm"
                    />
                    <p v-if="errors.limit" :class="INPUT_FIELD_WARN_CLASS">{{ errors.limit }}</p>
                </div>
            </UFormField>

            <!-- START DATE -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.startDate"
                :ui="{ 
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.input-field.start-date') || 'Start Date' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInputDate
                        ref="inputStartDate"
                        v-model="modelValueStart"
                        locale="en-GB"
                        format="dd/mm/yyyy"
                        :max-value="modelValueEnd"
                        class="w-full font-reguler"
                        :class="TEXT_SIZE_CLASS"
                        :ui="{
                            base: errors.startDate
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                        :disabled="disableForm"
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
                                    :disabled="disableForm"
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
                    <p v-if="errors.startDate" :class="INPUT_FIELD_WARN_CLASS">{{ errors.startDate }}</p>
                </div>
            </UFormField>

            <!-- END DATE -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.endDate"
                :ui="{ 
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.input-field.end-date') || 'End Date' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInputDate
                        ref="inputEndDate"
                        v-model="modelValueEnd"
                        locale="en-GB"
                        format="dd/mm/yyyy"
                        :min-value="modelValueStart"
                        class="w-full font-reguler"
                        :class="TEXT_SIZE_CLASS"
                        :ui="{
                            base: errors.endDate
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                        :disabled="disableEndDateForm"
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
                                    :disabled="disableEndDateForm"
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
                    <p v-if="errors.endDate" :class="INPUT_FIELD_WARN_CLASS">{{ errors.endDate }}</p>
                </div>
            </UFormField>
        </template>

        <template #footer>
            <UButton
                v-if="!editMode && props.canUpdateFuncProfile"
                :class="`${BUTTON_CLEAR_CLASS} ${TEXT_SIZE_CLASS}`"
                :disabled="isSubmitting"
                @click="resetFormfuncProfile"
            >{{ t('text.button.clear') || 'Clear' }}</UButton>

            <UButton
                v-if="props.canUpdateFuncProfile"
                :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`"
                :loading="isSubmitting"
                :disabled="isSubmitting"
                @click="postSubmitFuncProfile"
            >
                {{ t('text.button.submit') || 'Submit' }}
            </UButton>

            <UButton
                v-if="!props.canUpdateFuncProfile"
                :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`"
                @click="closeModal"
            >
                {{ t('text.button.close') || 'Close' }}
            </UButton>
        </template>
    </UModal>
</template>
