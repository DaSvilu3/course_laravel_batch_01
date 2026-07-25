<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // updateOrCreate so re-seeding never duplicates the demo accounts.
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'مدير النظام',
                'password' => Hash::make('password'),
                'role' => UserRole::Admin,
                'phone' => '90000000',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'عميل تجريبي',
                'password' => Hash::make('password'),
                'role' => UserRole::User,
                'phone' => '91111111',
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        if (app()->environment('local') && User::count() < 12) {
            User::factory(10)->create();
        }
    }
}
