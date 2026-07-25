<?php

namespace App\Enums;

enum OrderSource: string
{
    /** Submitted by the customer through the public intake form. */
    case Form = 'form';

    /** Entered by the merchant from the dashboard. */
    case Manual = 'manual';

    public function label(): string
    {
        return __('enums.order_source.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Form => 'bg-violet-100 text-violet-800 dark:bg-violet-500/15 dark:text-violet-300',
            self::Manual => 'bg-ink-100 text-ink-700 dark:bg-ink-700/40 dark:text-ink-300',
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->all();
    }
}
