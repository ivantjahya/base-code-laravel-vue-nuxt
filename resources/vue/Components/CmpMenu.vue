<script setup lang="ts">
import { ref, computed, onMounted, onUnmounted, watch } from 'vue'
import { useRouter, useRoute } from 'vue-router' // Change reloads page
import { useI18n } from '../composables/useI18n'
import { useMainStore } from '../AppState'
import Swal from 'sweetalert2'
import CmpDialogMenuSearch from './CmpDialogMenuSearch.vue'

const { t } = useI18n()
const mainStore = useMainStore()
const router = useRouter()  // Change reloads page
const route = useRoute()  // Change reloads page

const venditorePlusLogoHorizontal = new URL('@/images/logo-venditore-plus-horizontal.webp', import.meta.url).href
const venditorePlusLogoHorizontalWhite = new URL('@/images/logo-venditore-plus-horizontal-white.webp', import.meta.url).href
const venditorePlusLogo = new URL('@/images/icon-venditore-plus.webp', import.meta.url).href

// Detect if user is on Mac or Windows
const isMac = computed(() => {
  return navigator.platform.toUpperCase().indexOf('MAC') >= 0
})

const modifierKey = computed(() => {
  return isMac.value ? '⌘' : 'Ctrl'
})

// Menu structure
const menuItems = computed(() => mainStore.accessMenuList)


const expandedMenus = ref<number[]>([]) // Will be set based on active menu
const searchQuery = ref('')
const isDialogMenuSearchOpen = ref(false)

// Function to set active menu based on current URL
const setActiveMenu = () => {
  // const currentPath = window.location.pathname // Change reloads page
  const currentPath = route.path
  
  // Reset all active states
  menuItems.value.forEach(item => {
    item.active = false
    if (item.submenu && item.submenu.length > 0) {
      item.submenu.forEach(child => {
        child.active = false
      })
    }
  })
  
  // Find and set active menu
  menuItems.value.forEach((item, index) => {
    // Check if current item URL matches
    if (item.url && currentPath === item.url) {
      item.active = true
      return
    }
    
    // Check submenu items
    if (item.submenu && item.submenu.length > 0) {
      const activeChild = item.submenu.find(child => child.url && currentPath === child.url)
      if (activeChild) {
        activeChild.active = true
        item.active = true // Also mark parent as active
        // Expand the parent menu
        if (!expandedMenus.value.includes(index)) {
          expandedMenus.value.push(index)
        }
      }
    }
  })
}

// Handle keyboard shortcut for dialog menu search
const handleKeydown = (event: KeyboardEvent) => {
  // Check for Ctrl+K (Windows/Linux) or Cmd+K (Mac)
  if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
    event.preventDefault()
    isDialogMenuSearchOpen.value = true
  }
}

// Open dialog menu search
const openDialogMenuSearch = () => {
  isDialogMenuSearchOpen.value = true
}

// Close dialog menu search
const closeDialogMenuSearch = () => {
  isDialogMenuSearchOpen.value = false
}

// Handle navigation from dialog menu search
const handleDialogMenuSearchNavigation = (url: string) => {
  navigateTo(url)
}

// Watch route changes to update active menu
watch(() => route.path, () => {
  setActiveMenu()
})

// Watch menu items specifically to handle async data loading (no deep watch to avoid infinite loop)
watch(() => mainStore.accessMenuList, () => {
  setActiveMenu()
})

// Call setActiveMenu on mount
onMounted(() => {
  setActiveMenu()
  // Add keyboard event listener
  window.addEventListener('keydown', handleKeydown)
})

// Remove keyboard event listener on unmount
onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown)
})

const toggleMenu = (index: number) => {
  const idx = expandedMenus.value.indexOf(index)
  if (idx > -1) {
    expandedMenus.value.splice(idx, 1)
  } else {
    expandedMenus.value.push(index)
  }
}

const isExpanded = (index: number) => {
  return expandedMenus.value.includes(index)
}

const navigateTo = (url: string | null) => {
  if (url) {
    // window.location.href = url // Change reloads page
    router.push(url)
  }
}

const postLogout = async () => {
  try {
    window.location.href = '/get-logout'
  } catch (error) {
    console.error('Logout failed:', error)
  }
}

const showProfile = () => {
  Swal.fire({
    title: 'User Profile',
    html: `
      <div class="text-left">
        <p><strong>Name:</strong> Username</p>
        <p><strong>Email:</strong> user@example.com</p>
        <p><strong>Role:</strong> Administrator</p>
      </div>
    `,
    icon: 'info',
    confirmButtonText: 'Close',
    confirmButtonColor: '#f97316',
    allowOutsideClick: false
  })
}

