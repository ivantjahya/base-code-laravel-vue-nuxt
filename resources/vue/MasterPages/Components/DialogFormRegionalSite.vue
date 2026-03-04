<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue'
import { useI18n } from '../../composables/useI18n'
import { useFormatters } from '../../composables/useFormatters'

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
const { formatDate } = useFormatters()

// ========================= STATE FOR MODAL =========================
const isSubmitting = ref(false)

// State for site
const site = ref('')
const siteName = ref('')
const siteAddress = ref('')
const siteCity = ref('')
const siteRegion = ref('')
const imAutoFlag = ref(false)
const dcFlag = ref(false)
const startDate = ref('')
const endDate = ref('')
const statusValue = ref(true)

// State for company
const companyCode = ref('')
const companyInitial = ref('')
const companyName = ref('')
const companyAddress = ref('')
const companyCity = ref('')
const companyRegion = ref('')

// State for regional kontrabon
const regionalCodeKontrabon = ref('')
const regionalInitialKontrabon = ref('')
const regionalNameKontrabon = ref('')
const regionalAddressKontrabon = ref('')
const regionalCityKontrabon = ref('')
const regionalRegionKontrabon = ref('')

// State for EBS
const codeEbs = ref('')
const initialEbs = ref('')
const nameEbs = ref('')

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

// ========================= ACTION =========================
const populateForm = () => {
    const d = props.initialData || {}

    site.value = `${d.site ?? ''} - ${d.initial ?? ''}`
    siteName.value = d.name ?? ''
    siteAddress.value = d.address ?? ''
    siteCity.value = d.city ?? ''
    siteRegion.value = d.region ?? ''
    imAutoFlag.value = Boolean(d.im_auto_flag ?? false)
    dcFlag.value = Boolean(d.dc_flag ?? false)
    startDate.value = formatDate(d.start_date ?? '')
    endDate.value = formatDate(d.end_date ?? '')
    statusValue.value = Boolean(d.status ?? true)

    companyCode.value = d.company_code ?? ''
    companyInitial.value = d.company_initial ?? ''
    companyName.value = d.company_name ?? ''
    companyAddress.value = d.company_address ?? ''
    companyCity.value = d.company_city ?? ''
    companyRegion.value = d.company_region ?? ''

    regionalCodeKontrabon.value = d.regional_code_kontrabon ?? ''
    regionalInitialKontrabon.value = d.regional_init_kontrabon ?? ''
    regionalNameKontrabon.value = d.regional_name_kontrabon ?? ''
    regionalAddressKontrabon.value = d.regional_address_kontrabon ?? ''
    regionalCityKontrabon.value = d.regional_city_kontrabon ?? ''
    regionalRegionKontrabon.value = d.regional_region_kontrabon ?? ''

    codeEbs.value = d.code_ebs ?? ''
    initialEbs.value = d.initial_ebs ?? ''
    nameEbs.value = d.name_ebs ?? ''
}

