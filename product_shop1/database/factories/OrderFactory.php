<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    public function definition(): array
    {
        $total = fake()->numberBetween(5, 200) * 1000;

        return [
            'user_id' => User::factory(),
            'status' => OrderStatus::Pending,
            'subtotal' => $total,
            'discount' => 0,
            'tax' => 0,
            'total' => $total,
            'currency' => 'OMR',
            'customer_name' => fake()->name(),
            'customer_email' => fake()->safeEmail(),
            'customer_phone' => '9'.fake()->numerify('#######'),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn () => [
            'status' => OrderStatus::Paid,
            'paid_at' => now(),
        ]);
    }
}
