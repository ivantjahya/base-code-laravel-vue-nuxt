<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef, onMounted, watch, getCurrentInstance } from 'vue'
import type { TableColumn } from '@nuxt/ui'
import { CalendarDate, DateFormatter, getLocalTimeZone, today } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'
import { useFormatters } from '../../composables/useFormatters'
import { useApiStore } from '../../AppState'
import axios from 'axios'

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
    }
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const isSubmitting = ref(false)

const valueMin = ref<number | null>(null)
const valueMax = ref<number | null>(null)

const inputStartDate = ref<any>(null)
const modelValueStart = shallowRef<CalendarDate>()
const inputEndDate = ref<any>(null)
const modelValueEnd = shallowRef<CalendarDate>()
const isPopoverFormStartDateOpen = ref(false)
const isPopoverFormEndDateOpen = ref(false)

const valueSwitch = ref(true)

// Validation error states
const errors = ref({
    valueMin: '',
    valueMax: '',
    startDate: '',
    endDate: '',
})

const resetForm = () => {
    valueMin.value = null
    valueMax.value = null
    modelValueStart.value = undefined
    modelValueEnd.value = undefined
    valueSwitch.value = true
    errors.value = {
        valueMin: '',
        valueMax: '',
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
        if (props.editMode && props.initialData) {
            valueMin.value = props.initialData.min_value
            valueMax.value = props.initialData.max_value
            modelValueStart.value = stringToCalendarDate(props.initialData.start_date)
            modelValueEnd.value = stringToCalendarDate(props.initialData.end_date)
            valueSwitch.value = props.initialData.is_active
        } else {
            resetForm()
        }
    }
})

// Submit create/update limit
const postSubmitLimit = async () => {
    // Reset errors
    errors.value = {
        valueMin: '',
        valueMax: '',
        startDate: '',
        endDate: '',
    }

    // Validation
    let hasError = false

    if (valueMin.value == null) {
        errors.value.valueMin = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (valueMax.value == null) {
        errors.value.valueMax = t('auth.validation.required' as any) || 'This field is required'
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

    if (valueMax.value != null && valueMin.value != null && valueMax.value < valueMin.value) {
        errors.value.valueMax = t('auth.validation.amount-max-greater-than-min' as any) || 'Maximum must be greater than or equal to minimum'
        hasError = true
    }

    if (hasError) {
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
        if (props.editMode && props.editingId) {
            // Update existing limit
            response = await axios.put(`${api.postLimitUpdate}${props.editingId}`, payload)
        } else {
            // Create new limit
            response = await axios.post(api.postLimitCreate, payload)
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

const isOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value)
})
</script>

<template>
    <UModal v-model:open="isOpen" :title="title" :dismissible="false" class="text-[16px] font-semibold" :ui="{ footer: 'justify-end' }">
        <template #body>
            <!-- MINIMUM -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.valueMin"
                :ui="{ 
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1">
                        {{ t('text.limit-management-pg.input-new-minimum') || 'Minimum' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInputNumber
                        v-model="valueMin"
                        required
                        locale="id-ID"
                        :format-options="{
                            style: 'currency',
                            currency: 'IDR'
                        }"
                        class="w-full font-reguler"
                        :ui="{
                            base: errors.valueMin
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                </div>
                <p v-if="errors.valueMin" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.valueMin }}</p>
            </UFormField>

            <!-- MAXIMUM -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.valueMax"
                :ui="{ 
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1">
                        {{ t('text.limit-management-pg.input-new-maximum') || 'Maximum' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInputNumber
                        v-model="valueMax"
                        locale="id-ID"
                        :format-options="{
                            style: 'currency',
                            currency: 'IDR'
                        }"
                        class="w-full font-reguler"
                        :ui="{
                            base: errors.valueMin
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.valueMax" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.valueMax }}</p>
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
                    <span class="flex items-center gap-1">
                        {{ t('text.limit-management-pg.input-new-start-date') || 'Start Date' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInputDate
                        ref="inputStartDate"
                        v-model="modelValueStart"
                        locale="id-ID" format="dd/mm/yyyy"
                        :max-value="modelValueEnd"
                        :disabled="editMode"
                        class="w-full font-reguler"
                        :ui="{
                            base: errors.startDate
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
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
                    <p v-if="errors.startDate" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.startDate }}</p>
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
                    <span class="flex items-center gap-1">
                        {{ t('text.limit-management-pg.input-new-end-date') || 'End Date' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInputDate
                        ref="inputEndDate"
                        v-model="modelValueEnd"
                        locale="id-ID"
                        format="dd/mm/yyyy"
                        :min-value="modelValueStart"
                        :disabled="editMode"
                        class="w-full font-reguler"
                        :ui="{
                            base: errors.endDate
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
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
                    <p v-if="errors.endDate" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.endDate }}</p>
                </div>
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
</template>
