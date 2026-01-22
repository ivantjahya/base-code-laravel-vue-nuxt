import { ref, computed } from 'vue'

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

const currentLocale = ref<'en' | 'id'>('en')
const isLoading = ref(false)
const isLoaded = ref(false)

// Load translations from Laravel API
async function loadTranslations(locale: 'en' | 'id') {
  if (translations.value[locale] && Object.keys(translations.value[locale]).length > 0) {
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
  } catch (error) {
    console.error(`Error loading translations for ${locale}:`, error)
    // Fallback translations if API fails
    translations.value[locale] = getFallbackTranslations(locale)
  } finally {
    isLoading.value = false
    isLoaded.value = true
  }
}

// Fallback translations in case API fails
function getFallbackTranslations(locale: 'en' | 'id'): Translations {
  if (locale === 'en') {
    return {
      'page.login': 'Login',
      'page.dashboard': 'Dashboard',
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
      'page.dashboard': 'Beranda',
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
  const t = (key: TranslationKey): string => {
    const locale = currentLocale.value
    const localeTranslations = translations.value[locale]
    
    if (!localeTranslations || Object.keys(localeTranslations).length === 0) {
      // Return fallback while loading
      return getFallbackTranslations(locale)[key] || key
    }
    
    return localeTranslations[key] || key
  }

  const setLocale = async (locale: 'en' | 'id') => {
    currentLocale.value = locale
    await loadTranslations(locale)
  }

  const locale = computed(() => currentLocale.value)

  // Initialize with default locale
  if (!isLoaded.value) {
    loadTranslations(currentLocale.value)
  }

  return {
    t,
    locale,
    setLocale,
    isLoading: computed(() => isLoading.value),
  }
}
