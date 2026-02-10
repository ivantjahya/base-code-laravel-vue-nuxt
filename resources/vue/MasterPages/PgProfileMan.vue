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
const profileCodeFilter = ref('')
const profileNameFilter = ref('')
const profileSourceValueFilter = ref(['Internal', 'External'])
const profileSourceFilter = ref<string | null>(null)
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
        profileCode: profileCodeFilter.value,
        profileName: profileNameFilter.value,
        profileSource: profileSourceFilter.value,
        status: statusFilter.value
    })
}

// ========================= MODAL =========================
const profileName = ref<string>('')
const description = ref<string>('')

const profileSourceValue = ref(['Internal', 'External'])
const profileSource = ref<string | null>(null)

const valueSwitch = ref(true)

const resetForm = () => {
    profileName.value = ''
    description.value = ''
    profileSource.value = null
    valueSwitch.value = true
    treeValue.value = []
}

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

const treeValue = ref<string[]>([])

function onSelect(e: TreeItemSelectEvent<TreeItem>) {
  if (e.detail.originalEvent.type === 'click') {
    e.preventDefault()
  }
}

</script>

<template>
    <CmpLayout :title="t('menu.master-data') || 'Master Data'">

        <!-- Content -->
        <div class="flex-1 overflow-auto p-3">

            <!-- Title Section -->
            <UCard class="mb-3">

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-4">

                        <UModal v-model:open="open" :title="t('text.profile-management-pg.add-new-profile') || 'Create New Profile'" class="text-[16px] font-semibold" :ui="{ footer: 'justify-end' }">

                            <!-- BUTTON NEW -->
                            <UButton type="button" class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[16px] px-5">
                                {{ t('text.button.new').toUpperCase() || 'NEW' }}
                            </UButton>

                            <template #body>

                                <!-- PROFILE NAME -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.profile-management-pg.input-new-profile-name') || 'Profile Name' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInput v-model="profileName" :placeholder="t('text.profile-management-pg.input-new-profile-name-placeholder') || 'Enter profile name'" class="w-80 border-[#CAD5E2] font-light"/>

                                </UFormField>

                                <!-- DESCRIPTION -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.profile-management-pg.input-new-description') || 'Description' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UTextarea v-model="description" :maxrows="5" autoresize class="w-80 font-light" />

                                </UFormField>

                                <!-- PROFILE SOURCE -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.profile-management-pg.input-new-profile-source') || 'Profile Source' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <USelectMenu v-model="profileSource" :items="profileSourceValue" placeholder="Select profile source" class="w-80 font-light"/>

                                </UFormField>

                                <!-- ACCESS RIGHT -->
                                <UFormField orientation="horizontal" class="mb-2" >

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

                            {{ t('text.profile-management-pg.list') || 'List of Profiles' }}

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

                                        <!-- PROFILE CODE -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.profile-management-pg.input-filter-profile-code') || 'Profile Code' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInput
                                                    v-model="profileCodeFilter"
                                                    :placeholder="t('text.profile-management-pg.placeholder-filter-profile-code') || 'Enter profile code'"
                                                    size="md"
                                                    class="w-full font-light text-base md:text-sm"
                                                />
                                            </div>

                                        </div>

                                        <div class="px-2"></div>

                                        <!-- PROFILE SOURCE -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.profile-management-pg.input-filter-profile-source') || 'Profile Source' }}</div>
                                            <div class="flex w-full text-sm">
                                                <USelectMenu v-model="profileSourceFilter" :items="profileSourceValueFilter" :placeholder="t('text.profile-management-pg.placeholder-filter-profile-source') || 'Select profile source'" class="w-full font-reguler"/>
                                            </div>

                                        </div>

                                    </div>

                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">

                                        <!-- PROFILE NAME -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.profile-management-pg.input-filter-profile-name') || 'Profile Name' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInput
                                                    v-model="profileNameFilter"
                                                    :placeholder="t('text.profile-management-pg.placeholder-filter-profile-name') || 'Enter profile name'"
                                                    size="md"
                                                    class="w-full font-light text-base md:text-sm"
                                                />
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
