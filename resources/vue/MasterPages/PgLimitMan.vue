<script setup lang="ts">
import { ref, computed, h, resolveComponent, shallowRef } from 'vue'
import type { TableColumn } from '@nuxt/ui'
import { getPaginationRowModel } from '@tanstack/table-core'
import { CalendarDate, DateFormatter, getLocalTimeZone } from '@internationalized/date'
import { useI18n } from '../composables/useI18n'
import CmpLayout from '../Components/CmpLayout.vue'

const { t } = useI18n()

const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')

// Sample data - replace with actual API call
// const limits = ref([
//   {
//     id: 1,
//     limitCode: 'L001',
//     minimum: 0,
//     maximum: 5000000,
//     startDate: '01-12-2025',
//     endDate: '31-12-2030'
//   },
//   {
//     id: 2,
//     limitCode: 'L002',
//     minimum: 5000000,
//     maximum: 10000000,
//     startDate: '15-01-2026',
//     endDate: '31-12-2030'
//   },
//   {
//     id: 3,
//     limitCode: 'L003',
//     minimum: 10000000,
//     maximum: 50000000,
//     startDate: '21-11-2025',
//     endDate: '31-12-2030'
//   },
//   {
//     id: 4,
//     limitCode: 'L004',
//     minimum: 0,
//     maximum: 10000000,
//     startDate: '01-09-2025',
//     endDate: '31-12-2030'
//   },
//   {
//     id: 5,
//     limitCode: 'L005',
//     minimum: 50000000,
//     maximum: 100000000,
//     startDate: '12-12-2024',
//     endDate: '31-12-2030'
//   }
// ])

// State
const moreFilterItems = computed(() => [
  {
    label: t('text.input-field.more-filters'),
    slot: 'panelMoreFilter',
    icon: 'i-lucide-filter',
    defaultOpen: false
  }
])
const limitCode = ref('')
const minAmount = ref<number | null>(null)
const maxAmount = ref<number | null>(null)
const startDate = shallowRef<CalendarDate>()
const endDate = shallowRef<CalendarDate>()
const startDatePopoverOpen = ref(false)
const endDatePopoverOpen = ref(false)

// Date formatter for display
const df = new DateFormatter('en-GB', {
  day: '2-digit',
  month: '2-digit',
  year: 'numeric'
})

// Computed properties for displaying formatted dates
const startDateDisplay = computed(() => 
  startDate.value ? df.format(startDate.value.toDate(getLocalTimeZone())) : ''
)
const endDateDisplay = computed(() => 
  endDate.value ? df.format(endDate.value.toDate(getLocalTimeZone())) : ''
)

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
    limitCode: limitCode.value,
    minAmount: minAmount.value,
    maxAmount: maxAmount.value,
    startDate: getDateString(startDate.value),
    endDate: getDateString(endDate.value)
  })
}

// const table = ref<any>(null)
// const rowSelection = ref({})

// Format number with thousand separators
// const formatNumber = (num: number) => {
//   if (num === 0) return '0'
//   return num.toLocaleString('id-ID')
// }

// const handleEdit = (limit: any) => {
//   console.log('Edit limit:', limit)
//   // Implement edit limit
// }

// const handleDelete = (limit: any) => {
//   console.log('Delete limit:', limit)
//   // Implement delete limit
// }

// Row actions dropdown
// const getRowActions = (row: any) => [
//   [
//     {
//       label: 'Edit',
//       icon: 'i-lucide-pencil',
//       onSelect: () => handleEdit(row.original)
//     }
//   ],
//   [
//     {
//       label: 'Delete',
//       icon: 'i-lucide-trash',
//       color: 'error',
//       onSelect: () => handleDelete(row.original)
//     }
//   ]
// ]

// Table columns definition
// const columns: TableColumn<any>[] = [
//   {
//     id: 'no',
//     header: 'No.',
//     cell: ({ row }) => {
//       const pageIndex = table.value?.tableApi?.getState().pagination.pageIndex || 0
//       const pageSize = table.value?.tableApi?.getState().pagination.pageSize || 20
//       return pageIndex * pageSize + row.index + 1
//     }
//   },
//   {
//     accessorKey: 'limitCode',
//     header: ({ column }) => {
//       const isSorted = column.getIsSorted()

