<?php

namespace Database\Factories;

use App\Enums\CatalogType;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);

        return [
            'type' => CatalogType::Service,
            'slug' => Str::slug($name).'-'.fake()->unique()->numberBetween(1, 9999),
            'name_ar' => 'تصنيف '.fake()->word(),
            'name_en' => Str::title($name),
            'sort_order' => 0,
            'is_active' => true,
        ];
    }

    public function product(): static
    {
        return $this->state(fn () => ['type' => CatalogType::Product]);
    }
}
