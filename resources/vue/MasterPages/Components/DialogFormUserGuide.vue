<script setup lang="ts">
import { ref, computed, watch, getCurrentInstance } from 'vue'
import { useI18n } from '../../composables/useI18n'
import { useFormatters } from '../../composables/useFormatters'
import { useApiStore } from '../../AppState'
import axios from 'axios'
import type { SelectMenuItem } from '@nuxt/ui'
import { TEXT_SIZE_CLASS, TEXT_TITLE_SIZE_CLASS, TITLE_MODAL_TEXT_CLASS, INPUT_FIELD_WARN_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    viewOnly: {
        type: Boolean,
        default: false
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
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

// ========================= STATE FOR MODAL =========================
const isSubmitting = ref(false)

const name = ref<string>('')

const menuValue = ref<SelectMenuItem[]>([
    {
        type: 'label',
        label: 'Master Data'
    },
    'Limit',
    'Profile',
    'Functional Profile',
    'User',
    'Approval Flow',
    'Regional Site',
    'User Guide',
    {
        type: 'separator'
    },
    {
        type: 'label',
        label: 'New Registration'
    },
    'Article',
    'Supplier',
    {
        type: 'separator'
    },
    {
        type: 'label',
        label: 'Purchase Order'
    },
    'PO Status Report',
    'PO List',
    'PO Cross Dock',
    'Return',
])

const menu = ref<string | null>(null)

const valueSwitch = ref(true)

// Validation error states
const errors = ref({
    name: '',
    menu: ''
})

const resetForm = () => {
    name.value = ''
    menu.value = null
    valueSwitch.value = true

    errors.value = {
        name: '',
        menu: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, (newVal) => {
    if (newVal) {
        if (props.editMode && props.initialData) {
            name.value = props.initialData.name || ''
            menu.value = props.initialData.menu || null
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
        name: '',
        menu: ''
    }

    // Validation
    let hasError = false


    if (!name.value.trim()) {
        errors.value.name = t('text.validation.required-field' as any) || 'This field is required.'
        hasError = true
    }

    if (!menu.value) {
        errors.value.menu = t('text.validation.required-field' as any) || 'This field is required.'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {

        const payload = {
            name: name.value.trim(),
            menu: menu.value,
            status: valueSwitch.value ? 1 : 0
        }

        // let response
        // if (props.editMode && props.editingId) {
        //     // Update existing user guide
        //     response = await axios.put(`${api.postUserGuideUpdate}${props.editingId}`, payload)
        // } else {
        //     // Create new user guide
        //     response = await axios.post(api.postUserGuideCreate, payload)
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
        console.error('Error submitting user guide:', error.response?.data || error.message)
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
            <!-- NAME -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.name"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.user-guide-management-pg.input-new-name') || 'Name' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInput
                        v-model="name"
                        required
                        :placeholder="t('text.user-guide-management-pg.input-new-name-placeholder') || 'Enter user guide name'"
                        class="w-full"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.name
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`
                        }"
                        :disabled="viewOnly"
                    />
                    <p v-if="errors.name" :class="INPUT_FIELD_WARN_CLASS">{{ errors.name }}</p>
                </div>
            </UFormField>

            <!-- MENU -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.menu"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.user-guide-management-pg.input-new-menu') || 'Menu' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="menu"
                        :items="menuValue"
                        placeholder="Select menu"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.menu
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="viewOnly"
                    />
                    <p v-if="errors.menu" :class="INPUT_FIELD_WARN_CLASS">{{ errors.menu }}</p>
                </div>
            </UFormField>

            <!-- STATUS -->
            <UFormField orientation="horizontal" class="mb-2" >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.profile-management-pg.input-new-status') || 'Status' }}
                    </span>
                </template>

                <div class="flex justify-start w-80">
                    <USwitch v-model="valueSwitch" :disabled="viewOnly" />
                </div>
            </UFormField>
        </template>

        <template #footer>
            <UButton
                v-if="!editMode && !viewOnly"
                :class="`${BUTTON_CLEAR_CLASS} ${TEXT_SIZE_CLASS}`"
                :disabled="isSubmitting"
                @click="resetForm"
            >{{ t('text.button.clear') || 'Clear' }}</UButton>

            <UButton
                v-if="!viewOnly"
                :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`"
                :loading="isSubmitting"
                :disabled="isSubmitting"
                @click="postSubmitProfile"
            >
                {{ t('text.button.submit') || 'Submit' }}
            </UButton>

            <UButton
                v-if="viewOnly"
                :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`"
                @click="closeModal"
            >
                {{ t('text.button.close') || 'Close' }}
            </UButton>
        </template>
    </UModal>
</template>
