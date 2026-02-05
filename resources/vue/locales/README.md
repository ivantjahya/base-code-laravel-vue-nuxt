# Translation Files - Single Source of Truth

## Overview
This project uses **ONE** set of translation files for both frontend (Vue) and backend (Laravel):
- **`resources/vue/locales/en.json`** - English translations
- **`resources/vue/locales/id.json`** - Indonesian translations

## How It Works

### Frontend (Vue/TypeScript)
The `useI18n.ts` composable imports these JSON files directly at build time.

### Backend (Laravel/PHP)
The Laravel language files (`lang/en/app.php` and `lang/id/app.php`) dynamically load translations from these JSON files at runtime.

**Both frontend and backend read from the same JSON files!**

## Updating Translations

✅ **DO**: Edit `resources/vue/locales/en.json` or `resources/vue/locales/id.json`

❌ **DON'T**: Edit `lang/en/app.php` or `lang/id/app.php` (these are loaders only)

### To Add/Update Translations:

1. Edit the JSON file: `resources/vue/locales/en.json` and/or `resources/vue/locales/id.json`
2. Add the TypeScript type in `useI18n.ts` under the `TranslationKey` type (for type safety)
3. Done! Both frontend and backend will use the updated translations

## File Structure

**Single Source** (`resources/vue/locales/en.json`):
```json
{
  "menu": {
    "home": "Home",
    "master-data": "Master Data"
  }
}
```

**Laravel Loader** (`lang/en/app.php`):
```php
<?php
// This file loads from resources/vue/locales/en.json
// DO NOT EDIT - Edit the JSON file instead
$jsonPath = resource_path('vue/locales/en.json');
return json_decode(file_get_contents($jsonPath), true);
```

## Usage in Vue Components

```typescript
import { useI18n } from '@/composables/useI18n'

const { t, locale, setLocale } = useI18n()

// Get translation
const homeText = t('menu.home')

// Change language
setLocale('id') // or 'en'

// Get current locale
console.log(locale.value) // 'en' or 'id'
```

## Benefits of Current Approach
✅ No API calls - faster loading  
✅ No async loading issues  
✅ Translations bundled with the app  
✅ Type-safe translation keys  
✅ Works offline  

## Migration Notes
Previous version used API endpoint `/api/translations/{locale}` which was slow. Now translations are loaded directly from imported JSON files at build time.
