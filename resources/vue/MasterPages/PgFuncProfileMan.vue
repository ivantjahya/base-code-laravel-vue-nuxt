<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef } from 'vue'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel } from '@tanstack/table-core'
import { CalendarDate, DateFormatter, getLocalTimeZone, today } from '@internationalized/date'
import { useI18n } from '../composables/useI18n'
import CmpLayout from '../Components/CmpLayout.vue'
import type { TreeItemSelectEvent } from 'reka-ui'
import type { TreeItem } from '@nuxt/ui'

const { t } = useI18n()

const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')

// State
const moreFilterItems = computed(() => [
  {
    label: t('text.input-field.more-filters'),
    slot: 'panelMoreFilter',
    icon: 'i-lucide-filter',
    defaultOpen: false
  }
])

// ========================= FILTER =========================
const functionalProfileCodeFilter = ref('')
const functionalProfileNameFilter = ref('')
const profileValueFilter = ref(['SUPERADMIN', 'ADMIN', 'MD FASHION', 'MD FRESH'])
const profileFilter = ref<string | null>(null)
const divisionValueFilter = ref(['FASHION', 'NON FOOD', 'FRESH'])
const divisionFilter = ref<string | null>(null)
const limitValueFilter = ref(['L001', 'L002', 'L003'])
const limitFilter = ref<string | null>(null)
const statusValueFilter = ref(['Active', 'Not Active'])
const statusFilter = ref<string | null>(null)

// Convert CalendarDate to string format for API
const getDateString = (calendarDate: CalendarDate | undefined) => {
    if (!calendarDate) return null
    const date = calendarDate.toDate(getLocalTimeZone())
    const year = date.getFullYear()
    const month = String(date.getMonth() + 1).padStart(2, '0')
    const day = String(date.getDate()).padStart(2, '0')
    return `${year}-${month}-${day}` // Returns YYYY-MM-DD format
}

// Actions
const postFindData = () => {
    console.log('Finding data with filters:', {
        functionalProfileCode: functionalProfileCodeFilter.value,
        functionalProfileName: functionalProfileNameFilter.value,
        profile: profileFilter.value,
        division: divisionFilter.value,
        limit: limitFilter.value,
        status: statusFilter.value
    })
}

// ========================= MODAL =========================
const functionalProfileName = ref<string>('')

const profileValue = ref(['SUPERADMIN', 'ADMIN', 'MD FASHION', 'MD FRESH'])
const profile = ref<string | null>(null)
const limitValue = ref(['L001', 'L002', 'L003'])
const limit = ref<string | null>(null)

const valueSwitch = ref(true)

const resetForm = () => {
    functionalProfileName.value = ''
    profile.value = null
    limit.value = null
    valueSwitch.value = true
}

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

</script>

