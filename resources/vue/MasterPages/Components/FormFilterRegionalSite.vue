<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import type { CalendarDate } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'

const { t } = useI18n()

const profileSourceValueFilter = ref(['Internal', 'External'])
const statusValueFilter = ref(['Active', 'Not Active'])

const props = defineProps({
    profileCode: {
        type: String,
        default: ''
    },
    profileName: {
        type: String,
        default: ''
    },
    profileSource: {
        type: Array as () => string[],
        default: () => []
    },
    status: {
        type: Array as () => string[],
        default: () => []
    },
    loading: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:profileCode', 'update:profileName', 'update:profileSource', 'update:status', 'clear', 'find'])

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

            <!-- PROFILE CODE -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.profile-code') || 'Profile Code' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="profileCode"
                        @update:model-value="$emit('update:profileCode', $event)"
                        :placeholder="t('text.input-field.profile-code-placeholder') || 'Enter profile code'"
                        size="md"
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>

            </div>

            <div class="px-2"></div>

            <!-- PROFILE SOURCE -->
            <div class="flex w-full">

                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.profile-source') || 'Profile Source' }}</div>
                <div class="flex w-full text-sm">
                    <USelectMenu
                        :model-value="profileSource"
                        @update:model-value="$emit('update:profileSource', $event)"
                        :items="profileSourceValueFilter"
                        :placeholder="t('text.input-field.profile-source-placeholder') || 'Select profile source'"
                        class="w-full font-reguler"
                    />
                </div>

            </div>

        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <!-- PROFILE NAME -->
            <div class="flex w-full">

                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.profile-name') || 'Profile Name' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="profileName"
                        @update:model-value="$emit('update:profileName', $event)"
                        :placeholder="t('text.input-field.profile-name-placeholder') || 'Enter profile name'"
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
                        :items="statusValueFilter"
                        :placeholder="t('text.input-field.status-placeholder') || 'Select status'"
                        class="w-full font-reguler"/>

                </div>

            </div>

        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <div class="flex w-full"></div>

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