const showChangePassword = () => {
  Swal.fire({
    title: 'Change Password',
    html: `
      <input type="password" id="currentPassword" class="swal2-input" placeholder="Current Password">
      <input type="password" id="newPassword" class="swal2-input" placeholder="New Password">
      <input type="password" id="confirmPassword" class="swal2-input" placeholder="Confirm New Password">
    `,
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'Change Password',
    confirmButtonColor: '#f97316',
    cancelButtonText: 'Cancel',
    preConfirm: () => {
      const currentPassword = (document.getElementById('currentPassword') as HTMLInputElement)?.value
      const newPassword = (document.getElementById('newPassword') as HTMLInputElement)?.value
      const confirmPassword = (document.getElementById('confirmPassword') as HTMLInputElement)?.value

      if (!currentPassword || !newPassword || !confirmPassword) {
        Swal.showValidationMessage('Please fill in all fields')
        return false
      }

      if (newPassword !== confirmPassword) {
        Swal.showValidationMessage('New passwords do not match')
        return false
      }

      if (newPassword.length < 8) {
        Swal.showValidationMessage('Password must be at least 8 characters')
        return false
      }

      return { currentPassword, newPassword }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      // Here you would make an API call to change the password
      Swal.fire({
        title: 'Success!',
        text: 'Your password has been changed successfully',
        icon: 'success',
        confirmButtonColor: '#f97316'
      })
    }
  })
}
</script>

