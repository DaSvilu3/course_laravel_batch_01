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
                'description_ar' => 'للبداية والتجربة.',
                'description_en' => 'To get started and explore.',
                'price' => 0,
                'interval' => BillingInterval::Month,
                'features' => ['max_projects' => 1, 'api_access' => false, 'support' => 'community'],
                'sort_order' => 0,
            ],
            [
                'slug' => 'pro',
                'name_ar' => 'برو',
                'name_en' => 'Pro',
                'description_ar' => 'للأفراد والمشاريع الصغيرة.',
                'description_en' => 'For individuals and small projects.',
                'price' => 15_000, // 15.000 OMR / month
                'interval' => BillingInterval::Month,
                'trial_days' => 14,
                'features' => ['max_projects' => 20, 'api_access' => true, 'support' => 'email'],
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'slug' => 'business',
                'name_ar' => 'الأعمال',
                'name_en' => 'Business',
                'description_ar' => 'للشركات والفرق الكبيرة.',
                'description_en' => 'For companies and larger teams.',
                'price' => 40_000, // 40.000 OMR / month
                'interval' => BillingInterval::Month,
                'features' => ['max_projects' => -1, 'api_access' => true, 'support' => 'priority'],
                'sort_order' => 2,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan + ['is_active' => true]);
        }
    }
}
