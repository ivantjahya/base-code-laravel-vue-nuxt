<script setup lang="ts">
/**
 * ADVANCED TABLE EXAMPLE
 * 
 * This example demonstrates all features of CmpCustomTable:
 * - Filters (text, number, date, select)
 * - Sortable columns
 * - Custom formatters
 * - Row actions
 * - Pagination
 */

import { ref } from 'vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'

const currentPage = ref(1)

// Sample product data
const products = ref([
  {
    id: 1,
    product_code: 'PRD001',
    name: 'Laptop Dell XPS 13',
    category: 'Electronics',
    price: 15000000,
    stock: 25,
    created_at: '2025-01-15',
    status: 'Available'
  },
  {
    id: 2,
    product_code: 'PRD002',
    name: 'iPhone 15 Pro',
    category: 'Electronics',
    price: 20000000,
    stock: 10,
    created_at: '2025-01-18',
    status: 'Available'
  },
  {
    id: 3,
    product_code: 'PRD003',
    name: 'Samsung Galaxy S24',
    category: 'Electronics',
    price: 18000000,
    stock: 0,
    created_at: '2025-01-20',
    status: 'Out of Stock'
  },
  {
    id: 4,
    product_code: 'PRD004',
    name: 'MacBook Pro 16"',
    category: 'Electronics',
    price: 35000000,
    stock: 8,
    created_at: '2025-01-22',
    status: 'Available'
  },
  {
    id: 5,
    product_code: 'PRD005',
    name: 'Sony WH-1000XM5',
    category: 'Audio',
    price: 5000000,
    stock: 50,
    created_at: '2025-01-25',
    status: 'Available'
  },
])

// ========================= COLUMNS =========================
const formatCurrency = (value: number) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(value)
}

const formatDate = (value: string) => {
  return new Date(value).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: '2-digit',
    year: 'numeric'
  })
}

const columns = [
  {
    key: 'product_code',
    label: 'Product Code',
    sortable: true
  },
  {
    key: 'name',
    label: 'Product Name',
    sortable: true
  },
  {
    key: 'category',
    label: 'Category',
    sortable: true
  },
  {
    key: 'price',
    label: 'Price',
    sortable: true,
    formatter: formatCurrency
  },
  {
    key: 'stock',
    label: 'Stock',
    sortable: true,
    formatter: (value: number) => {
      if (value === 0) return '0 (Out of Stock)'
      if (value < 10) return `${value} (Low Stock)`
      return value.toString()
    }
  },
  {
    key: 'created_at',
    label: 'Created Date',
    sortable: true,
    formatter: formatDate
  },
  {
    key: 'status',
    label: 'Status',
    sortable: false,
    formatter: (value: string) => {
      const colors: Record<string, string> = {
        'Available': 'text-green-600 dark:text-green-400',
        'Out of Stock': 'text-red-600 dark:text-red-400',
        'Discontinued': 'text-gray-600 dark:text-gray-400'
      }
      return `<span class="font-medium ${colors[value] || ''}">${value}</span>`
    }
  }
]

// ========================= FILTERS =========================
const filters = [
  {
    key: 'product_code',
    label: 'Product Code',
    type: 'text' as const,
    placeholder: 'Enter product code'
  },
  {
    key: 'name',
    label: 'Product Name',
    type: 'text' as const,
    placeholder: 'Enter product name'
  },
  {
    key: 'category',
    label: 'Category',
    type: 'select' as const,
    options: [
      { label: 'All Categories', value: null },
      { label: 'Electronics', value: 'Electronics' },
      { label: 'Audio', value: 'Audio' },
      { label: 'Accessories', value: 'Accessories' }
    ]
  },
  {
    key: 'status',
    label: 'Status',
    type: 'select' as const,
    options: [
      { label: 'All Status', value: null },
      { label: 'Available', value: 'Available' },
      { label: 'Out of Stock', value: 'Out of Stock' },
      { label: 'Discontinued', value: 'Discontinued' }
    ]
  },
  {
    key: 'min_price',
    label: 'Minimum Price',
    type: 'number' as const
  },
  {
    key: 'max_price',
    label: 'Maximum Price',
    type: 'number' as const
  },
  {
    key: 'created_from',
    label: 'Created From',
    type: 'date' as const
  },
  {
    key: 'created_to',
    label: 'Created To',
    type: 'date' as const
  }
]

// ========================= ACTIONS =========================
const handleView = (row: any) => {
  alert(`Viewing product: ${row.name}`)
  console.log('View product:', row)
}

const handleEdit = (row: any) => {
  alert(`Editing product: ${row.name}`)
  console.log('Edit product:', row)
}

