<?php

namespace App\Services;

use App\Enums\SubscriptionStatus;
use App\Events\SubscriptionStarted;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    /**
     * Create a pending subscription for a plan. The caller then sends it
     * through CheckoutService (unless it is free, which activates at once).
     */
    public function subscribe(User $user, Plan $plan): Subscription
    {
        $subscription = Subscription::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => SubscriptionStatus::Pending,
            'plan_name' => $plan->translate('name'),
            'price' => $plan->price,
            'interval' => $plan->interval,
            'currency' => config('payments.currency', 'OMR'),
            'trial_ends_at' => $plan->trial_days > 0 ? now()->addDays($plan->trial_days) : null,
        ]);

        // Free plans (and pure trials) need no payment — grant access now.
        if ($plan->isFree()) {
            $this->activate($subscription);
        }

        return $subscription;
    }

    /**
     * Turn a paid (or free) subscription on and open / extend the billing term.
     * Handles three cases from one place:
     *   - renewal  : still inside the term  -> extend from the current end date
     *   - trial    : trial not finished yet -> term starts at the trial end
     *   - new/lapsed: everything else       -> term starts now
     */
    public function activate(Subscription $subscription): Subscription
    {
        return DB::transaction(function () use ($subscription) {
            // One active subscription per user: retire the others.
            $this->supersedeOthers($subscription);

            $now = now();

            if ($subscription->ends_at && $subscription->ends_at->isFuture()) {
                $status = SubscriptionStatus::Active;
                $termEnd = $subscription->interval->advance($subscription->ends_at);
            } elseif ($subscription->trial_ends_at && $subscription->trial_ends_at->isFuture()) {
                $status = SubscriptionStatus::Trialing;
                $termEnd = $subscription->interval->advance($subscription->trial_ends_at);
            } else {
                $status = SubscriptionStatus::Active;
                $termEnd = $subscription->interval->advance($now);
            }

            $subscription->forceFill([
                'status' => $status,
                'starts_at' => $subscription->starts_at ?? $now,
                'ends_at' => $termEnd,
                'canceled_at' => null,
                'renewal_reminded_at' => null,
            ])->save();

            SubscriptionStarted::dispatch($subscription->fresh(['user', 'plan']));

            return $subscription;
        });
    }

    /**
     * Cancel: keep access until the end of the paid term, then let it expire.
     * Pass $immediately to revoke access right away.
     */
    public function cancel(Subscription $subscription, bool $immediately = false): Subscription
    {
        $subscription->forceFill([
            'canceled_at' => now(),
            'status' => $immediately ? SubscriptionStatus::Canceled : $subscription->status,
            'ends_at' => $immediately ? now() : $subscription->ends_at,
        ])->save();

        return $subscription;
    }

    public function expire(Subscription $subscription): Subscription
    {
        $subscription->update(['status' => SubscriptionStatus::Expired]);

        return $subscription;
    }

    private function supersedeOthers(Subscription $keep): void
    {
        Subscription::where('user_id', $keep->user_id)
            ->whereKeyNot($keep->id)
            ->whereIn('status', [SubscriptionStatus::Active->value, SubscriptionStatus::Trialing->value])
            ->update([
                'status' => SubscriptionStatus::Canceled->value,
                'canceled_at' => now(),
                'ends_at' => now(),
            ]);
    }
}
