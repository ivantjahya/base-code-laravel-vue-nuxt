<script setup>
import { ref } from 'vue'
import { useI18n } from '../composables/useI18n'
import { useCmpToast } from '../composables/useCmpToast.ts'

const { t, locale, setLocale } = useI18n()
const count = ref(0)
const name = ref('')
const { toastDisplay } = useCmpToast()

const increment = () => {
  count.value++
  toastDisplay({
    type: 'success',
    title: 'Counter Incremented',
    description: `The counter is now at ${count.value}.`,
    color: 'primary',
    icon: 'i-heroicons-plus-circle',
  })
}

const decrement = () => {
  count.value--
  toastDisplay({
    type: 'success',
    title: 'Counter Decremented',
    description: `The counter is now at ${count.value}.`,
    color: 'warning',
    icon: 'i-heroicons-minus-circle',
  })
}

const reset = () => {
  count.value = 0
}

const isExpanded = ref(false)
const localeOptions = [
  { label: 'English', id: 'en' },
  { label: 'Indonesia', id: 'id' },
]
const selectedLocale = ref(locale.value)

const onChangeLocale = (value) => {
  const newLocale = value?.id || value
  if (newLocale === 'en' || newLocale === 'id') {
    setLocale(newLocale)
    selectedLocale.value = newLocale
  }
}
</script>

<template>
  <div class="container mx-auto p-6">
    <h1 class="text-3xl font-bold mb-4">Welcome to Dashboard Page</h1>
    <p class="text-gray-600 mb-4">This is a Vue component page rendered via Laravel route using Nuxt UI.</p>
    
    <UCard class="mb-4">
      <template #header>
        <h3 class="text-lg font-semibold">Counter Example</h3>
      </template>
      
      <div class="space-y-4">
        <p class="text-2xl font-bold">Counter: {{ count }}</p>
        
        <div class="flex gap-2">
          <UButton @click="increment" color="primary">
            Increment
          </UButton>
          <UButton @click="decrement" color="gray">
            Decrement
          </UButton>
          <UButton @click="reset" color="red" variant="soft">
            Reset
          </UButton>
        </div>
      </div>
    </UCard>

    <UCard>
      <template #header>
        <h3 class="text-lg font-semibold">Nuxt UI Components Demo</h3>
      </template>
      
      <div class="space-y-4">
        <UInput v-model="name" placeholder="Enter your name" />
        <p v-if="name">Hello, {{ name }}! 👋</p>
        
        <UBadge color="green">Success</UBadge>
        <UBadge color="yellow">Warning</UBadge>
        <UBadge color="red">Error</UBadge>
      </div>
    </UCard>

    <UCard>
      <template #header>
        <h3 class="text-lg font-semibold">Nuxt UI Testing Components</h3>
      </template>
      
      <div class="space-y-4">
        <UButton
          color="primary"
          variant="solid"
          size="md"
          icon="i-heroicons-pencil-square"
          :label="isExpanded ? 'Edit Document' : ''"
          :ui="{ 
            base: 'transition-all duration-500 ease-in-out px-2 gap-1',
            // Ensure icon doesn't shrink awkwardly
            icon: 'shrink-0' 
          }"
          @click="isExpanded = !isExpanded"
        >
          <!-- Optional: Custom trailing icon that rotates when open -->
          <template #trailing>
            <UIcon
              name="i-heroicons-chevron-right-20-solid"
              :class="['transition-transform duration-500', open && 'rotate-90']"
            />
          </template>
        </UButton>
        <USelectMenu
          v-model="selectedLocale"
          :items="localeOptions"
          value-key="id"
          value-attribute="id"
          option-attribute="label"
          size="sm"
          class="w-24 ml-2"
          @update:model-value="onChangeLocale"
        />
        <USelectMenu
          v-model="selectedLocale"
          :items="localeOptions"
          value-key="id"
          value-attribute="id"
          option-attribute="label"
          size="xs"
          class="ml-2"
          @update:modelValue="onChangeLocale"
          :popper="{
            placement: 'bottom-start',
            modifiers: [{ name: 'flip', enabled: false }]
          }"
        >
          <!-- Customize the trigger button -->
          <template #default="{ open }">
            <UButton
              color="white"
              icon="i-heroicons-globe-alt"
              size="sm"
              :class="[
                'transition-all duration-500 ease-in-out overflow-hidden whitespace-nowrap px-0',
                open ? 'w-40' : 'w-10'
              ]"
            >
              <span v-if="!open">{{ selectedLocale }}</span>
              <span v-if="open" class="ml-2">{{ t('text.dropdown.select-language') }}</span>
            </UButton>
          </template>
        </USelectMenu>
      </div>
    </UCard>
  </div>
</template>
