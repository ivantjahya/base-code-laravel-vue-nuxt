<script setup lang="ts">
import { useI18n } from '../../composables/useI18n'
import { useGlobalOptions } from '../../composables/useGlobalOptions'
import { TEXT_SIZE_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

const { t } = useI18n()

const props = defineProps({
    username: {
        type: String,
        default: ''
    },
    name: {
        type: String,
        default: ''
    },
    profile: {
        type: String as () => string | null,
        default: null
    },
    category: {
        type: String as () => string | null,
        default: null
    },
    status: {
        type: String as () => string | null,
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
    categoryOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    userStatusOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    }
})

const emit = defineEmits(['update:username', 'update:name', 'update:profile', 'update:category', 'update:status', 'clear', 'find'])


const onClear = () => {
    emit('clear')
}

const onFind = () => {

    // console.log(props.profile);
    emit('find')

}
</script>

<template>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-4 pb-2 text-sm">

        <!-- USERNAME -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.username') || 'Username' }}
            </div>

            <UInput
                :model-value="username"
                @update:model-value="$emit('update:username', $event)"
                :placeholder="t('text.input-field.username-placeholder') || 'Enter username'"
                size="md"
                class="w-full font-light"
                :ui="{
                    base: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- CATEGORY -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.category') || 'Category' }}
            </div>

            <USelectMenu
                :model-value="category"
                @update:model-value="$emit('update:category', $event)"
                :items="categoryOptions"
                value-key="value"
                value-attribute="value"
                option-attribute="label"
                :placeholder="t('text.input-field.category-placeholder') || 'Select category'"
                class="w-full font-reguler"
                :ui="{
                    base: `truncate ${TEXT_SIZE_CLASS}`,
                    content: TEXT_SIZE_CLASS,
                    item: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- NAME -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.name') || 'Name' }}
            </div>

            <UInput
                :model-value="name"
                @update:model-value="$emit('update:name', $event)"
                :placeholder="t('text.input-field.name-placeholder') || 'Enter name'"
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
                :items="userStatusOptions"
                value-key="value"
                value-attribute="value"
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