const resetForm = () => {
    site.value = siteName.value = siteAddress.value = siteCity.value = siteRegion.value = ''
    imAutoFlag.value = dcFlag.value = statusValue.value = false
    startDate.value = endDate.value = ''
    companyCode.value = companyInitial.value = companyName.value = ''
    companyAddress.value = companyCity.value = companyRegion.value = ''
    regionalCodeKontrabon.value = regionalInitialKontrabon.value = regionalNameKontrabon.value = ''
    regionalAddressKontrabon.value = regionalCityKontrabon.value = regionalRegionKontrabon.value = ''
    codeEbs.value = initialEbs.value = nameEbs.value = ''
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

const tabItems = computed(() => [
    { label: t('text.regional-site-pg.tab-site' as any), key: 'site' },
    { label: t('text.regional-site-pg.tab-company' as any), key: 'company' },
    { label: t('text.regional-site-pg.tab-regional-kontrabon' as any), key: 'regional' },
    { label: t('text.regional-site-pg.tab-ebs' as any), key: 'ebs' }
])
</script>

<template>
    <UModal
        v-model:open="isOpen"
        :title="title"
        :dismissible="false"
        class="text-[16px] font-semibold"
        :ui="{
            content: `w-full mx-auto
                sm:max-w-md sm:text-xs
                md:max-w-2xl md:text-sm
                lg:max-w-5xl lg:text-sm
            `,
            body: 'sm:pt-2 sm:px-6',
            footer: 'justify-end'
        }"
    >
        <template #body>
            <div class="h-[300px] overflow-y-auto pr-1">
                <UTabs
                    :items="tabItems"
                    variant="link"
                    class="w-full"
                    :ui="{
                        list: {
                            tab: {
                                wrapper: 'flex',
                                base: 'justify-center',
                                active: 'border-b-2 border-primary-500 text-primary-500',
                                inactive: 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200'
                            },
                            indicator: 'hidden'
                        }
                    }"
                >
                    <template #content="{ item }">
                        <div class="mt-4">
                            <!-- SITE -->
                            <div v-if="item.key === 'site'" class="grid grid-flow-row text-sm md:text-xs gap-2">
                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- SITE -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-site') || 'Site' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="site"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>

                                    <div class="px-2"></div>

                                    <!-- SITE START DATE -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-start-date') || 'Site Start Date' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="startDate"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- SITE NAME -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-name') || 'Name' }}
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

                                    <!-- SITE END DATE -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-end-date') || 'Site End Date' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="endDate"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- SITE ADDRESS -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-address') || 'Address' }}
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

                                    <!-- SITE IM AUTO -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-site-im-auto-flag') || 'IM Auto Flag' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <USwitch v-model="imAutoFlag" :disabled="viewMode" />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- SITE CITY -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-city') || 'City' }}
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

                                    <!-- SITE DC FLAG -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-site-dc-flag') || 'DC Flag' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <USwitch v-model="dcFlag" :disabled="viewMode" />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- SITE REGION -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-region') || 'Region' }}
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

                                    <!-- SITE STATUS -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-status') || 'Status' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <USwitch v-model="statusValue" :disabled="viewMode" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- COMPANY -->
                            <div v-else-if="item.key === 'company'" class="grid grid-flow-row text-sm md:text-xs gap-2">
                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- COMPANY CODE -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-code') || 'Code' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="companyCode"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- COMPANY INITIAL -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-initial') || 'Initial' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="companyInitial"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- COMPANY NAME -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-name') || 'Name' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="companyName"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- COMPANY ADDRESS -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-address') || 'Address' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="companyAddress"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- COMPANY CITY -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-city') || 'City' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="companyCity"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- COMPANY REGION -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-region') || 'Region' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="companyRegion"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- REGIONAL -->
                            <div v-else-if="item.key === 'regional'" class="grid grid-flow-row text-sm md:text-xs gap-2">
                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- REGIONAL CODE KONTRABON -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-code') || 'Code' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="regionalCodeKontrabon"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- REGIONAL INITIAL KONTRABON -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-initial') || 'Initial' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="regionalInitialKontrabon"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- REGIONAL NAME KONTRABON -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-name') || 'Name' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="regionalNameKontrabon"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- REGIONAL ADDRESS KONTRABON -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-address') || 'Address' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="regionalAddressKontrabon"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- REGIONAL CITY KONTRABON -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-city') || 'City' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="regionalCityKontrabon"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- REGIONAL REGION KONTRABON -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-region') || 'Region' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="regionalRegionKontrabon"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- EBS -->
                            <div v-else-if="item.key === 'ebs'" class="grid grid-flow-row text-sm gap-2">
                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- EBS CODE -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-code') || 'Code' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="codeEbs"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- EBS INITIAL -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-initial') || 'Initial' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="initialEbs"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div class="flex flex-col md:flex-row w-full gap-2">
                                    <!-- EBS NAME -->
                                    <div class="flex w-full">
                                        <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">
                                            {{ t('text.table-column.column-name') || 'Name' }}
                                        </div>
                                        <div class="flex w-full text-sm">
                                            <UInput
                                                v-model="nameEbs"
                                                size="md"
                                                :disabled="viewMode"
                                                class="w-full font-light text-base md:text-sm"
                                            />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                </UTabs>
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