<template>
  <!-- Dialog Menu Search -->
  <CmpDialogMenuSearch
    :menu-items="menuItems"
    :is-open="isDialogMenuSearchOpen"
    @close="closeDialogMenuSearch"
    @navigate="handleDialogMenuSearchNavigation"
  />
  <div 
    :class="[
      'h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-all duration-300 overflow-hidden',
      mainStore.isCollapsed ? 'w-16' : 'w-60'
    ]"
  >
    <!-- Logo Section -->
    <div class="h-16 flex items-center justify-center px-4 flex-shrink-0">
      <div v-if="!mainStore.isCollapsed" class="flex items-center gap-2">
        <img :src="mainStore.mode === 'dark' ? venditorePlusLogoHorizontalWhite : venditorePlusLogoHorizontal" alt="Venditore+" class="h-6" />
      </div>
      <div v-else class="text-orange-500 text-2xl font-bold">
        <img :src="venditorePlusLogo" alt="Venditore+" class="h-6" />
      </div>
    </div>

    <!-- Search -->
    <div v-if="!mainStore.isCollapsed" class="max-w-md mx-3">
      <button 
        @click="openDialogMenuSearch"
        class="w-full text-left"
        type="button"
      >
        <UInput
          :model-value="searchQuery"
          icon="i-lucide-search"
          size="md"
          :placeholder="t('text.input-field.search') || 'Search...'"
          :ui="{ icon: { trailing: { pointer: '' } } }"
          readonly
          class="cursor-pointer"
        >
          <template #trailing>
            <div class="flex items-center gap-1 text-xs text-gray-400">
              <kbd class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded">{{ modifierKey }}</kbd>
              <kbd class="px-1.5 py-0.5 bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded">K</kbd>
            </div>
          </template>
        </UInput>
      </button>
    </div>
    <div v-if="mainStore.isCollapsed" class="flex justify-center py-2">
      <UPopover 
        v-if="mainStore.isCollapsed"
        mode="hover" 
        :open-delay="200"
        :close-delay="100"
        :content="{ side: 'right', align: 'start', sideOffset: 8 }"
      >
        <button 
          @click="openDialogMenuSearch"
          class="p-2 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
          type="button"
        >
          <UIcon
            name="i-lucide-search"
            :class="[
              'w-5 h-5 flex-shrink-0 transition-colors text-[#9F9FA9] dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300',
            ]"
          />
        </button>
        <template #content>
          <div class="px-3 py-2 font-semibold text-sm text-gray-900 dark:text-gray-100">
            {{ t('text.input-field.search') }}
          </div>
        </template>
      </UPopover>
    </div>

    <!-- Menu Items -->
    <div class="flex-1 overflow-y-auto py-2 px-3">
      <nav class="space-y-0.5">
        <div v-for="(item, index) in menuItems" :key="index">
          <!-- Menu Item with Popover when collapsed -->
          <UPopover 
            v-if="mainStore.isCollapsed"
            mode="hover" 
            :open-delay="200"
            :close-delay="100"
            :content="{ side: 'right', align: 'start', sideOffset: 8 }"
          >
            <button
              @click="item.submenu && item.submenu.length > 0 ? toggleMenu(index) : navigateTo(item.url)"
              :class="[
                'w-full flex items-center justify-center gap-2.5 px-2.5 py-2 rounded-md transition-all duration-150 text-sm group',
                item.active 
                  ? 'bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 font-medium' 
                  : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100'
              ]"
            >
              <UIcon 
                :name="item.icon" 
                :class="[
                  'w-5 h-5 flex-shrink-0 transition-colors',
                  item.active ? 'text-orange-600 dark:text-orange-400' : 'text-[#9F9FA9] dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300'
                ]"
              />
            </button>
            <template #content>
              <div v-if="item.submenu && item.submenu.length > 0" class="min-w-[200px] max-w-[280px] p-2">
                <!-- Main menu item -->
                <div class="px-3 py-2 font-semibold text-sm text-gray-900 dark:text-gray-100 border-b border-gray-200 dark:border-gray-700 mb-2">
                  {{ t(item.name_code) }}
                </div>
                
                <!-- Submenu items if available -->
                <div v-if="item.submenu && item.submenu.length > 0" class="space-y-0.5">
                  <button
                    v-for="(child, childIndex) in item.submenu"
                    :key="childIndex"
                    @click="navigateTo(child.url)"
                    :class="[
                      'w-full flex items-center gap-2.5 px-3 py-2 rounded-md text-sm transition-all duration-150 group',
                      child.active 
                        ? 'bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 font-medium' 
                        : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100'
                    ]"
                  >
                    <span class="text-left truncate">{{ t(child.name_code) }}</span>
                  </button>
                </div>
                
                <!-- If no submenu, show clickable item -->
                <div v-else>
                  <button
                    @click="navigateTo(item.url)"
                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-md text-sm transition-all duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100"
                  >
                    <span class="text-left">Open {{ t(item.name_code) }}</span>
                  </button>
                </div>
              </div>
              <div v-else>
                <div class="px-3 py-2 font-semibold text-sm text-gray-900 dark:text-gray-100">
                  {{ t(item.name_code) }}
                </div>
              </div>
            </template>
          </UPopover>

          <!-- Menu Item (expanded state) -->
          <button
            v-else
            @click="item.submenu && item.submenu.length > 0 ? toggleMenu(index) : navigateTo(item.url)"
            :class="[
              'w-full flex items-center gap-2.5 px-2.5 py-2 rounded-md transition-all duration-150 text-sm group',
              item.active 
                ? 'bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 font-medium' 
                : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100'
            ]"
          >
            <UIcon 
              :name="item.icon" 
              :class="[
                'w-5 h-5 flex-shrink-0 transition-colors',
                item.active ? 'text-orange-600 dark:text-orange-400' : 'text-[#9F9FA9] dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300'
              ]"
            />
            <span class="flex-1 text-left">{{ t(item.name_code) }}</span>
            <UIcon 
              v-if="item.submenu && item.submenu.length > 0"
              :name="isExpanded(index) ? 'i-heroicons-chevron-down' : 'i-heroicons-chevron-right'"
              :class="[
                'w-4 h-4 flex-shrink-0 transition-transform duration-200',
                item.active || isExpanded(index) ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500'
              ]"
            />
          </button>
          <!-- Submenu Items (expanded state) -->
          <div 
            v-if="item.submenu && isExpanded(index) && !mainStore.isCollapsed && item.submenu.length > 0"
            class="mt-0.5 mb-1 ml-5 space-y-0.5 relative pl-2.5 border-l-2 border-gray-200 dark:border-gray-700"
          >
            <button
              v-for="(child, childIndex) in item.submenu"
              :key="childIndex"
              @click="navigateTo(child.url)"
              :class="[
                'w-full flex items-center gap-2.5 px-2.5 py-1.5 rounded-md text-sm transition-all duration-150 group',
                child.active 
                  ? 'bg-orange-50 dark:bg-orange-950/50 text-orange-600 dark:text-orange-400 font-medium' 
                  : 'text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100'
              ]"
            >
              <UIcon 
                :name="child.icon" 
                :class="[
                  'w-4 h-4 flex-shrink-0 transition-colors',
                  child.active ? 'text-orange-600 dark:text-orange-400' : 'text-[#9F9FA9] dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300'
                ]"
              />
              <span class="text-left truncate">{{ t(child.name_code) }}</span>
            </button>
          </div>
        </div>
      </nav>
    </div>

    <!-- User Profile Section -->
    <div class="border-t border-gray-200 dark:border-gray-700 p-3 flex-shrink-0">
      <div v-if="!mainStore.isCollapsed">
        <UDropdownMenu
          :items="[
            [
              {
                label: 'Profile',
                icon: 'i-lucide-circle-user-round',
                onClick: () => { showProfile() }
              },
              {
                label: 'Change Password',
                icon: 'i-lucide-lock',
                onClick: () => { showChangePassword() }
              },
              {
                label: 'Appearance',
                icon: 'i-lucide-sun-moon',
                children: [
                  [
                    {
                      label: 'Light',
                      icon: 'i-lucide-sun',
                      active: mainStore.mode === 'light',
                      onClick: () => { mainStore.setLight() }
                    },
                    {
                      label: 'Dark',
                      icon: 'i-lucide-moon',
                      active: mainStore.mode === 'dark',
                      onClick: () => { mainStore.setDark() }
                    }
                  ]
                ]
              }
            ],
            [
              {
                label: 'Logout',
                icon: 'i-lucide-log-out',
                color: 'error',
                onClick: () => {postLogout()}
              }
            ]
          ]"
          :popper="{ placement: 'top-start' }"
          :ui="{ content: 'w-(--reka-dropdown-menu-trigger-width)' }"
        >
          <template #default="{ }">
            <button class="w-full flex items-center gap-2.5 px-2.5 rounded-md hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
              <UAvatar 
                :src="`https://ui-avatars.com/api/?name=${mainStore.userName}&background=f97316&color=fff`" 
                size="sm"
              />
              <div class="flex-1 min-w-0 text-left">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">{{ mainStore.userName }}</p>
              </div>
              <UIcon name="i-lucide-chevrons-up-down" class="w-4 h-4 text-gray-400 dark:text-gray-500" />
            </button>
          </template>
        </UDropdownMenu>
      </div>
      <div v-else class="flex justify-center">
        <UPopover 
          mode="hover" 
          :open-delay="200"
          :close-delay="100"
          :content="{ side: 'right', align: 'end', sideOffset: 8 }"
        >
          <UAvatar 
            :src="`https://ui-avatars.com/api/?name=${mainStore.userName}&background=f97316&color=fff`"
            size="sm"
            class="cursor-pointer"
          />
          
          <template #content>
            <div class="min-w-[180px] p-2">
              <div class="px-3 py-2 border-b border-gray-200 dark:border-gray-700 mb-2">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ mainStore.userName }}</p>
                <p class="text-xs text-gray-500 dark:text-gray-400"></p>
              </div>
              
              <div class="space-y-0.5">
                <button
                  @click="showProfile()"
                  class="w-full flex items-center gap-2.5 px-3 py-2 rounded-md text-sm transition-all duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100"
                >
                  <UIcon name="i-lucide-circle-user-round" class="w-4 h-4" />
                  <span>Profile</span>
                </button>
                
                <button
                  @click="showChangePassword()"
                  class="w-full flex items-center gap-2.5 px-3 py-2 rounded-md text-sm transition-all duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100"
                >
                  <UIcon name="i-lucide-lock" class="w-4 h-4" />
                  <span>Change Password</span>
                </button>
                
                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                
                <button
                  @click="mainStore.mode === 'light' ? mainStore.setDark() : mainStore.setLight()"
                  class="w-full flex items-center gap-2.5 px-3 py-2 rounded-md text-sm transition-all duration-150 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 hover:text-gray-900 dark:hover:text-gray-100"
                >
                  <UIcon :name="mainStore.mode === 'light' ? 'i-lucide-moon' : 'i-lucide-sun'" class="w-4 h-4" />
                  <span>{{ mainStore.mode === 'light' ? 'Dark' : 'Light' }} Mode</span>
                </button>
                
                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>
                
                <button
                  @click="postLogout()"
                  class="w-full flex items-center gap-2.5 px-3 py-2 rounded-md text-sm transition-all duration-150 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-950/50"
                >
                  <UIcon name="i-lucide-log-out" class="w-4 h-4" />
                  <span>Logout</span>
                </button>
              </div>
            </div>
          </template>
        </UPopover>
      </div>
    </div>
  </div>
</template>
