<script setup>
import { ref } from 'vue'
import { useI18n } from '../composables/useI18n'

const { t } = useI18n()

const count = ref(0)
const name = ref('')
const isLoggingOut = ref(false)

const increment = () => {
  count.value++
}

const decrement = () => {
  count.value--
}

const reset = () => {
  count.value = 0
}

const postLogout = async () => {
  isLoggingOut.value = true
  try {
    window.location.href = '/get-logout'
  } catch (error) {
    console.error('Logout failed:', error)
    isLoggingOut.value = false
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

    <div class="mt-6 flex justify-end">
      <UButton
        type="button"
        color="gray"
        size="lg"
        block
        :loading="isLoggingOut"
        class="bg-gray-800 hover:bg-gray-900 text-white"
        @click="postLogout"
      >
        {{ t('page.logout') }}
      </UButton>
    </div>
  </div>
</template>
