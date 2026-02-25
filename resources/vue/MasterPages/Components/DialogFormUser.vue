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
    },
    profileOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    siteOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const { formatDate, formatCurrency, getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const isSubmitting = ref(false)

const username = ref<string>('')
const name = ref<string>('')
const description = ref<string>('')

const profileValue = ref(['SUPERADMIN', 'ADMIN', 'MD FASHION', 'MD FRESH'])
const profile = ref<string | null>(null)
const categoryValue = ref(['A1 - MISSY', 'A2 - YOUNG', 'A3 - INTIMATE', 'A4 - BRANDED OUTRIGHT NORMAL'])
const category = ref<string | null>(null)
const siteValue = ref(['YJP - YOGYA JUNCTION PARAHYANGAN', 'S60 - YOGYA SUNDA 60', 'YJS - YOGYA JUNCTION SUMBERSARI', 'KPT - YOGYA KEPATIHAN'])
const site = ref<string | null>(null)

const inputValidityDate = ref<any>(null)
const modelValueValidity = shallowRef<CalendarDate>()
const isPopoverFormValidityDateOpen = ref(false)

const valueSwitch = ref(true)

// Validation error states
const errors = ref({
    username: '',
    name: '',
    description: '',
    profile: '',
    category: '',
    site: '',
    validityDate: ''
})

const resetForm = () => {
    username.value = ''
    name.value = ''
    description.value = ''
    profile.value = null
    category.value = null
    site.value = null
    modelValueValidity.value = undefined
    valueSwitch.value = true
    // Reset errors
    errors.value = {
        username: '',
        name: '',
        description: '',
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
        if (props.editMode && props.initialData) {
            username.value = props.initialData.username || ''
            name.value = props.initialData.name || ''
            description.value = props.initialData.description || ''
            profile.value = props.initialData.profile || null
            category.value = props.initialData.category || null
            site.value = props.initialData.site || null
            modelValueValidity.value = stringToCalendarDate(props.initialData.validity_date)
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
        username: '',
        name: '',
        description: '',
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

    if (!profile.value) {
        errors.value.profile = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!category.value) {
        errors.value.category = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!site.value) {
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

        const validityDate = getDateString(modelValueValidity.value)

        const payload = {
            username: username.value,
            name: name.value,
            description: description.value,
            profile: profile.value,
            category: category.value,
            site: site.value,
            validity_date: validityDate,
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
                    <span class="flex items-center gap-1">
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
                            base: errors.username
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.username" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.username }}</p>
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
                    <span class="flex items-center gap-1">
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
                            base: errors.name
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.name" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.name }}</p>
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
                        {{ t('text.user-management-pg.input-new-description') || 'Description' }}
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
                        {{ t('text.user-management-pg.input-new-profile') || 'Profile' }}
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
                        :placeholder="t('text.user-management-pg.input-new-profile-placeholder') || 'Select profile'"
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
                    <span class="flex items-center gap-1">
                        {{ t('text.user-management-pg.input-new-category') || 'Category' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        multiple
                        v-model="category"
                        :items="categoryValue"
                        placeholder="Select category"
                        class="w-full font-light"
                        :ui="{
                            base: errors.category
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.category" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.category }}</p>
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
                    <span class="flex items-center gap-1">
                        {{ t('text.user-management-pg.input-new-site') || 'Site' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        multiple
                        v-model="site"
                        :items="siteOptions"
                        placeholder="Select site"
                        class="w-full font-light"
                        :ui="{
                            base: errors.site
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
                    />
                    <p v-if="errors.site" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.site }}</p>
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
                    <span class="flex items-center gap-1">
                        {{ t('text.user-management-pg.input-new-validity-date') || 'Validity Date' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInputDate
                        ref="inputValidityDate"
                        v-model="modelValueValidity"
                        locale="id-ID" format="dd/mm/yyyy"
                        class="w-full font-light"
                        :ui="{
                            base: errors.validityDate
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                        }"
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
                    <p v-if="errors.validityDate" class="text-[#FB2C36] text-xs italic mt-1">{{ errors.validityDate }}</p>
                </div>

            </UFormField>

            <!-- STATUS -->
            <UFormField orientation="horizontal" class="mb-2" >
                <template #label>
                    <span class="flex items-center gap-1">
                        {{ t('text.user-management-pg.input-new-status') || 'Status' }}
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
