<script setup>
import { ref } from 'vue'
import { useI18n } from '../composables/useI18n'
import CmpLayout from '../Components/CmpLayout.vue'

const { t } = useI18n()

const bgHome = new URL('@/images/bg-home.webp', import.meta.url).href
const iconVenditorePlusWhite = new URL('@/images/icon-venditore-plus-white.webp', import.meta.url).href
</script>

<template>
  <CmpLayout :title="t('menu.home') || 'Home'">
    <div class="p-3 h-full">
      <UCard class="h-full" body-class="!p-2">
        <!-- Welcome Banner -->
        <div class="relative mb-6 rounded-xl overflow-hidden h-48 bg-gradient-to-br from-gray-700 to-gray-900">
          <!-- Background Image Overlay -->
          <div class="absolute inset-0 bg-cover bg-center">
            <img :src="bgHome" class="h-full w-full object-cover" />
            <img :src="iconVenditorePlusWhite" alt="Venditore+" class="absolute top-4 right-6 h-6" />
          </div>
          
          <!-- Content -->
          <div class="relative z-10 h-full flex items-center px-10">
            <div class="flex items-center gap-4">
              <div class="text-white">
                <h1 class="text-3xl font-bold mb-2">{{ t('text.home-pg.welcome') }} Venditore<sup>+</sup></h1>
                <p class="text-gray-200 text-sm max-w-2xl">
                  {{ t('text.home-pg.welcome-msg') }}
                </p>
              </div>
            </div>
          </div>
        </div>
        <div>
          <p class="text-gray-600 dark:text-gray-400">This is a Vue component page rendered via Laravel route using Nuxt UI.</p>
        </div>
      </UCard>
    </div>
  </CmpLayout>
</template>