const handleDuplicate = (row: any) => {
  alert(`Duplicating product: ${row.name}`)
  console.log('Duplicate product:', row)
}

const handleDelete = (row: any) => {
  if (confirm(`Are you sure you want to delete ${row.name}?`)) {
    const index = products.value.findIndex(p => p.id === row.id)
    if (index > -1) {
      products.value.splice(index, 1)
      alert('Product deleted successfully!')
    }
  }
}

const handleRestock = (row: any) => {
  const quantity = prompt(`Enter restock quantity for ${row.name}:`, '10')
  if (quantity) {
    const product = products.value.find(p => p.id === row.id)
    if (product) {
      product.stock += parseInt(quantity)
      product.status = 'Available'
      alert(`Added ${quantity} units to stock!`)
    }
  }
}

const actions = [
  [
    {
      label: 'View Details',
      icon: 'i-lucide-eye',
      onSelect: handleView
    },
    {
      label: 'Edit',
      icon: 'i-lucide-pencil',
      onSelect: handleEdit
    },
    {
      label: 'Duplicate',
      icon: 'i-lucide-copy',
      onSelect: handleDuplicate
    }
  ],
  [
    {
      label: 'Restock',
      icon: 'i-lucide-package-plus',
      color: 'primary',
      disabled: (row: any) => row.stock > 10,
      onSelect: handleRestock
    }
  ],
  [
    {
      label: 'Delete',
      icon: 'i-lucide-trash-2',
      color: 'error',
      onSelect: handleDelete
    }
  ]
]

// ========================= FILTER HANDLER =========================
const handleFilter = (filterValues: Record<string, any>) => {
  console.log('Applied filters:', filterValues)
  
  // In a real application, you would:
  // 1. Make an API call with these filter parameters
  // 2. Update the products data with the filtered results
  
  // Example API call:
  // try {
  //   const response = await fetch('/api/products', {
  //     method: 'POST',
  //     headers: { 'Content-Type': 'application/json' },
  //     body: JSON.stringify(filterValues)
  //   })
  //   products.value = await response.json()
  // } catch (error) {
  //   console.error('Filter error:', error)
  // }
  
  alert('Filters applied! Check console for values.')
}

// ========================= OTHER HANDLERS =========================
const handleRowClick = (row: any) => {
  console.log('Row clicked:', row)
}

const handleSelectionChange = (selectedRows: any[]) => {
  console.log('Selected rows:', selectedRows)
}
</script>

<template>
  <div class="p-4">
    
    <!-- Page Header -->
    <div class="mb-6">
      <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">
        Product Management
      </h1>
      <p class="text-gray-600 dark:text-gray-400">
        Advanced table with all features enabled
      </p>
    </div>
    
    <!-- Action Buttons -->
    <UCard class="mb-4">
      <div class="flex items-center justify-between">
        <div class="flex gap-2">
          <UButton
            icon="i-lucide-plus"
            color="primary"
            class="text-white"
          >
            Add Product
          </UButton>
          <UButton
            icon="i-lucide-upload"
            color="neutral"
            variant="outline"
          >
            Import
          </UButton>
          <UButton
            icon="i-lucide-download"
            color="neutral"
            variant="outline"
          >
            Export
          </UButton>
        </div>
        <div class="text-sm text-gray-600 dark:text-gray-400">
          Total Products: {{ products.length }}
        </div>
      </div>
    </UCard>
    
    <!-- Table -->
    <UCard>
      <CmpCustomTable
        :data="products"
        :columns="columns"
        :filters="filters"
        :actions="actions"
        :show-filters="true"
        :show-pagination="true"
        :show-number-column="true"
        :show-actions="true"
        :page-size="10"
        v-model:current-page="currentPage"
        filter-title="Advanced Filters"
        empty-text="No products found"
        @filter="handleFilter"
        @row-click="handleRowClick"
      />
    </UCard>
    
    <!-- Info Panel -->
    <UCard class="mt-4">
      <div class="text-sm text-gray-600 dark:text-gray-400">
        <h3 class="font-semibold mb-2">Features Demonstrated:</h3>
        <ul class="list-disc list-inside space-y-1">
          <li>Sortable columns (click column headers with arrows)</li>
          <li>Advanced filters (text, number, date, select)</li>
          <li>Custom formatters (currency, date, conditional styling)</li>
          <li>Row actions with conditional disable</li>
          <li>Pagination (10 items per page)</li>
          <li>Auto-numbered rows</li>
        </ul>
      </div>
    </UCard>
    
  </div>
</template>

<style scoped>
/* Custom styles if needed */
</style>
