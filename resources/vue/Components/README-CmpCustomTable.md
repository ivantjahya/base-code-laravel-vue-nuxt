# CmpCustomTable Component Documentation

A reusable, feature-rich table component built with Nuxt UI that provides filtering, sorting, pagination, and row actions out of the box.

## Features

- ✅ Sortable columns
- ✅ Collapsible filters with multiple input types (text, number, date, select)
- ✅ Row actions with dropdown menu
- ✅ Pagination
- ✅ Number column (auto-numbered rows)
- ✅ Row selection (optional)
- ✅ Custom formatters for data display
- ✅ Responsive design
- ✅ Dark mode support
- ✅ Loading state

## Basic Usage

```vue
<script setup lang="ts">
import CmpCustomTable from '../Components/CmpCustomTable.vue'
import { ref } from 'vue'

const currentPage = ref(1)

const data = ref([
  { id: 1, name: 'John Doe', email: 'john@example.com', age: 30 },
  { id: 2, name: 'Jane Smith', email: 'jane@example.com', age: 25 },
])

const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'age', label: 'Age', sortable: true },
]
</script>

<template>
  <CmpCustomTable
    :data="data"
    :columns="columns"
    v-model:current-page="currentPage"
  />
</template>
```

## Props

### Required Props

| Prop | Type | Description |
|------|------|-------------|
| `data` | `Array` | Array of data objects to display in the table |
| `columns` | `Array<Column>` | Column definitions (see Column Configuration below) |

### Optional Props

| Prop | Type | Default | Description |
|------|------|---------|-------------|
| `actions` | `Array<Array<Action>>` | `[]` | Row action definitions (see Actions Configuration) |
| `showActions` | `Boolean` | `true` | Show/hide the actions column |
| `showFilters` | `Boolean` | `true` | Show/hide the filter section |
| `filters` | `Array<Filter>` | `[]` | Filter definitions (see Filters Configuration) |
| `filterTitle` | `String` | `'More Filters'` | Title for the filter accordion |
| `showPagination` | `Boolean` | `true` | Show/hide pagination |
| `pageSize` | `Number` | `20` | Number of rows per page |
| `currentPage` | `Number` | `1` | Current page number (v-model supported) |
| `showSelection` | `Boolean` | `false` | Enable row selection |
| `showNumberColumn` | `Boolean` | `true` | Show/hide the auto-numbered column |
| `emptyText` | `String` | `'No data available'` | Text to display when no data |
| `loading` | `Boolean` | `false` | Show loading state |

## Column Configuration

### Column Object Structure

```typescript
interface Column {
  key: string                                    // Data key to access value
  label: string                                  // Column header label
  sortable?: boolean                             // Enable sorting (default: false)
  formatter?: (value: any, row: any) => any     // Custom formatter function
  align?: 'left' | 'center' | 'right'           // Text alignment
}
```

### Column Examples

```javascript
// Simple column
{ key: 'name', label: 'Name' }

// Sortable column
{ key: 'email', label: 'Email', sortable: true }

// Column with custom formatter
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

// Column with row data access in formatter
{
  key: 'status',
  label: 'Status',
  formatter: (value, row) => {
    return row.is_active ? 'Active' : 'Inactive'
  }
}
```

## Filter Configuration

### Filter Object Structure

```typescript
interface Filter {
  key: string                                   // Data key to filter
  label: string                                 // Filter label
  type: 'text' | 'number' | 'date' | 'select'  // Input type
  placeholder?: string                          // Placeholder text
  options?: Array<{                             // Options for select type
    label: string
    value: any
  }>
  modelValue?: any                              // Default value
}
```

### Filter Examples

```javascript
// Text filter
{
  key: 'name',
  label: 'Name',
  type: 'text',
  placeholder: 'Enter name'
}

// Number filter (formatted as currency)
{
  key: 'amount',
  label: 'Amount',
  type: 'number'
}

// Date filter
{
  key: 'start_date',
  label: 'Start Date',
  type: 'date'
}

// Select filter
{
  key: 'status',
  label: 'Status',
  type: 'select',
  options: [
    { label: 'All', value: null },
    { label: 'Active', value: 'active' },
    { label: 'Inactive', value: 'inactive' }
  ]
}
```

### Filter Handler

```javascript
const handleFilter = (filterValues) => {
  console.log('Applied filters:', filterValues)
  // filterValues is an object: { key: value, ... }
  
  // Example: Make API call with filters
  fetchData({
    name: filterValues.name,
    status: filterValues.status,
    start_date: filterValues.start_date
  })
}
```

```vue
<CmpCustomTable
  :filters="filters"
  @filter="handleFilter"
/>
```

## Actions Configuration

### Action Object Structure

