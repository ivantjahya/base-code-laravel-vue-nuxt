<script setup lang="ts">
import { useI18n } from '../../composables/useI18n'
import { useGlobalOptions } from '../../composables/useGlobalOptions'
import { TEXT_SIZE_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

const { t } = useI18n()
const { statusOptions } = useGlobalOptions()

const props = defineProps({
    userGuideName: {
        type: String,
        default: ''
    },
    menu: {
        type: String as () => string | null,
        default: null
    },
    status: {
        type: Number,
        default: null
    },
    loading: {
        type: Boolean,
        default: false
    },
    menuOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    }
})

const emit = defineEmits(['update:userGuideName', 'update:menu', 'update:status', 'clear', 'find'])

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
                        :items="statusOptions"
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

            <!-- MENU -->
            <div class="flex w-full">

                <div class="w-full md:w-50 my-auto font-semibold" :class="TEXT_SIZE_CLASS">{{ t('text.input-field.menu') || 'Menu' }}</div>
                <div class="flex w-full text-sm">
                    <USelectMenu
                        :model-value="menu"
                        @update:model-value="$emit('update:menu', $event)"
                        :items="menuOptions"
                        value-key="value"
                        value-attribute="value"
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
