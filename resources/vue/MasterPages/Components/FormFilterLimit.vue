<script setup lang="ts">
import { ref, shallowRef } from 'vue'
import type { CalendarDate } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'
import { TEXT_SIZE_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

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
    <div class="grid grid-cols-1 md:grid-cols-2 gap-y-2 gap-x-4 pb-2 text-sm">
        <!-- LIMIT CODE -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.limit-code') || 'Limit Code' }}
            </div>

            <UInput
                :model-value="limitCode"
                @update:model-value="$emit('update:limitCode', $event)"
                :placeholder="t('text.input-field.limit-code-placeholder') || 'Enter limit code'"
                size="md"
                class="w-full font-light"
                :ui="{
                    base: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- START DATE -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.start-date') || 'Start Date' }}
            </div>

            <UInputDate
                :model-value="startDate"
                @update:model-value="$emit('update:startDate', $event)"
                locale="en-GB"
                format="dd/mm/yyyy"
                :max-value="endDate"
                class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524]"
                :class="TEXT_SIZE_CLASS"
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

        <!-- MINIMUN -->
         <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.min-amount') || 'Minimum Amount' }}
            </div>

            <UInputNumber
                :model-value="minAmount"
                @update:model-value="$emit('update:minAmount', $event)"
                locale="id-ID"
                :format-options="{
                    style: 'currency',
                    currency: 'IDR'
                }"
                class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524] "
                :ui="{
                    base: TEXT_SIZE_CLASS
                }"
            />
        </div>

        <!-- END DATE -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.end-date') || 'End Date' }}
            </div>

            <UInputDate
                :model-value="endDate"
                @update:model-value="$emit('update:endDate', $event)"
                locale="en-GB"
                format="dd/mm/yyyy"
                :min-value="startDate"
                class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524]"
                :class="TEXT_SIZE_CLASS"
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

        <!-- MAXIMUM -->
        <div class="flex items-center gap-3">
            <div class="w-32 font-semibold shrink-0" :class="TEXT_SIZE_CLASS">
                {{ t('text.input-field.max-amount') || 'Maximum Amount' }}
            </div>

            <UInputNumber
                :model-value="maxAmount"
                @update:model-value="$emit('update:maxAmount', $event)"
                locale="id-ID"
                :format-options="{
                    style: 'currency',
                    currency: 'IDR'
                }"
                class="w-full border-[#CAD5E2] font-reguler focus:border-[#F26524] "
                :ui="{
                    base: TEXT_SIZE_CLASS
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
