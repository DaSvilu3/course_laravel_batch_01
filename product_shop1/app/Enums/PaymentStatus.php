<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case Pending = 'pending';
    case Paid = 'paid';
    case Failed = 'failed';
    case Cancelled = 'cancelled';
    case Refunded = 'refunded';

    public function label(): string
    {
        return __('enums.payment_status.'.$this->value);
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300',
            self::Paid => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300',
            self::Failed, self::Cancelled => 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-300',
            self::Refunded => 'bg-slate-100 text-slate-800 dark:bg-slate-500/15 dark:text-slate-300',
        };
    }

    /**
     * Map Thawani's `payment_status` field onto our own status.
     *
     * @see https://thawani-technologies.stoplight.io
     */
    public static function fromThawani(?string $status): self
    {
        return match ($status) {
            'paid' => self::Paid,
            'cancelled' => self::Cancelled,
            'expired', 'failed' => self::Failed,
            default => self::Pending,
        };
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->all();
    }
}
