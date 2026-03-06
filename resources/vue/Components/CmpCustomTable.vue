<script setup lang="ts">
import { ref, computed, h, resolveComponent, watch } from 'vue'
import type { TableColumn } from '@nuxt/ui'
import { useI18n } from '../composables/useI18n'
import CmpLoadingOverlay from './CmpLoadingOverlay.vue'

const { t } = useI18n()

// Props definition
interface Column {
  key: string
  label: string
  sortable?: boolean
  formatter?: (value: any, row: any) => any
  alignHeader?: 'left' | 'center' | 'right'
  alignBody?: 'left' | 'center' | 'right'
  cellRenderer?: (value: any, row: any) => any // For custom components like radio, input, etc.
  size?: number // Explicit column width in pixels (required for proper column pinning)
}

interface Filter {
  key: string
  label: string
  type: 'text' | 'number' | 'date' | 'select'
  placeholder?: string
  options?: Array<{ label: string; value: any }>
  modelValue?: any
  span?: number // Grid span for responsive layout
}

interface Action {
  label: string
  icon: string
  color?: string
  show?: (row: any) => boolean
  disabled?: (row: any) => boolean
  onSelect: (row: any) => void
}

interface Props {
  // Data
  data: any[]
  columns: Column[]


  // Actions
  actions?: Action[][]
  showActions?: boolean


  // Filters
  showFilters?: boolean
  filters?: Filter[]
  filterTitle?: string
  onFilter?: (filters: Record<string, any>) => void


  // Pagination
  showPagination?: boolean
  pageSize?: number
  currentPage?: number
  countTotalData?: number


  // Selection
  showSelection?: boolean


  // Styling
  showNumberColumn?: boolean
  emptyText?: string


  // Loading
  loading?: boolean
  showLoadingOverlay?: boolean

  // Column pinning
  columnPinning?: { left?: string[], right?: string[] }
}

const props = withDefaults(defineProps<Props>(), {
  showActions: true,
  showFilters: true,
  showPagination: true,
  showSelection: false,
  showNumberColumn: true,
  pageSize: 25,
  currentPage: 1,
  filterTitle: 'More Filters',
  emptyText: 'No data available',
  loading: false
})

const emit = defineEmits<{
  'update:currentPage': [page: number]
  'update:pageSize': [size: number]
  'row-click': [row: any]
  'selection-change': [selectedRows: any[]]
  'search': [query: string] // For global search, server-side
}>()

// Refs
const table = ref<any>(null)
const rowSelection = ref({})
const searchQuery = ref('')
let searchDebounceTimer: ReturnType<typeof setTimeout> | null = null // For global search, server-side

// Page size options
const pageSizeOptions = [
  { label: '10', value: 10 },
  { label: '25', value: 25 },
  { label: '50', value: 50 },
  { label: '100', value: 100 }
]

// Components
const UButton = resolveComponent('UButton')
const UDropdownMenu = resolveComponent('UDropdownMenu')

// Handle page size change
const handlePageSizeChange = (size: number) => {
  emit('update:pageSize', size)
}

// Direct handler for pagination changes
const handlePageChange = (newPage: number) => {
  emit('update:currentPage', newPage)
}

// Watch search query and emit to parent with debouncing for server-side search // For global search, server-side
watch(searchQuery, (newQuery) => {
  // Clear previous timer
  if (searchDebounceTimer) {
    clearTimeout(searchDebounceTimer)
  }

  // Debounce search to avoid too many API calls
  searchDebounceTimer = setTimeout(() => {
    emit('search', newQuery)
  }, 500) // Wait 500ms after user stops typing
})

// No client-side filtering - all filtering done on server // For global search, server-side
const filteredData = computed(() => props.data)

// // Client-side filtering on current page data (filter accordion is server-side)
// const filteredData = computed(() => {
//   if (!searchQuery.value || !props.showFilters) {
//     return props.data
//   }

//   const query = searchQuery.value.toLowerCase()

//   return props.data.filter((row) => {
//     return props.columns.some((col) => {
//       const value = row[col.key]
//       if (value === null || value === undefined) return false
//       return String(value).toLowerCase().includes(query)
//     })
//   })
// })

