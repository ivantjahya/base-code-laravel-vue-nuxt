<script setup lang="ts">
import { ref, computed } from 'vue'
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

// Detect if user is on Mac or Windows
const isMac = computed(() => {
  return navigator.platform.toUpperCase().indexOf('MAC') >= 0
})

const modifierKey = computed(() => {
  return isMac.value ? '⌘' : 'Ctrl'
})

// Menu structure
const menuItems = ref([
  {
    name: 'Home',
    url: '/home',
    icon: 'i-lucide-house',
    code: 0,
    submenu: [],
    active: true
  },
  {
    name: 'Master Data',
    url: null,
    icon: 'i-lucide-folder-open',
    code: 1000,
    submenu: [
      {
        name: 'Limits',
        url: '/limit-management',
        icon: null,
        code: 1010,
        submenu: [],
        active: false
      },
      {
        name: 'Profiles',
        url: '/profile-management',
        icon: null,
        code: 1020,
        submenu: [],
        active: false
      },
      {
        name: 'Functional Profiles',
        url: '/functional-profile-management',
        icon: null,
        code: 1030,
        submenu: [],
        active: false
      },
      {
        name: 'Users',
        url: '/user-management',
        icon: null,
        code: 1040,
        submenu: [],
        active: false
      },
      {
        name: 'Approval Flow',
        url: '/approval-flow-management',
        icon: null,
        code: 1050,
        submenu: [],
        active: false
      },
      {
        name: 'Regional Site',
        url: '/regional-site',
        icon: null,
        code: 1060,
        submenu: [],
        active: false
      },
      {
        name: 'User Guide',
        url: '/user-guide-management',
        icon: null,
        code: 1070,
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'New Registration',
    url: null,
    icon: 'i-lucide-file-pen',
    code: 2000,
    submenu: [
      {
        name: 'Article',
        url: '/article',
        icon: null,
        code: 2010,
        submenu: [],
        active: false
      },
      {
        name: 'Supplier',
        url: '/supplier',
        icon: null,
        code: 2020,
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'Purchase Order',
    url: null,
    icon: 'i-lucide-baggage-claim',
    code: 3000,
    submenu: [
      {
        name: 'PO Status Report',
        url: '/po-status-report',
        icon: null,
        code: 3010,
        submenu: [],
        active: false
      },
      {
        name: 'PO List',
        url: '/po-list',
        icon: null,
        code: 3020,
        submenu: [],
        active: false
      },
      {
        name: 'PO Cross Dock',
        url: '/po-cross-dock',
        icon: null,
        code: 3030,
        submenu: [],
        active: false
      },
      {
        name: 'Return',
        url: '/return',
        icon: null,
        code: 3040,
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'Consignment',
    url: null,
    icon: 'i-lucide-handshake',
    code: 4000,
    submenu: [
      {
        name: 'Pengajuan Retur',
        url: '/pengajuan-retur',
        icon: null,
        code: 4010,
        submenu: [],
        active: false
      },
      {
        name: 'Pengajuan Acara',
        url: '/pengajuan-acara',
        icon: null,
        code: 4020,
        submenu: [],
        active: false
      },
      {
        name: 'Upload Brand Store',
        url: '/upload-brand-store',
        icon: null,
        code: 4030,
        submenu: [],
        active: false
      },
      {
        name: 'Surat Acara',
        url: '/surat-acara',
        icon: null,
        code: 4040,
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'Principal Report',
    url: null,
    icon: 'i-lucide-file-chart-column',
    code: 5000,
    submenu: [
      {
        name: 'Stock',
        url: '/stock-report',
        icon: null,
        code: 5010,
        submenu: [],
        active: false
      },
      {
        name: 'Sales',
        url: '/sales-report',
        icon: null,
        code: 5020,
        submenu: [],
        active: false
      },
      {
        name: 'Frozen for Purchase',
        url: '/frozen-for-purchase-report',
        icon: null,
        code: 5030,
        submenu: [],
        active: false
      },
      {
        name: 'Market Share',
        url: '/market-share-report',
        icon: null,
        code: 5040,
        submenu: [],
        active: false
      },
      {
        name: 'Merchandise Structure',
        url: '/merchandise-structure-report',
        icon: null,
        code: 5050,
        submenu: [],
        active: false
      },
      {
        name: 'Sell Out By Month',
        url: '/sell-out-by-month-report',
        icon: null,
        code: 5060,
        submenu: [],
        active: false
      },
      {
        name: 'Sell Out By Div Month',
        url: '/sell-out-by-div-month-report',
        icon: null,
        code: 5070,
        submenu: [],
        active: false
      },
      {
        name: 'Sell Out By Div By Cat By Brand',
        url: '/sell-out-by-div-by-cat-by-brand-report',
        icon: null,
        code: 5080,
        submenu: [],
        active: false
      },
      {
        name: '50 Top SKU',
        url: '/50-top-sku-report',
        icon: null,
        code: 5090,
        submenu: [],
        active: false
      },
      {
        name: 'Sell In By Month',
        url: '/sell-in-by-month-report',
        icon: null,
        code: 5100,
        submenu: [],
        active: false
      },
      {
        name: 'Sell In By Div Month',
        url: '/sell-in-by-div-month-report',
        icon: null,
        code: 5110,
        submenu: [],
        active: false
      },
      {
        name: 'SL By Month',
        url: '/sl-by-month-report',
        icon: null,
        code: 5120,
        submenu: [],
        active: false
      },
      {
        name: 'SL By Div Month',
        url: '/sl-by-div-month-report',
        icon: null,
        code: 5130,
        submenu: [],
        active: false
      },
      {
        name: 'SL Distributor By Div Month',
        url: '/sl-distributor-by-div-by-month-report',
        icon: null,
        code: 5140,
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'Finance',
    url: null,
    icon: 'i-lucide-wallet',
    code: 6000,
    submenu: [
      {
        name: 'Payment Check',
        url: '/payment-check',
        icon: null,
        code: 6010,
        submenu: [],
        active: false
      },
      {
        name: 'Invoice Input Putus',
        url: '/invoice-input-putus',
        icon: null,
        code: 6020,
        submenu: [],
        active: false
      },
      {
        name: 'Invoice Consignment',
        url: '/invoice-consignment',
        icon: null,
        code: 6030,
        submenu: [],
        active: false
      },
      {
        name: 'PO Kontra Bon',
        url: '/po-kontra-bon',
        icon: null,
        code: 6040,
        submenu: [],
        active: false
      },
      {
        name: 'Data Retur Coretax',
        url: '/data-retur-coretax',
        icon: null,
        code: 6050,
        submenu: [],
        active: false
      },
      {
        name: 'Report Payment',
        url: '/report-payment',
        icon: null,
        code: 6060,
        submenu: [],
        active: false
      },
      {
        name: 'Tax Supplier Data',
        url: '/tax-supplier-data',
        icon: null,
        code: 6070,
        submenu: [],
        active: false
      },
      {
        name: 'Billing',
        url: '/billing',
        icon: null,
        code: 6080,
        submenu: [],
        active: false
      },
    ],
    active: false
  },
  {
    name: 'DC Fee',
    url: null,
    icon: 'i-lucide-truck',
    code: 7000,
    submenu: [
      {
        name: 'DC List',
        url: '/dc-list',
        icon: null,
        code: 7010,
        submenu: [],
        active: false
      },
      {
        name: 'Handling Fee',
        url: '/handling-fee',
        icon: null,
        code: 7020,
        submenu: [],
        active: false
      },
      {
        name: 'Distribution Fee',
        url: '/distribution-fee',
        icon: null,
        code: 7030,
        submenu: [],
        active: false
      },
      {
        name: 'DC Fee Supplier',
        url: '/dc-fee-supplier',
        icon: null,
        code: 7040,
        submenu: [],
        active: false
      },
      {
        name: 'Set Handling Method',
        url: '/set-handling-method',
        icon: null,
        code: 7050,
        submenu: [],
        active: false
      },
      {
        name: 'DC Fee Validation',
        url: '/dc-fee-validation',
        icon: null,
        code: 7060,
        submenu: [],
        active: false
      },
      {
        name: 'DC Fee By Receiving',
        url: '/dc-fee-by-receiving',
        icon: null,
        code: 7070,
        submenu: [],
        active: false
      },
    ],
    active: false
  },
])

const expandedMenus = ref<number[]>([0]) // Consignment menu expanded by default
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

const navigateTo = (url: string | null) => {
  if (url) {
    window.location.href = url
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
    <div v-if="!isCollapsed" class="max-w-md mx-3">
      <UInput
        v-model="searchQuery"
        icon="i-lucide-search"
        size="md"
        :placeholder="t('text.input-field.search') || 'Search...'"
        :ui="{ icon: { trailing: { pointer: '' } } }"
      >
        <template #trailing>
          <div class="flex items-center gap-1 text-xs text-gray-400">
            <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-200 rounded">{{ modifierKey }}</kbd>
            <kbd class="px-1.5 py-0.5 bg-gray-100 border border-gray-200 rounded">K</kbd>
          </div>
        </template>
      </UInput>
    </div>
    <div v-if="isCollapsed" class="flex justify-center py-2">
      <UIcon
        name="i-lucide-search"
        :class="[
          'w-5 h-5 flex-shrink-0 transition-colors text-[#9F9FA9] dark:text-gray-400 group-hover:text-gray-700 dark:group-hover:text-gray-300',
        ]"
      />
    </div>

    <!-- Menu Items -->
    <div class="flex-1 overflow-y-auto py-2 px-3">
      <nav class="space-y-0.5">
        <div v-for="(item, index) in menuItems" :key="index">
          <!-- Menu Item -->
          <button
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
            <span v-if="!isCollapsed" class="flex-1 text-left">{{ item.name }}</span>
            <UIcon 
              v-if="!isCollapsed && item.submenu && item.submenu.length > 0"
              :name="isExpanded(index) ? 'i-heroicons-chevron-down' : 'i-heroicons-chevron-right'"
              :class="[
                'w-4 h-4 flex-shrink-0 transition-transform duration-200',
                item.active || isExpanded(index) ? 'text-gray-700 dark:text-gray-300' : 'text-gray-400 dark:text-gray-500'
              ]"
            />
          </button>

          <!-- Submenu Items -->
          <div 
            v-if="item.submenu && isExpanded(index) && !isCollapsed && item.submenu.length > 0"
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
              <span class="text-left truncate">{{ child.name }}</span>
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
          src="https://ui-avatars.com/api/?name=Username&background=f97316&color=fff"
          size="sm"
        />
      </div>
    </div>
  </div>
</template>
