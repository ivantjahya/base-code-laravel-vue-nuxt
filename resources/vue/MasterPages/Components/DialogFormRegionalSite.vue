<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue'
import { useI18n } from '../../composables/useI18n'

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    viewMode: {
        type: Boolean,
        default: false
    },
    viewingId: {
        type: String,
        default: null
    },
    initialData: {
        type: Object,
        default: () => ({})
    }
})

const emit = defineEmits(['update:open', 'close'])

const { t } = useI18n()

const siteCode     = ref('')
const siteInitial  = ref('')
const siteName     = ref('')
const siteAddress  = ref('')
const siteCity     = ref('')
const siteRegion   = ref('')

const regionalCode    = ref('')
const regionalInitial = ref('')
const regionalName    = ref('')
const regionalAddress = ref('')
const regionalCity    = ref('')
const regionalRegion  = ref('')

const statusValue = ref(true)

const siteAddressInputRef = ref<HTMLElement | null>(null)
const isSiteAddressOverflowing = ref(false)

const checkSiteAddressOverflow = () => {
    nextTick(() => {
        const input = siteAddressInputRef.value?.querySelector('input')
        if (input) {
            isSiteAddressOverflowing.value = input.scrollWidth > input.clientWidth
        }
    })
}

const populateForm = () => {
    const d = props.initialData || {}
    siteCode.value     = d.code     ?? ''
    siteInitial.value  = d.initial  ?? ''
    siteName.value     = d.name     ?? ''
    siteAddress.value  = d.address  ?? ''
    siteCity.value     = d.city     ?? ''
    siteRegion.value   = d.region   ?? ''

    regionalCode.value    = d.regional_code_kontrabon    ?? ''
    regionalInitial.value = d.regional_init_kontrabon    ?? ''
    regionalName.value    = d.regional_name_kontrabon    ?? ''
    regionalAddress.value = d.regional_address_kontrabon ?? ''
    regionalCity.value    = d.regional_city_kontrabon    ?? ''
    regionalRegion.value  = d.regional_region_kontrabon  ?? ''

    statusValue.value = Boolean(d.status ?? true)
}

const resetForm = () => {
    siteCode.value = siteInitial.value = siteName.value = ''
    siteAddress.value = siteCity.value = siteRegion.value = ''
    regionalCode.value = regionalInitial.value = regionalName.value = ''
    regionalAddress.value = regionalCity.value = regionalRegion.value = ''
    statusValue.value = true
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, (opened) => {
    if (opened) {
        props.initialData && Object.keys(props.initialData).length
            ? populateForm()
            : resetForm()
    }
})

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
        class="text-[16px] font-semibold"
        :ui="{
            content: 'max-w-5xl',
            footer: 'justify-end'
        }"
    >
        <template #body>
            <div class="grid grid-flow-row text-sm">
                <div class="flex flex-col md:flex-row w-full my-1 gap-2">
                    <!-- SITE CODE -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-site-code') || 'Site Code' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="siteCode"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>

                    <div class="px-2"></div>

                    <!-- REGIONAL CODE -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-regional-code') || 'Regional Code' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="regionalCode"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row w-full my-1 gap-2">
                    <!-- SITE INITIAL -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-site-initial') || 'Site Initial' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="siteInitial"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>

                    <div class="px-2"></div>

                    <!-- REGIONAL INITIAL -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-regional-initial') || 'Regional Initial' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="regionalInitial"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row w-full my-1 gap-2">
                    <!-- SITE NAME -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-site-name') || 'Site Name' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="siteName"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>

                    <div class="px-2"></div>

                    <!-- REGIONAL NAME -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-regional-name') || 'Regional Name' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="regionalName"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row w-full my-1 gap-2">
                    <!-- SITE ADDRESS -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-site-address') || 'Site Address' }}
                        </div>
                        <div class="flex w-full text-sm" ref="siteAddressInputRef" @mouseenter="checkSiteAddressOverflow">
                            <UTooltip
                                :text="siteAddress"
                                :disabled="!isSiteAddressOverflowing"
                                :ui="{ 
                                    content: 'bg-gray-900 text-white ring-1 ring-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:ring-gray-300'
                                }"
                                :content="{
                                    align: 'start',
                                    side: 'bottom',
                                    sideOffset: 10
                                }"
                            >
                                <UInput 
                                    v-model="siteAddress" 
                                    size="md"
                                    :disabled="viewMode"
                                    class="w-full font-light text-base md:text-sm"
                                />
                            </UTooltip>
                        </div>
                    </div>

                    <div class="px-2"></div>

                    <!-- REGIONAL ADDRESS -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-regional-address') || 'Regional Address' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="regionalAddress"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row w-full my-1 gap-2">
                    <!-- SITE CITY -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-site-city') || 'Site City' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="siteCity"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>

                    <div class="px-2"></div>

                    <!-- REGIONAL CITY -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-regional-city') || 'Regional City' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="regionalCity"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row w-full my-1 gap-2">
                    <!-- SITE REGION -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-site-region') || 'Site Region' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="siteRegion"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>

                    <div class="px-2"></div>

                    <!-- REGIONAL REGION -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-regional-region') || 'Regional Region' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <UInput
                                v-model="regionalRegion"
                                size="md"
                                :disabled="viewMode"
                                class="w-full font-light text-base md:text-sm"
                            />
                        </div>
                    </div>
                </div>

                <div class="flex flex-col md:flex-row w-full my-1 gap-2">
                    <!-- STATUS -->
                    <div class="flex w-full">
                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                            {{ t('text.table-column.column-status') || 'Status' }}
                        </div>
                        <div class="flex w-full text-sm">
                            <USwitch v-model="statusValue" :disabled="viewMode" />
                        </div>
                    </div>

                    <div class="px-2"></div>

                    <div class="flex w-full"></div>
                </div>
            </div>

        </template>

        <template #footer>
            <UButton
                class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[14px] px-5"
                :loading="isSubmitting"
                :disabled="isSubmitting"
                @click="closeModal"
            >
                {{ t('text.button.close') || 'Close' }}
            </UButton>
        </template>
    </UModal>
</template>
