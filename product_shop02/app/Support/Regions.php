<?php

namespace App\Support;

/**
 * Resolves delivery region keys (config/regions.php) into localized labels and
 * grouped option lists. Oman is modelled as governorates → wilayats.
 */
class Regions
{
    protected static function label(array $names): string
    {
        return $names[app()->getLocale()] ?? $names['en'] ?? reset($names);
    }

    public static function defaultCountry(): string
    {
        return (string) config('regions.default_country', 'OM');
    }

    public static function countries(): array
    {
        return array_keys((array) config('regions.countries', []));
    }

    /** ['OM' => 'عُمان', ...] */
    public static function countryOptions(): array
    {
        return collect((array) config('regions.countries', []))
            ->map(fn (array $names) => self::label($names))
            ->all();
    }

    public static function countryLabel(?string $code): ?string
    {
        $names = config('regions.countries.'.$code);

        return $code && is_array($names) ? self::label($names) : $code;
    }

    // ---------------------------------------------------------- governorates

    public static function governorateKeys(): array
    {
        return array_keys((array) config('regions.governorates', []));
    }

    /** ['muscat' => 'مسقط', ...] */
    public static function governorateOptions(): array
    {
        return collect((array) config('regions.governorates', []))
            ->map(fn (array $gov) => self::label($gov['name']))
            ->all();
    }

    public static function governorateLabel(?string $key): ?string
    {
        $gov = config('regions.governorates.'.$key);

        return $key && is_array($gov) ? self::label($gov['name']) : $key;
    }

    // ---------------------------------------------------------------- wilayats

    /** All wilayat keys, for validation. */
    public static function wilayatKeys(): array
    {
        return collect((array) config('regions.governorates', []))
            ->flatMap(fn (array $gov) => array_keys($gov['wilayats'] ?? []))
            ->all();
    }

    /** Grouped for <optgroup>: ['مسقط' => ['muscat' => 'مسقط', ...], ...] */
    public static function wilayatGroups(): array
    {
        return collect((array) config('regions.governorates', []))
            ->mapWithKeys(fn (array $gov) => [
                self::label($gov['name']) => collect($gov['wilayats'] ?? [])
                    ->map(fn (array $names) => self::label($names))
                    ->all(),
            ])
            ->all();
    }

    public static function wilayatLabel(?string $key): ?string
    {
        foreach ((array) config('regions.governorates', []) as $gov) {
            if (isset($gov['wilayats'][$key])) {
                return self::label($gov['wilayats'][$key]);
            }
        }

        return $key;
    }

    /** The governorate key a wilayat belongs to. */
    public static function governorateOfWilayat(?string $key): ?string
    {
        foreach ((array) config('regions.governorates', []) as $govKey => $gov) {
            if (isset($gov['wilayats'][$key])) {
                return $govKey;
            }
        }

        return null;
    }
}
