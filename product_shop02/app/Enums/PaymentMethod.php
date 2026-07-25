<?php

namespace App\Enums;

enum PaymentMethod: string
{
    /** Cash on delivery / on hand-over. */
    case Cod = 'cod';

    /** Bank / wallet transfer. */
    case Transfer = 'transfer';

    public function label(): string
    {
        return __('enums.payment_method.'.$this->value);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $m) => [$m->value => $m->label()])
            ->all();
    }
}
