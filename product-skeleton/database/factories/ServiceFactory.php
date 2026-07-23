<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => null,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'name_ar' => 'خدمة '.fake()->word(),
            'name_en' => Str::title($name),
            'description_ar' => 'وصف تجريبي للخدمة.',
            'description_en' => fake()->sentence(12),
            // baisa: 5.000 - 150.000 OMR
            'price' => fake()->numberBetween(5, 150) * 1000,
            'duration_minutes' => fake()->randomElement([30, 45, 60, 90, 120]),
            'is_bookable' => true,
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
