<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Carbon;

/**
 * Enforces how many orders a merchant may receive within their plan's window.
 * Limits live in the plan's `features` JSON as:
 *   orders_limit  (int, null/-1 = unlimited)
 *   orders_period ('day' | 'month')
 *
 * A merchant without an active subscription falls back to the free plan.
 */
class OrderQuotaService
{
    private ?Plan $freePlan = null;

    /** The plan that currently governs the merchant's quota. */
    public function planFor(User $merchant): ?Plan
    {
        return $merchant->currentPlan() ?? $this->freePlan();
    }

    public function freePlan(): ?Plan
    {
        return $this->freePlan ??= Plan::where('slug', 'free')->first()
            ?? Plan::where('price', 0)->ordered()->first();
    }

    /** null means unlimited. */
    public function limit(User $merchant): ?int
    {
        $limit = $this->planFor($merchant)?->feature('orders_limit');

        if ($limit === null || (int) $limit === -1) {
            return null;
        }

        return (int) $limit;
    }

    public function period(User $merchant): string
    {
        return $this->planFor($merchant)?->feature('orders_period', 'month') ?? 'month';
    }

    protected function windowStart(User $merchant): Carbon
    {
        return $this->period($merchant) === 'day'
            ? now()->startOfDay()
            : now()->startOfMonth();
    }

    /** Orders received within the current window. */
    public function used(User $merchant): int
    {
        return $merchant->orders()
            ->where('created_at', '>=', $this->windowStart($merchant))
            ->count();
    }

    /** null means unlimited. */
    public function remaining(User $merchant): ?int
    {
        $limit = $this->limit($merchant);

        return $limit === null ? null : max(0, $limit - $this->used($merchant));
    }

    public function hasReachedLimit(User $merchant): bool
    {
        $remaining = $this->remaining($merchant);

        return $remaining !== null && $remaining <= 0;
    }

    /** 0..100 for the usage bar; 0 when unlimited. */
    public function usagePercent(User $merchant): int
    {
        $limit = $this->limit($merchant);

        if ($limit === null || $limit === 0) {
            return 0;
        }

        return (int) min(100, round($this->used($merchant) / $limit * 100));
    }
}
