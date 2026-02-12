<script setup lang="ts">
import { useI18n } from '../composables/useI18n'

const { t } = useI18n()

defineProps({
    showLoading: Boolean,
    loading: Boolean,
    label: String
})
</script>

<template>
    <div class="relative w-full h-full">
        <div :class="showLoading && loading ? 'opacity-80 pointer-events-none transition-opacity' : ''">
            <slot />
        </div>

        <div 
            v-if="showLoading && loading"
            class="absolute inset-0 z-10 flex items-center justify-center bg-white/60 dark:bg-gray-900/60 backdrop-blur-[2px]"
        >
            <div class="flex flex-col items-center gap-2">
                <UIcon name="i-lucide-loader-2" class="w-10 h-10 animate-spin text-primary-500" />
                <span class="text-sm font-medium">{{ label || t('text.message.loading') }}</span>
            </div>
        </div>
    </div>
</template>