```typescript
interface Action {
  label: string                           // Action label
  icon: string                           // Lucide icon name
  color?: string                         // Action color (error, warning, etc.)
  disabled?: (row: any) => boolean      // Function to disable action
  onSelect: (row: any) => void          // Click handler
}
```

### Actions Examples

```javascript
const actions = [
  // First group
  [
    {
      label: 'View',
      icon: 'i-lucide-eye',
      onSelect: (row) => {
        console.log('View', row)
      }
    },
    {
      label: 'Edit',
      icon: 'i-lucide-pencil',
      onSelect: (row) => {
        console.log('Edit', row)
      }
    }
  ],
  // Second group (separated by divider)
  [
    {
      label: 'Delete',
      icon: 'i-lucide-trash',
      color: 'error',
      disabled: (row) => row.status === 'Active',
      onSelect: (row) => {
        if (confirm('Are you sure?')) {
          deleteItem(row.id)
        }
      }
    }
  ]
]
```

## Events

| Event | Payload | Description |
|-------|---------|-------------|
| `update:currentPage` | `number` | Emitted when page changes |
| `filter` | `Record<string, any>` | Emitted when filters are applied |
| `row-click` | `any` | Emitted when a row is clicked |
| `selection-change` | `Array<any>` | Emitted when row selection changes |

## Complete Example

```vue
<script setup lang="ts">
import { ref } from 'vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'

// Data
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
  // ... more data
])

// Pagination
const currentPage = ref(1)

// Columns
const columns = [
  {
    key: 'limit_code',
    label: 'Limit Code',
    sortable: true
  },
  {
    key: 'minimum',
    label: 'Minimum',
    sortable: true,
    formatter: (value) => formatCurrency(value)
  },
  {
    key: 'maximum',
    label: 'Maximum',
    sortable: true,
    formatter: (value) => formatCurrency(value)
  },
  {
    key: 'start_date',
    label: 'Start Date',
    sortable: true
  },
  {
    key: 'end_date',
    label: 'End Date',
    sortable: true
  }
]

// Filters
const filters = [
  {
    key: 'limit_code',
    label: 'Limit Code',
    type: 'text',
    placeholder: 'Enter limit code'
  },
  {
    key: 'start_date',
    label: 'Start Date',
    type: 'date'
  },
  {
    key: 'minimum',
    label: 'Minimum Amount',
    type: 'number'
  }
]

// Actions
const actions = [
  [
    {
      label: 'Edit',
      icon: 'i-lucide-pencil',
      onSelect: (row) => editItem(row)
    }
  ],
  [
    {
      label: 'Delete',
      icon: 'i-lucide-trash',
      color: 'error',
      onSelect: (row) => deleteItem(row)
    }
  ]
]

// Handlers
const handleFilter = (filterValues) => {
  console.log('Filters:', filterValues)
  // Make API call or filter data locally
}

const editItem = (row) => {
  console.log('Edit:', row)
}

const deleteItem = (row) => {
  console.log('Delete:', row)
}

// Formatting
const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(value)
}
</script>

<template>
  <CmpCustomTable
    :data="limits"
    :columns="columns"
    :filters="filters"
    :actions="actions"
    :show-filters="true"
    :show-pagination="true"
    :show-number-column="true"
    :page-size="20"
    v-model:current-page="currentPage"
    filter-title="Advanced Filters"
    @filter="handleFilter"
  />
</template>
```

## Styling

The component uses Nuxt UI's default styling and supports dark mode out of the box. You can customize the appearance using Tailwind CSS classes.

## Icons

The component uses Lucide icons via Nuxt UI. Common icons:
- `i-lucide-pencil` - Edit
- `i-lucide-trash` - Delete
- `i-lucide-eye` - View
- `i-lucide-download` - Download
- `i-lucide-upload` - Upload
- `i-lucide-check` - Approve
- `i-lucide-x` - Reject
- `i-lucide-arrow-up-narrow-wide` - Sort ascending
- `i-lucide-arrow-down-wide-narrow` - Sort descending

Browse all icons: https://lucide.dev/icons/

## Tips

1. **Performance**: For large datasets, consider server-side pagination and filtering
2. **Formatters**: Use formatters to keep your data clean and presentation separate
3. **Actions**: Group related actions together with the nested array structure
4. **Filters**: Combine different filter types for powerful search capabilities
5. **Responsive**: The filter section automatically adapts to mobile screens

## Common Use Cases

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
formatter: (value) => {
  return new Date(value).toLocaleDateString('id-ID')
}
```

### Badge/Status Display
```javascript
formatter: (value) => {
  return value === 'active' 
    ? '<span class="text-green-600">Active</span>'
    : '<span class="text-red-600">Inactive</span>'
}
```

### Conditional Row Actions
```javascript
{
  label: 'Approve',
  icon: 'i-lucide-check',
  disabled: (row) => row.status !== 'pending',
  onSelect: (row) => approveItem(row)
}
```
