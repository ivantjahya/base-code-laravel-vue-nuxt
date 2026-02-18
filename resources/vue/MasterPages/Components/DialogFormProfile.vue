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

const profileName = ref<string>('')
const description = ref<string>('')

const profileSourceValue = ref(['Internal', 'External'])
const profileSource = ref<string | null>(null)

const treeValue = ref<string[]>([])
const items: TreeItem[] = [
  {
    label: 'Master Data',
    value: 'master-data',
    children: [
      {
        label: 'Limit',
        value: 'master-limit',
        children: [
          { label: 'List', value: 'limit-list' },
          { label: 'Create', value: 'limit-create' },
          { label: 'Update', value: 'limit-update' }
        ]
      },
      {
        label: 'Profile',
        value: 'master-profile',
        children: [
          { label: 'List', value: 'profile-list' },
          { label: 'Create', value: 'profile-create' },
          { label: 'Update', value: 'profile-update' }
        ]
      },
      {
        label: 'Functional Profile',
        value: 'functional-profile',
        children: [
          { label: 'List', value: 'fp-list' },
          { label: 'Create', value: 'fp-create' },
          { label: 'Update', value: 'fp-update' },
          { label: 'Reset Password', value: 'fp-reset-password' }
        ]
      },
      {
        label: 'User',
        value: 'user',
        children: [
          { label: 'List', value: 'user-list' },
          { label: 'Create', value: 'user-create' },
          { label: 'Update', value: 'user-update' },
          { label: 'Reset Password', value: 'user-reset-password' }
        ]
      }
    ]
  },
  { label: 'New Registration', value: 'new-registration' },
  { label: 'Purchase Order (PO)', value: 'purchase-order' },
  { label: 'Consignment', value: 'consignment' }
]

function onSelect(e: TreeItemSelectEvent<TreeItem>) {
  if (e.detail.originalEvent.type === 'click') {
    e.preventDefault()
  }
}


const valueSwitch = ref(true)

// Validation error states
const errors = ref({
    profileName: '',
    description: '',
    profileSource: '',
    accessRight: ''
})

const resetForm = () => {
    profileName.value = ''
    description.value = ''
    profileSource.value = null
    valueSwitch.value = true

    errors.value = {
        profileName: '',
        description: '',
        profileSource: '',
        accessRight: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, (newVal) => {
    if (newVal) {
        if (props.editMode && props.initialData) {
            profileName.value = props.initialData.profileName || ''
            description.value = props.initialData.description || ''
            profileSource.value = props.initialData.profileSource || null
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
        profileName: '',
        description: '',
        profileSource: '',
        accessRight: ''
    }

    // Validation
    let hasError = false

    if (profileName.value.trim() === '') {
        errors.value.profileName = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!profileSource.value) {
        errors.value.profileSource = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {

        const payload = {
            profile_name: profileName.value,
            description: description.value,
            profile_source: profileSource.value,
            access_right: treeValue.value,
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
        console.error('Error submitting profile:', error.response?.data || error.message)
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
                        {{ t('text.profile-management-pg.input-new-profile-name') || 'Profile Name' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInput
                        v-model="profileName"
                        required
                        :placeholder="t('text.profile-management-pg.input-new-profile-name-placeholder') || 'Enter profile name'"
                        class="w-full"
                        :ui="{
                            base: errors.profileName
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.profileName" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.profileName }}</p>
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
                        {{ t('text.profile-management-pg.input-new-description') || 'Description' }}
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

            <!-- PROFILE SOURCE -->
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
                        {{ t('text.profile-management-pg.input-new-profile-source') || 'Profile Source' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="profileSource"
                        :items="profileSourceValue"
                        placeholder="Select profile source"
                        class="w-full font-light"
                        :ui="{
                            base: errors.profileSource
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.profileSource" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.profileSource }}</p>
                </div>

            </UFormField>

            <!-- ACCESS RIGHT -->
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
                        {{ t('text.profile-management-pg.input-new-access-right') || 'Access Right' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80 max-h-70 overflow-y-auto border border-gray-300 rounded-md p-2 dark:border-gray-700">
                    <UTree
                        v-model="treeValue"
                        :as="{ link: 'div' }"
                        :items="items"
                        multiple
                        propagate-select
                        bubble-select
                        @select="onSelect"
                        class="font-light"
                    >
                        <template #item-leading="{ selected, indeterminate, handleSelect }">
                        <UCheckbox
                            :model-value="indeterminate ? 'indeterminate' : selected"
                            tabindex="-1"
                            @change="handleSelect"
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
                        {{ t('text.profile-management-pg.input-new-status') || 'Status' }}
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
