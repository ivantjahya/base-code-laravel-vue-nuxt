# CmpCustomTable - Quick Start Guide

## 📚 Files Created

1. **Component** - `resources/vue/Components/CmpCustomTable.vue`
2. **Documentation** - `resources/vue/Components/README-CmpCustomTable.md`
3. **Examples**:
   - `resources/vue/Examples/SimpleTableExample.vue` - Minimal setup
   - `resources/vue/Examples/ExampleTableUsage.vue` - Limits table example
   - `resources/vue/Examples/AdvancedTableExample.vue` - All features demo

## 🚀 Getting Started

### Step 1: Import the Component

```vue
<script setup lang="ts">
import CmpCustomTable from '../Components/CmpCustomTable.vue'
import { ref } from 'vue'
</script>
```

### Step 2: Prepare Your Data

```javascript
const myData = ref([
  { id: 1, name: 'Item 1', price: 1000 },
  { id: 2, name: 'Item 2', price: 2000 },
])
```

### Step 3: Define Columns

```javascript
const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'price', label: 'Price', sortable: true },
]
```

### Step 4: Add to Template

```vue
<template>
  <CmpCustomTable
    :data="myData"
    :columns="columns"
  />
</template>
```

That's it! You now have a working table with sorting and pagination.

## 📖 Next Steps

### Add Filters

```javascript
const filters = [
  {
    key: 'name',
    label: 'Name',
    type: 'text',
    placeholder: 'Search name...'
  }
]
```

```vue
<CmpCustomTable
  :data="myData"
  :columns="columns"
  :filters="filters"
  @filter="(values) => console.log(values)"
/>
```

### Add Actions

```javascript
const actions = [
  [
    {
      label: 'Edit',
      icon: 'i-lucide-pencil',
      onSelect: (row) => editItem(row)
    }
  ]
]
```

```vue
<CmpCustomTable
  :data="myData"
  :columns="columns"
  :actions="actions"
/>
```

### Format Data

```javascript
const columns = [
  {
    key: 'price',
    label: 'Price',
    sortable: true,
    formatter: (value) => {
      return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
      }).format(value)
    }
  }
]
```

## 🎯 Common Patterns

### Currency Formatting
```javascript
formatter: (value) => new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  minimumFractionDigits: 0
}).format(value)
```

### Date Formatting
```javascript
formatter: (value) => new Date(value).toLocaleDateString('id-ID')
```

### Conditional Styling
```javascript
formatter: (value) => {
  const color = value > 100 ? 'text-green-600' : 'text-red-600'
  return `<span class="${color}">${value}</span>`
}
```

### Number Formatting
```javascript
formatter: (value) => value.toLocaleString('id-ID')
```

## 📝 Configuration Options

### Hide Features

```vue
<CmpCustomTable
  :show-filters="false"      <!-- Hide filters -->
  :show-actions="false"       <!-- Hide actions column -->
  :show-pagination="false"    <!-- Hide pagination -->
  :show-number-column="false" <!-- Hide row numbers -->
/>
```

### Customize Pagination

```vue
<CmpCustomTable
  :page-size="50"             <!-- 50 items per page -->
  v-model:current-page="page" <!-- Bind to page ref -->
/>
```

### Custom Empty State

```vue
<CmpCustomTable
  empty-text="No records found. Try adjusting your filters."
/>
```

### Loading State

```vue
<CmpCustomTable
  :loading="isLoading"
/>
```

## 🔍 Example Use Cases

### 1. User Management Table
```javascript
const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'role', label: 'Role', sortable: false },
  { 
    key: 'status', 
    label: 'Status',
    formatter: (v) => v === 'active' ? '✅ Active' : '❌ Inactive'
  }
]

const actions = [
  [{
    label: 'Edit',
    icon: 'i-lucide-pencil',
    onSelect: (row) => openEditModal(row)
  }],
  [{
    label: 'Delete',
    icon: 'i-lucide-trash',
    color: 'error',
    onSelect: (row) => deleteUser(row)
  }]
]
```

