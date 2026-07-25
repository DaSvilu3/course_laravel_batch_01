<?php

namespace Database\Seeders;

use App\Enums\BillingInterval;
use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'slug' => 'free',
                'name_ar' => 'مجاني',
                'name_en' => 'Free',
                'description_ar' => 'للبداية واستقبال أول طلباتك.',
                'description_en' => 'To get started and receive your first orders.',
                'price' => 0,
                'interval' => BillingInterval::Month,
                'features' => [
                    'daily_orders' => 10,
                    'monthly_orders' => -1,
                    'tracking' => true,
                    'support' => 'community',
                ],
                'sort_order' => 0,
            ],
            [
                'slug' => 'pro',
                'name_ar' => 'برو',
                'name_en' => 'Pro',
                'description_ar' => 'للمحلات النشطة التي تنمو طلباتها.',
                'description_en' => 'For active shops with growing orders.',
                'price' => 15_000, // 15.000 OMR / month
                'interval' => BillingInterval::Month,
                'features' => [
                    'daily_orders' => -1,
                    'monthly_orders' => 1000,
                    'tracking' => true,
                    'support' => 'email',
                ],
                'is_featured' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan + ['is_active' => true]);
        }

        // Retire any plan from the old skeleton so only the two tiers show.
        Plan::whereNotIn('slug', ['free', 'pro'])->update(['is_active' => false]);
    }
}
