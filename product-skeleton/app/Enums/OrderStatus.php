<?php

namespace App\Enums;

enum OrderStatus: string
{
    /** Created, nothing paid yet. */
    case Pending = 'pending';

    /** Checkout session opened at the gateway, waiting for the customer. */
    case AwaitingPayment = 'awaiting_payment';

    /** Money received. */
    case Paid = 'paid';

    /** Being fulfilled (service delivered / product shipped). */
    case Processing = 'processing';

    case Completed = 'completed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return __('enums.order_status.'.$this->value);
    }

    /** Tailwind classes used by the status badge component. */
    public function color(): string
    {
        return match ($this) {
            self::Pending, self::AwaitingPayment => 'bg-amber-100 text-amber-800',
            self::Paid, self::Processing => 'bg-blue-100 text-blue-800',
            self::Completed => 'bg-emerald-100 text-emerald-800',
            self::Cancelled, self::Refunded => 'bg-rose-100 text-rose-800',
        };
    }

    public function isPaid(): bool
    {
        return in_array($this, [self::Paid, self::Processing, self::Completed], true);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->all();
    }
}
