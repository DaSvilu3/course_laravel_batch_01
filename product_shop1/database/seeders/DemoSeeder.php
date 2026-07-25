<?php

namespace Database\Seeders;

use App\Enums\MerchantOrderStatus;
use App\Enums\PaymentStatus;
use App\Enums\SubscriptionStatus;
use App\Enums\UserRole;
use App\Models\MerchantOrder;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Fills the dashboards with believable data: subscriptions across every
 * status and the payments behind them. Safe to re-run — it clears the demo
 * subscriptions/payments first so counts never drift.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $pro = Plan::where('slug', 'pro')->first();

        if (! $pro) {
            return; // plans not seeded yet
        }

        // Start clean so re-seeding stays idempotent.
        Payment::where('payable_type', Subscription::class)->delete();
        Subscription::query()->delete();

        $this->seedMerchantOrders();

        // ---- The demo customer gets a healthy, paid Pro subscription -------
        $demo = User::where('email', 'user@example.com')->first();
        if ($demo) {
            $sub = $this->makeSubscription($demo, $pro, SubscriptionStatus::Active, [
                'starts_at' => now()->subMonths(2),
                'ends_at' => now()->addDays(20),
            ]);
            // Three months of history, all paid.
            foreach ([2, 1, 0] as $monthsAgo) {
                $this->makePayment($sub, PaymentStatus::Paid, now()->subMonths($monthsAgo));
            }
        }

        // ---- A spread across the rest of the customers --------------------
        $customers = User::where('role', UserRole::User)
            ->where('email', '!=', 'user@example.com')
            ->get();

        foreach ($customers->values() as $i => $customer) {
            // Rotate through the interesting lifecycle states.
            match ($i % 5) {
                0 => $this->activePaid($customer, $pro),
                1 => $this->trialing($customer, $pro),
                2 => $this->activePaid($customer, $pro),
                3 => $this->gracePeriod($customer, $pro),
                default => $this->expired($customer, $pro),
            };
        }
    }

    /**
     * Fill the demo merchant's dashboard with a believable spread of orders
     * across every status, plus a few for today so the quota gauge moves.
     */
    private function seedMerchantOrders(): void
    {
        $demo = User::where('email', 'user@example.com')->first();
        if (! $demo) {
            return;
        }

        MerchantOrder::where('user_id', $demo->id)->delete();

        // A spread over the past few weeks across every status.
        foreach (MerchantOrderStatus::cases() as $status) {
            MerchantOrder::factory()
                ->count(3)
                ->status($status)
                ->for($demo)
                ->create([
                    'created_at' => now()->subDays(rand(1, 25)),
                    'updated_at' => now()->subDays(rand(0, 3)),
                ]);
        }

        // A couple that came in today, so "today's orders" is non-zero.
        MerchantOrder::factory()
            ->count(2)
            ->status(MerchantOrderStatus::New)
            ->for($demo)
            ->create(['created_at' => now()->subHours(rand(1, 6))]);
    }

    private function activePaid(User $user, Plan $plan): void
    {
        $sub = $this->makeSubscription($user, $plan, SubscriptionStatus::Active, [
            'starts_at' => now()->subDays(rand(5, 40)),
            'ends_at' => now()->addDays(rand(5, 28)),
        ]);
        $this->makePayment($sub, PaymentStatus::Paid, $sub->starts_at);
    }

    private function trialing(User $user, Plan $plan): void
    {
        $this->makeSubscription($user, $plan, SubscriptionStatus::Trialing, [
            'starts_at' => now()->subDays(rand(1, 5)),
            'trial_ends_at' => now()->addDays(rand(3, 12)),
            'ends_at' => now()->addDays(rand(3, 12)),
        ]);
    }

    private function gracePeriod(User $user, Plan $plan): void
    {
        $sub = $this->makeSubscription($user, $plan, SubscriptionStatus::Active, [
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addDays(rand(2, 10)),
            'canceled_at' => now()->subDays(2),
        ]);
        $this->makePayment($sub, PaymentStatus::Paid, $sub->starts_at);
    }

    private function expired(User $user, Plan $plan): void
    {
        $sub = $this->makeSubscription($user, $plan, SubscriptionStatus::Expired, [
            'starts_at' => now()->subMonths(3),
            'ends_at' => now()->subMonth(),
        ]);
        $this->makePayment($sub, PaymentStatus::Paid, $sub->starts_at);
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

    private function makePayment(Subscription $sub, PaymentStatus $status, $paidAt): void
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
