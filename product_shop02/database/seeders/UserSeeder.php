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

        // The primary demo merchant — a phone shop, fully set up.
        User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'يوسف الكندي',
                'password' => Hash::make('password'),
                'role' => UserRole::User,
                'phone' => '91234567',
                'is_active' => true,
                'store_name' => 'متجر النخبة للهواتف',
                'intake_slug' => 'elite-phones',
                'whatsapp' => '91234567',
                'email_verified_at' => now(),
            ],
        );
    }
}