<template>
    <CmpLayout :title="t('menu.master-data') || 'Master Data'">

        <!-- Content -->
        <div class="flex-1 overflow-auto p-3">

            <!-- Title Section -->
            <UCard class="mb-3">

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-4">

                        <UModal v-model:open="open" :title="t('text.functional-profile-management-pg.add-new-functional-profile') || 'Create New Functional Profile'" class="text-[16px] font-semibold" :ui="{ content: 'sm:max-w-xl', footer: 'justify-end' }">

                            <!-- BUTTON NEW -->
                            <UButton type="button" class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[16px] px-5">
                                {{ t('text.button.new').toUpperCase() || 'NEW' }}
                            </UButton>

                            <template #body>

                                <!-- FUNCTIONAL PROFILE NAME -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.functional-profile-management-pg.input-new-functional-profile-name') || 'Functional Profile Name' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInput v-model="functionalProfileName" :placeholder="t('text.functional-profile-management-pg.input-new-functional-profile-name-placeholder') || 'Enter functional profile name'" class="w-80 border-[#CAD5E2] font-light"/>

                                </UFormField>

                                <!-- PROFILE -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.functional-profile-management-pg.input-new-profile') || 'Profile' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <USelectMenu v-model="profile" :items="profileValue" :placeholder="t('text.functional-profile-management-pg.input-new-profile-placeholder') || 'Select profile'" class="w-80 font-light"/>

                                </UFormField>

                                <!-- LIMIT -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.functional-profile-management-pg.input-new-limit') || 'Limit' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <USelectMenu v-model="limit" :items="limitValue" :placeholder="t('text.functional-profile-management-pg.input-new-limit-placeholder') || 'Select limit'" class="w-80 font-light"/>

                                </UFormField>

                                <!-- ACCESS RIGHT -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.functional-profile-management-pg.input-new-division') || 'Division' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <div class="w-80 max-h-70 overflow-y-auto border border-gray-300 rounded-md p-2 dark:border-gray-700">
                                        <UTree
                                            v-model="selectDivision"
                                            :as="{ link: 'div' }"
                                            :items="items"
                                            @select="onSelect"
                                            >
                                            <template #item-leading="{ selected, handleSelect }">
                                                <URadioGroup :model-value="selected"
                                                    @update:model-value="handleSelect"
                                                    @click.stop />
                                            </template>
                                        </UTree>

                                    </div>

                                </UFormField>

                                <!-- STATUS -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.limit-management-pg.input-new-status') || 'Status' }}
                                        </span>
                                    </template>

                                    <div class="flex justify-start w-80">
                                        <USwitch v-model="valueSwitch" />
                                    </div>

                                </UFormField>

                            </template>

                            <template #footer>

                                <UButton
                                    class="bg-[#FEE9D6] text-[#F26524] hover:bg-[#FBD0AD] hover:text-[#E34613] active:bg-[#FBD0AD] active:text-[#E34613] text-[14px] px-5"
                                    @click="resetForm"
                                >{{ t('text.button.clear') || 'Clear' }}</UButton>

                                <UButton label="Submit" class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[14px] px-5">
                                    {{ t('text.button.submit') || 'Submit' }}
                                </UButton>

                            </template>

                        </UModal>

                        <h1 class="text-lg font-semibold text-gray-900 dark:text-white">

                            {{ t('text.functional-profile-management-pg.list') || 'List of Functional Profiles' }}

                        </h1>

                    </div>

                </div>

            </UCard>

            <UCard>

                <!-- Filters Section with Accordion -->
                <div class="mb-6">

                    <UAccordion
                        :items="moreFilterItems"
                        class="w-full border-1 border-gray-200 dark:border-gray-700 rounded-lg px-4"
                        :ui="{
                        wrapper: 'w-full',
                        item: {
                            base: 'border-1 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden',
                        }
                        }"
                    >

                        <template #panelMoreFilter>

                            <div class="py-2 space-y-4 bg-white dark:bg-gray-900">
                                <div class="grid grid-flow-row text-sm">

                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">

                                        <!-- FUNCTIONAL PROFILE CODE -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.functional-profile-management-pg.input-filter-functional-profile-code') || 'Func. Profile Code' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInput
                                                    v-model="functionalProfileCodeFilter"
                                                    :placeholder="t('text.functional-profile-management-pg.placeholder-filter-functional-profile-code') || 'Enter functional profile code'"
                                                    size="md"
                                                    class="w-full font-light text-base md:text-sm"
                                                />
                                            </div>

                                        </div>

                                        <div class="px-2"></div>

                                        <!-- DIVISION -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.functional-profile-management-pg.input-filter-division') || 'Division' }}</div>
                                            <div class="flex w-full text-sm">
                                                <USelectMenu v-model="divisionFilter" :items="divisionValueFilter" :placeholder="t('text.functional-profile-management-pg.placeholder-filter-division') || 'Select division'" class="w-full font-reguler"/>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">

                                        <!-- FUNCTIONAL PROFILE NAME -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.functional-profile-management-pg.input-filter-functional-profile-name') || 'Func. Profile Name' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInput
                                                    v-model="functionalProfileNameFilter"
                                                    :placeholder="t('text.functional-profile-management-pg.placeholder-filter-functional-profile-name') || 'Enter functional profile name'"
                                                    size="md"
                                                    class="w-full font-light text-base md:text-sm"
                                                />
                                            </div>

                                        </div>

                                        <div class="px-2"></div>

                                        <!-- LIMIT -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.functional-profile-management-pg.input-filter-limit') || 'Limit' }}</div>
                                            <div class="flex w-full text-sm">

                                                <USelectMenu v-model="limitFilter" :items="limitValueFilter" :placeholder="t('text.functional-profile-management-pg.placeholder-filter-limit') || 'Select limit'" class="w-full font-reguler"/>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">

                                        <!-- PROFILE -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.functional-profile-management-pg.input-filter-profile') || 'Profile' }}</div>
                                            <div class="flex w-full text-sm">

                                                <USelectMenu v-model="profileFilter" :items="profileValueFilter" :placeholder="t('text.functional-profile-management-pg.placeholder-filter-profile') || 'Select profile'" class="w-full font-reguler"/>

                                            </div>

                                        </div>

                                        <div class="px-2"></div>

                                        <!-- STATUS -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.profile-management-pg.input-filter-status') || 'Status' }}</div>
                                            <div class="flex w-full text-sm">

                                                <USelectMenu v-model="statusFilter" :items="statusValueFilter" :placeholder="t('text.profile-management-pg.placeholder-filter-status') || 'Select status'" class="w-full font-reguler"/>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">

                                        <div class="flex w-full"></div>

                                        <div class="px-2"></div>

                                        <!-- BUTTON FIND -->
                                        <div class="flex w-full mb-1">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm"></div>
                                            <div class="flex w-full text-sm">
                                                <UButton
                                                    @click="postFindData"
                                                    color="primary"
                                                    size="md"
                                                    icon="i-lucide-search"
                                                    class="w-full justify-center text-base md:text-sm text-white"
                                                >
                                                    {{ t('text.button.find') || 'Find' }}
                                                </UButton>
                                            </div>

                                        </div>
                                    </div>

                                </div>
                            </div>

                        </template>

                    </UAccordion>

                </div>

            </UCard>

        </div>

    </CmpLayout>
</template>
