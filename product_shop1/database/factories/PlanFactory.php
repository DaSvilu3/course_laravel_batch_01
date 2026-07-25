<?php

namespace Database\Factories;

use App\Enums\BillingInterval;
use App\Models\Plan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Plan>
 */
class PlanFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'name_ar' => 'باقة '.$name,
            'name_en' => Str::title($name),
            'description_ar' => 'وصف الباقة.',
            'description_en' => fake()->sentence(8),
            'price' => fake()->numberBetween(5, 50) * 1000,
            'interval' => BillingInterval::Month,
            'trial_days' => 0,
            'features' => ['max_projects' => fake()->numberBetween(1, 50)],
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    public function free(): static
    {
        return $this->state(fn () => ['price' => 0]);
    }

    public function yearly(): static
    {
        return $this->state(fn () => ['interval' => BillingInterval::Year]);
    }

    public function trial(int $days = 14): static
    {
        return $this->state(fn () => ['trial_days' => $days]);
    }
}
