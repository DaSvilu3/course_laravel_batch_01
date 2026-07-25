<?php

namespace App\Enums;

enum MerchantOrderStatus: string
{
    /** Just came in through the intake form. */
    case New = 'new';

    /** Merchant confirmed it (usually after the WhatsApp chat). */
    case Confirmed = 'confirmed';

    /** Being prepared / packed. */
    case Preparing = 'preparing';

    /** Handed to delivery / on its way. */
    case OutForDelivery = 'out_for_delivery';

    /** Delivered to the customer. */
    case Delivered = 'delivered';

    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __('enums.merchant_order_status.'.$this->value);
    }

    /** Tailwind classes used by the status badge component. */
    public function color(): string
    {
        return match ($this) {
            self::New => 'bg-brand-100 text-brand-800 dark:bg-brand-500/15 dark:text-brand-300',
            self::Confirmed => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-500/15 dark:text-indigo-300',
            self::Preparing => 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300',
            self::OutForDelivery => 'bg-violet-100 text-violet-800 dark:bg-violet-500/15 dark:text-violet-300',
            self::Delivered => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300',
            self::Cancelled => 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-300',
        };
    }

    public function isOpen(): bool
    {
        return ! in_array($this, [self::Delivered, self::Cancelled], true);
    }

    /** Ordered position on the tracking timeline (Cancelled sits outside it). */
    public function step(): int
    {
        return match ($this) {
            self::New => 1,
            self::Confirmed => 2,
            self::Preparing => 3,
            self::OutForDelivery => 4,
            self::Delivered => 5,
            self::Cancelled => 0,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->all();
    }
}
