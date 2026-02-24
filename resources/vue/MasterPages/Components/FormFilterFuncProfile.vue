<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import { useI18n } from '../../composables/useI18n'

const { t } = useI18n()

const props = defineProps({
    funcProfileCode: {
        type: String,
        default: ''
    },
    funcProfileName: {
        type: String,
        default: ''
    },
    profile: {
        type: String as () => string | null,
        default: null
    },
    division: {
        type: String as () => string | null,
        default: null
    },
    limit: {
        type: String as () => string | null,
        default: null
    },
    status: {
        type: Number as () => number | null,
        default: null
    },
    loading: {
        type: Boolean,
        default: false
    },
    limitOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    profileOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    statusOptions: {
        type: Array as () => Array<{ id: number; label: string }>,
        default: () => []
    },
    divisionOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    }
})

const emit = defineEmits(['update:funcProfileCode', 'update:funcProfileName', 'update:profile', 'update:division', 'update:limit', 'update:status', 'clear', 'find'])

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
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.functional-profile-code') || 'Functional Profile Code' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="funcProfileCode"
                        @update:model-value="$emit('update:funcProfileCode', $event)"
                        :placeholder="t('text.input-field.functional-profile-code-placeholder') || 'Enter functional profile code'"
                        size="md"
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>
            </div>

            <div class="px-2"></div>

            <!-- DIVISION -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.division') || 'Division' }}</div>
                <div class="flex w-full text-sm">
                    <USelectMenu
                        :model-value="division"
                        @update:model-value="$emit('update:division', $event)"
                        :items="divisionOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.input-field.division-placeholder') || 'Select division'"
                        class="w-full font-reguler"
                    />
                </div>
            </div>

        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <!-- FUNCTIONAL PROFILE NAME -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.functional-profile-name') || 'Functional Profile Name' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="funcProfileName"
                        @update:model-value="$emit('update:funcProfileName', $event)"
                        :placeholder="t('text.input-field.functional-profile-name-placeholder') || 'Enter functional profile name'"
                        size="md"
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>
            </div>

            <div class="px-2"></div>

            <!-- LIMIT -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.limit') || 'Limit' }}</div>
                <div class="flex w-full text-sm">
                    <USelectMenu
                        :model-value="limit"
                        @update:model-value="$emit('update:limit', $event)"
                        :items="limitOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.input-field.limit-placeholder') || 'Select limit'"
                        class="w-full font-reguler"
                    />
                </div>
            </div>

        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <!-- PROFILE -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.profile') || 'Profile' }}</div>
                <div class="flex w-full text-sm">
                    <USelectMenu
                        :model-value="profile"
                        @update:model-value="$emit('update:profile', $event)"
                        :items="profileOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.input-field.profile-placeholder') || 'Select profile'"
                        class="w-full font-reguler"
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
                        class="w-full font-reguler"
                    />
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
