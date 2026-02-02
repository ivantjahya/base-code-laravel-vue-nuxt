<script setup lang="ts">
import { ref } from 'vue'
import { useI18n } from '../composables/useI18n'
import { localeOptions } from '../composables/useLocale'
import { useWebStore } from '../AppRouter'

const { t, locale, setLocale } = useI18n()
const web = useWebStore()

const props = defineProps<{
  title: string
  isMenuCollapsed: boolean
}>()

const emit = defineEmits<{
  'toggle-menu': []
}>()

const selectedLocale = ref(locale.value)

const onChangeLocale = (value: any) => {
  const newLocale = value?.id || value
  if (newLocale === 'en' || newLocale === 'id') {
    setLocale(newLocale)
    selectedLocale.value = newLocale
  }
}

const toggleMenu = () => {
  emit('toggle-menu')
}
</script>

<template>
  <header class="h-16 bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between px-6 flex-shrink-0">
    <!-- Left Section -->
    <div class="flex items-center gap-4 flex-1">
      <!-- Menu Toggle Button -->
      <UButton
        color="gray"
        variant="ghost"
        icon="i-lucide-panel-right-open"
        size="lg"
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
