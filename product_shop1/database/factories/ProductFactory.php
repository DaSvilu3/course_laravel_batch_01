<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'category_id' => null,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 99999),
            'sku' => Str::upper(Str::random(8)),
            'name_ar' => 'منتج '.fake()->word(),
            'name_en' => Str::title($name),
            'description_ar' => 'وصف تجريبي للمنتج.',
            'description_en' => fake()->sentence(12),
            'price' => fake()->numberBetween(1, 80) * 1000,
            'stock' => fake()->numberBetween(5, 100),
            'is_active' => true,
            'is_featured' => false,
            'sort_order' => 0,
        ];
    }

    public function featured(): static
    {
        return $this->state(fn () => ['is_featured' => true]);
    }

    public function outOfStock(): static
    {
        return $this->state(fn () => ['stock' => 0]);
    }

    public function unlimited(): static
    {
        return $this->state(fn () => ['stock' => null]);
    }
}
