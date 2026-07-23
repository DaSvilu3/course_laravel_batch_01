<?php

namespace App\Support;

/**
 * Everything the layouts need to know about the active language.
 */
class Locale
{
    /** @return list<string> */
    public static function supported(): array
    {
        return config('app.supported_locales', ['ar', 'en']);
    }

    public static function isRtl(?string $locale = null): bool
    {
        $locale = $locale ?: app()->getLocale();

        return in_array($locale, config('app.rtl_locales', ['ar']), true);
    }

    /** "rtl" or "ltr", for the dir attribute on <html>. */
    public static function direction(?string $locale = null): string
    {
        return self::isRtl($locale) ? 'rtl' : 'ltr';
    }

    /** BCP 47 tag for the lang attribute, e.g. "ar-OM". */
    public static function tag(?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();

        return match ($locale) {
            'ar' => 'ar-OM',
            default => str_replace('_', '-', $locale),
        };
    }

    /** Native name of each language, for the switcher. */
    public static function name(string $locale): string
    {
        return match ($locale) {
            'ar' => 'العربية',
            'en' => 'English',
            default => strtoupper($locale),
        };
    }
}
