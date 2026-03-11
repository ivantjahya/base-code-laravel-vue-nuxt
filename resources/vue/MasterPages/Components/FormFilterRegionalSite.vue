<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import type { CalendarDate } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'
import { TEXT_SIZE_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

const { t } = useI18n()

const props = defineProps({
    siteCode: {
        type: String,
        default: ''
    },
    siteName: {
        type: String,
        default: ''
    },
    regionalCode: {
        type: String,
        default: ''
    },
    regionalName: {
        type: String,
        default: ''
    },
    status: {
        type: Number as () => number | null,
        default: () => null
    },
    loading: {
        type: Boolean,
        default: false
    },
    statusOptions: {
        type: Array as () => Array<{ id: number; label: string }>,
        default: () => []
    },
})

const emit = defineEmits(['update:siteCode', 'update:siteName', 'update:regionalCode', 'update:regionalName', 'update:status', 'clear', 'find'])

const onClear = () => {
    emit('clear')
}

const onFind = () => {
    emit('find')
}
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-4 pb-2 text-sm">

        <!-- SITE CODE -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.site-code') || 'Site Code' }}
            </div>

            <UInput
                :model-value="siteCode"
                @update:model-value="$emit('update:siteCode', $event)"
                :placeholder="t('text.input-field.site-code-placeholder') || 'Enter site code'"
                size="md"
                class="w-full font-light"
                :ui="{
                    base: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- REGIONAL NAME -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.regional-name') || 'Regional Name' }}
            </div>

            <UInput
                :model-value="regionalName"
                @update:model-value="$emit('update:regionalName', $event)"
                :placeholder="t('text.input-field.regional-name-kontrabon-placeholder') || 'Enter regional name kontrabon'"
                size="md"
                class="w-full font-light"
                :ui="{
                    base: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- SITE NAME -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.site-name') || 'Site Name' }}
            </div>

            <UInput
                :model-value="siteName"
                @update:model-value="$emit('update:siteName', $event)"
                :placeholder="t('text.input-field.site-name-placeholder') || 'Enter site name'"
                size="md"
                class="w-full font-light"
                :ui="{
                    base: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- STATUS -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.status') || 'Status' }}
            </div>

            <USelectMenu
                :model-value="status"
                @update:model-value="$emit('update:status', $event)"
                :items="statusOptions"
                value-key="id"
                value-attribute="id"
                option-attribute="label"
                :placeholder="t('text.input-field.status-placeholder') || 'Select status'"
                class="w-full font-reguler"
                :ui="{
                    base: `truncate ${TEXT_SIZE_CLASS}`,
                    content: TEXT_SIZE_CLASS,
                    item: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- REGIONAL CODE -->
         <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.regional-code') || 'Regional Code' }}
            </div>

            <UInput
                :model-value="regionalCode"
                @update:model-value="$emit('update:regionalCode', $event)"
                :placeholder="t('text.input-field.regional-code-kontrabon-placeholder') || 'Enter regional code kontrabon'"
                size="md"
                class="w-full font-light"
                :ui="{
                    base: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- BUTTON FIND -->
        <div class="flex items-center gap-3">
            <div class="w-32 shrink-0"></div>

            <div class="flex gap-2 w-full">
                <UButton
                    class="flex-1 w-full justify-center"
                    :class="`${BUTTON_CLEAR_CLASS} ${TEXT_SIZE_CLASS}`"
                    :disabled="loading"
                    @click="onClear"
                >{{ t('text.button.clear') || 'Clear' }}</UButton>

                <UButton
                    class="flex-3 w-full justify-center"
                    :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`"
                    :loading="loading"
                    size="md"
                    icon="i-lucide-search"
                    @click="onFind"
                >
                    {{ t('text.button.find') || 'Find' }}
                </UButton>
            </div>
        </div>

    </div>
</template>
