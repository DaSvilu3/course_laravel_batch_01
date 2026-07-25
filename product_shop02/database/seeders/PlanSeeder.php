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
                'description_ar' => 'للبداية والمتاجر الصغيرة.',
                'description_en' => 'To get started and for small stores.',
                'price' => 0,
                'interval' => BillingInterval::Month,
                'features' => ['orders_limit' => 10, 'orders_period' => 'day', 'support' => 'email'],
                'sort_order' => 0,
            ],
            [
                'slug' => 'pro',
                'name_ar' => 'قيد برو',
                'name_en' => 'Qaid Pro',
                'description_ar' => 'للمتاجر النشطة وحجم الطلبات الأكبر.',
                'description_en' => 'For active stores with a higher order volume.',
                'price' => 15_000, // 15.000 OMR / month
                'interval' => BillingInterval::Month,
                'trial_days' => 14,
                'features' => ['orders_limit' => 1000, 'orders_period' => 'month', 'support' => 'priority'],
                'is_featured' => true,
                'sort_order' => 1,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan + ['is_active' => true]);
        }
    }
}
