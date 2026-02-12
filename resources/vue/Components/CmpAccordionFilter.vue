<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from '../composables/useI18n'

const { t } = useI18n()

const props = defineProps({
    label: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: 'i-lucide-filter'
    },
    defaultOpen: {
        type: Boolean,
        default: false
    }
})

const accordionItems = computed(() => [
    {
        label: props.label || t('text.input-field.more-filters'),
        slot: 'content',
        icon: props.icon,
        defaultOpen: props.defaultOpen
    }
])
</script>

<template>
    <div class="mb-3">
        <UAccordion
            :items="accordionItems"
            class="w-full border-1 border-gray-200 dark:border-gray-700 rounded-lg px-4"
            :ui="{
                wrapper: 'w-full',
                item: {
                    base: 'border-1 border-gray-200 dark:border-gray-700 rounded-lg overflow-hidden',
                }
            }"
        >
            <template #content>
                <div class="py-2 space-y-4 bg-white dark:bg-gray-900">
                    <slot />
                </div>
            </template>
        </UAccordion>
    </div>
</template>
