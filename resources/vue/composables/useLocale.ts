export const localeOptions = [
  { label: 'English', id: 'en' },
  { label: 'Indonesia', id: 'id' },
] as const

export type LocaleOption = typeof localeOptions[number]
export type LocaleId = LocaleOption['id']
