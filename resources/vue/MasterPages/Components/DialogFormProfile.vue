<script setup lang="ts">
import { ref, computed, watch, getCurrentInstance, nextTick } from 'vue'
import { useI18n } from '../../composables/useI18n'
import { useApiStore } from '../../AppState'
import { useGlobalOptions } from '../../composables/useGlobalOptions'
import axios from 'axios'
import type { TreeItem } from '@nuxt/ui'
import type { TreeItemSelectEvent } from 'reka-ui'
import { TEXT_SIZE_CLASS, TEXT_TITLE_SIZE_CLASS, TITLE_MODAL_TEXT_CLASS, INPUT_FIELD_WARN_CLASS, BUTTON_PRIMARY_CLASS, BUTTON_CLEAR_CLASS } from '../../constants'

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
    }
})

const emit = defineEmits(['update:open', 'submitted', 'close'])

const { t } = useI18n()
const api = useApiStore()
const { profileSourceOptions } = useGlobalOptions()
const Swal = getCurrentInstance()?.appContext.config.globalProperties.$swal

const isSubmitting = ref(false)
const isLoadingTree = ref(false)

const profileName = ref<string>('')
const description = ref<string>('')
const profileSource = ref<number | null>(null)

// Tree state
const treeValue = ref<TreeItem[]>([])
const items = ref<TreeItem[]>([])
const treeNodeByValueMap = ref<Record<string, TreeItem>>({})
const menuAccessMap = ref<Record<string, { menu_id: string; acc_control_id: string }>>({})
const controlToMenuMap = ref<Record<string, string>>({})
const menuNodeMap = ref<Record<string, string>>({})

const getTreeItemKey = (item: any) => item?.value

const getLabel = (value: any): string => {
    if (!value || typeof value !== 'object') return ''
    const translated = value.name_code ? t(value.name_code as any) : ''
    return translated || value.name || ''
}

const getMenuId = (value: any): string => {
    if (!value || typeof value !== 'object') return ''
    return value.id || value.menu_id || value.submenu_id || ''
}

const getAccessControlId = (value: any): string => {
    if (!value || typeof value !== 'object') return ''
    return value.id || value.acc_control_id || ''
}

const getControlMenuId = (value: any): string => {
    if (!value || typeof value !== 'object') return ''
    return value.id || value.menu_id || value.parent_menu_id || ''
}

const getNodeValue = (node: any, fallbackLabel: string): string => {
    if (!node || typeof node !== 'object') return fallbackLabel
    return node.value || node.name || node.id || fallbackLabel
}

const mapTreeNode = (node: any, inheritedMenuId = '', nodePath = ''): TreeItem | null => {
    if (!node || typeof node !== 'object') return null

    const label = getLabel(node)
    if (!label) return null

    const menuId = getMenuId(node)
    const effectiveMenuId = menuId || inheritedMenuId
    const nodeIdentity = String(getNodeValue(node, label))
    const currentPath = nodePath ? `${nodePath}/${nodeIdentity}` : nodeIdentity
    const value = `menu:${currentPath}`

    if (effectiveMenuId) {
        menuNodeMap.value[value] = effectiveMenuId
    }

    const childMenus = [
        ...(Array.isArray(node.children) ? node.children : []),
        ...(Array.isArray(node.submenu) ? node.submenu : []),
        ...(Array.isArray(node.menus) ? node.menus : [])
    ]

    const accessControls = [
        ...(Array.isArray(node.access_controls) ? node.access_controls : []),
        ...(Array.isArray(node.acc_controls) ? node.acc_controls : []),
        ...(Array.isArray(node.access_control) ? node.access_control : []),
        ...(Array.isArray(node.menu_acc_controls) ? node.menu_acc_controls : [])
    ]

    const mappedMenuChildren: TreeItem[] = childMenus
        .map((child: any) => mapTreeNode(child, effectiveMenuId, currentPath))
        .filter(Boolean) as TreeItem[]

    const mappedControlChildren: TreeItem[] = accessControls
        .map((control: any) => {
            const controlId = getAccessControlId(control)
            const controlLabel = getLabel(control)
            if (!controlId || !controlLabel) return null

            const controlCode = control.code ? String(control.code) : ''
            const displayLabel = controlLabel ? controlLabel : controlCode

            const controlValue = `acc:${currentPath}:${controlCode}`
            const mappedMenuId = effectiveMenuId || getControlMenuId(control)
            if (mappedMenuId) {
                menuAccessMap.value[controlValue] = {
                    menu_id: mappedMenuId,
                    acc_control_id: controlId
                }
                controlToMenuMap.value[controlId] = mappedMenuId
            }

            const controlNode = {
                label: displayLabel,
                value: controlValue
            } as TreeItem

            treeNodeByValueMap.value[controlValue] = controlNode
            return controlNode
        })
        .filter(Boolean) as TreeItem[]

    const menuNode = {
        label,
        value,
        children: [...mappedMenuChildren, ...mappedControlChildren]
    } as TreeItem

    treeNodeByValueMap.value[value] = menuNode
    return menuNode
}

