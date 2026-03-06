<script setup lang="ts">
import { computed } from 'vue'
import { useI18n } from '../composables/useI18n'

const { t } = useI18n()

const props = defineProps({
    label: {
        type: String,
        default: ''
    },
    icon: {
        type: String,
        default: 'i-lucide-info'
    },
    defaultOpen: {
        type: Boolean,
        default: false
    }
})

const drawerItems = computed(() => [
    {
        label: props.label || t('text.input-field.more-filters'),
        slot: 'content',
        icon: props.icon,
        defaultOpen: props.defaultOpen
    }
])
</script>

<template>
    <UDrawer
        :items="drawerItems"
        :ui="{
            wrapper: 'w-full',
            item: {
                base: 'border-1 border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden',
            }
        }">
        <UButton label="User Guide" color="neutral" variant="subtle" icon="i-lucide-info" size="sm"/>

        <template #body>
            <div class="px-6">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <h1 class="text-highlighted font-semibold text-lg">List of User Guide for Profile</h1>
                </div>

                <USeparator class="mb-6"/>

                <!-- Nuxt UI Table -->
                <!-- <CmpCustomTable
                    :data="profileData"
                    :columns="columns"
                    :actions="actions"
                    :showNumberColumn="false"
                    :showFilters="true"
                    :loading="loadingTable"
                    :showLoadingOverlay="showLoadingOverlay"
                    :page-size="itemPerPage"
                    :current-page="currentPage"
                    :count-total-data="countTotalData"
                    :column-pinning="columnPinning"
                    @update:currentPage="handlePageChange"
                    @update:pageSize="handlePageSizeChange"
                    @search="handleSearch"
                    class="mb-6"
                /> -->

            </div>
        </template>
    </UDrawer>
</template>
