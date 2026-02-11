<script setup lang="ts">
import { ref, computed } from 'vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'
import CmpLayout from '../Components/CmpLayout.vue'
import { useI18n } from '../composables/useI18n'

const { t } = useI18n()

// ========================= DATA =========================
const limits = ref([
  {
    id: 1,
    limit_code: 'L001',
    minimum: 0,
    maximum: 5000000,
    start_date: '01-12-2025',
    end_date: '31-12-2030',
    status: 'Active'
  },
  {
    id: 2,
    limit_code: 'L002',
    minimum: 5000000,
    maximum: 10000000,
    start_date: '15-01-2026',
    end_date: '31-12-2030',
    status: 'Active'
  },
  {
    id: 3,
    limit_code: 'L003',
    minimum: 10000000,
    maximum: 50000000,
    start_date: '21-11-2025',
    end_date: '31-12-2030',
    status: 'Inactive'
  },
  {
    id: 4,
    limit_code: 'L004',
    minimum: 0,
    maximum: 10000000,
    start_date: '01-09-2025',
    end_date: '31-12-2030',
    status: 'Active'
  },
  {
    id: 5,
    limit_code: 'L005',
    minimum: 50000000,
    maximum: 100000000,
    start_date: '12-12-2024',
    end_date: '31-12-2030',
    status: 'Active'
  }
])

// ========================= PAGINATION =========================
const currentPage = ref(1)

// ========================= TABLE COLUMNS =========================
const formatCurrency = (value: number) => {
  if (value === null || value === undefined) return '-'
  if (value === 0) return '0'
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const columns = [
  {
    key: 'limit_code',
    label: t('text.table-column.column-limit-code') || 'Limit Code',
    sortable: true
  },
  {
    key: 'minimum',
    label: t('text.table-column.column-minimum-amount') || 'Minimum',
    sortable: true,
    formatter: (value: number) => formatCurrency(value)
  },
  {
    key: 'maximum',
    label: t('text.table-column.column-maximum-amount') || 'Maximum',
    sortable: true,
    formatter: (value: number) => formatCurrency(value)
  },
  {
    key: 'start_date',
    label: t('text.table-column.column-start-date') || 'Start Date',
    sortable: true
  },
  {
    key: 'end_date',
    label: t('text.table-column.column-end-date') || 'End Date',
    sortable: true
  },
  {
    key: 'status',
    label: 'Status',
    sortable: false
  }
]

// ========================= FILTERS =========================
const filters = [
  {
    key: 'limit_code',
    label: t('text.input-field.limit-code') || 'Limit Code',
    type: 'text' as const,
    placeholder: 'Enter limit code'
  },
  {
    key: 'start_date',
    label: t('text.input-field.start-date') || 'Start Date',
    type: 'date' as const
  },
  {
    key: 'minimum',
    label: t('text.input-field.min-amount') || 'Minimum',
    type: 'number' as const
  },
  {
    key: 'end_date',
    label: t('text.input-field.end-date') || 'End Date',
    type: 'date' as const
  },
  {
    key: 'maximum',
    label: t('text.input-field.max-amount') || 'Maximum',
    type: 'number' as const
  },
  {
    key: 'status',
    label: 'Status',
    type: 'select' as const,
    options: [
      { label: 'All', value: null },
      { label: 'Active', value: 'Active' },
      { label: 'Inactive', value: 'Inactive' }
    ]
  }
]

// ========================= ACTIONS =========================
const handleEdit = (row: any) => {
  console.log('Edit limit:', row)
  // Implement edit logic here
  // You can open a modal, navigate to edit page, etc.
}

const handleDelete = (row: any) => {
  console.log('Delete limit:', row)
  // Implement delete logic here
  // Show confirmation dialog, then delete
}

const handleView = (row: any) => {
  console.log('View limit:', row)
  // Implement view logic here
}

const actions = [
  [
    {
      label: 'View',
      icon: 'i-lucide-eye',
      onSelect: handleView
    },
    {
      label: 'Edit',
      icon: 'i-lucide-pencil',
      onSelect: handleEdit
    }
  ],
  [
    {
      label: 'Delete',
      icon: 'i-lucide-trash',
      color: 'error',
      disabled: (row: any) => row.status === 'Active', // Example: disable delete for active items
      onSelect: handleDelete
    }
  ]
]

// ========================= FILTER HANDLER =========================
const handleFilter = (filterValues: Record<string, any>) => {
  console.log('Filter applied:', filterValues)
  // Implement your filter logic here
  // You can make API call with filter params or filter the data locally
  
  // Example: API call
  // const params = {
  //   limit_code: filterValues.limit_code,
  //   minimum: filterValues.minimum,
  //   maximum: filterValues.maximum,
  //   start_date: filterValues.start_date,
  //   end_date: filterValues.end_date,
  //   status: filterValues.status
  // }
  // fetchData(params)
}

// ========================= ROW CLICK HANDLER =========================
const handleRowClick = (row: any) => {
  console.log('Row clicked:', row)
}

// ========================= SELECTION HANDLER =========================
const handleSelectionChange = (selectedRows: any[]) => {
  console.log('Selected rows:', selectedRows)
}
</script>

<template>
  <CmpLayout :title="t('menu.master-data') || 'Master Data'">
    
    <div class="flex-1 overflow-auto p-3">
      
      <!-- Title Section -->
      <UCard class="mb-3">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-4">
            
            <!-- Add New Button -->
            <UButton 
              type="button" 
              class="bg-[#F26524] text-white hover:bg-[#E34613] active:bg-[#E34613] text-[16px] px-5"
            >
              {{ t('text.button.new').toUpperCase() || 'NEW' }}
            </UButton>
            
            <h1 class="text-lg font-semibold text-gray-900 dark:text-white">
              {{ t('text.limit-management-pg.list') || 'List of Limits' }}
            </h1>
            
          </div>
        </div>
      </UCard>
      
      <!-- Table Card -->
      <UCard>
        <CmpCustomTable
          :data="limits"
          :columns="columns"
          :filters="filters"
          :actions="actions"
          :show-filters="true"
          :show-pagination="true"
          :show-number-column="true"
          :show-actions="true"
          :page-size="20"
          v-model:current-page="currentPage"
          filter-title="More Filters"
          @filter="handleFilter"
          @row-click="handleRowClick"
          @selection-change="handleSelectionChange"
        />
      </UCard>
      
    </div>
    
  </CmpLayout>
</template>

<style scoped>
/* Add any custom styles here */
</style>
