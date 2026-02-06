<script setup lang="ts">
import { provide } from 'vue'
import { useMainStore } from '../AppState'
import CmpHeader from './CmpHeader.vue'
import CmpMenu from './CmpMenu.vue'

const props = defineProps<{
  title?: string
}>()

const mainStore = useMainStore()

// Provide collapse state to children if needed
provide('isMenuCollapsed', () => mainStore.isCollapsed)
</script>

<template>
  <div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
    <!-- Sidebar Menu -->
    <CmpMenu />
    
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <CmpHeader 
        :title="title || 'Home'"
      />
      
      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
