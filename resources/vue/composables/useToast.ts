import { reactive } from 'vue'

// Global toast state
interface Toast {
  id: number
  title: string
  description?: string
  color?: string
  icon?: string
  timeout?: number
}

const state = reactive({
  toasts: [] as Toast[]
})

let toastId = 0

const getIcon = (color?: string, icon?: string) => {
  if (icon) return icon
  
  switch (color) {
    case 'green':
      return 'i-heroicons-check-circle'
    case 'red':
      return 'i-heroicons-x-circle'
    case 'yellow':
      return 'i-heroicons-exclamation-triangle'
    case 'blue':
      return 'i-heroicons-information-circle'
    default:
      return 'i-heroicons-bell'
  }
}

const add = (options: {
  title: string
  description?: string
  color?: string
  icon?: string
  timeout?: number
}) => {
  const id = ++toastId
  const toast: Toast = {
    id,
    ...options,
    icon: getIcon(options.color, options.icon),
    timeout: options.timeout || 5000
  }
  
  state.toasts.push(toast)
  
  setTimeout(() => {
    remove(id)
  }, toast.timeout)
}

const remove = (id: number) => {
  const index = state.toasts.findIndex(t => t.id === id)
  if (index !== -1) {
    state.toasts.splice(index, 1)
  }
}

export const useToast = () => {
  const showSuccess = (title: string, description?: string, timeout: number = 5000) => {
    add({
      title,
      description,
      color: 'green',
      timeout,
    })
  }

  const showError = (title: string, description?: string, timeout: number = 5000) => {
    add({
      title,
      description,
      color: 'red',
      timeout,
    })
  }

  const showWarning = (title: string, description?: string, timeout: number = 5000) => {
    add({
      title,
      description,
      color: 'yellow',
      timeout,
    })
  }

  const showInfo = (title: string, description?: string, timeout: number = 5000) => {
    add({
      title,
      description,
      color: 'blue',
      timeout,
    })
  }

  return {
    showSuccess,
    showError,
    showWarning,
    showInfo,
    add,
    state,
    remove,
  }
}
