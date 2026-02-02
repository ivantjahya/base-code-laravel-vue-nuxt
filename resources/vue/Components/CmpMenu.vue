<script setup lang="ts">
import { ref } from 'vue'
import { useI18n } from '../composables/useI18n'
import { useMainStore } from '../AppState'
import Swal from 'sweetalert2'

const { t } = useI18n()
const mainStore = useMainStore()

const props = defineProps<{
  isCollapsed: boolean
}>()

const emit = defineEmits<{
  'update:isCollapsed': [value: boolean]
}>()

const venditorePlusLogoHorizontal = new URL('@/images/logo-venditore-plus-horizontal.webp', import.meta.url).href
const venditorePlusLogoHorizontalWhite = new URL('@/images/logo-venditore-plus-horizontal-white.webp', import.meta.url).href
const venditorePlusLogo = new URL('@/images/icon-venditore-plus.webp', import.meta.url).href

// Menu structure
const menuItems = ref([
  {
    label: 'Home',
    icon: 'i-lucide-house',
    route: '/home',
    active: true
  },
  {
    label: 'Master Data',
    icon: 'i-lucide-folder-open',
    route: '/master-data',
    active: false
  },
  {
    label: 'Purchase Order',
    icon: 'i-lucide-baggage-claim',
    route: '/purchase-order',
    active: false
  },
  {
    label: 'Finance',
    icon: 'i-lucide-wallet',
    route: '/finance',
    active: false,
    children: [
      { label: 'Payment Check', icon: null, route: '/payment-check', active: false },
      { label: 'Invoice Putus', icon: null, route: '/invoice-putus', active: false },
      { label: 'Invoice Consignment', icon: null, route: '/invoice-consignment', active: false },
      { label: 'PO Kontra Bon', icon: null, route: '/po-kontra-bon', active: false },
      { label: 'Coretax Return Data', icon: null, route: '/coretax-return', active: false },
      { label: 'Payment Report', icon: null, route: '/payment-report', active: false },
      { label: 'Billing', icon: null, route: '/billing', active: false }
    ]
  },
  {
    label: 'DC Fee',
    icon: 'i-lucide-truck',
    route: '/dc-fee',
    active: false
  }
])

const expandedMenus = ref<number[]>([3]) // Finance menu expanded by default
const searchQuery = ref('')

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
  <div 
    :class="[
      'h-screen bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-700 flex flex-col transition-all duration-300 overflow-hidden',
      isCollapsed ? 'w-16' : 'w-60'
    ]"
  >
    <!-- Logo Section -->
    <div class="h-16 flex items-center justify-center px-4 flex-shrink-0">
      <div v-if="!isCollapsed" class="flex items-center gap-2">
        <img :src="mainStore.mode === 'dark' ? venditorePlusLogoHorizontalWhite : venditorePlusLogoHorizontal" alt="Venditore+" class="h-6" />
      </div>
      <div v-else class="text-orange-500 text-2xl font-bold">
        <img :src="venditorePlusLogo" alt="Venditore+" class="h-6" />
      </div>
    </div>

    <!-- Search -->
    <div class="max-w-md mx-3">
      <UInput
        v-model="searchQuery"
        icon="i-heroicons-magnifying-glass"
        size="md"
        :placeholder="t('form.search') || 'Search...'"
        :ui="{ icon: { trailing: { pointer: '' } } }"
      >
        <template #trailing>
          <div class="flex items-center gap-1 text-xs text-gray-400">
            <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-200 rounded">⌘</kbd>
            <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-200 rounded">K</kbd>
          </div>
        </template>
      </UInput>
    </div>

    <!-- Menu Items -->
    <div class="flex-1 overflow-y-auto py-2 px-3">
      <nav class="space-y-0.5">
        <div v-for="(item, index) in menuItems" :key="index">
          <!-- Menu Item -->
          <button
            @click="item.children ? toggleMenu(index) : null"
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
            <span v-if="!isCollapsed" class="flex-1 text-left">{{ item.label }}</span>
            <UIcon 
              v-if="!isCollapsed && item.children"
              :name="isExpanded(index) ? 'i-heroicons-chevron-down' : 'i-heroicons-chevron-right'"
              :class="[
                'w-4 h-4 flex-shrink-0 transition-transform duration-200',
                item.active || isExpanded(index) ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500'
              ]"
            />
          </button>

          <!-- Submenu Items -->
          <div 
            v-if="item.children && isExpanded(index) && !isCollapsed"
            class="mt-0.5 mb-1 ml-5 space-y-0.5 relative pl-2.5 border-l-2 border-gray-200 dark:border-gray-700"
          >
            <button
              v-for="(child, childIndex) in item.children"
              :key="childIndex"
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
              <span class="text-left truncate">{{ child.label }}</span>
            </button>
          </div>
        </div>
      </nav>
    </div>

    <!-- User Profile Section -->
    <div class="border-t border-gray-200 dark:border-gray-700 p-3 flex-shrink-0">
      <div v-if="!isCollapsed">
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
                src="https://ui-avatars.com/api/?name=Username&background=f97316&color=fff" 
                size="sm"
              />
              <div class="flex-1 min-w-0 text-left">
                <p class="text-sm font-medium text-gray-900 dark:text-gray-100 truncate">Username</p>
              </div>
              <UIcon name="i-lucide-chevrons-up-down" class="w-4 h-4 text-gray-400 dark:text-gray-500" />
            </button>
          </template>
        </UDropdownMenu>
      </div>
      <div v-else class="flex justify-center">
        <UAvatar 
          src="https://ui-avatars.com/api/?name=Kenken+Tjahjadi&background=f97316&color=fff" 
          size="sm"
        />
      </div>
    </div>
  </div>
</template>