const loadMenuAccessTree = async () => {
    items.value = []
    isLoadingTree.value = true
    try {
        const response = await axios.get(api.getMenuAccControlList)
        const sourceItems = response?.data?.data?.items || response?.data?.data || response?.data || []
        const sourceArray = Array.isArray(sourceItems) ? sourceItems : []

        menuAccessMap.value = {}
        controlToMenuMap.value = {}
        menuNodeMap.value = {}
        treeNodeByValueMap.value = {}
        items.value = sourceArray
            .map((node: any, index: number) => mapTreeNode(node, '', `root`))
            .filter(Boolean) as TreeItem[]
    } catch (error: any) {
        console.error('Error loading menu access control list:', error.response?.data || error.message)
        items.value = []
        menuAccessMap.value = {}
        controlToMenuMap.value = {}
        menuNodeMap.value = {}
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

const mapInitialTreeValue = (initialData: any): TreeItem[] => {
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

    const rawMenuAccess = initialData?.menuAccess ?? initialData?.menu_access ?? []
    const menuAccessArray = Array.isArray(rawMenuAccess) ? rawMenuAccess : []
    if (menuAccessArray.length === 0) return []

    const resolvedSelection = new Set<string>()

    const addMenuWithAncestors = (menuNodeKey?: string) => {
        if (!menuNodeKey || !menuNodeKey.startsWith('menu:')) return

        const menuPath = menuNodeKey.slice('menu:'.length)
        const parts = menuPath.split('/').filter((part) => !!part)
        for (let index = 1; index <= parts.length; index += 1) {
            resolvedSelection.add(`menu:${parts.slice(0, index).join('/')}`)
        }
    }

    const findMenuNodeKey = (menuId: string): string | undefined => {
        if (!menuId) return undefined
        return Object.keys(menuNodeMap.value).find((key) => menuNodeMap.value[key] === menuId)
    }

    const findAccessNodeKey = (menuId: string, accControlId: string): string | undefined => {
        if (!menuId || !accControlId) return undefined
        return Object.keys(menuAccessMap.value).find((key) => {
            const mapped = menuAccessMap.value[key]
            return mapped?.menu_id === menuId && mapped?.acc_control_id === accControlId
        })
    }

    menuAccessArray.forEach((entry: any) => {
        const menuId = String(entry?.menu_id || entry?.menuId || entry?.id || '')
        const menuNodeKey = findMenuNodeKey(menuId)
        const rawControlList = [
            ...(Array.isArray(entry?.acc_controls) ? entry.acc_controls : []),
            ...(Array.isArray(entry?.access_controls) ? entry.access_controls : []),
            ...(Array.isArray(entry?.menu_acc_controls) ? entry.menu_acc_controls : []),
            ...(Array.isArray(entry?.access_control) ? entry.access_control : [])
        ]

        if (rawControlList.length > 0) {
            rawControlList.forEach((control: any) => {
                const accControlId = String(control?.acc_control_id || control?.id || '')
                const accessNodeKey = findAccessNodeKey(menuId, accControlId)
                if (accessNodeKey) {
                    resolvedSelection.add(accessNodeKey)
                }
            })
            addMenuWithAncestors(menuNodeKey)
            return
        }

        const accControlId = String(entry?.acc_control_id || entry?.accControlId || '')
        if (accControlId) {
            const accessNodeKey = findAccessNodeKey(menuId, accControlId)
            if (accessNodeKey) {
                resolvedSelection.add(accessNodeKey)
            }
            addMenuWithAncestors(menuNodeKey)
            return
        }

        if (menuId) {
            if (menuNodeKey) {
                addMenuWithAncestors(menuNodeKey)
            }
        }
    })

    return resolveTreeNodes(Array.from(resolvedSelection))
}

const selectedMenuAccess = computed(() => {
    const selectedValues = (Array.isArray(treeValue.value) ? treeValue.value : [])
        .map((entry: any) => {
            if (typeof entry === 'string') return entry
            if (entry && typeof entry === 'object') return String(entry.value || entry.id || '')
            return ''
        })
        .filter((value) => !!value)

    const getAncestorMenuValues = (menuPath: string): string[] => {
        if (!menuPath) return []
        const parts = menuPath.split('/').filter((part) => !!part)
        const ancestors: string[] = []
        for (let index = 1; index <= parts.length; index += 1) {
            ancestors.push(`menu:${parts.slice(0, index).join('/')}`)
        }
        return ancestors
    }

    const expandedValues = new Set<string>()
    selectedValues.forEach((value) => {
        expandedValues.add(value)

        if (value.startsWith('menu:')) {
            const menuPath = value.slice('menu:'.length)
            getAncestorMenuValues(menuPath).forEach((ancestor) => expandedValues.add(ancestor))
            return
        }

        if (value.startsWith('acc:')) {
            const rawValue = value.slice('acc:'.length)
            const lastSeparatorIndex = rawValue.lastIndexOf(':')
            const menuPath = lastSeparatorIndex >= 0 ? rawValue.slice(0, lastSeparatorIndex) : ''
            getAncestorMenuValues(menuPath).forEach((ancestor) => expandedValues.add(ancestor))
        }
    })

    const resolved = Array.from(expandedValues)
        .map((value) => {
            const direct = menuAccessMap.value[value]
            if (direct) return direct

            if (value.startsWith('menu:')) {
                const menuId = menuNodeMap.value[value]
                if (menuId) {
                    return {
                        menu_id: menuId,
                        acc_control_id: null
                    }
                }
            }

            const accControlId = value.startsWith('acc:') ? value.split(':').at(-1) || '' : value
            const menuId = controlToMenuMap.value[accControlId]
            if (menuId && accControlId) {
                return {
                    menu_id: menuId,
                    acc_control_id: accControlId
                }
            }

            return null
        })
        .filter((item): item is { menu_id: string; acc_control_id: string | null } => !!item)

    const unique = new Map<string, { menu_id: string; acc_control_id: string | null }>()
    resolved.forEach((item) => {
        unique.set(`${item.menu_id}:${item.acc_control_id ?? 'null'}`, item)
    })

    return Array.from(unique.values())
})

const getSelectedTreeValues = (): Set<string> => {
    const values = (Array.isArray(treeValue.value) ? treeValue.value : [])
        .map((entry: any) => {
            if (typeof entry === 'string') return entry
            if (entry && typeof entry === 'object') return String(entry.value || entry.id || '')
            return ''
        })
        .filter((value) => !!value)

    return new Set(values)
}

const getAllTreeNodes = (): TreeItem[] => {
    const nodes = Object.values(treeNodeByValueMap.value) as TreeItem[]

    const unique = new Map<string, TreeItem>()
    nodes.forEach((node) => {
        const key = String(node?.value || '')
        if (key) unique.set(key, node)
    })

    return Array.from(unique.values())
}

const allAccessChecked = computed({
    get: () => {
        const allNodes = getAllTreeNodes()
        if (allNodes.length === 0) return false

        const selectedValues = getSelectedTreeValues()
        // If we have selected values, check if all nodes are selected
        return allNodes.every((node: any) => selectedValues.has(String(node?.value || '')))
    },
    set: (checked: boolean) => {
        const allNodes = getAllTreeNodes()
        if (!checked || allNodes.length === 0) {
            treeValue.value = []
            return
        }

        treeValue.value = allNodes
    }
})

const isAccessControlNode = (item: any): boolean => String(item?.value || '').startsWith('acc:')

function onSelect(e: TreeItemSelectEvent<TreeItem>, item: any) {
    if (e.detail.originalEvent.type === 'click' && !isAccessControlNode(item)) {
        e.preventDefault()
    }
}

const valueSwitch = ref(true)

// Validation error states
const errors = ref({
    profileName: '',
    description: '',
    profileSource: '',
    accessRight: ''
})

const resetForm = () => {
    profileName.value = ''
    description.value = ''
    profileSource.value = null
    treeValue.value = []
    valueSwitch.value = true

    errors.value = {
        profileName: '',
        description: '',
        profileSource: '',
        accessRight: ''
    }
}

const closeModal = () => {
    resetForm()
    emit('update:open', false)
    emit('close')
}

const debugCheckKeys = () => {
    const allGeneratedKeys = new Set();
    const flatten = (nodes: any[]) => {
        nodes.forEach(n => {
            allGeneratedKeys.add(n.value);
            if (n.children) flatten(n.children);
        });
    }
    flatten(items.value);
    
    treeValue.value.forEach((entry: any) => {
        const key = typeof entry === 'string' ? entry : String(entry?.value || entry?.id || '')
        if (!key) return
        if (!allGeneratedKeys.has(key)) {
            console.error(`❌ Key NOT found in tree: "${key}"`);
        } else {
            console.log(`✅ Key found: "${key}"`);
        }
    });
}

watch(() => props.open, async (newVal) => {
    if (newVal) {
        if ((props.editMode || props.viewOnly) && props.initialData) {
            profileName.value = props.initialData.profileName || ''
            description.value = props.initialData.description || ''
            profileSource.value = props.initialData.profileSource
            valueSwitch.value = props.initialData.status ?? true
        }
        await loadMenuAccessTree()

        if (props.editMode && props.initialData) {
            await nextTick()
            treeValue.value = mapInitialTreeValue(props.initialData)
            // debugCheckKeys()
        } else {
            resetForm()
        }
    }
})

// Submit create/update profile
const postSubmitProfile = async () => {
    // Reset errors
    errors.value = {
        profileName: '',
        description: '',
        profileSource: '',
        accessRight: ''
    }

    // Validation
    let hasError = false

    if (profileName.value.trim() === '') {
        errors.value.profileName = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (profileSource.value == null) {
        errors.value.profileSource = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (selectedMenuAccess.value.length === 0) {
        errors.value.accessRight = t('auth.validation.required' as any) || 'This field is required'
        hasError = true
    }

    if (hasError) {
        return
    }

    isSubmitting.value = true
    try {
        const payload = {
            profile_name: profileName.value,
            profile_description: description.value,
            profile_source: profileSource.value,
            menu_access: selectedMenuAccess.value,
            status: valueSwitch.value ? 1 : 0
        }

        if (props.editMode && props.editingId) {
            await axios.put(`${api.postProfileUpdate}${props.editingId}`, payload)
        } else {
            await axios.post(api.postProfileCreate, payload)
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
        console.error('Error submitting profile:', error.response?.data || error.message)
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
    set: (value) => {
        if (!value) {
            closeModal()
            return
        }
        emit('update:open', value)
    }
})
</script>

<template>
    <UModal
        v-model:open="isOpen"
        :title="title"
        :dismissible="false"
        :class="TITLE_MODAL_TEXT_CLASS"
        :ui="{
            footer: 'justify-end',
            // content: 'h-[500px] flex flex-col', // Fixed height
            // content: 'max-h-[80vh] flex flex-col' // Responsive max height
        }"
    >
        <template #body>
            <!-- PROFILE NAME -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.profileName"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.profile-management-pg.input-new-profile-name') || 'Profile Name' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <UInput
                        v-model="profileName"
                        required
                        :placeholder="t('text.profile-management-pg.input-new-profile-name-placeholder') || 'Enter profile name'"
                        class="w-full"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.profileName
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`
                        }"
                        :disabled="viewOnly"
                    />
                    <p v-if="errors.profileName" :class="INPUT_FIELD_WARN_CLASS">{{ errors.profileName }}</p>
                </div>
            </UFormField>

            <!-- DESCRIPTION -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.description"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.profile-management-pg.input-new-description') || 'Description' }}
                    </span>
                </template>

                <div class="w-80">
                    <UTextarea
                        v-model="description"
                        :maxrows="5"
                        autoresize
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.description
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`
                        }"
                        :disabled="viewOnly"
                    />
                </div>
            </UFormField>

            <!-- PROFILE SOURCE -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.profileSource"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.profile-management-pg.input-new-profile-source') || 'Profile Source' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80">
                    <USelectMenu
                        v-model="profileSource"
                        :items="profileSourceOptions"
                        value-key="id"
                        value-attribute="id"
                        option-attribute="label"
                        :placeholder="t('text.input-field.profile-source-placeholder') || 'Select profile source'"
                        class="w-full font-light"
                        :ui="{
                            base: `${TEXT_SIZE_CLASS} ${
                                errors.profileSource
                                ? 'ring-2 ring-[#FB2C36] focus-within:ring-[#FB2C36]'
                                : ''
                            }`,
                            content: TEXT_SIZE_CLASS,
                            item: TEXT_SIZE_CLASS
                        }"
                        :disabled="viewOnly"
                    />
                    <p v-if="errors.profileSource" :class="INPUT_FIELD_WARN_CLASS">{{ errors.profileSource }}</p>
                </div>
            </UFormField>

            <!-- ACCESS RIGHT -->
            <UFormField
                orientation="horizontal"
                class="mb-2"
                :error="!!errors.accessRight"
                :ui="{
                    error: 'hidden',
                }"
            >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.profile-management-pg.input-new-access-right') || 'Access Right' }}
                        <span class="text-red-500">*</span>
                    </span>
                </template>

                <div class="w-80 max-h-50 overflow-y-auto border border-gray-300 rounded-md p-2 dark:border-gray-700">
                    <UTree
                        v-model="treeValue"
                        :as="{ link: 'div' }"
                        :items="items"
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
                                v-if="isAccessControlNode(item)"
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
                <p v-if="errors.accessRight" :class="INPUT_FIELD_WARN_CLASS">{{ errors.accessRight }}</p>
            </UFormField>

            <!-- CHECK ALL -->
            <UFormField orientation="horizontal" class="mb-2">
                <div class="flex items-center w-80 gap-2">
                    <UCheckbox
                        v-model="allAccessChecked"
                        :disabled="isLoadingTree || items.length === 0 || viewOnly"
                    />
                    <span class="font-light" :class="TEXT_SIZE_CLASS">
                        {{ t('text.button.check-all') || 'Check All' }}
                    </span>
                </div>
            </UFormField>

            <!-- STATUS -->
            <UFormField orientation="horizontal" class="mb-2" >
                <template #label>
                    <span class="flex items-center gap-1" :class="TEXT_SIZE_CLASS">
                        {{ t('text.profile-management-pg.input-new-status') || 'Status' }}
                    </span>
                </template>

                <div class="flex justify-start w-80">
                    <USwitch v-model="valueSwitch" :disabled="viewOnly" />
                </div>
            </UFormField>
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
                @click="postSubmitProfile"
            >
                {{ t('text.button.submit') || 'Submit' }}
            </UButton>

            <!-- Close button — shown only in view-only mode -->
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
