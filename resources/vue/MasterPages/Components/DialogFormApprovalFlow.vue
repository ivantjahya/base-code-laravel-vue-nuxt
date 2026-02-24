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

const description = ref<string>('')

const profileValue = ref(['SUPERADMIN', 'ADMIN', 'MD FASHION', 'MD FRESH'])
const profile = ref<string | null>(null)
const divisionValue = ref(['A1 - MISSY', 'A2 - YOUNG', 'A3 - INTIMATE', 'A4 - BRANDED OUTRIGHT NORMAL'])
const division = ref<string | null>(null)

const valueSwitch = ref(true)

// Validation error states
const errors = ref({
    profile: '',
    division: '',
    description: ''
})

const resetForm = () => {
    profile.value = null
    division.value = null
    description.value = ''
    // Reset errors
    errors.value = {
        profile: '',
        division: '',
        description: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, (newVal) => {
    if (newVal) {
        if (props.editMode && props.initialData) {
            description.value = props.initialData.description || ''
            profile.value = props.initialData.profile || null
            division.value = props.initialData.division || null
            valueSwitch.value = props.initialData.status ?? true
        } else {
            resetForm()
        }
    }
})

// Submit create/update profile
const postSubmitProfile = async () => {
    // Reset errors
    errors.value = {
        description: '',
        profile: '',
        division: ''
    }

    // Validation
    let hasError = false

    if (!profile.value) {
        errors.value.profile = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!division.value) {
        errors.value.division = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {

        const payload = {
            description: description.value,
            profile: profile.value,
            division: division.value,
            status: valueSwitch.value ? 1 : 0
        }

        // let response
        // if (props.editMode && props.editingId) {
        //     // Update existing profile
        //     response = await axios.put(`${api.postProfileUpdate}${props.editingId}`, payload)
        // } else {
        //     // Create new profile
        //     response = await axios.post(api.postProfileCreate, payload)
        // }

        Swal?.fire({
            icon: 'success',
            title: t('text.message.success' as any) || 'Success!',
            text: props.editMode ? t('text.message.data-updated-msg' as any) || 'The data has been updated successfully!' : t('text.message.data-saved-msg' as any) || 'The data has been saved successfully!',
            confirmButtonText: 'OK'
        })

        emit('submitted')
        closeModal()
    } catch (error: any) {
        console.error('Error submitting user:', error.response?.data || error.message)
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

            <!-- PROFILE -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.profile"
                :ui="{
                    error: 'hidden',
                }"
            >

                <template #label>
                    <span class="flex items-center gap-1">
                        {{ t('text.approval-flow-management-pg.input-new-profile') || 'Profile' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="profile"
                        :items="profileValue"
                        placeholder="Select profile"
                        class="w-full font-light"
                        :ui="{
                            base: errors.profile
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.profile" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.profile }}</p>
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
                    <span class="flex items-center gap-1">
                        {{ t('text.approval-flow-management-pg.input-new-division') || 'Division' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        multiple
                        v-model="division"
                        :items="divisionValue"
                        placeholder="Select division"
                        class="w-full font-light"
                        :ui="{
                            base: errors.division
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.category" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.category }}</p>
                </div>

            </UFormField>

            <!-- DESCRIPTION -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.description"
                :ui="{
                    error: 'hidden',
                }"
            >

                <template #label>
                    <span class="flex items-center gap-1">
                        {{ t('text.approval-flow-management-pg.input-new-description') || 'Description' }}
                    </span>
                </template>

                <div class="w-80">
                    <UTextarea
                        v-model="description"
                        :maxrows="5"
                        autoresize
                        class="w-full font-light"
                        :ui="{
                            base: errors.description
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                </div>

            </UFormField>

            <!-- STATUS -->
            <UFormField orientation="horizontal" class="mb-2" >
                <template #label>
                    <span class="flex items-center gap-1">
                        {{ t('text.approval-flow-management-pg.input-new-status') || 'Status' }}
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
                @click="postSubmitProfile"
            >
                {{ t('text.button.submit') || 'Submit' }}
            </UButton>
        </template>
    </UModal>
</template>
