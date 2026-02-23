import { computed } from 'vue'
import { useI18n } from './useI18n'

export function useGlobalOptions() {
  const { t } = useI18n()

  const statusOptions = computed(() => [
    { id: 1, label: t('text.message.active' as any) || 'Active' },
    { id: 0, label: t('text.message.not-active' as any) || 'Not Active' },
  ])

  const profileSourceOptions = computed(() => [
    { id: 1, label: t('text.message.internal' as any) || 'Internal' },
    { id: 0, label: t('text.message.external' as any) || 'External' },
  ])

  return {
    statusOptions,
    profileSourceOptions,
  }
}
