<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef, onMounted, watch, getCurrentInstance } from 'vue'
import type { TableColumn } from '@nuxt/ui'
import { CalendarDate, DateFormatter, getLocalTimeZone, today } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'
import { useFormatters } from '../../composables/useFormatters'
import { useApiStore } from '../../AppState'
import axios from 'axios'
import type { TreeItemSelectEvent } from 'reka-ui'
import type { TreeItem } from '@nuxt/ui'

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


const functionalProfileName = ref<string>('')
const profileValue = ref(['SUPERADMIN', 'ADMIN', 'MD FASHION', 'MD FRESH'])
const profile = ref<string | null>(null)
const limitValue = ref(['L001', 'L002', 'L003'])
const limit = ref<string | null>(null)

type DivisionItem = {
  label: string
  value?: string
  children?: DivisionItem[]
}

const items: TreeItem[] = [
  {
    label: 'A - LADIES',
    children: [
        { label: 'A1 - MISSY', },
        { label: 'A2 - YOUNG', },
        { label: 'A3 - INTIMATE', },
        { label: 'A4 - BRANDED OUTRIGHT NORMAL', },
        { label: 'A5 - SPECIAL BRAND',  }
    ]
  },
  {
    label: 'B - MENS',
    children: [
        { label: 'B1 - FORMAL', },
        { label: 'B2 - ADULT', },
        { label: 'B3 - YOUTH', },
        { label: 'B4 - MENS NEEDS', },
        { label: 'B5 - MOSLEM WEAR',  }
    ]
  },
  {
    label: 'C  - BABY AND KIDS',
    children: [
        { label: 'C1 - KIDS BOYS', },
        { label: 'C2 - KIDS GIRLS', },
        { label: 'C3  - SCHOOL UNIFORM', },
        { label: 'C4 - TODDLER BOYS', },
        { label: 'C5 - TODDLER GIRLS',  }
    ]
  },
]

const selectDivision = ref<string | null>(null)

const valueSwitch = ref(true)

// Validation error states
const errors = ref({
    functionalProfileName: '',
    profile: '',
    limit: ''
})

const resetForm = () => {
    functionalProfileName.value = ''
    profile.value = null
    limit.value = null
    valueSwitch.value = true

    errors.value = {
        functionalProfileName: '',
        profile: '',
        limit: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, (newVal) => {
    if (newVal) {
        if (props.editMode && props.initialData) {
            functionalProfileName.value = props.initialData.functionalProfileName || ''
            profile.value = props.initialData.profile || null
            limit.value = props.initialData.limit || null
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
        functionalProfileName: '',
        profile: '',
        limit: ''
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

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {

        const payload = {
            functional_profile_name: functionalProfileName.value,
            profile: profile.value,
            limit: limit.value,
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
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="profile"
                        :items="profileValue"
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
                        :items="limitValue"
                        :placeholder="t('text.functional-profile-management-pg.input-new-limit-placeholder') || 'Select limit'"
                        class="w-80 font-light"
                    />
                    <p v-if="errors.limit" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.limit }}</p>
                </div>

            </UFormField>

            <!-- DIVISION -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.accessRight"
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

                <div class="w-80 font-light max-h-70 overflow-y-auto border border-gray-300 rounded-md p-2 dark:border-gray-700">
                    <UTree
                        v-model="selectDivision"
                        :as="{ link: 'div' }"
                        :items="items"
                        @select="onSelect"
                    >
                        <template #item-leading="{ selected, handleSelect }">
                            <URadioGroup :model-value="selected"
                                @update:model-value="handleSelect"
                                @click.stop
                            />
                        </template>
                    </UTree>
                </div>

            </UFormField>

            <!-- STATUS -->
            <UFormField orientation="horizontal" class="mb-2" >
                <template #label>
                    <span class="flex items-center gap-1">
                        {{ t('text.functional-profile-management-pg.input-new-status') || 'Status' }}
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
