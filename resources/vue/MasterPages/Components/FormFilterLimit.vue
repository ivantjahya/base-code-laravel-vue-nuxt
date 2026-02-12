<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import type { CalendarDate } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'

const { t } = useI18n()

const props = defineProps({
    limitCode: {
        type: String,
        default: ''
    },
    minAmount: {
        type: [Number, null] as any,
        default: null
    },
    maxAmount: {
        type: [Number, null] as any,
        default: null
    },
    startDate: {
        type: Object as () => CalendarDate | undefined,
        default: undefined
    },
    endDate: {
        type: Object as () => CalendarDate | undefined,
        default: undefined
    },
    loading: {
        type: Boolean,
        default: false
    }
})

const emit = defineEmits(['update:limitCode', 'update:minAmount', 'update:maxAmount', 'update:startDate', 'update:endDate', 'clear', 'find'])

const isPopoverStartDateOpen = ref(false)
const isPopoverEndDateOpen = ref(false)

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
            <!-- LIMIT CODE -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.limit-code') || 'Limit Code' }}</div>
                <div class="flex w-full text-sm">
                    <UInput
                        :model-value="limitCode"
                        @update:model-value="$emit('update:limitCode', $event)"
                        :placeholder="t('text.input-field.limit-code-placeholder') || 'Enter limit code'"
                        size="md"
                        class="w-full font-light text-base md:text-sm"
                    />
                </div>
            </div>

            <div class="px-2"></div>

            <!-- START DATE -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.start-date') || 'Start Date' }}</div>
                <div class="flex w-full text-sm">
                    <UInputDate
                        :model-value="startDate"
                        @update:model-value="$emit('update:startDate', $event)"
                        locale="en-GB"
                        format="dd/mm/yyyy"
                        :max-value="endDate"
                        class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524]"
                    >
                        <template #trailing>
                            <UPopover v-model:open="isPopoverStartDateOpen">
                                <UButton
                                    color="neutral"
                                    variant="link"
                                    size="sm"
                                    icon="i-lucide-calendar"
                                    aria-label="Select a date"
                                    class="px-0"
                                />

                                <template #content>
                                    <UCalendar
                                        :model-value="startDate"
                                        @update:model-value="$emit('update:startDate', $event); isPopoverStartDateOpen = false"
                                        :max-value="endDate"
                                        class="p-2"
                                    />
                                </template>
                            </UPopover>
                        </template>
                    </UInputDate>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">
            <!-- MINIMUM -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.min-amount') || 'Minimum' }}</div>
                <div class="flex w-full text-sm">
                    <UInputNumber
                        :model-value="minAmount"
                        @update:model-value="$emit('update:minAmount', $event)"
                        locale="id-ID"
                        :format-options="{
                            style: 'currency',
                            currency: 'IDR'
                        }"
                        class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524] "
                    />
                </div>
            </div>

            <div class="px-2"></div>

            <!-- END DATE -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.end-date') || 'End Date' }}</div>
                <div class="flex w-full text-sm">
                    <UInputDate
                        :model-value="endDate"
                        @update:model-value="$emit('update:endDate', $event)"
                        locale="en-GB"
                        format="dd/mm/yyyy"
                        :min-value="startDate"
                        class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524]"
                    >
                        <template #trailing>
                            <UPopover v-model:open="isPopoverEndDateOpen">
                                <UButton
                                    color="neutral"
                                    variant="link"
                                    size="sm"
                                    icon="i-lucide-calendar"
                                    aria-label="Select a date"
                                    class="px-0"
                                />

                                <template #content>
                                    <UCalendar
                                        :model-value="endDate"
                                        @update:model-value="$emit('update:endDate', $event); isPopoverEndDateOpen = false"
                                        :min-value="startDate"
                                        class="p-2"
                                    />
                                </template>
                            </UPopover>
                        </template>
                    </UInputDate>
                </div>
            </div>
        </div>

        <div class="flex flex-col md:flex-row w-full my-1 gap-2">
            <!-- MAXIMUM -->
            <div class="flex w-full">
                <div class="w-full md:w-50 my-auto text-base md:text-sm font-semibold">{{ t('text.input-field.max-amount') || 'Maximum' }}</div>
                <div class="flex w-full text-sm">
                    <UInputNumber
                        :model-value="maxAmount"
                        @update:model-value="$emit('update:maxAmount', $event)"
                        locale="id-ID"
                        :format-options="{
                            style: 'currency',
                            currency: 'IDR'
                        }"
                        class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524] "
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
