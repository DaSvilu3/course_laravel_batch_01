<?php

namespace App\Models\Concerns;

/**
 * Simple two-column translation strategy: `name_ar` / `name_en`.
 *
 * $service->name          -> value for the active locale
 * $service->translate('description')
 *
 * This is deliberately the simplest thing that works. For a bigger catalog
 * consider a dedicated `translations` table or spatie/laravel-translatable.
 */
trait HasTranslatedAttributes
{
    /** @var list<string> */
    protected array $defaultTranslatable = ['name', 'description'];

    public function translate(string $field): ?string
    {
        $locale = app()->getLocale();
        $fallback = config('app.fallback_locale', 'en');

        return $this->getAttribute("{$field}_{$locale}")
            ?: $this->getAttribute("{$field}_{$fallback}");
    }

    /** @return list<string> */
    public function translatableFields(): array
    {
        return property_exists($this, 'translatable') ? $this->translatable : $this->defaultTranslatable;
    }

    public function getAttribute($key)
    {
        if (in_array($key, $this->translatableFields(), true) && ! array_key_exists($key, $this->attributes)) {
            return $this->translate($key);
        }

        return parent::getAttribute($key);
    }
}
