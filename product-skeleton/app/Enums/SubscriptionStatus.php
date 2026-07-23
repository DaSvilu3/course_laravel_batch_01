<?php

namespace App\Enums;

enum SubscriptionStatus: string
{
    /** Created, first payment not settled yet. */
    case Pending = 'pending';

    /** In a free trial. */
    case Trialing = 'trialing';

    /** Paid and running. */
    case Active = 'active';

    /** Term ended without renewal. */
    case Expired = 'expired';

    /** Ended early by the customer or an admin. */
    case Canceled = 'canceled';

    public function label(): string
    {
        return __('enums.subscription_status.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800',
            self::Trialing => 'bg-indigo-100 text-indigo-800',
            self::Active => 'bg-emerald-100 text-emerald-800',
            self::Expired => 'bg-gray-100 text-gray-600',
            self::Canceled => 'bg-rose-100 text-rose-800',
        };
    }

    /** Does this status grant access (before checking the end date)? */
    public function grantsAccess(): bool
    {
        return in_array($this, [self::Trialing, self::Active], true);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->all();
    }
}
