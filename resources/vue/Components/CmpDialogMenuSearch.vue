<script setup lang="ts">
import { ref, computed, watch, nextTick } from 'vue'
import { useI18n } from '../composables/useI18n'

const { t } = useI18n()

interface MenuItem {
  name: string
  url: string | null
  icon: string
  code: number
  name_code: string
  submenu?: MenuItem[]
  active: boolean
  parent?: string
}

interface Props {
  menuItems: MenuItem[]
  isOpen: boolean
}

const props = defineProps<Props>()
const emit = defineEmits<{
  (e: 'close'): void
  (e: 'navigate', url: string): void
}>()

const searchQuery = ref('')
const selectedIndex = ref(0)
const searchInputRef = ref<HTMLInputElement | null>(null)

// Flatten all menu items including submenus for searching
const flattenedMenuItems = computed(() => {
  const items: Array<{
    name: string
    url: string
    icon: string
    name_code: string
    parent?: string
    breadcrumb: string
  }> = []
  
  props.menuItems.forEach(item => {
    // Add top-level items that have URLs
    if (item.url) {
      items.push({
        name: item.name,
        url: item.url,
        icon: item.icon,
        name_code: item.name_code,
        breadcrumb: t(item.name_code as any)
      })
    }
    
    // Add submenu items
    if (item.submenu && item.submenu.length > 0) {
      item.submenu.forEach(child => {
        if (child.url) {
          items.push({
            name: child.name,
            url: child.url,
            icon: child.icon || item.icon,
            name_code: child.name_code,
            parent: t(item.name_code as any),
            breadcrumb: `${t(item.name_code as any)} › ${t(child.name_code as any)}`
          })
        }
      })
    }
  })
  
  return items
})

// Filter menu items based on search query
const filteredMenuItems = computed(() => {
  if (!searchQuery.value.trim()) {
    return flattenedMenuItems.value
  }
  
  const query = searchQuery.value.toLowerCase()
  return flattenedMenuItems.value.filter(item => {
    const nameMatch = t(item.name_code as any).toLowerCase().includes(query)
    const breadcrumbMatch = item.breadcrumb.toLowerCase().includes(query)
    return nameMatch || breadcrumbMatch
  })
})

// Reset selected index when filtered items change
watch(filteredMenuItems, () => {
  selectedIndex.value = 0
})

// Focus search input when modal opens
watch(() => props.isOpen, (isOpen) => {
  if (isOpen) {
    nextTick(() => {
      searchInputRef.value?.focus()
    })
    searchQuery.value = ''
    selectedIndex.value = 0
  }
})

// Handle keyboard navigation
const handleKeydown = (event: KeyboardEvent) => {
  const maxIndex = filteredMenuItems.value.length - 1
  
  if (event.key === 'ArrowDown') {
    event.preventDefault()
    selectedIndex.value = Math.min(selectedIndex.value + 1, maxIndex)
    scrollToSelected()
  } else if (event.key === 'ArrowUp') {
    event.preventDefault()
    selectedIndex.value = Math.max(selectedIndex.value - 1, 0)
    scrollToSelected()
  } else if (event.key === 'Enter') {
    event.preventDefault()
    if (filteredMenuItems.value[selectedIndex.value]) {
      navigateToItem(filteredMenuItems.value[selectedIndex.value].url)
    }
  } else if (event.key === 'Escape') {
    event.preventDefault()
    emit('close')
  }
}

// Scroll selected item into view
const scrollToSelected = () => {
  nextTick(() => {
    const selectedElement = document.querySelector('.dialog-menu-search-item-selected')
    if (selectedElement) {
      selectedElement.scrollIntoView({ block: 'nearest', behavior: 'smooth' })
    }
  })
}

// Navigate to selected item
const navigateToItem = (url: string) => {
  emit('navigate', url)
  emit('close')
}

// Handle mouse hover
const handleHover = (index: number) => {
  selectedIndex.value = index
}

// Helper to translate menu item names
const translateMenuItem = (nameCode: string) => {
  return t(nameCode as any)
}

// Group menu items by parent
const groupedMenuItems = computed(() => {
  const groups: Record<string, typeof flattenedMenuItems.value> = {}
  
  flattenedMenuItems.value.forEach(item => {
    const groupName = item.parent || t('text.message.navigation' as any)
    if (!groups[groupName]) {
      groups[groupName] = []
    }
    groups[groupName].push(item)
  })
  
  return groups
})

// Filter and group menu items based on search query
const filteredGroups = computed(() => {
  if (!searchQuery.value.trim()) {
    return groupedMenuItems.value
  }
  
  const groups: Record<string, typeof flattenedMenuItems.value> = {}
  filteredMenuItems.value.forEach(item => {
    const groupName = item.parent || t('text.message.navigation' as any)
    if (!groups[groupName]) {
      groups[groupName] = []
    }
    groups[groupName].push(item)
  })
  
  return groups
})

// Calculate total filtered items count
const totalFilteredItems = computed(() => {
  return Object.values(filteredGroups.value).reduce((sum, items) => sum + items.length, 0)
})
</script>

