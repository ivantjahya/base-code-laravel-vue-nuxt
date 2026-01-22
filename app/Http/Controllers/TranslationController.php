<?php

namespace App\Http\Controllers;

class TranslationController extends Controller
{
    /**
     * Get translations for a specific locale
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(string $locale)
    {
        // Validate locale
        if (! in_array($locale, ['en', 'id'])) {
            return response()->json([
                'error' => 'Invalid locale',
            ], 400);
        }

        // Load all translation files for the locale
        $translations = [];

        // Get app translations
        $appTranslations = __('app', [], $locale);

        if (is_array($appTranslations)) {
            $translations = $this->flattenTranslations($appTranslations);
        }

        return response()->json($translations);
    }

    /**
     * Flatten nested translation array to dot notation
     */
    private function flattenTranslations(array $array, string $prefix = ''): array
    {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenTranslations($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }
}
