<?php

namespace App\Enums;

enum OrderStatus: string
{
    /** Just received, not started yet. */
    case New = 'new';

    /** The merchant is preparing / fulfilling the order. */
    case InProgress = 'in_progress';

    /** Delivered / handed over. */
    case Completed = 'completed';

    case Cancelled = 'cancelled';

    public function label(): string
    {
        return __('enums.order_status.'.$this->value);
    }

    /** Tailwind classes used by the status badge component. */
    public function color(): string
    {
        return match ($this) {
            self::New => 'bg-blue-100 text-blue-800 dark:bg-blue-500/15 dark:text-blue-300',
            self::InProgress => 'bg-amber-100 text-amber-800 dark:bg-amber-500/15 dark:text-amber-300',
            self::Completed => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/15 dark:text-emerald-300',
            self::Cancelled => 'bg-rose-100 text-rose-800 dark:bg-rose-500/15 dark:text-rose-300',
        };
    }

    /** Still active in the pipeline (not completed or cancelled). */
    public function isOpen(): bool
    {
        return in_array($this, [self::New, self::InProgress], true);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $s) => [$s->value => $s->label()])
            ->all();
    }
}
