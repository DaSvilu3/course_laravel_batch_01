<?php

namespace Database\Factories;

use App\Enums\MerchantOrderStatus;
use App\Models\MerchantOrder;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MerchantOrder>
 */
class MerchantOrderFactory extends Factory
{
    public function definition(): array
    {
        $withAmount = fake()->boolean(70);

        return [
            'user_id' => User::factory(),
            'status' => fake()->randomElement(MerchantOrderStatus::cases()),
            'customer_name' => fake()->name(),
            'customer_phone' => '9'.fake()->numerify('#######'),
            'customer_location' => fake()->boolean(60) ? fake()->city() : null,
            'item_description' => fake()->sentence(4),
            'quantity' => fake()->numberBetween(1, 5),
            'amount' => $withAmount ? fake()->numberBetween(1, 60) * 1000 : null,
            'notes' => fake()->boolean(30) ? fake()->sentence(6) : null,
        ];
    }

    public function status(MerchantOrderStatus $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }

    /** Build a believable status history leading up to the current status. */
    public function configure(): static
    {
        return $this->afterCreating(function (MerchantOrder $order) {
            $chain = $order->status === MerchantOrderStatus::Cancelled
                ? [MerchantOrderStatus::New, MerchantOrderStatus::Cancelled]
                : collect(MerchantOrderStatus::cases())
                    ->filter(fn (MerchantOrderStatus $s) => $s->step() >= 1 && $s->step() <= $order->status->step())
                    ->sortBy(fn (MerchantOrderStatus $s) => $s->step())
                    ->values()
                    ->all();

            $start = $order->created_at ?? now();
            $end = $order->updated_at ?? now();
            $span = max(0, $start->diffInSeconds($end));
            $count = count($chain);

            foreach ($chain as $i => $status) {
                $at = $count > 1
                    ? $start->copy()->addSeconds((int) round($span * $i / ($count - 1)))
                    : $start;
                $order->events()->create(['status' => $status, 'created_at' => $at]);
            }
        });
    }
}
