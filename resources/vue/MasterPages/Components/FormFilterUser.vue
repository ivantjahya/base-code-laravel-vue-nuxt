<script setup lang="ts">
import { useI18n } from '../../composables/useI18n'
import { useGlobalOptions } from '../../composables/useGlobalOptions'

const { t } = useI18n()
const { profileSourceOptions, statusOptions } = useGlobalOptions()

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
    categoryOptions: {
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
    <div class="grid grid-flow-row text-sm">
        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <!-- USERNAME -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.username') || 'Username' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="username"
                        @update:model-value="$emit('update:username', $event)"
                        :placeholder="t('text.input-field.username-placeholder') || 'Enter username'"
                        size="md"
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>

            </div>

            <div class="px-2"></div>

            <!-- CATEGORY -->
            <div class="flex w-full">

                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.category') || 'Category' }}</div>
                <div class="flex w-full text-sm">
                    <USelectMenu
                        :model-value="category"
                        @update:model-value="$emit('update:category', $event)"
                        :items="categoryOptions"
                        value-key="value"
                        value-attribute="value"
                        option-attribute="label"
                        :placeholder="t('text.input-field.category-placeholder') || 'Select category'"
                        class="w-full font-reguler"
                    />
                </div>

            </div>

        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">

            <!-- NAME -->
            <div class="flex w-full">

                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.name') || 'Name' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="name"
                        @update:model-value="$emit('update:name', $event)"
                        :placeholder="t('text.input-field.name-placeholder') || 'Enter name'"
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