// Get row actions
const getRowActions = (row: any) => {
  if (!props.actions || !props.showActions) return []

  return props.actions.map(group =>
    group
      .filter(action => typeof action.show === 'function' ? action.show(row.original) : true) // Filter out actions based on the custom 'show' logic
      .filter(action => typeof action.disabled === 'function' ? !action.disabled(row.original) : true) // Filter out disabled actions
      .map(action => ({
        ...action,
        disabled: action.disabled ? action.disabled(row.original) : false,
        onSelect: () => action.onSelect(row.original)
      }))
  )
}

// Build table columns
const tableColumns = computed<TableColumn<any>[]>(() => {
  const cols: TableColumn<any>[] = []

  // Number column
  if (props.showNumberColumn) {
    cols.push({
      id: 'no',
      header: 'No.',
      cell: ({ row }) => {
        const pageIndex = props.currentPage - 1
        const pageSize = props.pageSize || 25
        return pageIndex * pageSize + row.index + 1
      }
    })
  }

  // Data columns
  props.columns.forEach(col => {
    // Determine alignment class
    const alignHeaderClass = col.alignHeader === 'right' ? 'text-right' : col.alignHeader === 'center' ? 'text-center' : 'text-left'
    const alignBodyClass = col.alignBody === 'right' ? 'text-right' : col.alignBody === 'center' ? 'text-center' : 'text-left'

    if (col.sortable) {
      cols.push({
        accessorKey: col.key,
        ...(col.size ? { size: col.size } : {}),
        header: ({ column }) => {
          const isSorted = column.getIsSorted()

          return h(
            'div',
            { class: alignHeaderClass },
            h(UButton, {
              color: 'neutral',
              variant: 'ghost',
              label: col.label,
              icon: isSorted
                ? isSorted === 'asc'
                  ? 'i-lucide-arrow-up-narrow-wide'
                  : 'i-lucide-arrow-down-wide-narrow'
                : 'i-lucide-arrow-up-down',
              class: '-mx-2.5 text-xs md:text-sm lg:text-base',
              onClick: () => column.toggleSorting(column.getIsSorted() === 'asc')
            })
          )
        },
        cell: ({ row }) => {
          const value = row.getValue(col.key)
          let content

          if (col.cellRenderer) {
            content = col.cellRenderer(value, row.original)
          } else if (col.formatter) {
            content = col.formatter(value, row.original)
          } else {
            content = value
          }

          return h('div', { class: alignBodyClass }, content)
        }
      })
    } else {
      cols.push({
        accessorKey: col.key,
        ...(col.size ? { size: col.size } : {}),
        header: () => h('div', { class: alignHeaderClass }, col.label),
        cell: ({ row }) => {
          const value = row.getValue(col.key)
          let content

          if (col.cellRenderer) {
            content = col.cellRenderer(value, row.original)
          } else if (col.formatter) {
            content = col.formatter(value, row.original)
          } else {
            content = value
          }

          return h('div', { class: alignBodyClass }, content)
        }
      })
    }
  })

  // Actions column
  if (props.showActions && props.actions && props.actions.length > 0) {
    cols.push({
      id: 'actions',
      header: () => h('div', { class: 'text-right' }, ''),
      cell: ({ row }) => {
        return h(
          'div',
          { class: 'text-center' },
          h(
            UDropdownMenu,
            {
              content: {
                align: 'end'
              },
              ui: {
                content: 'min-w-36 border-1 border-gray-200 dark:border-gray-700',
                item: 'px-2 gap-2',   // Change the horizontal gap between the icon and text
                itemLeadingIcon: 'size-4.5' // Change icon size specifically
              },
              items: getRowActions(row)
            },
            () =>
              h(UButton, {
                icon: 'i-lucide-ellipsis-vertical',
                color: 'neutral',
                variant: 'ghost',
                size: 'sm'
              })
          )
        )
      }
    })
  }

  return cols
})

// Watch row selection changes
watch(rowSelection, (newVal) => {
  if (props.showSelection) {
    const selectedRows = Object.keys(newVal)
      .filter(key => newVal[key as any])
      .map(key => props.data[parseInt(key)])
    emit('selection-change', selectedRows)
  }
})
</script>

