<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use App\Notifications\SubscriptionExpiringNotification;
use App\Services\SubscriptionService;
use Illuminate\Console\Command;

/**
 * Housekeeping for subscriptions. Runs daily (see routes/console.php):
 *
 *   1. Remind customers whose term ends within SUBSCRIPTION_REMINDER_DAYS.
 *   2. Expire subscriptions whose term (plus any grace) has passed.
 *
 * This is the "manual renewal" model: we never charge a saved card. When a
 * term ends the subscription expires and the customer re-subscribes / renews
 * through a normal Thawani checkout.
 */
class ProcessSubscriptions extends Command
{
    protected $signature = 'subscriptions:process {--dry-run : Report only, change nothing}';

    protected $description = 'Send renewal reminders and expire ended subscriptions';

    public function handle(SubscriptionService $service): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $reminderDays = (int) config('subscriptions.reminder_days', 3);
        $graceDays = (int) config('subscriptions.grace_days', 0);

        $reminded = $this->sendReminders($reminderDays, $dryRun);
        $expired = $this->expireEnded($service, $graceDays, $dryRun);

        $this->info(($dryRun ? '[dry-run] ' : '')."Reminders: {$reminded}, expired: {$expired}.");

        return self::SUCCESS;
    }

    private function sendReminders(int $days, bool $dryRun): int
    {
        $window = now()->addDays($days);

        $due = Subscription::query()
            ->active()
            ->whereNull('canceled_at')
            ->whereNull('renewal_reminded_at')
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $window)
            ->where('ends_at', '>', now())
            ->with('user')
            ->get();

        foreach ($due as $subscription) {
            if ($dryRun) {
                continue;
            }

            $subscription->user?->notify(new SubscriptionExpiringNotification($subscription));
            $subscription->update(['renewal_reminded_at' => now()]);
        }

        return $due->count();
    }

    private function expireEnded(SubscriptionService $service, int $graceDays, bool $dryRun): int
    {
        $cutoff = now()->subDays($graceDays);

        $ended = Subscription::query()
            ->whereIn('status', [
                SubscriptionStatus::Active->value,
                SubscriptionStatus::Trialing->value,
            ])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $cutoff)
            ->get();

        foreach ($ended as $subscription) {
            if ($dryRun) {
                continue;
            }

            $service->expire($subscription);
        }

        return $ended->count();
    }
}
