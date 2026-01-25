<script setup lang="ts">
import axios from 'axios';
import { ref, computed } from 'vue'
import { useI18n } from '../composables/useI18n'
import { useToast } from '../composables/useToast'
import { useWebApiStore } from '../AppState'
import { useWebStore } from '../AppRouter'

const web = useWebStore();
const webapi = useWebApiStore();

// i18n
const { t, locale, setLocale } = useI18n()

// Toast
const { showSuccess, showError } = useToast()

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

const onChangeLocale = (value: any) => {
  const newLocale = value?.id || value
  if (newLocale === 'en' || newLocale === 'id') {
    setLocale(newLocale)
    selectedLocale.value = newLocale
  }
}

// Handle login
const postLoginData = async () => {
  loading.value = true

  axios
    .post(webapi.postLogin, {
        username: username.value,
        password: password.value,
    })
    .then((response) => {
        clearData();
        console.log('Login successful:', response.data);
        
        // Show success toast
        showSuccess(
          response.data.title || 'Login Successful',
          response.data.message || 'You have successfully logged in.'
        );
    })
    .then(() => {
        window.location.href = web.dashboard;
    })
    .catch((error) => {
        loading.value = false;
        console.log('Login error:', error);
        
        // Show error toast
        showError(
          error.response?.data?.title || 'Login Failed',
          error.response?.data?.message || 'Invalid username or password. Please try again.'
        );
    });
}

const clearData = () => {
    username.value = '';
    password.value = '';
};

</script>

<style scoped>
/* Additional custom styles if needed */
</style>

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
          size="xs"
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
                open ? 'w-22' : 'w-10'
              ]"
            >
              <span v-if="!open">{{ selectedLocale }}</span>
              <span v-if="open" class="ml-2">{{ localeOptions.find((item) => item.id === selectedLocale)?.label }}</span>
            </UButton>
          </template>
        </USelectMenu>
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
              @keyup.enter="postLoginData"
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
