<script setup lang="ts">
import { useToast } from '../composables/useToast'

const { state, remove } = useToast()

const getColorClasses = (color?: string) => {
  switch (color) {
    case 'green':
      return 'bg-green-50 border-green-500 text-green-900'
    case 'red':
      return 'bg-red-50 border-red-500 text-red-900'
    case 'yellow':
      return 'bg-yellow-50 border-yellow-500 text-yellow-900'
    case 'blue':
      return 'bg-blue-50 border-blue-500 text-blue-900'
    case 'gray':
      return 'bg-gray-50 border-gray-500 text-gray-900'
    default:
      return 'bg-white border-gray-300 text-gray-900'
  }
}
</script>

<template>
  <div class="fixed top-4 right-4 z-50 flex flex-col gap-3 max-w-md pointer-events-none">
    <TransitionGroup name="toast">
      <div
        v-for="toast in state.toasts"
        :key="toast.id"
        :class="[
          'flex items-start gap-3 p-4 rounded-lg border-l-4 shadow-lg pointer-events-auto',
          getColorClasses(toast.color)
        ]"
      >
        <UIcon v-if="toast.icon" :name="toast.icon" class="w-5 h-5 flex-shrink-0 mt-0.5" />
        <div class="flex-1 min-w-0">
          <p class="font-semibold text-sm">{{ toast.title }}</p>
          <p v-if="toast.description" class="text-sm mt-1 opacity-80">{{ toast.description }}</p>
        </div>
        <button
          @click="remove(toast.id)"
          class="flex-shrink-0 opacity-60 hover:opacity-100 transition-opacity"
        >
          <UIcon name="i-heroicons-x-mark" class="w-4 h-4" />
        </button>
      </div>
    </TransitionGroup>
  </div>
</template>

<style scoped>
.toast-enter-active,
.toast-leave-active {
  transition: all 0.3s ease;
}

.toast-enter-from {
  opacity: 0;
  transform: translateX(100%);
}

.toast-leave-to {
  opacity: 0;
  transform: translateX(100%);
}

.toast-move {
  transition: transform 0.3s ease;
}
</style>
