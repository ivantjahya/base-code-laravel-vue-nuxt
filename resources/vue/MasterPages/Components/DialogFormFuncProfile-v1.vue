<script setup lang="ts">
import { ref, computed, watch, getCurrentInstance } from 'vue'
import axios from 'axios'
import { useI18n } from '../../composables/useI18n'
import { useApiStore } from '../../AppState'

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
    limitOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    profileOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    divisionOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    divisionLoading: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const isSubmitting = ref(false)

const functionalProfileName = ref<string>('')
const profile = ref<string | null>(null)
const limit = ref<string | null>(null)
const selectDivision = ref<string | null>(null)
const valueSwitch = ref(true)

// Validation error states
const errors = ref({
    functionalProfileName: '',
    profile: '',
    limit: '',
    division: ''
})

const resetForm = () => {
    functionalProfileName.value = ''
    profile.value = null
    limit.value = null
    selectDivision.value = null
    valueSwitch.value = true

    errors.value = {
        functionalProfileName: '',
        profile: '',
        limit: '',
        division: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, (newVal) => {
    if (newVal) {
        if (props.editMode && props.initialData) {
            functionalProfileName.value = props.initialData.name || ''
            profile.value = props.initialData.profile?.id || null
            limit.value = props.initialData.limit?.code || null
            selectDivision.value = props.initialData.merch_struct?.id || null
            valueSwitch.value = Boolean(props.initialData.status ?? true)
        } else {
            resetForm()
        }
    }
})

// Submit create/update profile
const postSubmitFuncProfile = async () => {
    // Reset errors
    errors.value = {
        functionalProfileName: '',
        profile: '',
        limit: '',
        division: ''
    }

    // Validation
    let hasError = false

    if (functionalProfileName.value.trim() === '') {
        errors.value.functionalProfileName = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!profile.value) {
        errors.value.profile = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!limit.value) {
        errors.value.limit = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!selectDivision.value) {
        errors.value.division = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {

        const payload = {
            name: functionalProfileName.value,
            profile: profile.value,
            limit: limit.value,
            merch_struct: selectDivision.value,
            status: valueSwitch.value ? 1 : 0
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
    <UModal v-model:open="isOpen" :title="title" :dismissible="false" class="text-[16px] font-semibold" :ui="{ footer: 'justify-end' }">
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
                    <span class="flex items-center gap-1">
                        {{ t('text.functional-profile-management-pg.input-new-functional-profile-name') || 'Functional Profile Name' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>
                <div class="w-80">
                    <UInput
                        v-model="functionalProfileName"
                        required
                        :placeholder="t('text.functional-profile-management-pg.input-new-functional-profile-name-placeholder') || 'Enter functional profile name'"
                        class="w-80 border-[#CAD5E2] font-light"
                        :ui="{
                            base: errors.functionalProfileName
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.functionalProfileName" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.functionalProfileName }}</p>
                </div>
            </UFormField>

            <!-- PROFILE -->
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
                        {{ t('text.functional-profile-management-pg.input-new-profile') || 'Profile' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>
                <div class="w-80">
                    <USelectMenu
                        v-model="profile"
                        :items="profileOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.functional-profile-management-pg.input-new-profile-placeholder') || 'Select profile'"
                        class="w-80 font-light"
                        :ui="{
                            base: errors.profile
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.profile" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.profile }}</p>
                </div>
            </UFormField>

            <!-- LIMIT -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.profileSource"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1">
                        {{ t('text.functional-profile-management-pg.input-new-limit') || 'Limit' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>
                <div class="w-80">
                    <USelectMenu
                        v-model="limit"
                        :items="limitOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.functional-profile-management-pg.input-new-limit-placeholder') || 'Select limit'"
                        class="w-80 font-light"
                        :ui="{
                            base: errors.profile
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.limit" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.limit }}</p>
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
                        {{ t('text.functional-profile-management-pg.input-new-division') || 'Division' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>
                <div class="w-80 font-light max-h-48 overflow-y-auto border border-gray-300 rounded-md p-2 dark:border-gray-700">
                    <p v-if="divisionLoading" class="text-xs md:text-sm text-gray-500">{{ t('text.message.loading') || 'Loading...' }}</p>
                    <URadioGroup
                        v-model="selectDivision"
                        :items="divisionOptions"
                        value-key="value"
                        option-attribute="label"
                        :disabled="divisionLoading || isSubmitting"
                    />
                </div>
                <p v-if="errors.division" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.division }}</p>
            </UFormField>

            <!-- STATUS -->
            <UFormField orientation="horizontal" class="mb-2" >
                <template #label>
                    <span class="flex items-center gap-1">
                        {{ t('text.functional-profile-management-pg.input-new-status') || 'Status' }}
                        <span class="text-red-500">*</span>
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
                @click="postSubmitFuncProfile"
            >
                {{ t('text.button.submit') || 'Submit' }}
            </UButton>
        </template>
    </UModal>
</template>
