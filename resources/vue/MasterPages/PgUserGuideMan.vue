<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef, watch } from 'vue'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel } from '@tanstack/table-core'
import { CalendarDate, DateFormatter, getLocalTimeZone, today } from '@internationalized/date'
import { useI18n } from '../composables/useI18n'
import CmpLayout from '../Components/CmpLayout.vue'
import type { SelectMenuItem } from '@nuxt/ui'

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

const menuFilter = ref<string | null>(null)
const menuValueFilter = ref<SelectMenuItem[]>([
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

const statusValueFilter = ref(['Active', 'Not Active'])
const statusFilter = ref<string | null>(null)

// Actions
const postFindData = () => {
    console.log('Finding data with filters:', {
        name: nameFilter.value,
        menu: menuFilter.value,
        status: statusFilter.value
    })
}

// ========================= MODAL =========================
const name = ref<string>('')

const menu = ref<string | null>(null)
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

const files = ref<File[]>([])
// const MAX_SIZE = 2 * 1024 * 1024 // 2MB
// const toastchild = ref<InstanceType<typeof CmpToast> | null>(null);

// watch(files, (newFiles) => {
//   const validFiles = newFiles.filter(file => file.size <= MAX_SIZE)

//   if (validFiles.length !== newFiles.length) {
//     toastchild.value?.toastDisplay({
//         type: 'error',
//         summary: 'File terlalu besar',
//         detail: 'Ukuran file maksimal 2MB',
//     });
//   }

//   files.value = validFiles
// })

const valueSwitch = ref(true)

const resetForm = () => {
    name.value = ''
    menu.value = null
    valueSwitch.value = true
    files.value = []
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

                                <!-- NAME -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.user-guide-management-pg.input-new-name') || 'Name' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <UInput v-model="name" :placeholder="t('text.user-guide-management-pg.input-new-name-placeholder') || 'Enter user guide name'" class="w-80 border-[#CAD5E2] font-light"/>

                                </UFormField>

                                <!-- MENU -->
                                <UFormField orientation="horizontal" class="mb-2" >

                                    <template #label>
                                        <span class="flex items-center gap-1">
                                            {{ t('text.user-guide-management-pg.input-new-menu') || 'Menu' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <USelectMenu v-model="menu" :items="menuValue" :placeholder="t('text.user-guide-management-pg.input-new-menu-placeholder') || 'Select menu'" class="w-80 font-light"/>

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

                                <UFileUpload
                                    v-model="files"
                                    position="inside"
                                    layout="list"
                                    multiple
                                    label="Drop your file here"
                                    description="PDF or DOCX (max. 2MB)"
                                    class="w-full"
                                    accept=".pdf,.docx"
                                    :ui="{ base: 'min-h-48' }"
                                />

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

                            {{ t('text.user-guide-management-pg.list') || 'List of User Guides' }}

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

                                        <!-- NAME -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.user-guide-management-pg.input-filter-name') || 'User Guide Name' }}</div>
                                            <div class="flex w-full text-sm">
                                                <UInput
                                                    v-model="nameFilter"
                                                    :placeholder="t('text.user-guide-management-pg.placeholder-filter-name') || 'Enter user guide name'"
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

                                        <!-- MENU -->
                                        <div class="flex w-full">

                                            <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.user-guide-management-pg.input-filter-menu') || 'Menu' }}</div>
                                            <div class="flex w-full text-sm">

                                                <USelectMenu v-model="menuFilter" :items="menuValueFilter" :placeholder="t('text.user-guide-management-pg.placeholder-filter-menu') || 'Select menu'" class="w-full font-reguler"/>

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
