<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import type { CalendarDate } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'

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
    <div class="grid grid-flow-row text-sm">
        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <!-- SITE CODE -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.site-code') || 'Site Code' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="siteCode"
                        @update:model-value="$emit('update:siteCode', $event)"
                        :placeholder="t('text.input-field.site-code-placeholder') || 'Enter site code'"
                        size="md"
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>
            </div>

            <div class="px-2"></div>

            <!-- REGIONAL NAME -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.regional-name-kontrabon') || 'Regional Name Kontrabon' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="regionalName"
                        @update:model-value="$emit('update:regionalName', $event)"
                        :placeholder="t('text.input-field.regional-name-kontrabon-placeholder') || 'Enter regional name kontrabon'"
                        size="md"
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>
            </div>

        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <!-- SITE NAME -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.site-name') || 'Site Name' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="siteName"
                        @update:model-value="$emit('update:siteName', $event)"
                        :placeholder="t('text.input-field.site-name-placeholder') || 'Enter site name'"
                        size="md"
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>
            </div>

            <div class="px-2"></div>

            <!-- STATUS -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.status') || 'Status' }}</div>
                <div class="flex w-full text-sm">
                    <USelectMenu
                        :model-value="status"
                        @update:model-value="$emit('update:status', $event)"
                        :items="statusOptions"
                        value-key="id"
                        value-attribute="id"
                        option-attribute="label"
                        :placeholder="t('text.input-field.status-placeholder') || 'Select status'"
                        class="w-full font-reguler"/>
                </div>
            </div>

        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <!-- REGIONAL CODE -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.regional-code-kontrabon') || 'Regional Code Kontrabon' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="regionalCode"
                        @update:model-value="$emit('update:regionalCode', $event)"
                        :placeholder="t('text.input-field.regional-code-kontrabon-placeholder') || 'Enter regional code kontrabon'"
                        size="md"
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>
            </div>

            <div class="px-2"></div>

            <!-- BUTTON FIND -->
            <div class="flex w-full mb-1">
                <div class="w-full md:w-50 my-auto text-base md:text-sm"></div>
                <div class="flex w-full text-sm gap-2">
                    <UButton
                        class="flex-1 w-full justify-center text-base md:text-sm text-[#F26524] hover:text-[#E34613] bg-[#FEE9D6] hover:bg-[#FBD0AD] active:bg-[#FBD0AD] active:text-[#E34613]"
                        :disabled="loading"
                        @click="onClear"
                    >{{ t('text.button.clear') || 'Clear' }}</UButton>

                    <UButton
                        class="flex-3 w-full justify-center text-base md:text-sm text-white bg-[#F26524] hover:bg-[#E34613] active:bg-[#E34613]"
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
    </div>
</template>