//       return h(UButton, {
//         color: 'neutral',
//         variant: 'ghost',
//         label: 'Limit Code',
//         icon: isSorted
//           ? isSorted === 'asc'
//             ? 'i-lucide-arrow-up-narrow-wide'
//             : 'i-lucide-arrow-down-wide-narrow'
//           : 'i-lucide-arrow-up-down',
//         class: '-mx-2.5',
//         onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
//       })
//     }
//   },
//   {
//     accessorKey: 'minimum',
//     header: ({ column }) => {
//       const isSorted = column.getIsSorted()

//       return h(UButton, {
//         color: 'neutral',
//         variant: 'ghost',
//         label: 'Minimum',
//         icon: isSorted
//           ? isSorted === 'asc'
//             ? 'i-lucide-arrow-up-narrow-wide'
//             : 'i-lucide-arrow-down-wide-narrow'
//           : 'i-lucide-arrow-up-down',
//         class: '-mx-2.5',
//         onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
//       })
//     },
//     cell: ({ row }) => formatNumber(row.getValue('minimum'))
//   },
//   {
//     accessorKey: 'maximum',
//     header: ({ column }) => {
//       const isSorted = column.getIsSorted()

//       return h(UButton, {
//         color: 'neutral',
//         variant: 'ghost',
//         label: 'Maximum',
//         icon: isSorted
//           ? isSorted === 'asc'
//             ? 'i-lucide-arrow-up-narrow-wide'
//             : 'i-lucide-arrow-down-wide-narrow'
//           : 'i-lucide-arrow-up-down',
//         class: '-mx-2.5',
//         onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
//       })
//     },
//     cell: ({ row }) => formatNumber(row.getValue('maximum'))
//   },
//   {
//     accessorKey: 'startDate',
//     header: ({ column }) => {
//       const isSorted = column.getIsSorted()

//       return h(UButton, {
//         color: 'neutral',
//         variant: 'ghost',
//         label: 'Start Date',
//         icon: isSorted
//           ? isSorted === 'asc'
//             ? 'i-lucide-arrow-up-narrow-wide'
//             : 'i-lucide-arrow-down-wide-narrow'
//           : 'i-lucide-arrow-up-down',
//         class: '-mx-2.5',
//         onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
//       })
//     }
//   },
//   {
//     accessorKey: 'endDate',
//     header: ({ column }) => {
//       const isSorted = column.getIsSorted()

//       return h(UButton, {
//         color: 'neutral',
//         variant: 'ghost',
//         label: 'End Date',
//         icon: isSorted
//           ? isSorted === 'asc'
//             ? 'i-lucide-arrow-up-narrow-wide'
//             : 'i-lucide-arrow-down-wide-narrow'
//           : 'i-lucide-arrow-up-down',
//         class: '-mx-2.5',
//         onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
//       })
//     }
//   },
//   {
//     id: 'actions',
//     header: () => h('div', { class: 'text-right' }, ''),
//     cell: ({ row }) => {
//       return h(
//         'div',
//         { class: 'text-right' },
//         h(
//           UDropdownMenu,
//           {
//             content: {
//               align: 'end'
//             },
//             items: getRowActions(row)
//           },
//           () =>
//             h(UButton, {
//               icon: 'i-lucide-ellipsis-vertical',
//               color: 'neutral',
//               variant: 'ghost',
//               size: 'sm'
//             })
//         )
//       )
//     }
//   }
// ]

// const pagination = ref({
//   pageIndex: 4, // Start at page 5 (0-indexed)
//   pageSize: 20
// })
</script>

