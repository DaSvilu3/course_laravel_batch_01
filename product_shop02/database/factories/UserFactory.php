<?php

namespace Database\Factories;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        $storeName = fake()->randomElement(['متجر', 'ركن', 'بوتيك', 'محل']).' '
            .fake()->randomElement(['النخبة', 'الأصالة', 'اللمسة', 'الريّان', 'الوفاء', 'السعادة', 'الأناقة']);

        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => UserRole::User,
            'phone' => '9'.fake()->numerify('#######'),
            'is_active' => true,
            'store_name' => $storeName,
            'intake_slug' => 'store-'.fake()->unique()->numerify('####'),
            'whatsapp' => '9'.fake()->numerify('#######'),
            'remember_token' => Str::random(10),
        ];
    }

    public function admin(): static
    {
        return $this->state(fn () => ['role' => UserRole::Admin]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }

    public function unverified(): static
    {
        return $this->state(fn () => ['email_verified_at' => null]);
    }
}
