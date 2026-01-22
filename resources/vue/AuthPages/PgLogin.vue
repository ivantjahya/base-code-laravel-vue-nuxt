<template>
  <div class="min-h-screen flex">
    <!-- Left Side -->
    <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-gray-700 to-gray-900 relative overflow-hidden">
      <!-- Background Image Overlay -->
      <div class="absolute inset-0 bg-cover bg-center opacity-30">
        <img :src="bgLogin" class="h-full w-full object-cover" />
      </div>
      <div class="relative z-10 flex flex-col p-12 w-full">
        <!-- Yogya Logo -->
        <div class="mb-auto">
          <img :src="yogyaLogo" alt="YOGYA" class="h-8" />
        </div>

        <!-- Venditore Plus Logo -->
        <div class="flex items-center justify-center flex-1">
          <img :src="venditorePlusLogoLogin" alt="Venditore+" class="max-w-md w-full" />
        </div>
      </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-white relative">
      <!-- Language Selector -->
      <div class="absolute top-4 right-4">
        <USelectMenu
          v-model="selectedLocale"
          :items="localeOptions"
          value-key="id"
          value-attribute="id"
          option-attribute="label"
          size="sm"
          class="w-24"
          @update:model-value="onChangeLocale"
        />
      </div>

      <div class="w-full max-w-md">
        <!-- Welcome Header -->
        <div class="mb-8">
          <div class="flex items-center gap-2 mb-2">
            <img :src="venditorePlusLogo" alt="Venditore+" class="h-8 mr-2" />
            <h1 class="text-3xl font-bold text-gray-800">{{ t('text.login-pg.welcome') }}!</h1>
          </div>
          <p class="text-gray-600 text-sm">
            {{ t('text.login-pg.welcome-msg') }}
          </p>
        </div>

        <!-- Login Form -->
        <div class="space-y-6">
          <!-- Divider -->
          <div class="border-t border-gray-200"></div>

          <!-- Username Field -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ t('form.username') }}
            </label>
            <UInput
              v-model="username"
              :placeholder="t('form.username-placeholder')"
              size="lg"
              class="w-full"
            />
          </div>

          <!-- Password Field -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              {{ t('form.password') }}
            </label>
            <UInput
              v-model="password"
              :type="showPassword ? 'text' : 'password'"
              placeholder="••••••••"
              size="lg"
              class="w-full"
            >
              <template #trailing>
                <UButton
                  color="gray"
                  variant="link"
                  :icon="showPassword ? 'i-heroicons-eye-20-solid' : 'i-heroicons-eye-slash-20-solid'"
                  :padded="false"
                  @click="showPassword = !showPassword"
                />
              </template>
            </UInput>
          </div>

          <!-- Login Button -->
          <UButton
            type="button"
            color="gray"
            size="lg"
            block
            :loading="loading"
            class="bg-gray-800 hover:bg-gray-900 text-white"
            @click="postLoginData"
          >
            {{ t('page.login') }}
          </UButton>
        </div>

        <!-- Register Link -->
        <div class="mt-6 text-center text-sm text-gray-600">
          {{ t('text.login-pg.new-supplier-msg') }}
          <a href="/register" class="text-orange-500 hover:text-orange-600 font-medium">
            {{ t('text.login-pg.register') }}
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useI18n } from '../composables/useI18n'

// i18n
const { t, locale, setLocale } = useI18n()

// Import logos using Vite asset imports
const yogyaLogo = new URL('@/images/logo-yogya-group-white.png', import.meta.url).href
const venditorePlusLogoLogin = new URL('@/images/logo-venditore-plus-login.png', import.meta.url).href
const venditorePlusLogo = new URL('@/images/logo-venditore-plus.png', import.meta.url).href
const bgLogin = new URL('@/images/bg-login.png', import.meta.url).href

// Locale options
const localeOptions = [
  { label: 'English', id: 'en' },
  { label: 'Indonesia', id: 'id' },
]

// Form state
const username = ref('')
const password = ref('')
const showPassword = ref(false)
const loading = ref(false)
const selectedLocale = ref(locale.value)

const onChangeLocale = (value) => {
  const newLocale = value?.id || value
  if (newLocale === 'en' || newLocale === 'id') {
    setLocale(newLocale)
    selectedLocale.value = newLocale
  }
}

// Handle login
const postLoginData = async () => {
  loading.value = true
  
  try {
    // TODO: Implement your login logic here
    console.log('Login attempt:', { username: username.value, password: password.value })
    
    // Simulate API call
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Handle successful login
    // window.location.href = '/dashboard'
  } catch (error) {
    console.error('Login error:', error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Additional custom styles if needed */
</style>
