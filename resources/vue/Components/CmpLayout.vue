<script setup lang="ts">
import { ref, provide } from 'vue'
import CmpHeader from './CmpHeader.vue'
import CmpMenu from './CmpMenu.vue'

const props = defineProps<{
  title?: string
}>()

const isMenuCollapsed = ref(false)

const toggleMenu = () => {
  isMenuCollapsed.value = !isMenuCollapsed.value
}

// Provide collapse state to children if needed
provide('isMenuCollapsed', isMenuCollapsed)
</script>

<template>
  <div class="flex h-screen overflow-hidden bg-gray-50 dark:bg-gray-950">
    <!-- Sidebar Menu -->
    <CmpMenu :isCollapsed="isMenuCollapsed" @update:isCollapsed="isMenuCollapsed = $event" />
    
    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col overflow-hidden">
      <!-- Header -->
      <CmpHeader 
        :title="title || 'Home'" 
        :isMenuCollapsed="isMenuCollapsed"
        @toggle-menu="toggleMenu"
      />
      
      <!-- Page Content -->
      <main class="flex-1 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
