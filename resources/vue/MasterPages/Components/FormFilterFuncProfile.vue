<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import { useI18n } from '../../composables/useI18n'
import { TEXT_SIZE_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

const { t } = useI18n()

const props = defineProps({
    profile: {
        type: String as () => string | null,
        default: null
    },
    company: {
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
    profileOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    companyOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    limitOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    divisionOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    statusOptions: {
        type: Array as () => Array<{ id: number; label: string }>,
        default: () => []
    },
})

const emit = defineEmits(['update:profile', 'update:company', 'update:limit', 'update:division', 'update:status', 'clear', 'find'])

const onClear = () => {
    emit('clear')
}

const onFind = () => {
    emit('find')
}
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-4 pb-2 text-sm">

        <!-- PROFILE -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.profile') || 'Profile' }}
            </div>

            <USelectMenu
                :model-value="profile"
                @update:model-value="$emit('update:profile', $event)"
                :items="profileOptions"
                value-key="value"
                value-attribute="value"
                option-attribute="label"
                :placeholder="t('text.input-field.profile-placeholder') || 'Select profile'"
                class="w-full font-reguler"
                :ui="{
                    base: `truncate ${TEXT_SIZE_CLASS}`,
                    content: TEXT_SIZE_CLASS,
                    item: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- DIVISION -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.division') || 'Division' }}
            </div>

            <USelectMenu
                :model-value="division"
                @update:model-value="$emit('update:division', $event)"
                :items="divisionOptions"
                value-key="value"
                value-attribute="value"
                option-attribute="label"
                :placeholder="t('text.input-field.division-placeholder') || 'Select division'"
                class="w-full font-reguler"
                :ui="{
                    base: `truncate ${TEXT_SIZE_CLASS}`,
                    content: TEXT_SIZE_CLASS,
                    item: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- COMPANY -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.functional-profile-management-pg.input-company') || 'Company' }}
            </div>

            <USelectMenu
                :model-value="company"
                @update:model-value="$emit('update:company', $event)"
                :items="companyOptions"
                value-key="value"
                value-attribute="value"
                option-attribute="label"
                :placeholder="t('text.functional-profile-management-pg.input-company-placeholder') || 'Select company'"
                class="w-full font-reguler"
                :ui="{
                    base: `truncate ${TEXT_SIZE_CLASS}`,
                    content: TEXT_SIZE_CLASS,
                    item: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- LIMIT -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.limit') || 'Limit' }}
            </div>

            <USelectMenu
                :model-value="limit"
                @update:model-value="$emit('update:limit', $event)"
                :items="limitOptions"
                value-key="value"
                value-attribute="value"
                option-attribute="label"
                :placeholder="t('text.input-field.limit-placeholder') || 'Select limit'"
                class="w-full font-reguler"
                :ui="{
                    base: `truncate ${TEXT_SIZE_CLASS}`,
                    content: TEXT_SIZE_CLASS,
                    item: TEXT_SIZE_CLASS
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
