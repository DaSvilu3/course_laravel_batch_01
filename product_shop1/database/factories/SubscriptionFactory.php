<?php

namespace Database\Factories;

use App\Enums\BillingInterval;
use App\Enums\SubscriptionStatus;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'plan_id' => Plan::factory(),
            'status' => SubscriptionStatus::Pending,
            'plan_name' => 'Pro',
            'price' => 15_000,
            'interval' => BillingInterval::Month,
            'currency' => 'OMR',
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatus::Active,
            'starts_at' => now(),
            'ends_at' => now()->addMonthNoOverflow(),
        ]);
    }

    public function expiring(int $inDays = 2): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subMonth(),
            'ends_at' => now()->addDays($inDays),
        ]);
    }

    public function expired(): static
    {
        return $this->state(fn () => [
            'status' => SubscriptionStatus::Active,
            'starts_at' => now()->subMonths(2),
            'ends_at' => now()->subDay(),
        ]);
    }
}