<template>
  <CmpLayout :title="t('menu.master-data') || 'Master Data'">
    <!-- Content -->
    <div class="flex-1 overflow-auto p-3">
      <!-- Title Section -->
      <UCard>
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-3">
            <UButton
              type="button"
              color="primary"
              size="md"
              class="px-5 py-2 font-bold text-white"
            >
              {{ t('text.button.new').toUpperCase() || 'NEW' }}
            </UButton>
            <h1 class="text-xl font-semibold text-gray-900 dark:text-white">
              {{ t('text.limit-management-pg.list') || 'List of Limits' }}
            </h1>
          </div>
        </div>
      </UCard>
      <div class="py-1"></div>
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
                  <div class="flex flex-col md:flex-row w-full my-0.5 gap-2">
                    <div class="flex w-full">
                      <div class="w-full md:w-36 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.limit-code') || 'Limit Code' }}</div>
                      <div class="flex w-full text-sm">
                          <UInput
                            v-model="limitCode"
                            :placeholder="t('text.input-field.limit-code-placeholder') || 'Enter limit code'"
                            size="md"
                            class="w-full text-base md:text-sm"
                          />
                      </div>
                    </div>
                    <div class="px-2"></div>
                    <div class="flex w-full">
                      <div class="w-full md:w-36 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.start-date') || 'Start Date' }}</div>
                      <div class="flex w-full text-sm">
                        <UPopover v-model:open="startDatePopoverOpen" :popper="{ placement: 'bottom-start' }">
                          <UInput
                            :model-value="startDateDisplay"
                            placeholder="dd / mm / yyyy"
                            size="md"
                            readonly
                            trailing-icon="i-lucide-calendar"
                            class="w-full text-base md:text-sm cursor-pointer"
                            :ui="{ base: 'text-left' }"
                            @click="startDatePopoverOpen = true"
                          />
                          <template #content>
                            <UCalendar v-model="startDate" class="p-2" @update:model-value="startDatePopoverOpen = false" :max-value="endDate" />
                          </template>
                        </UPopover>
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-col md:flex-row w-full my-0.5 gap-2">
                    <div class="flex w-full">
                      <div class="w-full md:w-36 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.min-amount') || 'Minimum' }}</div>
                      <div class="flex w-full text-sm">
                          <UInput
                            v-model="minAmount"
                            :placeholder="t('text.input-field.min-amount-placeholder') || 'Enter minimum'"
                            size="md"
                            class="w-full text-base md:text-sm"
                            type="number"
                          />
                      </div>
                    </div>
                    <div class="px-2"></div>
                    <div class="flex w-full">
                      <div class="w-full md:w-36 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.end-date') || 'End Date' }}</div>
                      <div class="flex w-full text-sm">
                        <UPopover v-model:open="endDatePopoverOpen" :popper="{ placement: 'bottom-start' }">
                          <UInput
                            :model-value="endDateDisplay"
                            placeholder="dd / mm / yyyy"
                            size="md"
                            readonly
                            trailing-icon="i-lucide-calendar"
                            class="w-full text-base md:text-sm cursor-pointer"
                            :ui="{ base: 'text-left' }"
                            @click="endDatePopoverOpen = true"
                          />
                          <template #content>
                            <UCalendar v-model="endDate" class="p-2" @update:model-value="endDatePopoverOpen = false" :min-value="startDate" />
                          </template>
                        </UPopover>
                      </div>
                    </div>
                  </div>
                  <div class="flex flex-col md:flex-row w-full my-0.5 gap-2">
                    <div class="flex w-full">
                      <div class="w-full md:w-36 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.max-amount') || 'Maximum' }}</div>
                      <div class="flex w-full text-sm">
                          <UInput
                            v-model="maxAmount"
                            :placeholder="t('text.input-field.max-amount-placeholder') || 'Enter maximum'"
                            size="md"
                            class="w-full text-base md:text-sm"
                            type="number"
                          />
                      </div>
                    </div>
                    <div class="px-2"></div>
                    <div class="flex w-full">
                      <div class="w-full md:w-36 my-auto text-base md:text-sm"></div>
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

        <!-- Nuxt UI Table -->
        <!-- <UTable
          ref="table"
          v-model:row-selection="rowSelection"
          v-model:pagination="pagination"
          :pagination-options="{
            getPaginationRowModel: getPaginationRowModel()
          }"
          :data="limits"
          :columns="columns"
          class="shrink-0"
          :ui="{
            base: 'table-fixed border-separate border-spacing-0',
            thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none',
            tbody: '[&>tr]:last:[&>td]:border-b-0',
            th: 'py-2 first:rounded-l-lg last:rounded-r-lg border-y border-default first:border-l last:border-r',
            td: 'border-b border-default',
            separator: 'h-0'
          }"
        /> -->

        <!-- Table Footer -->
        <!-- <div class="flex items-center justify-between gap-3 border-t border-default pt-4 mt-auto">
          <div class="text-sm text-muted">
            {{ table?.tableApi?.getFilteredSelectedRowModel().rows.length || 0 }} of
            {{ table?.tableApi?.getFilteredRowModel().rows.length || 0 }} row(s) selected.
          </div>

          <div class="flex items-center gap-1.5">
            <UPagination
              :default-page="(table?.tableApi?.getState().pagination.pageIndex || 0) + 1"
              :items-per-page="table?.tableApi?.getState().pagination.pageSize"
              :total="table?.tableApi?.getFilteredRowModel().rows.length"
              @update:page="(p: number) => table?.tableApi?.setPageIndex(p - 1)"
            />
          </div>
        </div> -->
      </UCard>
    </div>
  </CmpLayout>
</template>

<style scoped>
@keyframes fade-in {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-fade-in {
  animation: fade-in 0.2s ease-out;
}
</style>
}
