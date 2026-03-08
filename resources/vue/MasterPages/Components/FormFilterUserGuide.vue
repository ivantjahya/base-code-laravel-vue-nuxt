<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import type { CalendarDate } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'
import { TEXT_SIZE_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

const { t } = useI18n()
const profileSourceValueFilter = ref(['Internal', 'External'])
const statusValueFilter = ref(['Active', 'Not Active'])

const props = defineProps({
    userGuideCode: {
        type: String,
        default: ''
    },
    userGuideName: {
        type: String,
        default: ''
    },
    menu: {
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

const emit = defineEmits(['update:userGuideCode', 'update:userGuideName', 'update:menu', 'update:status', 'clear', 'find'])

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

            <!-- USER GUIDE CODE -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto font-semibold" :class="TEXT_SIZE_CLASS">{{ t('text.input-field.user-guide-code') || 'User Guide Code' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="userGuideCode"
                        @update:model-value="$emit('update:userGuideCode', $event)"
                        :placeholder="t('text.input-field.user-guide-code-placeholder') || 'Enter user guide code'"
                        size="md"
                        class="w-full font-light"
                        :ui="{
                            base: TEXT_SIZE_CLASS
                        }"
                    />
                </div>

            </div>

            <div class="px-2"></div>

            <!-- MENU -->
            <div class="flex w-full">

                <div class="w-full md:w-50 my-auto font-semibold" :class="TEXT_SIZE_CLASS">{{ t('text.input-field.menu') || 'Menu' }}</div>
                <div class="flex w-full text-sm">
                    <USelectMenu
                        :model-value="menu"
                        @update:model-value="$emit('update:menu', $event)"
                        :items="profileSourceValueFilter"
                        value-key="id"
                        value-attribute="id"
                        option-attribute="label"
                        :placeholder="t('text.input-field.menu-placeholder') || 'Select menu'"
                        class="w-full font-reguler"
                        :ui="{
                            base: TEXT_SIZE_CLASS,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                    />
                </div>

            </div>

        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <!-- USER GUIDE NAME -->
            <div class="flex w-full">

                <div class="w-full md:w-50 my-auto font-semibold" :class="TEXT_SIZE_CLASS">{{ t('text.input-field.user-guide-name') || 'User Guide Name' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="userGuideName"
                        @update:model-value="$emit('update:userGuideName', $event)"
                        :placeholder="t('text.input-field.user-guide-name-placeholder') || 'Enter user guide name'"
                        size="md"
                        class="w-full font-light"
                        :ui="{
                            base: TEXT_SIZE_CLASS
                        }"
                    />
                </div>

            </div>

            <div class="px-2"></div>

            <!-- STATUS -->
            <div class="flex w-full">

                <div class="w-full md:w-50 my-auto font-semibold" :class="TEXT_SIZE_CLASS">{{ t('text.input-field.status') || 'Status' }}</div>
                <div class="flex w-full text-sm">

                    <USelectMenu
                        :model-value="status"
                        @update:model-value="$emit('update:status', $event)"
                        :items="statusValueFilter"
                        value-key="id"
                        value-attribute="id"
                        option-attribute="label"
                        :placeholder="t('text.input-field.status-placeholder') || 'Select status'"
                        class="w-full font-reguler"
                        :ui="{
                            base: TEXT_SIZE_CLASS,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
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

    </div>
</template>
