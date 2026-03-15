<script setup lang="ts">
import { ref, computed, shallowRef, watch, getCurrentInstance, nextTick } from 'vue'
import { CalendarDate } from '@internationalized/date'
import { useI18n } from '../../composables/useI18n'
import { useFormatters } from '../../composables/useFormatters'
import { useApiStore } from '../../AppState'
import axios, { all } from 'axios'
import type { TreeItem } from '@nuxt/ui'
import type { TreeItemSelectEvent } from 'reka-ui'
import { TEXT_SIZE_CLASS, TITLE_MODAL_TEXT_CLASS, INPUT_FIELD_WARN_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

type SelectOption = {
  label: string
  value: string
}

const props = defineProps({
    open: {
        type: Boolean,
        default: false
    },
    title: {
        type: String,
        default: ''
    },
    viewOnly: {
        type: Boolean,
        default: false
    },
    editMode: {
        type: Boolean,
        default: false
    },
    editingId: {
        type: String,
        default: null
    },
    initialData: {
        type: Object,
        default: () => ({})
    },
    profileOptions: {
        type: Array as () => Array<{ label: string; value: string; is_internal?: number }>,
        default: () => []
    },
    categoryOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
    allCategoryOptions: {
        type: Array as () => Array<any>,
        default: () => []
    },
    siteOptions: {
        type: Array as () => Array<{ label: string; value: string }>,
        default: () => []
    },
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const { getDateString, stringToCalendarDate } = useFormatters()
const api = useApiStore()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

// ========================= STATE FOR MODAL =========================
const isSubmitting = ref(false)

const username = ref<string>('')
const name = ref<string>('')
const selectedProfile = ref<string | null>(null)
const selectedSiteArr = ref<SelectOption[]>([])

const inputValidityDate = ref<any>(null)
const modelValueValidity = shallowRef<CalendarDate>()
const isPopoverFormValidityDateOpen = ref(false)

// Validation error states
const errors = ref({
    username: '',
    name: '',
    profile: '',
    category: '',
    site: '',
    validityDate: ''
})

// ========================= STATE FOR TABS =========================
const activeTab = ref('general')
const tabItems = computed(() => [
    { label: t('text.user-management-pg.tab-general' as any), key: 'general', value: 'general' },
    { label: t('text.user-management-pg.tab-category' as any), key: 'category', value: 'category', disabled: !isInternalProfile.value }
])

const tabErrors = computed(() => ({
    general: !!errors.value.username ||
        !!errors.value.name ||
        !!errors.value.profile ||
        !!errors.value.validityDate ||
        !!errors.value.site,
    category: !!errors.value.category,
}))

const goToFirstErrorTab = () => {
    if (tabErrors.value.general) activeTab.value = 'general'
    else if (tabErrors.value.category) activeTab.value = 'category'
}

// ========================= STATE FOR TREE =========================
const isLoadingTree = ref(false)
const categoryTreeValue = ref<TreeItem[]>([])
const categoryItems = ref<TreeItem[]>([])
const treeNodeByValueMap = ref<Record<string, TreeItem>>({})
const categoryMap = ref<Record<string, SelectOption>>({})
const categoryNodeMap = ref<Record<string, string>>({})
const categoryNodeDataMap = ref<Record<string, any>>({})

const getTreeItemKey = (item: any) => item?.value

const getLabel = (value: any): string => {
    if (!value || typeof value !== 'object') return ''
    const code = value.code ? String(value.code) : ''
    const name = value.name ? String(value.name) : ''
    const label = code + ' - ' + name

    const translated = label ? t(label as any) : ''
    return translated || value.name || ''
}

const getCategoryId = (value: any): string => {
    if (!value || typeof value !== 'object') return ''
    return value.id || value.category_id || ''
}

const getNodeValue = (node: any, fallbackLabel: string): string => {
    if (!node || typeof node !== 'object') return fallbackLabel
    return node.value || node.name || node.id || fallbackLabel
}

const mapTreeNode = (node: any, inheritedDataId = '', nodePath = ''): TreeItem | null => {
    if (!node || typeof node !== 'object') return null

    const label = getLabel(node)
    if (!label) return null

    const categoryId = getCategoryId(node)
    const nodeCategoryId = categoryId || inheritedDataId
    const nodeIdentity = String(getNodeValue(node, label))

    const tag = inheritedDataId ? 'category' : 'division'
    const currentPath = nodePath ? `${nodePath}/${nodeIdentity}` : nodeIdentity
    const value = `${tag}:${currentPath}`

    if (nodeCategoryId) {
        categoryNodeMap.value[value] = nodeCategoryId
        categoryNodeDataMap.value[value] = node
    }

    const childMenus = [
        ...(Array.isArray(node.categories) ? node.categories : [])
    ]

    const mappedMenuChildren: TreeItem[] = childMenus
        .map((child: any) => mapTreeNode(child, nodeCategoryId, currentPath))
        .filter(Boolean) as TreeItem[]

    const treeNode = {
        label,
        value,
        children: [...mappedMenuChildren]
    } as TreeItem

    treeNodeByValueMap.value[value] = treeNode
    return treeNode
}

const loadCategoryTree = async () => {
    categoryItems.value = []
    isLoadingTree.value = true
    try {
        const sourceArray = Array.isArray(props.allCategoryOptions) ? props.allCategoryOptions : []

        categoryMap.value = {}
        categoryNodeMap.value = {}
        treeNodeByValueMap.value = {}
        categoryItems.value = sourceArray
            .map((node: any, index: number) => mapTreeNode(node, '', `path`))
            .filter(Boolean) as TreeItem[]
    } catch (error: any) {
        console.error('Error loading menu access control list:', error.response?.data || error.message)
        categoryItems.value = []
        categoryMap.value = {}
        categoryNodeMap.value = {}
        treeNodeByValueMap.value = {}
        Swal?.fire({
                icon: 'error',
                title: t('text.message.error' as any) || 'Error!',
                text: t('text.message.failed-to-load-data-msg' as any) || 'Failed to load data.',
                confirmButtonText: 'OK'
        })
    } finally {
        isLoadingTree.value = false
    }
}

// Utility to resolve tree nodes from values, ensuring uniqueness and filtering out invalid entries
const resolveTreeNodes = (values: string[]): TreeItem[] => {
    const resolved = values
        .map((value) => treeNodeByValueMap.value[value])
        .filter((item): item is TreeItem => !!item)

    const unique = new Map<string, TreeItem>()
    resolved.forEach((item) => {
        const key = String(item?.value || '')
        if (key) unique.set(key, item)
    })

    return Array.from(unique.values())
}

// Utility to resolve tree nodes from values, ensuring uniqueness and filtering out invalid entries
const mapInitialCategoryTreeValue = (initialData: any): TreeItem[] => {
    const directTreeValue = Array.isArray(initialData?.treeValue) ? initialData.treeValue : []
    if (directTreeValue.length > 0) {
        const directValues = directTreeValue
            .map((entry: any) => {
                if (typeof entry === 'string') return entry
                if (entry && typeof entry === 'object') return String(entry.value || entry.id || '')
                return ''
            })
            .filter((value: string) => !!value)

        return resolveTreeNodes(directValues)
    }

    const rawData = initialData?.category || []
    const categoryArray = Array.isArray(rawData) ? rawData : []
    if (categoryArray.length === 0) return []

    const resolvedSelection = new Set<string>()

    const addCategoryWithAncestors = (categoryNodeKey?: string) => {
        if (!categoryNodeKey || !categoryNodeKey.startsWith('category:')) return

        const categoryPath = categoryNodeKey.slice('category:'.length)
        const parts = categoryPath.split('/').filter((part) => !!part)
        for (let index = 1; index <= parts.length; index += 1) {
            const partialPath = parts.slice(0, index).join('/')
            const categoryKey = `category:${partialPath}`
            const divisionKey = `division:${partialPath}`
            if (treeNodeByValueMap.value[categoryKey]) {
                resolvedSelection.add(categoryKey)
            } else if (treeNodeByValueMap.value[divisionKey]) {
                resolvedSelection.add(divisionKey)
            }
        }
    }

    const findCategoryNodeKey = (categoryId: string): string | undefined => {
        if (!categoryId) return undefined
        return Object.keys(categoryNodeMap.value).find((key) => categoryNodeMap.value[key] === categoryId)
    }
    
    categoryArray.forEach((entry: any) => {
        const categoryId = String(entry?.id || entry?.value || '')
        const categoryNodeKey = findCategoryNodeKey(categoryId)

        if (categoryId) {
            if (categoryNodeKey) {
                addCategoryWithAncestors(categoryNodeKey)
            }
        }
    })

    return resolveTreeNodes(Array.from(resolvedSelection))
}

// Utility to resolve tree nodes from values, ensuring uniqueness and filtering out invalid entries
const selectedCategoryData = computed(() => {
    // Get checked values
    const selectedValues = (Array.isArray(categoryTreeValue.value) ? categoryTreeValue.value : [])
        .map((entry: any) => {
            if (typeof entry === 'string') return entry
            if (entry && typeof entry === 'object') return String(entry.value || entry.id || '')
            return ''
        })
        .filter((value) => !!value)

    // Utility to get ancestor category values based on the path structure
    const getAncestorCategoryValues = (categoryPath: string): string[] => {
        if (!categoryPath) return []
        const parts = categoryPath.split('/').filter((part) => !!part)
        const ancestors: string[] = []
        for (let index = 1; index <= parts.length; index += 1) {
            ancestors.push(`category:${parts.slice(0, index).join('/')}`)
        }
        return ancestors
    }

    const expandedValues = new Set<string>()
    selectedValues.forEach((value) => {
        expandedValues.add(value)

        if (value.startsWith('category:')) {
            const categoryPath = value.slice('category:'.length)
            getAncestorCategoryValues(categoryPath).forEach((ancestor) => expandedValues.add(ancestor))
            return
        }
    })

    const resolved = Array.from(expandedValues)
        .map((value) => {
            const direct = categoryMap.value[value]
            if (direct) return direct

            if (value.startsWith('category:')) {
                const dataId = categoryNodeMap.value[value]
                const nodeData = categoryNodeDataMap.value[value]

                if (dataId) {
                    return {
                        label: nodeData ? nodeData.name || nodeData.value || '-' : null,
                        value: dataId
                    }
                }
            }

            return null
        })
        .filter((item): item is { label: string; value: string } => !!item)

    const unique = new Map<string, { label: string; value: string }>()
    resolved.forEach((item) => {
        unique.set(`${item.label}:${item.value}`, item)
    })

    return Array.from(unique.values())
})

// Utility to get selected tree values as a Set for easy lookup
const getSelectedCategoryTreeValues = (): Set<string> => {
    const values = (Array.isArray(categoryTreeValue.value) ? categoryTreeValue.value : [])
        .map((entry: any) => {
            if (typeof entry === 'string') return entry
            if (entry && typeof entry === 'object') return String(entry.value || entry.id || '')
            return ''
        })
        .filter((value) => !!value)

    return new Set(values)
}

// Utility to get all tree nodes as a flat array, ensuring uniqueness
const getAllTreeNodes = (): TreeItem[] => {
    const nodes = Object.values(treeNodeByValueMap.value) as TreeItem[]

    const unique = new Map<string, TreeItem>()
    nodes.forEach((node) => {
        const key = String(node?.value || '')
        if (key) unique.set(key, node)
    })

    return Array.from(unique.values())
}

// Computed property to determine if "Check All" should be checked based on the current tree selection
const allAccessChecked = computed({
    get: () => {
        const allNodes = getAllTreeNodes()
        if (allNodes.length === 0) return false

        const selectedValues = getSelectedCategoryTreeValues()
        // If we have selected values, check if all nodes are selected
        return allNodes.every((node: any) => selectedValues.has(String(node?.value || '')))
    },
    set: (checked: boolean) => {
        const allNodes = getAllTreeNodes()
        if (!checked || allNodes.length === 0) {
            categoryTreeValue.value = []
            return
        }

        categoryTreeValue.value = allNodes
    }
})

const isCategoryNode = (item: any): boolean => String(item?.value || '').startsWith('category:')

function onSelect(e: TreeItemSelectEvent<TreeItem>, item: any) {
    if (props.viewOnly) {
        e.preventDefault()
        return
    }
    if (e.detail.originalEvent.type === 'click' && !isCategoryNode(item)) {
        e.preventDefault()
    }
}

// Computed to check if the selected profile is internal (is_internal === 1)
const isInternalProfile = computed(() => {
    if (!selectedProfile.value) return false
    const profile = (props.profileOptions as Array<{ label: string; value: string; is_internal?: number }>)
        .find(p => p.value === selectedProfile.value)
    return profile?.is_internal === 1
})

// Flag to prevent clearing category/site during form initialization
let isInitializingForm = false

// Clear category and site when profile changes to non-internal
watch(isInternalProfile, (newVal) => {
    if (!newVal && !isInitializingForm) {
        categoryTreeValue.value = []
        selectedSiteArr.value = []
        errors.value.category = ''
        errors.value.site = ''
    }
})

// ========================= ACTION =========================
const resetForm = () => {
    username.value = ''
    name.value = ''
    selectedProfile.value = null
    categoryTreeValue.value = []
    selectedSiteArr.value = []
    modelValueValidity.value = undefined
    activeTab.value = 'general'
    
    // Reset errors
    errors.value = {
        username: '',
        name: '',
        profile: '',
        category: '',
        site: '',
        validityDate: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('close')
}

watch(() => props.open, async (newVal) => {
    if (newVal) {
        await loadCategoryTree()

        if ((props.editMode || props.viewOnly) && props.initialData) {
            isInitializingForm = true
            const initCategory = Array.isArray(props.initialData.category) ? props.initialData.category : []
            const initSite = Array.isArray(props.initialData.site) ? props.initialData.site : []

            username.value = props.initialData.username || ''
            name.value = props.initialData.name || ''
            selectedProfile.value = props.initialData.profile?.id || null
            selectedSiteArr.value = props.siteOptions.filter(option =>
                initSite.some((item: any) => (item.id ?? item.value ?? item) === option.value)
            )
            modelValueValidity.value = stringToCalendarDate(props.initialData.valid_date)

            await nextTick()
            categoryTreeValue.value = mapInitialCategoryTreeValue(props.initialData)
            isInitializingForm = false
        } else {
            resetForm()
            categoryTreeValue.value = []
        }
    }
})

// Submit create/update profile
const postSubmitUser = async () => {
    // Reset errors
    errors.value = {
        username: '',
        name: '',
        profile: '',
        category: '',
        site: '',
        validityDate: ''
    }

    // Validation
    let hasError = false

    if (username.value.trim() === '') {
        errors.value.username = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (name.value.trim() === '') {
        errors.value.name = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!selectedProfile.value) {
        errors.value.profile = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (isInternalProfile.value && selectedCategoryData.value.length === 0) {
        errors.value.category = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (isInternalProfile.value && selectedSiteArr.value.length === 0) {
        errors.value.site = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (!getDateString(modelValueValidity.value)) {
        errors.value.validityDate = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (hasError) {
        goToFirstErrorTab()
        return
    }

    isSubmitting.value = true
    try {
        const categoryPayload = isInternalProfile.value ? selectedCategoryData.value.map(category => category.value) : []
        const sitePayload = isInternalProfile.value ? selectedSiteArr.value.map(site => site.value) : []
        const validityDate = getDateString(modelValueValidity.value)

        const payload = {
            username: username.value,
            name: name.value,
            profile_id: selectedProfile.value,
            category: categoryPayload,
            site: sitePayload,
            valid_date: validityDate,
        }

        let response
        if (props.editMode && props.editingId) {
            response = await axios.put(`${api.postUserUpdate}${props.editingId}`, payload)
        } else {
            response = await axios.post(api.postUserCreate, payload)
        }

        Swal?.fire({
            icon: 'success',
            title: t('text.message.success' as any) || 'Success!',
            text: props.editMode ? t('text.message.data-updated-msg' as any) || 'The data has been updated successfully!' : t('text.message.data-saved-msg' as any) || 'The data has been saved successfully!',
            confirmButtonText: 'OK'
        })

        emit('submitted')
        closeModal()
    } catch (error: any) {
        console.error('Error submitting user:', error.response?.data || error.message)
        Swal?.fire({
            icon: 'error',
            title: t('text.message.failed-to-save-data' as any) || 'Failed to save data!',
            text: t('text.message.failed-to-save-data-msg' as any) || 'Failed to save the data. An error occurred.',
            confirmButtonText: 'OK'
        })
    } finally {
        isSubmitting.value = false
    }
}

const isOpen = computed({
    get: () => props.open,
    set: (value) => emit('update:open', value)
})
</script>

<template>
    <UModal
        v-model:open="isOpen"
        :title="title"
        :dismissible="false"
        :class="TITLE_MODAL_TEXT_CLASS"
        :ui="{
            content: `w-full mx-auto`,
            body: 'sm:pt-2 sm:px-6',
            footer: 'justify-end'
        }"
    >
        <template #body>
            <div class="h-[325px] overflow-y-auto pr-1">
                <UTabs
                    v-model="activeTab"
                    :items="tabItems"
                    variant="link"
                    class="w-full"
                    :ui="{
                        list: {
                            tab: {
                                wrapper: 'flex',
                                base: 'justify-center',
                                active: 'border-b-2 border-primary-500 text-primary-500',
                                inactive: 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200',
                            },
                            indicator: 'hidden'
                        }
                    }"
                >
                    <template #default="{ item }">
                        <div class="flex items-center gap-2">
                            <span>{{ item.label }}</span>

                            <div v-if="tabErrors[item.value]">
                                <UIcon name="i-carbon-warning-filled" class="text-red-500" />
                            </div>
                        </div>
                    </template>
                    <template #content="{ item }">
                        <div class="mt-4">
                            <!-- GENERAL -->
                            <div v-if="item.key === 'general'" class="grid grid-flow-row text-sm md:text-xs gap-0">
                                <!-- USERNAME -->
                                <UFormField
                                    orientation="horizontal"
                                    class="mb-2"
                                    :error="!!errors.username"
                                    :ui="{
                                        error: 'hidden',
                                    }"
                                >
                                    <template #label>
                                        <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                                            {{ t('text.user-management-pg.input-new-username') || 'Username' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <div class="w-80">
                                        <UInput
                                            v-model="username"
                                            required
                                            :placeholder="t('text.user-management-pg.input-new-username-placeholder') || 'Enter username'"
                                            class="w-full font-light"
                                            :ui="{
                                                base: `${TEXT_SIZE_CLASS} ${
                                                    errors.username
                                                    ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                                    : ''
                                                }`
                                            }"
                                            :disabled="props.editMode || props.viewOnly"
                                        />
                                        <p v-if="errors.username" :class="INPUT_FIELD_WARN_CLASS">{{ errors.username }}</p>
                                    </div>
                                </UFormField>

                                <!-- NAME -->
                                <UFormField
                                    orientation="horizontal"
                                    class="mb-2"
                                    :error="!!errors.name"
                                    :ui="{
                                        error: 'hidden',
                                    }"
                                >
                                    <template #label>
                                        <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                                            {{ t('text.user-management-pg.input-new-name') || 'Name' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <div class="w-80">
                                        <UInput
                                            v-model="name"
                                            required
                                            :placeholder="t('text.user-management-pg.input-new-name-placeholder') || 'Enter name'"
                                            class="w-full font-light"
                                            :ui="{
                                                base: `${TEXT_SIZE_CLASS} ${
                                                    errors.name
                                                    ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                                    : ''
                                                }`
                                            }"
                                            :disabled="props.viewOnly"
                                        />
                                        <p v-if="errors.name" :class="INPUT_FIELD_WARN_CLASS">{{ errors.name }}</p>
                                    </div>
                                </UFormField>

                                <!-- PROFILE -->
                                <UFormField
                                    orientation="horizontal"
                                    class="mb-2"
                                    :error="!!errors.profile"
                                    :ui="{
                                        error: 'hidden',
                                    }"
                                >
                                    <template #label>
                                        <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                                            {{ t('text.user-management-pg.input-new-profile') || 'Profile' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <div class="w-80">
                                        <USelectMenu
                                            v-model="selectedProfile"
                                            :items="profileOptions"
                                            value-key="value"
                                            value-attribute="value"
                                            option-attribute="label"
                                            :placeholder="t('text.user-management-pg.input-new-profile-placeholder') || 'Select profile'"
                                            class="w-full font-light"
                                            :ui="{
                                                base: `${TEXT_SIZE_CLASS} ${
                                                    errors.profile
                                                    ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                                    : ''
                                                }`,
                                                content: TEXT_SIZE_CLASS,
                                                item: TEXT_SIZE_CLASS
                                            }"
                                            :disabled="props.viewOnly"
                                        />
                                        <p v-if="errors.profile" :class="INPUT_FIELD_WARN_CLASS">{{ errors.profile }}</p>
                                    </div>
                                </UFormField>

                                <!-- VALIDITY DATE -->
                                <UFormField
                                    orientation="horizontal"
                                    class="mb-2"
                                    :error="!!errors.validityDate"
                                    :ui="{
                                        error: 'hidden',
                                    }"
                                >
                                    <template #label>
                                        <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                                            {{ t('text.user-management-pg.input-new-validity-date') || 'Validity Date' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <div class="w-80">
                                        <UInputDate
                                            ref="inputValidityDate"
                                            v-model="modelValueValidity"
                                            locale="en-GB"
                                            format="dd/mm/yyyy"
                                            class="w-full font-light"
                                            :class="TEXT_SIZE_CLASS"
                                            :ui="{
                                                base: errors.validityDate
                                                    ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                                    : ''
                                            }"
                                            :disabled="props.viewOnly"
                                        >
                                            <template #trailing>
                                                <UPopover v-model:open="isPopoverFormValidityDateOpen">
                                                    <UButton
                                                        color="neutral"
                                                        variant="link"
                                                        size="sm"
                                                        icon="i-lucide-calendar"
                                                        aria-label="Select a date"
                                                        class="px-0"
                                                        :disabled="props.viewOnly"
                                                    />

                                                    <template #content>
                                                        <UCalendar
                                                            v-model="modelValueValidity"
                                                            class="p-2"
                                                            @update:model-value="isPopoverFormValidityDateOpen = false"
                                                        />
                                                    </template>
                                                </UPopover>
                                            </template>
                                        </UInputDate>
                                        <p v-if="errors.validityDate" :class="INPUT_FIELD_WARN_CLASS">{{ errors.validityDate }}</p>
                                    </div>
                                </UFormField>

                                <!-- SITE -->
                                <UFormField
                                    v-if="isInternalProfile"
                                    orientation="horizontal"
                                    class="mb-2"
                                    :error="!!errors.site"
                                    :ui="{
                                        error: 'hidden',
                                    }"
                                >
                                    <template #label>
                                        <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                                            {{ t('text.user-management-pg.input-new-site') || 'Site' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <div class="w-80">
                                        <USelectMenu
                                            v-model="selectedSiteArr"
                                            :items="siteOptions"
                                            multiple
                                            option-attribute="label"
                                            :placeholder="t('text.user-management-pg.input-new-site-placeholder') || 'Select site'"
                                            class="w-full font-light"
                                            :ui="{
                                                base: `${TEXT_SIZE_CLASS} ${
                                                    errors.site
                                                    ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                                    : ''
                                                }`,
                                                content: TEXT_SIZE_CLASS,
                                                item: TEXT_SIZE_CLASS
                                            }"
                                            :disabled="props.viewOnly"
                                        />
                                        <p v-if="errors.site" :class="INPUT_FIELD_WARN_CLASS">{{ errors.site }}</p>
                                    </div>
                                </UFormField>
                            </div>

                            <!-- CATEGORY -->
                            <div v-else-if="item.key === 'category'" class="grid grid-flow-row text-sm md:text-xs gap-2">
                                <!-- CATEGORY TREE -->
                                <UFormField
                                    v-if="isInternalProfile"
                                    orientation="horizontal"
                                    class="mb-2"
                                    :error="!!errors.category"
                                    :ui="{
                                        error: 'hidden',
                                    }"
                                >
                                    <template #label>
                                        <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                                            {{ t('text.user-management-pg.input-new-category') || 'Category' }}
                                            <span class="text-red-500">*</span>
                                        </span>
                                    </template>

                                    <div class="w-80 max-h-50 overflow-y-auto border border-gray-300 rounded-md p-2 dark:border-gray-700">
                                        <UTree
                                            v-model="categoryTreeValue"
                                            :as="{ link: 'div' }"
                                            :items="categoryItems"
                                            :get-key="getTreeItemKey"
                                            multiple
                                            propagate-select
                                            bubble-select
                                            @select="onSelect"
                                            class="font-light"
                                            :ui="{
                                                link: 'hover:bg-gray-50 dark:hover:bg-gray-800/50',
                                                active: 'bg-transparent hover:bg-gray-50 dark:hover:bg-gray-800/50', // This specifically targets the highlighted background
                                                linkLabel: `${TEXT_SIZE_CLASS} text-gray-700 dark:text-gray-200` // Ensure the text doesn't turn white/primary on selection
                                            }"
                                        >
                                            <template #item-leading="{ selected, indeterminate, handleSelect }">
                                                <UCheckbox
                                                    :model-value="indeterminate ? 'indeterminate' : selected"
                                                    tabindex="-1"
                                                    @update:model-value="() => handleSelect()"
                                                    @click.stop
                                                    :disabled="viewOnly"
                                                />
                                            </template>
                                            <template #item-label="{ item, handleSelect }">
                                                <button
                                                    v-if="isCategoryNode(item)"
                                                    type="button"
                                                    class="w-full text-left"
                                                    @click.stop="handleSelect"
                                                    :disabled="viewOnly"
                                                >
                                                    {{ item.label }}
                                                </button>
                                                <span v-else>{{ item.label }}</span>
                                            </template>
                                        </UTree>
                                        <p v-if="isLoadingTree" class="text-xs md:text-sm text-gray-500">{{ t('text.message.loading') }}</p>
                                    </div>
                                    <p v-if="errors.category" :class="INPUT_FIELD_WARN_CLASS">{{ errors.category }}</p>
                                </UFormField>

                                <!-- CHECK ALL -->
                                <UFormField v-if="isInternalProfile" orientation="horizontal" class="mb-2">
                                    <div class="flex items-center w-80 gap-2">
                                        <UCheckbox
                                            v-model="allAccessChecked"
                                            :disabled="isLoadingTree || categoryItems.length === 0 || viewOnly"
                                        />
                                        <span class="font-light" :class="TEXT_SIZE_CLASS">
                                            {{ t('text.button.check-all') || 'Check All' }}
                                        </span>
                                    </div>
                                </UFormField>
                            </div>
                        </div>
                    </template>
                </UTabs>
            </div>
        </template>

        <template #footer>
            <UButton
                v-if="!editMode && !viewOnly"
                :class="`${BUTTON_CLEAR_CLASS} ${TEXT_SIZE_CLASS}`"
                :disabled="isSubmitting"
                @click="resetForm"
            >{{ t('text.button.clear') || 'Clear' }}</UButton>

            <UButton
                v-if="!viewOnly"
                :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`"
                :loading="isSubmitting"
                :disabled="isSubmitting"
                @click="postSubmitUser"
            >
                {{ t('text.button.submit') || 'Submit' }}
            </UButton>

            <UButton
                v-if="viewOnly"
                :class="`${BUTTON_PRIMARY_CLASS} ${TEXT_SIZE_CLASS}`"
                @click="closeModal"
            >
                {{ t('text.button.close') || 'Close' }}
            </UButton>
        </template>
    </UModal>
</template>
