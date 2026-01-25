import { ref, computed, watch } from 'vue'
import { useMainStore } from '../AppState'

// Define translation type
type TranslationKey = 
  | 'page.login'
  | 'page.dashboard'
  | 'text.login-pg.welcome'
  | 'text.login-pg.welcome-msg'
  | 'text.login-pg.new-supplier-msg'
  | 'text.login-pg.register'
  | 'form.username'
  | 'form.password'
  | 'form.username-placeholder'

type Translations = Record<string, string>

// Language translations - will be loaded from API
const translations = ref<Record<'en' | 'id', Translations>>({
  en: {},
  id: {},
})

const isLoading = ref(false)
const loadedLocales = new Set<string>()

// Load translations from Laravel API
async function loadTranslations(locale: 'en' | 'id') {
  if (loadedLocales.has(locale)) {
    return // Already loaded
  }

  isLoading.value = true
  try {
    const response = await fetch(`/api/translations/${locale}`)
    if (!response.ok) {
      throw new Error(`Failed to load translations for ${locale}`)
    }
    const data = await response.json()
    translations.value[locale] = data
    loadedLocales.add(locale)
  } catch (error) {
    console.error(`Error loading translations for ${locale}:`, error)
    // Fallback translations if API fails
    translations.value[locale] = getFallbackTranslations(locale)
    loadedLocales.add(locale)
  } finally {
    isLoading.value = false
  }
}

// Fallback translations in case API fails
function getFallbackTranslations(locale: 'en' | 'id'): Translations {
  if (locale === 'en') {
    return {
      'page.login': 'Login',
      'page.logout': 'Logout',
      'page.dashboard': 'Dashboard',
      'text.dropdown.select-language': 'Select language',
      'text.login-pg.welcome': 'Welcome',
      'text.login-pg.welcome-msg': 'Welcome to Venditore Plus - please login first before accessing the website.',
      'text.login-pg.new-supplier-msg': 'Are you a new supplier?',
      'text.login-pg.register': 'Register',
      'form.username': 'Username',
      'form.password': 'Password',
      'form.username-placeholder': 'Enter username',
    }
  } else {
    return {
      'page.login': 'Login',
      'page.logout': 'Logout',
      'page.dashboard': 'Beranda',
      'text.dropdown.select-language': 'Pilih bahasa',
      'text.login-pg.welcome': 'Selamat datang',
      'text.login-pg.welcome-msg': 'Selamat datang di Venditore Plus - silakan login terlebih dahulu sebelum mengakses situs web.',
      'text.login-pg.new-supplier-msg': 'Apakah Anda mitra baru?',
      'text.login-pg.register': 'Daftar',
      'form.username': 'Nama Pengguna',
      'form.password': 'Kata Sandi',
      'form.username-placeholder': 'Masukkan nama pengguna',
    }
  }
}

export function useI18n() {
  const mainStore = useMainStore()

  const t = (key: TranslationKey): string => {
    const locale = mainStore.locale
    const localeTranslations = translations.value[locale]
    
    if (!localeTranslations || Object.keys(localeTranslations).length === 0) {
      // Return fallback while loading
      return getFallbackTranslations(locale)[key] || key
    }
    
    return localeTranslations[key] || key
  }

  const setLocale = async (locale: 'en' | 'id') => {
    mainStore.setLocale(locale)
    await loadTranslations(locale)
  }

  // Load translations when locale changes
  watch(() => mainStore.locale, (newLocale) => {
    loadTranslations(newLocale)
  }, { immediate: true })

  return {
    t,
    locale: computed(() => mainStore.locale),
    setLocale,
    isLoading: computed(() => isLoading.value),
  }
}
