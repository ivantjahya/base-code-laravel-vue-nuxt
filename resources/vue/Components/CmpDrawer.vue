<script setup lang="ts">
import { computed, ref, watch } from 'vue'
import { useI18n } from '../composables/useI18n'
import { useApiStore } from '../AppState'
import axios from 'axios'

const { t } = useI18n()
const api = useApiStore()

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
    },
    pageName: {
        type: String,
        default: ''
    },
    urlPath: {
        type: String,
        default: ''
    }
})

const isOpen = ref(props.defaultOpen)
const userGuideList = ref<any[]>([])
const loading = ref(false)

const getUserGuideList = async () => {
    loading.value = true
    try {
        const response = await axios.get(api.getUserGuideList, {
            params: {
                menu_url_path: props.urlPath || undefined,
                status: 1,
                skip: 0,
                limit: 100,
                sort_by: 'name',
                sort_order: 'asc',
            }
        })
        userGuideList.value = response.data?.data?.data || response.data?.data || []
    } catch (error) {
        console.error('Error fetching user guides:', error)
        userGuideList.value = []
    } finally {
        loading.value = false
    }
}

watch(isOpen, (val) => {
    if (val) getUserGuideList()
})

const handleDownload = async (item: any) => {
    const id = String(item?.id || '')
    if (!id) return

    try {
        const response = await axios.get(`${api.getUserGuideDownload}${id}`, {
            responseType: 'blob'
        })

        const url = window.URL.createObjectURL(new Blob([response.data]))
        const link = document.createElement('a')
        link.href = url

        const contentDisposition = response.headers['content-disposition']
        const fileName = contentDisposition
            ? contentDisposition.split('filename=')[1]?.replace(/['"]/g, '').trim()
            : (item?.file_name || 'download')

        link.setAttribute('download', fileName)
        document.body.appendChild(link)
        link.click()
        link.remove()
        window.URL.revokeObjectURL(url)
    } catch (error) {
        console.error('Error downloading user guide:', error)
    }
}

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
        v-model:open="isOpen"
        :ui="{
            wrapper: 'w-full',
        }">
        <UButton
            :label="t('page.user-guide-management')"
            color="neutral"
            variant="subtle"
            icon="i-lucide-info"
            size="sm"
        />

        <template #body>
            <div class="px-6 pb-6">
                <div class="flex items-center justify-between gap-4 mb-6">
                    <h1 class="text-highlighted font-semibold text-lg">
                        {{ t('text.user-guide-management-pg.list-user-guide-for') || 'List of User Guide for' }}
                        {{ props.pageName }}
                    </h1>
                </div>

                <USeparator class="mb-6"/>

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

                <!-- Loading state -->
                <div v-if="loading" class="flex justify-center py-8">
                    <UIcon name="i-lucide-loader-circle" class="animate-spin text-2xl text-gray-400" />
                </div>

                <!-- Empty state -->
                <div v-else-if="userGuideList.length === 0" class="flex flex-col items-center py-8 text-gray-400 gap-2">
                    <UIcon name="i-lucide-file-x" class="text-3xl" />
                    <p class="text-sm">{{ t('text.message.no-data') || 'No user guides available.' }}</p>
                </div>

                <!-- List -->
                <ul v-else class="space-y-3">
                    <li
                        v-for="item in userGuideList"
                        :key="item.id"
                        class="flex items-start justify-between gap-4 rounded-lg border border-gray-200 dark:border-gray-700 p-4"
                    >
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-sm truncate">{{ item.name }}</p>
                            <p v-if="item.description" class="text-xs text-gray-500 mt-0.5 line-clamp-2">{{ item.description }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ item.file_name }}</p>
                        </div>
                        <UButton
                            icon="i-lucide-download"
                            color="neutral"
                            variant="ghost"
                            size="sm"
                            :aria-label="t('text.button.download') || 'Download'"
                            @click="handleDownload(item)"
                        />
                    </li>
                </ul>
            </div>
        </template>
    </UDrawer>
</template>
