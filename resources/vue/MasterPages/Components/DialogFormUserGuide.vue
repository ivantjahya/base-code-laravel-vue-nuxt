<script setup lang="ts">
import { ref, computed, watch, getCurrentInstance } from 'vue'
import { useI18n } from '../../composables/useI18n'
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
    },
    menuOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    menuLoading: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

// ========================= STATE FOR MODAL =========================
const isSubmitting = ref(false)

const name = ref<string>('')
const description = ref<string>('')
const menu = ref<string | null>(null)
const valueSwitch = ref(true)

const selectedFile = ref<File | null>(null)
const currentFileName = ref<string>('')

// Validation error states
const errors = ref({
    name: '',
    description: '',
    menu: '',
    file: ''
})

// ========================= ACTION =========================
const resetForm = () => {
    name.value = ''
    description.value = ''
    menu.value = null
    valueSwitch.value = true
    selectedFile.value = null
    currentFileName.value = ''

    errors.value = {
        name: '',
        description: '',
        menu: '',
        file: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('update:open', false)
    emit('close')
}

watch(() => props.open, (newVal) => {
    if (newVal) {
        if (props.editMode && props.initialData) {
            name.value = props.initialData.name || ''
            description.value = props.initialData.description || ''
            menu.value = props.initialData.menu || null
            valueSwitch.value = props.initialData.status ?? true
            currentFileName.value = props.initialData.file_name || ''
            selectedFile.value = null
        } else {
            resetForm()
        }
    }
})

const handleDownload = async (data: any) => {
    const userGuideId = String(data?.id || '')
    if (!userGuideId) return

    isSubmitting.value = true
    try {
        const response = await axios.get(`${api.getUserGuideDownload}${userGuideId}`, {
            responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url

        const contentDisposition = response.headers['content-disposition']
        const fileName = contentDisposition
            ? contentDisposition.split('filename=')[1]?.replace(/['"]/g, '').trim()
            : (data?.file_name || 'download')

        link.setAttribute('download', fileName)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)
    } catch (error: any) {
        console.error('Error downloading user guide:', error?.response?.data || error?.message)
        Swal?.fire({
            icon: 'error',
            title: t('text.message.error' as any) || 'Error!',
            text: t('text.message.failed-to-load-data-msg' as any) || 'Failed to download file.',
            confirmButtonText: 'OK'
        })
    } finally {
        isSubmitting.value = false
    }
}

// Submit create/update profile
const postSubmitProfile = async () => {
    // Reset errors
    errors.value = {
        name: '',
        description: '',
        menu: '',
        file: ''
    }

    // Validation
    let hasError = false

    if (!name.value.trim()) {
        errors.value.name = t('auth.validation.required' as any) || 'This field is required.'
        hasError = true
    }

    if (!menu.value) {
        errors.value.menu = t('auth.validation.required' as any) || 'This field is required.'
        hasError = true
    }

    if (!props.editMode && !selectedFile.value) {
        errors.value.file = t('auth.validation.required' as any) || 'This field is required.'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {
        const formData = new FormData()
        formData.append('name', name.value.trim())
        formData.append('description', description.value.trim())
        formData.append('menu', menu.value as string)
        formData.append('status', valueSwitch.value ? '1' : '0')
        if (selectedFile.value) {
            formData.append('file', selectedFile.value)
        }

        let response
        if (props.editMode && props.editingId) {
            // PUT via POST with _method override — PHP only populates $_FILES on POST
            formData.append('_method', 'PUT')
            response = await axios.post(`${api.postUserGuideUpdate}${props.editingId}`, formData)
        } else {
            // Create new user guide
            response = await axios.post(api.postUserGuideCreate, formData)
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
    set: (value) => {
        if (!value) {
            closeModal()
            return
        }
        emit('update:open', value)
    }
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
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
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
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.description
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                        }"
                        :disabled="viewOnly"
                    />
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
                        :items="menuOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.user-guide-management-pg.input-new-menu-placeholder') || 'Select menu'"
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
                        {{ t('text.approval-flow-management-pg.input-new-status') || 'Status' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>
                <div class="flex justify-start w-80">
                    <USwitch v-model="valueSwitch" :disabled="viewOnly" />
                </div>
            </UFormField>

            <!-- FILE UPLOAD -->
            <UFormField v-if="editMode && currentFileName" orientation="horizontal" class="mb-2" >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.user-guide-management-pg.current-file') || 'Current File' }}
                    </span>
                </template>
                <div class="flex justify-start w-80">
                    <!-- <span :class="TEXT_SIZE_CLASS">{{ currentFileName }}</span> -->
                     <button
                        type="button"
                        @click="handleDownload({ id: editingId, file_name: currentFileName })"
                        class="text-left text-blue-600 hover:underline"
                        :class="TEXT_SIZE_CLASS"
                    >
                        {{ currentFileName }}
                    </button>
                </div>
            </UFormField>
            <div>
                <UFileUpload
                    v-model="selectedFile"
                    accept=".pdf,.doc,.docx"
                    position="inside"
                    layout="list"
                    :label="t('text.user-guide-management-pg.click-to-select-file') || 'Click to select a file or drag and drop it here'"
                    :description="t('text.user-guide-management-pg.file-requirements') || 'PDF or DOCX (max. 2MB)'"
                    class="w-full mt-4"
                    :ui="{
                        base: 'min-h-40 border-2 border-dashed rounded-lg'
                    }"
                />
                <p v-if="errors.file" :class="INPUT_FIELD_WARN_CLASS">{{ errors.file }}</p>
            </div>
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
