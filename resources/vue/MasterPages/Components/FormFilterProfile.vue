<script setup lang="ts">
import { useI18n } from '../../composables/useI18n'
import { useGlobalOptions } from '../../composables/useGlobalOptions'
import { TEXT_SIZE_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

const { t } = useI18n()
const { profileSourceOptions, statusOptions } = useGlobalOptions()

const props = withDefaults(defineProps<{
    profileCode?: string
    profileName?: string
    profileSource?: number | null
    status?: number | null
    loading?: boolean
}>(), {
    profileCode: '',
    profileName: '',
    profileSource: null,
    status: null,
    loading: false
})

const emit = defineEmits<{
    (e: 'update:profileCode', value: string): void
    (e: 'update:profileName', value: string): void
    (e: 'update:profileSource', value: number | null): void
    (e: 'update:status', value: number | null): void
    (e: 'clear'): void
    (e: 'find'): void
}>()

const onClear = () => {
    emit('clear')
}

const onFind = () => {
    emit('find')
}
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-4 pb-2 text-sm">
        <!-- PROFILE CODE -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.profile-code') || 'Profile Code' }}
            </div>

            <UInput
                :model-value="profileCode"
                @update:model-value="$emit('update:profileCode', $event)"
                :placeholder="t('text.input-field.profile-code-placeholder') || 'Enter profile code'"
                size="md"
                class="w-full font-light"
                :ui="{
                    base: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- PROFILE SOURCE -->
         <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.profile-source') || 'Profile Source' }}
            </div>

            <USelectMenu
                :model-value="profileSource"
                @update:model-value="$emit('update:profileSource', $event)"
                :items="profileSourceOptions"
                value-key="id"
                value-attribute="id"
                option-attribute="label"
                :placeholder="t('text.input-field.profile-source-placeholder') || 'Select profile source'"
                class="w-full font-reguler"
                :ui="{
                    base: `truncate ${TEXT_SIZE_CLASS}`,
                    content: TEXT_SIZE_CLASS,
                    item: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- PROFILE NAME -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.profile-name') || 'Profile Name' }}
            </div>

            <UInput
                :model-value="profileName"
                @update:model-value="$emit('update:profileName', $event)"
                :placeholder="t('text.input-field.profile-name-placeholder') || 'Enter profile name'"
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

        <div class="flex items-center gap-3"></div>

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
