<script setup lang="ts">
import { ref, computed, shallowRef, watch, getCurrentInstance } from 'vue'
import { CalendarDate } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'
import { useFormatters } from '../../composables/useFormatters'
import { useApiStore } from '../../AppState'
import axios, { all } from 'axios'
import { TEXT_SIZE_CLASS, TITLE_MODAL_TEXT_CLASS, INPUT_FIELD_WARN_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

type SelectOption = {
  label: string
  value: string
}

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
    categoryOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    allCategoryOptions: {
        type: Array as () => Array<any>,
        default: () => []
    },
    siteOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const { getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

// ========================= STATE FOR MODAL =========================
const isSubmitting = ref(false)

const username = ref<string>('')
const name = ref<string>('')
const selectedProfile = ref<string | null>(null)
const selectedCategoryArr = ref<SelectOption[]>([])
const selectedSiteArr = ref<SelectOption[]>([])

const inputValidityDate = ref<any>(null)
const modelValueValidity = shallowRef<CalendarDate>()
const isPopoverFormValidityDateOpen = ref(false)

// Validation error states
const errors = ref({
    username: '',
    name: '',
    profile: '',
    category: '',
    site: '',
    validityDate: ''
})

// ========================= ACTION =========================
const resetForm = () => {
    username.value = ''
    name.value = ''
    selectedProfile.value = null
    selectedCategoryArr.value = []
    selectedSiteArr.value = []
    modelValueValidity.value = undefined
    
    // Reset errors
    errors.value = {
        username: '',
        name: '',
        profile: '',
        category: '',
        site: '',
        validityDate: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, (newVal) => {
    if (newVal) {
        if ((props.editMode || props.viewOnly) && props.initialData) {
            const initCategory = Array.isArray(props.initialData.category) ? props.initialData.category : []
            const initSite = Array.isArray(props.initialData.site) ? props.initialData.site : []

            username.value = props.initialData.username || ''
            name.value = props.initialData.name || ''
            selectedProfile.value = props.initialData.profile?.id || null
            selectedCategoryArr.value = props.categoryOptions.filter(option =>
                initCategory.some((item: any) => (item.id ?? item.value ?? item) === option.value)
            )
            selectedSiteArr.value = props.siteOptions.filter(option =>
                initSite.some((item: any) => (item.id ?? item.value ?? item) === option.value)
            )
            modelValueValidity.value = stringToCalendarDate(props.initialData.valid_date)
        } else {
            resetForm()
        }
    }
})

// Submit create/update profile
const postSubmitUser = async () => {
    // Reset errors
    errors.value = {
        username: '',
        name: '',
        profile: '',
        category: '',
        site: '',
        validityDate: ''
    }

    // Validation
    let hasError = false

    if (username.value.trim() === '') {
        errors.value.username = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (name.value.trim() === '') {
        errors.value.name = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!selectedProfile.value) {
        errors.value.profile = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (selectedCategoryArr.value.length === 0) {
        errors.value.category = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (selectedSiteArr.value.length === 0) {
        errors.value.site = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!getDateString(modelValueValidity.value)) {
        errors.value.validityDate = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {
        const categoryPayload = selectedCategoryArr.value.map(category => category.value)
        const sitePayload = selectedSiteArr.value.map(site => site.value)
        const validityDate = getDateString(modelValueValidity.value)

        const payload = {
            username: username.value,
            name: name.value,
            profile_id: selectedProfile.value,
            category: categoryPayload,
            site: sitePayload,
            valid_date: validityDate,
        }

        let response
        if (props.editMode && props.editingId) {
            response = await axios.put(`${api.postUserUpdate}${props.editingId}`, payload)
        } else {
            response = await axios.post(api.postUserCreate, payload)
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
            <!-- USERNAME -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.username"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.user-management-pg.input-new-username') || 'Username' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInput
                        v-model="username"
                        required
                        :placeholder="t('text.user-management-pg.input-new-username-placeholder') || 'Enter username'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.username
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`
                        }"
                        :disabled="props.editMode || props.viewOnly"
                    />
                    <p v-if="errors.username" :class="INPUT_FIELD_WARN_CLASS">{{ errors.username }}</p>
                </div>
            </UFormField>

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
                        {{ t('text.user-management-pg.input-new-name') || 'Name' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInput
                        v-model="name"
                        required
                        :placeholder="t('text.user-management-pg.input-new-name-placeholder') || 'Enter name'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.name
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`
                        }"
                        :disabled="props.viewOnly"
                    />
                    <p v-if="errors.name" :class="INPUT_FIELD_WARN_CLASS">{{ errors.name }}</p>
                </div>
            </UFormField>

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
                        {{ t('text.user-management-pg.input-new-profile') || 'Profile' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="selectedProfile"
                        :items="profileOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.user-management-pg.input-new-profile-placeholder') || 'Select profile'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.profile
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="props.viewOnly"
                    />
                    <p v-if="errors.profile" :class="INPUT_FIELD_WARN_CLASS">{{ errors.profile }}</p>
                </div>
            </UFormField>

            <!-- CATEGORY -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.category"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.user-management-pg.input-new-category') || 'Category' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="selectedCategoryArr"
                        :items="categoryOptions"
                        multiple
                        option-attribute="label"
                        :placeholder="t('text.user-management-pg.input-new-category-placeholder') || 'Select category'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.category
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="props.viewOnly"
                    />
                    <p v-if="errors.category" :class="INPUT_FIELD_WARN_CLASS">{{ errors.category }}</p>
                </div>
            </UFormField>

            <!-- SITE -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.site"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.user-management-pg.input-new-site') || 'Site' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="selectedSiteArr"
                        :items="siteOptions"
                        multiple
                        option-attribute="label"
                        :placeholder="t('text.user-management-pg.input-new-site-placeholder') || 'Select site'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.site
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="props.viewOnly"
                    />
                    <p v-if="errors.site" :class="INPUT_FIELD_WARN_CLASS">{{ errors.site }}</p>
                </div>
            </UFormField>

            <!-- VALIDITY DATE -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.validityDate"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.user-management-pg.input-new-validity-date') || 'Validity Date' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInputDate
                        ref="inputValidityDate"
                        v-model="modelValueValidity"
                        locale="en-GB"
                        format="dd/mm/yyyy"
                        class="w-full font-light"
                        :class="TEXT_SIZE_CLASS"
                        :ui="{
                            base: errors.validityDate
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                        :disabled="props.viewOnly"
                    >
                        <template #trailing>
                            <UPopover v-model:open="isPopoverFormValidityDateOpen">
                                <UButton
                                    color="neutral"
                                    variant="link"
                                    size="sm"
                                    icon="i-lucide-calendar"
                                    aria-label="Select a date"
                                    class="px-0"
                                    :disabled="props.viewOnly"
                                />

                                <template #content>
                                    <UCalendar
                                        v-model="modelValueValidity"
                                        class="p-2"
                                        @update:model-value="isPopoverFormValidityDateOpen = false"
                                    />
                                </template>
                            </UPopover>
                        </template>
                    </UInputDate>
                    <p v-if="errors.validityDate" :class="INPUT_FIELD_WARN_CLASS">{{ errors.validityDate }}</p>
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
                @click="postSubmitUser"
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