<template>
  <div class="custom-table-wrapper">
    <!-- Global Search Filter -->
    <div v-if="showFilters" class="mb-3">
      <UInput
        v-model="searchQuery"
        icon="i-lucide-search"
        :placeholder="t('text.input-field.search')"
        size="lg"
        class="max-w-xs w-full rounded-lg"
      />
    </div>

    <!-- Table -->
    <CmpLoadingOverlay :show-loading="showLoadingOverlay" :loading="loading">
      <UTable
        ref="table"
        v-model:row-selection="rowSelection"
        :data="filteredData"
        :columns="tableColumns"
        :loading="loading"
        :column-pinning="columnPinning"
        class="shrink-0 border border-default rounded-lg"
        :ui="{
          base: 'table-fixed border-separate border-spacing-0 text-xs',
          thead: '[&>tr]:bg-elevated/50 [&>tr]:after:content-none text-left',
          tbody: `
            [&>tr:last-child>td:first-child]:rounded-bl-lg
            [&>tr:last-child>td:last-child]:rounded-br-lg
          `,
          th: 'py-2 border-y border-default border-1 first:rounded-tl-lg last:rounded-tr-lg',
          td: 'border-b border-default border-1 first:border-l last:border-r',
          separator: 'h-0'
        }"
      >
        <!-- The template #loading only works if the data was empty first -->
        <!-- <template #loading>
          <div v-if="showLoadingOverlay" class="text-center text-gray-500">
            <UIcon name="i-lucide-loader-2" class="animate-spin inline-block mr-2" />
            {{ t('text.message.loading') || 'Loading...' }}
          </div>
        </template> -->

        <template #empty>
          <div class="text-center text-gray-500">
            {{ t('text.message.no-data') || 'No data available.' }}
          </div>
        </template>
      </UTable>
    </CmpLoadingOverlay>

    <!-- Footer with Pagination -->
    <div
      v-if="showPagination"
      class="flex items-center justify-between gap-3 pt-4 mt-auto"
    >
      <div class="flex items-center gap-4">
        <!-- Data Info -->
        <div class="text-sm sm:text-xs md:text-sm lg:text-sm text-muted">
          <template v-if="showSelection">
            {{ table?.tableApi?.getFilteredSelectedRowModel().rows.length || 0 }} of
            {{ table?.tableApi?.getFilteredRowModel().rows.length || 0 }} row(s) selected.
          </template>
          <template v-else>
            {{ t('text.table.showing') || 'Showing' }} {{ ((currentPage - 1) * pageSize) + 1 }} {{ t('text.table.to') || 'to' }}
            {{ Math.min(currentPage * pageSize, countTotalData || 0) }} {{ t('text.table.of') || 'of' }}
            {{ countTotalData || 0 }} {{ t('text.table.entries') || 'entries' }}
          </template>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <!-- Page Size Selector -->
        <div class="flex items-center gap-2">
          <span class="sm:text-xs md:text-sm lg:text-sm text-muted">{{ t('text.table.rows-per-page') || 'Rows per page' }}</span>
          <USelect
            :model-value="pageSize"
            :items="pageSizeOptions"
            @update:model-value="handlePageSizeChange"
            class="w-20"
          />
        </div>

        <!-- Pagination -->
        <UPagination
          :page="currentPage"
          :items-per-page="pageSize"
          :total="countTotalData || 0"
          @update:page="handlePageChange"
          show-edges
          :sibling-count="1"
        />
      </div>
    </div>

  </div>
</template>

<style scoped>
.custom-table-wrapper {
  width: 100%;
}

/*
 * Sticky / pinned column backgrounds — light & dark mode aware.
 * Nuxt UI v3 UTable adds the `sticky` utility class to pinned th/td elements.
 * We use [class*="sticky"] so the rule only fires on those cells and never
 * on un-pinned columns (no more :first-child / :last-child side-effects).
 *
 * --ui-bg          → the page/card background (auto-switches light ↔ dark)
 * --ui-bg-elevated → slightly elevated surface used by the thead
 */

/* Pinned body cells */
:deep(table td[class*="sticky"]) {
  background-color: var(--ui-bg);
}

/* Pinned header cells — match the thead's bg-elevated tint */
:deep(table thead th[class*="sticky"]) {
  background-color: var(--ui-bg-elevated);
}

/* Divider shadow on the rightmost left-pinned column */
:deep(table th[class*="sticky"][class*="left"]:not([class*="right"])),
:deep(table td[class*="sticky"][class*="left"]:not([class*="right"])) {
  border-right: 1px solid var(--ui-border);
}

/* Divider shadow on the leftmost right-pinned column */
:deep(table th[class*="sticky"][class*="right"]:not([class*="left"])),
:deep(table td[class*="sticky"][class*="right"]:not([class*="left"])) {
  border-left: 1px solid var(--ui-border);
}
</style>
