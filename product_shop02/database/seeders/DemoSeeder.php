<?php

namespace Database\Seeders;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

/**
 * Fills the app with believable data: a fully set-up demo store with ~3 months
 * of historical orders, an active Pro subscription, and a few more merchants so
 * the admin dashboards look alive. Safe to re-run — clears demo data first.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $free = Plan::where('slug', 'free')->first();
        $pro = Plan::where('slug', 'pro')->first();

        if (! $pro) {
            return; // plans not seeded yet
        }

        // Start clean so re-seeding stays idempotent.
        Order::query()->delete();
        Payment::where('payable_type', Subscription::class)->delete();
        Subscription::query()->delete();

        // ---- Primary demo merchant: Pro, 3 months of paid history ---------
        $demo = User::where('email', 'user@example.com')->first();
        if ($demo) {
            $sub = $this->makeSubscription($demo, $pro, SubscriptionStatus::Active, [
                'starts_at' => now()->subMonths(3),
                'ends_at' => now()->addDays(18),
            ]);

            foreach ([3, 2, 1, 0] as $monthsAgo) {
                $this->makePayment($sub, PaymentStatus::Paid, now()->subMonths($monthsAgo));
            }

            $this->seedOrdersFor($demo, days: 92, perDayMax: 4);
        }

        // ---- A few extra merchants to populate the admin views ------------
        $extras = User::factory(3)->create();

        foreach ($extras->values() as $i => $merchant) {
            // Half of them pay for Pro, the rest stay on the free plan.
            if ($i === 0) {
                $sub = $this->makeSubscription($merchant, $pro, SubscriptionStatus::Active, [
                    'starts_at' => now()->subMonths(2),
                    'ends_at' => now()->addDays(rand(5, 25)),
                ]);
                $this->makePayment($sub, PaymentStatus::Paid, $sub->starts_at);
            } elseif ($i === 1 && $free) {
                $this->makeSubscription($merchant, $pro, SubscriptionStatus::Trialing, [
                    'starts_at' => now()->subDays(4),
                    'trial_ends_at' => now()->addDays(10),
                    'ends_at' => now()->addDays(10),
                ]);
            }

            $this->seedOrdersFor($merchant, days: 75, perDayMax: 2);
        }
    }

    /** Spread realistic orders across the last N days for one merchant. */
    private function seedOrdersFor(User $merchant, int $days, int $perDayMax): void
    {
        for ($d = $days; $d >= 0; $d--) {
            $count = rand(0, $perDayMax);

            for ($n = 0; $n < $count; $n++) {
                $createdAt = now()->subDays($d)
                    ->setTime(rand(9, 22), rand(0, 59));

                $status = $this->statusForAge($d);

                Order::factory()
                    ->for($merchant, 'user')
                    ->create([
                        'status' => $status,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                        'confirmed_at' => $status === OrderStatus::New ? null : (clone $createdAt)->addHours(rand(1, 20)),
                        'completed_at' => $status === OrderStatus::Completed ? (clone $createdAt)->addDays(rand(1, 3)) : null,
                    ]);
            }
        }
    }

    /** Older orders are mostly done; recent ones are still active. */
    private function statusForAge(int $daysAgo): OrderStatus
    {
        $roll = rand(1, 100);

        if ($daysAgo > 10) {
            return match (true) {
                $roll <= 72 => OrderStatus::Completed,
                $roll <= 88 => OrderStatus::Cancelled,
                $roll <= 96 => OrderStatus::InProgress,
                default => OrderStatus::New,
            };
        }

        return match (true) {
            $roll <= 40 => OrderStatus::New,
            $roll <= 72 => OrderStatus::InProgress,
            $roll <= 92 => OrderStatus::Completed,
            default => OrderStatus::Cancelled,
        };
    }

    private function makeSubscription(User $user, Plan $plan, SubscriptionStatus $status, array $attributes): Subscription
    {
        return Subscription::create(array_merge([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'status' => $status,
            'plan_name' => $plan->name_en,
            'price' => $plan->price,
            'interval' => $plan->interval,
            'currency' => config('payments.currency', 'OMR'),
        ], $attributes));
    }

    private function makePayment(Subscription $sub, PaymentStatus $status, Carbon $paidAt): void
    {
        Payment::create([
            'payable_type' => Subscription::class,
            'payable_id' => $sub->id,
            'user_id' => $sub->user_id,
            'gateway' => 'fake',
            'status' => $status,
            'reference' => 'SUB-'.$sub->id.'-'.$paidAt->format('ym'),
            'amount' => $sub->price,
            'currency' => $sub->currency,
            'paid_at' => $status === PaymentStatus::Paid ? $paidAt : null,
        ]);
    }
}
