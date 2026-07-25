<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

enum BillingInterval: string
{
    case Month = 'month';
    case Year = 'year';

    public function label(): string
    {
        return __('enums.billing_interval.'.$this->value);
    }

    /** Push a date forward by one billing period. */
    public function advance(Carbon $from): Carbon
    {
        return match ($this) {
            self::Month => $from->copy()->addMonthNoOverflow(),
            self::Year => $from->copy()->addYear(),
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $i) => [$i->value => $i->label()])
            ->all();
    }
}