<template>
  <Teleport to="body">
    <Transition
      enter-active-class="transition duration-200 ease-out"
      enter-from-class="opacity-0"
      enter-to-class="opacity-100"
      leave-active-class="transition duration-150 ease-in"
      leave-from-class="opacity-100"
      leave-to-class="opacity-0"
    >
      <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-start justify-center bg-black/50 backdrop-blur-sm pt-[10vh]"
        @click.self="emit('close')"
      >
        <Transition
          enter-active-class="transition duration-200 ease-out"
          enter-from-class="opacity-0 scale-95"
          enter-to-class="opacity-100 scale-100"
          leave-active-class="transition duration-150 ease-in"
          leave-from-class="opacity-100 scale-100"
          leave-to-class="opacity-0 scale-95"
        >
          <div
            v-if="isOpen"
            class="w-full max-w-2xl bg-white dark:bg-gray-900 rounded-lg shadow-2xl overflow-hidden"
            @click.stop
          >
            <!-- Header with Search -->
            <div class="relative border-b border-gray-200 dark:border-gray-700">
              <div class="flex items-center gap-3 px-4 py-3">
                <UIcon 
                  name="i-lucide-search" 
                  class="w-5 h-5 text-gray-400 dark:text-gray-500 flex-shrink-0"
                />
                <input
                  ref="searchInputRef"
                  v-model="searchQuery"
                  type="text"
                  :placeholder="t('text.input-field.search-menu-placeholder')"
                  class="flex-1 bg-transparent border-0 outline-none text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 text-sm"
                  @keydown="handleKeydown"
                />
                <button
                  @click="emit('close')"
                  class="flex-shrink-0 p-1.5 rounded-md hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                  type="button"
                >
                  <UIcon name="i-lucide-x" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
                </button>
              </div>
            </div>

            <!-- Content / Results -->
            <div class="max-h-[400px] overflow-y-auto">
              <div v-if="totalFilteredItems === 0" class="px-4 py-12 text-center text-gray-500 dark:text-gray-400">
                <UIcon name="i-lucide-search-x" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                <p class="text-sm">{{ t('text.message.no-results-found') }}</p>
              </div>
              
              <div v-else class="py-2">
                <div
                  v-for="(items, groupName) in filteredGroups"
                  :key="groupName"
                  class="mb-4 last:mb-0"
                >
                  <!-- Group Header -->
                  <div class="px-4 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                    {{ groupName }}
                  </div>
                  
                  <!-- Group Items -->
                  <div class="px-2">
                    <button
                      v-for="item in items"
                      :key="item.url"
                      @click="navigateToItem(item.url)"
                      @mouseenter="handleHover(flattenedMenuItems.indexOf(item))"
                      :class="[
                        'w-full flex items-center gap-3 px-3 py-2 rounded-md text-left transition-all duration-100 group',
                        selectedIndex === flattenedMenuItems.indexOf(item)
                          ? 'bg-orange-50 dark:bg-orange-950/50 dialog-menu-search-item-selected' 
                          : 'hover:bg-gray-50 dark:hover:bg-gray-800'
                      ]"
                      type="button"
                    >
                      <UIcon 
                        :name="item.icon" 
                        :class="[
                          'w-4 h-4 flex-shrink-0',
                          selectedIndex === flattenedMenuItems.indexOf(item)
                            ? 'text-orange-600 dark:text-orange-400' 
                            : 'text-gray-400 dark:text-gray-500'
                        ]"
                      />
                      <span :class="[
                        'flex-1 text-sm truncate',
                        selectedIndex === flattenedMenuItems.indexOf(item)
                          ? 'text-orange-600 dark:text-orange-400 font-medium' 
                          : 'text-gray-700 dark:text-gray-300'
                      ]">
                        {{ translateMenuItem(item.name_code) }}
                      </span>
                      <UIcon 
                        v-if="selectedIndex === flattenedMenuItems.indexOf(item)"
                        name="i-lucide-corner-down-left" 
                        class="w-3 h-3 flex-shrink-0 text-orange-500 dark:text-orange-400"
                      />
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 dark:border-gray-700 px-4 py-2 bg-gray-50 dark:bg-gray-800/50">
              <div class="flex items-center gap-4 text-xs text-gray-500 dark:text-gray-400">
                <div class="flex items-center gap-1.5">
                  <kbd class="px-1.5 py-0.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-xs font-mono">↑</kbd>
                  <kbd class="px-1.5 py-0.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-xs font-mono">↓</kbd>
                  <span>{{ t('text.message.to-navigate') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <kbd class="px-1.5 py-0.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-xs font-mono">↵</kbd>
                  <span>{{ t('text.message.to-select') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                  <kbd class="px-1.5 py-0.5 bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded text-xs font-mono">esc</kbd>
                  <span>{{ t('text.message.to-close') }}</span>
                </div>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </Teleport>
</template>

<style scoped>
/* Custom scrollbar for results */
.overflow-y-auto::-webkit-scrollbar {
  width: 6px;
}

.overflow-y-auto::-webkit-scrollbar-track {
  background: transparent;
}

.overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgb(209 213 219 / 0.5);
  border-radius: 3px;
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb {
  background: rgb(75 85 99 / 0.5);
}

.overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: rgb(156 163 175 / 0.7);
}

.dark .overflow-y-auto::-webkit-scrollbar-thumb:hover {
  background: rgb(107 114 128 / 0.7);
}
</style>
