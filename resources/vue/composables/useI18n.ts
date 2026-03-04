/**
 * useI18n Composable
 *
 * This composable provides internationalization (i18n) functionality for the application.
 *
 * SINGLE SOURCE OF TRUTH: Translation files are located at:
 * - resources/vue/locales/en.json (English translations)
 * - resources/vue/locales/id.json (Indonesian translations)
 *
 * Both frontend (Vue) and backend (Laravel) use these same JSON files:
 * - Frontend: Imported directly at build time
 * - Backend: Loaded dynamically via lang/xx/app.php loaders
 *
 * When updating translations:
 * 1. Edit the JSON files (resources/vue/locales/xx.json)
 * 2. Add the TypeScript type in TranslationKey (if new key)
 * 3. Both frontend and backend will automatically use the updated translations
 *
 * Translation keys use dot notation (e.g., 'menu.home', 'text.button.new')
 */
import { computed } from 'vue'
import { useMainStore } from '../AppState'

// Import translation files
import enTranslations from '../../../lang/en.json'
import idTranslations from '../../../lang/id.json'

// Define translation type
type TranslationKey =
  | 'menu.home'
  | 'menu.master-data'
  | 'menu.new-registration'
  | 'menu.purchase-order'
  | 'menu.consignment'
  | 'menu.principal-report'
  | 'menu.finance'
  | 'menu.dc-fee'
  | 'menu.tax-supplier-data'
  | 'page.explore-nuxt'
  | 'page.login'
  | 'page.logout'
  | 'page.home'
  | 'page.limit-management'
  | 'page.profile-management'
  | 'page.functional-profile-management'
  | 'page.user-management'
  | 'page.approval-flow-management'
  | 'page.regional-site-management'
  | 'page.user-guide-management'
  | 'text.login-pg.welcome'
  | 'text.login-pg.welcome-msg'
  | 'text.login-pg.new-supplier-msg'
  | 'text.login-pg.register'
  | 'text.home-pg.welcome'
  | 'text.home-pg.welcome-msg'
  | 'text.limit-management-pg.list'
  | 'text.button.new'
  | 'text.button.find'
  | 'text.button.clear'
  | 'text.button.submit'
  | 'text.button.edit'
  | 'text.button.extend'
  | 'text.button.view'
  | 'text.button.close'
  | 'text.button.check-all'
  | 'text.button.functional-profile'
  | 'text.button.add-row'
  | 'text.input-field.username'
  | 'text.input-field.username-placeholder'
  | 'text.input-field.password'
  | 'text.input-field.search'
  | 'text.input-field.more-filters'
  | 'text.input-field.limit-code'
  | 'text.input-field.min-amount'
  | 'text.input-field.max-amount'
  | 'text.input-field.start-date'
  | 'text.input-field.end-date'
  | 'text.dropdown.select-language'
  | 'text.table.rows-per-page'
  | 'text.table.showing'
  | 'text.table.to'
  | 'text.table.of'
  | 'text.table.entries'
  | 'text.table-column.column-no'
  | 'text.table-column.column-limit-code'
  | 'text.table-column.column-minimum-amount'
  | 'text.table-column.column-maximum-amount'
  | 'text.table-column.column-start-date'
  | 'text.table-column.column-end-date'
  | 'text.table-column.column-profile-code'
  | 'text.table-column.column-profile-name'
  | 'text.table-column.column-profile-source'
  | 'text.table-column.column-functional-profile-code'
  | 'text.table-column.column-functional-profile-name'
  | 'text.table-column.column-profile'
  | 'text.table-column.column-division'
  | 'text.table-column.column-limit'
  | 'text.table-column.column-company'
  | 'text.table-column.column-username'
  | 'text.table-column.column-name'
  | 'text.table-column.column-category'
  | 'text.table-column.column-site'
  | 'text.table-column.column-validity-date'
  | 'text.table-column.column-site-code'
  | 'text.table-column.column-site-initial'
  | 'text.table-column.column-site-name'
  | 'text.table-column.column-site-address'
  | 'text.table-column.column-site-city'
  | 'text.table-column.column-site-region'
  | 'text.table-column.column-site-im-auto-flag'
  | 'text.table-column.column-site-dc-flag'
  | 'text.table-column.column-company-code'
  | 'text.table-column.column-company-initial'
  | 'text.table-column.column-company-name'
  | 'text.table-column.column-company-address'
  | 'text.table-column.column-company-city'
  | 'text.table-column.column-company-region'
  | 'text.table-column.column-regional-code'
  | 'text.table-column.column-regional-initial'
  | 'text.table-column.column-regional-name'
  | 'text.table-column.column-regional-address'
  | 'text.table-column.column-regional-city'
  | 'text.table-column.column-regional-region'
  | 'text.table-column.column-regional-code-kontrabon'
  | 'text.table-column.column-regional-initial-kontrabon'
  | 'text.table-column.column-regional-name-kontrabon'
  | 'text.table-column.column-regional-address-kontrabon'
  | 'text.table-column.column-regional-city-kontrabon'
  | 'text.table-column.column-regional-region-kontrabon'
  | 'text.table-column.column-ebs-code'
  | 'text.table-column.column-ebs-initial'
  | 'text.table-column.column-ebs-name'
  | 'text.table-column.column-approval-flow-code'
  | 'text.table-column.column-user-guide-code'
  | 'text.table-column.column-user-guide-name'
  | 'text.table-column.column-user-guide-menu'
  | 'text.table-column.column-status'
  | 'text.table-column.column-description'
  | 'text.table-column.column-code'
  | 'auth.login-success.title'
  | 'auth.login-success.message'
  | 'auth.logout-success.title'
  | 'auth.logout-success.message'
  | 'auth.validation.username-required'
  | 'auth.validation.password-required'
  | 'auth.validation.username-incorrect'
  | 'auth.validation.password-incorrect'
  | 'auth.validation.required'
  | 'auth.validation.amount-max-greater-than-min'
  | 'auth.validation.inactive-user'
  | 'auth.validation.locked-user'
  | 'auth.validation.expired-user'
  | 'token.generated.title'
  | 'token.generated.message'
  | 'token.revoked.title'
  | 'token.revoked.message'

type Translations = Record<string, string>

// Flatten nested object to dot notation
function flattenObject(obj: any, prefix = ''): Translations {
  const flattened: Translations = {}

  for (const key in obj) {
    const value = obj[key]
    const newKey = prefix ? `${prefix}.${key}` : key

    if (typeof value === 'object' && value !== null && !Array.isArray(value)) {
      Object.assign(flattened, flattenObject(value, newKey))
    } else {
      flattened[newKey] = String(value)
    }
  }

  return flattened
}

// Load and flatten translations from imported files
function getTranslations(locale: 'en' | 'id'): Translations {
  const sourceTranslations = locale === 'en' ? enTranslations : idTranslations
  return flattenObject(sourceTranslations)
}

export function useI18n() {
  const mainStore = useMainStore()

  const t = (key: TranslationKey): string => {
    const locale = mainStore.locale
    const localeTranslations = getTranslations(locale)
    return localeTranslations[key] || key
  }

  const setLocale = (locale: 'en' | 'id') => {
    mainStore.setLocale(locale)
  }

  return {
    t,
    locale: computed(() => mainStore.locale),
    setLocale,
  }
}
