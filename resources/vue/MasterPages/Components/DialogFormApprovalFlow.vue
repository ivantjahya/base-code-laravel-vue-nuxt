<script setup lang="ts">
import { ref, computed, watch, getCurrentInstance, nextTick } from 'vue'
import { useI18n } from '../../composables/useI18n'
import { useApiStore } from '../../AppState'
import axios from 'axios'
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
    },
    poStatusOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    poStatusLoading: {
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

const profile = ref<string | null>(null)
const division = ref<string | null>(null)
const poStatus = ref<string | null>(null)
const request_to = ref<string | null>(null)
const nextPoStatus = ref<string | null>(null)
const description = ref<string>('')

const valueSwitch = ref(true)

// Validation error states
const errors = ref({
    profile: '',
    division: '',
    poStatus: '',
    request_to: '',
    nextPoStatus: '',
    description: ''
})

// ========================= ACTION =========================
const resetForm = () => {
    profile.value = null
    division.value = null
    poStatus.value = null
    request_to.value = null
    nextPoStatus.value = null
    description.value = ''
    valueSwitch.value = true

    // Reset errors
    errors.value = {
        profile: '',
        division: '',
        poStatus: '',
        request_to: '',
        nextPoStatus: '',
        description: ''
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
            description.value = props.initialData.description || ''
            profile.value = props.initialData.profile?.id || null
            division.value = props.initialData.division?.id || null
            poStatus.value = props.initialData.poStatus?.id || null
            request_to.value = props.initialData.request_to?.id || null
            nextPoStatus.value = props.initialData.nextPoStatus?.id || null
            valueSwitch.value = props.initialData.status ?? true
        } else {
            resetForm()
        }
    }
})

// Submit create/update approval flow
const postSubmitApproalFlow = async () => {
    // Reset errors
    errors.value = {
        description: '',
        profile: '',
        division: '',
        poStatus: '',
        request_to: '',
        nextPoStatus: ''
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

    if (!poStatus.value) {
        errors.value.poStatus = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!request_to.value) {
        errors.value.request_to = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!nextPoStatus.value) {
        errors.value.nextPoStatus = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {
        const payload = {
            profile: profile.value,
            division: division.value,
            poStatus: poStatus.value,
            request_to: request_to.value,
            nextPoStatus: nextPoStatus.value,
            description: description.value,
            status: valueSwitch.value ? 1 : 0
        }
        // console.log('Payload to submit:', payload);

        // let response
        if (props.editMode && props.editingId) {
            await axios.put(`${api.postApprovalFlowUpdate}${props.editingId}`, payload)
        } else {
            await axios.post(api.postApprovalFlowCreate, payload)
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
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.approval-flow-management-pg.input-new-profile') || 'Profile' }}
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
                        :placeholder="t('text.approval-flow-management-pg.input-new-profile-placeholder') || 'Select profile'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.profile
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS,
                        }"
                        :disabled="viewOnly"
                    />
                    <p v-if="errors.profile" :class="INPUT_FIELD_WARN_CLASS">{{ errors.profile }}</p>
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
                        {{ t('text.approval-flow-management-pg.input-new-division') || 'Division' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="division"
                        :items="divisionOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.approval-flow-management-pg.input-new-division-placeholder') || 'Select division'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.division
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="viewOnly"
                    />
                    <p v-if="errors.division" :class="INPUT_FIELD_WARN_CLASS">{{ errors.division }}</p>
                </div>
            </UFormField>

            <!-- PO STATUS -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.poStatus"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.approval-flow-management-pg.input-new-po-status') || 'PO Status' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="poStatus"
                        :items="poStatusOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.approval-flow-management-pg.input-new-po-status-placeholder') || 'Select PO Status'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.poStatus
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="viewOnly"
                    />
                    <p v-if="errors.poStatus" :class="INPUT_FIELD_WARN_CLASS">{{ errors.poStatus }}</p>
                </div>
            </UFormField>

            <!-- REQUEST TO -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.request_to"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.approval-flow-management-pg.input-new-request-to') || 'Request To' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="request_to"
                        :items="profileOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.approval-flow-management-pg.input-new-request-to-placeholder') || 'Select request to'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.request_to
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="viewOnly"
                    />
                    <p v-if="errors.request_to" :class="INPUT_FIELD_WARN_CLASS">{{ errors.request_to }}</p>
                </div>
            </UFormField>

            <!-- NEXT PO STATUS -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.nextPoStatus"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.approval-flow-management-pg.input-new-next-po-status') || 'Next PO Status' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="nextPoStatus"
                        :items="poStatusOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.approval-flow-management-pg.input-new-next-po-status-placeholder') || 'Select Next PO Status'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.nextPoStatus
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="viewOnly"
                    />
                    <p v-if="errors.nextPoStatus" :class="INPUT_FIELD_WARN_CLASS">{{ errors.nextPoStatus }}</p>
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
                @click="postSubmitApproalFlow"
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
