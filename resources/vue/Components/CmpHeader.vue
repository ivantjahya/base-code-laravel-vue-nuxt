<script setup lang="ts">
import { ref, computed } from 'vue'
import { useI18n } from '../composables/useI18n'
import { localeOptions } from '../composables/useLocale'
import { useWebStore } from '../AppRouter'
import { useMainStore } from '../AppState'
import { TEXT_SIZE_CLASS } from '../constants'

const { t, locale, setLocale } = useI18n()
const web = useWebStore()
const mainStore = useMainStore()

const props = defineProps<{
  title: string
}>()

const selectedLocale = ref(locale.value)

const onChangeLocale = (value: any) => {
  const newLocale = value?.id || value
  if (newLocale === 'en' || newLocale === 'id') {
    setLocale(newLocale)
    selectedLocale.value = newLocale
  }
}

const selectedCompany = computed({
  get: () => mainStore.selectedCompanyId,
  set: (value: string) => mainStore.setSelectedCompany(value),
})

const selectedCompanyLabel = computed(() =>
  mainStore.companyOptions.find(o => o.value === selectedCompany.value)?.label || selectedCompany.value
)

const toggleMenu = () => {
  mainStore.toggleSidebar()
}
</script>

<template>
  <header class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6 flex-shrink-0">
    <!-- Left Section -->
    <div class="flex items-center gap-1.5 flex-1">
      <!-- Menu Toggle Button -->
      <UButton
        color="gray"
        variant="ghost"
        :icon="!mainStore.isCollapsed ? 'i-lucide-panel-right-open' : 'i-lucide-panel-left-open'"
        size="xl"
        class="text-orange-600"
        @click="toggleMenu"
      />
      
      <!-- Page Title -->
      <div class="flex items-center gap-2">
        <h1 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ title }}</h1>
      </div>
    </div>

    <!-- Right Section -->
    <div class="flex items-center gap-3 flex-1 justify-end">

      <!-- Company Selector -->
      <USelectMenu
        v-model="selectedCompany"
        :items="mainStore.companyOptions"
        value-key="value"
        value-attribute="value"
        option-attribute="label"
        :placeholder="t('text.functional-profile-management-pg.input-company-placeholder') || 'Select company'"
        icon="i-lucide-building"
        class="font-reguler"
        :ui="{
            base: `truncate ${TEXT_SIZE_CLASS}`,
            content: TEXT_SIZE_CLASS,
            item: TEXT_SIZE_CLASS
        }"
        :popper="{
          placement: 'bottom-end',
          modifiers: [{ name: 'flip', enabled: false }]
        }"
      />

      <!-- Language Selector -->
      <USelectMenu
        v-model="selectedLocale"
        :items="localeOptions"
        value-key="id"
        value-attribute="id"
        option-attribute="label"
        size="xs"
        @update:modelValue="onChangeLocale"
        :popper="{
          placement: 'bottom-start',
          modifiers: [{ name: 'flip', enabled: false }]
        }"
        :ui="{
            base: `truncate ${TEXT_SIZE_CLASS}`,
            content: TEXT_SIZE_CLASS,
            item: TEXT_SIZE_CLASS
        }"
      >
        <template #default="{ open }">
          <UButton
            color="white"
            icon="i-heroicons-globe-alt"
            size="sm"
            :class="[
              'transition-all duration-500 ease-in-out overflow-hidden whitespace-nowrap px-0',
              open ? 'w-22' : 'w-10'
            ]"
          >
            <span v-if="!open">{{ selectedLocale }}</span>
            <span v-if="open" class="ml-2">{{ localeOptions.find((item) => item.id === selectedLocale)?.label }}</span>
          </UButton>
        </template>
      </USelectMenu>

    </div>
  </header>
</template>
