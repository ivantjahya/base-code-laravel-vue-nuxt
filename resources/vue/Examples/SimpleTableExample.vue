<script setup lang="ts">
/**
 * SIMPLE TABLE EXAMPLE
 * 
 * This is the minimal implementation of CmpCustomTable.
 * Perfect for getting started quickly!
 */

import { ref } from 'vue'
import CmpCustomTable from '../Components/CmpCustomTable.vue'

// Current page state
const currentPage = ref(1)

// Your data
const users = ref([
  { id: 1, name: 'John Doe', email: 'john@example.com', role: 'Admin', status: 'Active' },
  { id: 2, name: 'Jane Smith', email: 'jane@example.com', role: 'User', status: 'Active' },
  { id: 3, name: 'Bob Johnson', email: 'bob@example.com', role: 'User', status: 'Inactive' },
  { id: 4, name: 'Alice Williams', email: 'alice@example.com', role: 'Manager', status: 'Active' },
  { id: 5, name: 'Charlie Brown', email: 'charlie@example.com', role: 'User', status: 'Active' },
])

// Define columns
const columns = [
  { key: 'name', label: 'Name', sortable: true },
  { key: 'email', label: 'Email', sortable: true },
  { key: 'role', label: 'Role', sortable: false },
  { key: 'status', label: 'Status', sortable: true },
]

// That's it! The table is ready to use.
// The component will automatically:
// - Add row numbers
// - Enable sorting on marked columns
// - Add pagination
// - Show action menu (you can disable it with :show-actions="false")

</script>

<template>
  <div class="p-4">
    <h1 class="text-2xl font-bold mb-4">Simple Table Example</h1>
    
    <UCard>
      <!-- Minimal table setup - just data and columns! -->
      <CmpCustomTable
        :data="users"
        :columns="columns"
        v-model:current-page="currentPage"
        :show-filters="false"
        :show-actions="false"
      />
    </UCard>
  </div>
</template>
