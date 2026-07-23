<?php

namespace App\Support;

/**
 * Money helpers.
 *
 * Everything in this application stores money as an INTEGER number of baisa.
 * 1 OMR = 1000 baisa. Thawani's API also expects baisa (`unit_amount`), so we
 * never convert until we print something on screen.
 *
 * Why integers? 0.1 + 0.2 !== 0.3 in floating point. Money and floats do not mix.
 */
class Money
{
    public const SUBUNITS = 1000;

    /** "12.500" or 12.5 -> 12500 baisa */
    public static function toBaisa(float|int|string $amount): int
    {
        return (int) round(((float) $amount) * self::SUBUNITS);
    }

    /** 12500 baisa -> 12.5 */
    public static function toDecimal(int $baisa): float
    {
        return $baisa / self::SUBUNITS;
    }

    /** 12500 -> "12.500" (Omani rial is quoted with 3 decimals) */
    public static function decimalString(int $baisa): string
    {
        return number_format(self::toDecimal($baisa), 3, '.', '');
    }

    /** 12500 -> "12.500 ر.ع." / "OMR 12.500" */
    public static function format(int $baisa, ?string $currency = null): string
    {
        $currency = $currency ?? config('payments.currency', 'OMR');
        $amount = number_format(self::toDecimal($baisa), 3);

        if (app()->getLocale() === 'ar') {
            return $amount.' '.__('common.currency_symbol');
        }

        return $currency.' '.$amount;
    }
}
