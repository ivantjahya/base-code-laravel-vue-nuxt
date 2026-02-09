<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef } from 'vue'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel } from '@tanstack/table-core'
import { CalendarDate, DateFormatter, getLocalTimeZone, today } from '@internationalized/date'
import { useI18n } from '../composables/useI18n'
import CmpLayout from '../Components/CmpLayout.vue'

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
const usernameFilter = ref('')
const nameFilter = ref('')
const profileValueFilter = ref(['SUPERADMIN', 'ADMIN', 'MD FASHION', 'MD FRESH'])
const profileFilter = ref<string | null>(null)
const categoryValueFilter = ref(['A1 - MISSY', 'A2 - YOUNG', 'A3 - INTIMATE', 'A4 - BRANDED OUTRIGHT NORMAL'])
const categoryFilter = ref<string | null>(null)
const statusValueFilter = ref(['Active', 'Not Active'])
const statusFilter = ref<string | null>(null)

// Actions
const postFindData = () => {
    console.log('Finding data with filters:', {
        username: usernameFilter.value,
        name: nameFilter.value,
        profile: profileFilter.value,
        category: categoryFilter.value,
        status: statusFilter.value
    })
}

// ========================= MODAL =========================
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
const modelValueValidity = shallowRef<CalendarDate | null>(null)

const valueSwitch = ref(true)

const resetForm = () => {
    username.value = ''
    name.value = ''
    description.value = ''
    profile.value = null
    category.value = null
    site.value = null
    inputValidityDate.value = null
    modelValueValidity.value = null
    valueSwitch.value = true
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

                        <UModal v-model:open="open" :title="t('text.user-management-pg.add-new-user') || 'Create New User'" class="text-[16px] font-semibold" :ui="{ footer: 'justify-end' }">

                            <!-- BUTTON NEW -->
                            <UButton type="button" class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[16px] px-5">
                                {{ t('text.button.new').toUpperCase() || 'NEW' }}
                            </UButton>

                            <template #body>

                                <!-- USERNAME -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.user-management-pg.input-new-username') || 'Username' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInput v-model="username" :placeholder="t('text.user-management-pg.input-new-username-placeholder') || 'Enter username'" class="w-80 border-[#CAD5E2] font-light"/>

                                </UFormField>

                                <!-- NAME -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.user-management-pg.input-new-name') || 'Name' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInput v-model="name" :placeholder="t('text.user-management-pg.input-new-name-placeholder') || 'Enter name'" class="w-80 border-[#CAD5E2] font-light"/>

                                </UFormField>

                                <!-- DESCRIPTION -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.user-management-pg.input-new-description') || 'Description' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UTextarea v-model="description" :maxrows="5" autoresize class="w-80 font-light" />

                                </UFormField>

                                <!-- PROFILE -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.user-management-pg.input-new-profile') || 'Profile' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <USelectMenu v-model="profile" :items="profileValue" :placeholder="t('text.user-management-pg.input-new-profile-placeholder') || 'Select profile'" class="w-80 font-light"/>

                                </UFormField>

                                <!-- CATEGORY -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.user-management-pg.input-new-category') || 'Category' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <USelectMenu multiple v-model="category" :items="categoryValue" :placeholder="t('text.user-management-pg.input-new-category-placeholder') || 'Select category'" class="w-80 font-light"/>

                                </UFormField>

                                <!-- SITE -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.user-management-pg.input-new-site') || 'Site' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <USelectMenu v-model="site" :items="siteValue" :placeholder="t('text.user-management-pg.input-new-site-placeholder') || 'Select site'" class="w-80 font-light"/>

                                </UFormField>

                                <!-- VALIDITY DATE -->
                                <UFormField orientation="horizontal" class="mb-2" >
                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.user-management-pg.input-new-validity-date') || 'Validity Date' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInputDate
                                        ref="inputValidityDate"
                                        v-model="modelValueValidity"
                                        locale="id-ID"
                                        format="dd/mm/yyyy"
                                        class="w-80 border-[#CAD5E2] font-reguler focus:border-[#F26524]">

                                        <template #trailing>
                                            <UPopover>
                                                <UButton
                                                    color="neutral"
                                                    variant="link"
                                                    size="sm"
                                                    icon="i-lucide-calendar"
                                                    aria-label="Select a date"
                                                    class="px-0"
                                                />

                                                <template #content>
                                                    <UCalendar v-model="modelValueValidity" class="p-2" />
                                                </template>
                                            </UPopover>
                                        </template>
                                    </UInputDate>
                                </UFormField>

                                <!-- ACCESS RIGHT -->
                                <!-- <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.limit-management-pg.input-new-end-date') || 'End Date' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInputDate
                                        ref="inputEndDate"
                                        v-model="modelValueEnd"
                                        locale="id-ID"
                                        format="dd/mm/yyyy"
                                        :min-value="modelValueStart"
                                        class="w-80 border-[#CAD5E2] font-reguler focus:border-[#F26524]">

                                        <template #trailing>
                                            <UPopover>
                                                <UButton
                                                    color="neutral"
                                                    variant="link"
                                                    size="sm"
                                                    icon="i-lucide-calendar"
                                                    aria-label="Select a date"
                                                    class="px-0"
                                                />

                                                <template #content>
                                                    <UCalendar v-model="modelValueEnd" :min-value="modelValueStart" class="p-2" />
                                                </template>
                                            </UPopover>
                                        </template>

                                    </UInputDate>

                                </UFormField> -->

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

                            {{ t('text.user-management-pg.list') || 'List of Users' }}

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

                                        <!-- USERNAME -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.user-management-pg.input-filter-username') || 'Username' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInput
                                                    v-model="usernameFilter"
                                                    :placeholder="t('text.user-management-pg.placeholder-filter-username') || 'Enter username'"
                                                    size="md"
                                                    class="w-full font-light text-base md:text-sm"
                                                />
                                            </div>

                                        </div>

                                        <div class="px-2"></div>

                                        <!-- CATEGORY -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.user-management-pg.input-filter-category') || 'Category' }}</div>
                                            <div class="flex w-full text-sm">

                                                <USelectMenu v-model="categoryFilter" :items="categoryValueFilter" :placeholder="t('text.user-management-pg.placeholder-filter-category') || 'Select category'" class="w-full font-reguler"/>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">

                                        <!-- NAME -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.user-management-pg.input-filter-name') || 'Name' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInput
                                                    v-model="nameFilter"
                                                    :placeholder="t('text.user-management-pg.placeholder-filter-name') || 'Enter name'"
                                                    size="md"
                                                    class="w-full font-light text-base md:text-sm"
                                                />
                                            </div>

                                        </div>

                                        <div class="px-2"></div>

                                        <!-- STATUS -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.user-management-pg.input-filter-status') || 'Status' }}</div>
                                            <div class="flex w-full text-sm">

                                                <USelectMenu v-model="statusFilter" :items="statusValueFilter" :placeholder="t('text.user-management-pg.placeholder-filter-status') || 'Select status'" class="w-full font-reguler"/>

                                            </div>

                                        </div>

                                    </div>

                                    <div class="flex flex-col md:flex-row w-full my-1 gap-2">

                                        <!-- PROFILE -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.user-management-pg.input-filter-profile') || 'Profile' }}</div>
                                            <div class="flex w-full text-sm">

                                                <USelectMenu v-model="profileFilter" :items="profileValueFilter" :placeholder="t('text.user-management-pg.placeholder-filter-profile') || 'Select profile'" class="w-full font-reguler"/>

                                            </div>

                                        </div>

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