### 2. Transaction Table
```javascript
const columns = [
  { key: 'transaction_id', label: 'ID', sortable: true },
  { key: 'date', label: 'Date', sortable: true, formatter: formatDate },
  { key: 'amount', label: 'Amount', sortable: true, formatter: formatCurrency },
  { key: 'status', label: 'Status', sortable: true }
]

const filters = [
  { key: 'transaction_id', label: 'Transaction ID', type: 'text' },
  { key: 'date_from', label: 'From Date', type: 'date' },
  { key: 'date_to', label: 'To Date', type: 'date' },
  { 
    key: 'status', 
    label: 'Status', 
    type: 'select',
    options: [
      { label: 'All', value: null },
      { label: 'Pending', value: 'pending' },
      { label: 'Completed', value: 'completed' },
      { label: 'Failed', value: 'failed' }
    ]
  }
]
```

### 3. Product Inventory Table
```javascript
const columns = [
  { key: 'sku', label: 'SKU', sortable: true },
  { key: 'name', label: 'Product', sortable: true },
  { key: 'category', label: 'Category', sortable: true },
  { key: 'price', label: 'Price', sortable: true, formatter: formatCurrency },
  { 
    key: 'stock', 
    label: 'Stock',
    sortable: true,
    formatter: (v) => v === 0 ? '⚠️ Out of Stock' : v
  }
]

const actions = [
  [{
    label: 'View',
    icon: 'i-lucide-eye',
    onSelect: (row) => viewProduct(row)
  }, {
    label: 'Edit',
    icon: 'i-lucide-pencil',
    onSelect: (row) => editProduct(row)
  }],
  [{
    label: 'Restock',
    icon: 'i-lucide-package-plus',
    disabled: (row) => row.stock > 10,
    onSelect: (row) => restockProduct(row)
  }]
]
```

## 🎨 Styling Tips

### Custom Table Wrapper
```vue
<div class="bg-white dark:bg-gray-900 rounded-lg shadow">
  <CmpCustomTable ... />
</div>
```

### Responsive Container
```vue
<div class="overflow-x-auto">
  <CmpCustomTable ... />
</div>
```

### With Header
```vue
<UCard>
  <template #header>
    <div class="flex justify-between items-center">
      <h2 class="text-xl font-bold">My Table</h2>
      <UButton icon="i-lucide-plus">Add New</UButton>
    </div>
  </template>
  
  <CmpCustomTable ... />
</UCard>
```

## ⚡ Performance Tips

1. **Server-Side Pagination**: For large datasets (1000+ rows), implement API pagination
2. **Debounce Filters**: Add debounce to filter inputs for better UX
3. **Virtual Scrolling**: For very large tables, consider virtual scrolling
4. **Lazy Loading**: Load data on-demand as user scrolls/pages

## 🐛 Troubleshooting

### Table not showing data?
- Check if `data` prop is an array
- Verify column `key` values match your data object keys
- Check browser console for errors

### Sorting not working?
- Ensure `sortable: true` is set on the column
- Verify data types are consistent (all numbers or all strings)

### Filters not appearing?
- Make sure `:show-filters="true"` is set
- Verify `:filters` array is properly defined
- Check filter `type` values are valid

### Actions menu empty?
- Ensure `:actions` array is nested: `[[action1], [action2]]`
- Verify `:show-actions="true"` (default)
- Check action icons use correct format: `i-lucide-icon-name`

## 📚 Full Documentation

See [README-CmpCustomTable.md](./README-CmpCustomTable.md) for complete API reference and advanced usage.

## 💡 Need Help?

1. Check the example files in `resources/vue/Examples/`
2. Review the full documentation in `README-CmpCustomTable.md`
3. Inspect the component source code in `CmpCustomTable.vue`

Happy coding! 🎉
