<?php

namespace Database\Seeders;

use App\Enums\CatalogType;
use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['type' => CatalogType::Service, 'slug' => 'consulting', 'name_ar' => 'استشارات', 'name_en' => 'Consulting'],
            ['type' => CatalogType::Service, 'slug' => 'training', 'name_ar' => 'تدريب', 'name_en' => 'Training'],
            ['type' => CatalogType::Product, 'slug' => 'digital', 'name_ar' => 'منتجات رقمية', 'name_en' => 'Digital'],
            ['type' => CatalogType::Product, 'slug' => 'merch', 'name_ar' => 'مستلزمات', 'name_en' => 'Merchandise'],
        ];

        foreach ($categories as $index => $data) {
            Category::updateOrCreate(
                ['slug' => $data['slug']],
                $data + ['sort_order' => $index, 'is_active' => true],
            );
        }

        $consulting = Category::where('slug', 'consulting')->first();
        $training = Category::where('slug', 'training')->first();
        $digital = Category::where('slug', 'digital')->first();
        $merch = Category::where('slug', 'merch')->first();

        $services = [
            [
                'slug' => 'business-consultation',
                'category_id' => $consulting->id,
                'name_ar' => 'استشارة أعمال',
                'name_en' => 'Business consultation',
                'description_ar' => 'جلسة استشارية لمناقشة فكرة مشروعك ووضع خطة تنفيذ واضحة.',
                'description_en' => 'A one-to-one session to review your idea and build a clear execution plan.',
                'price' => 25_000, // 25.000 OMR
                'duration_minutes' => 60,
                'is_featured' => true,
            ],
            [
                'slug' => 'website-audit',
                'category_id' => $consulting->id,
                'name_ar' => 'تدقيق موقع إلكتروني',
                'name_en' => 'Website audit',
                'description_ar' => 'مراجعة فنية لموقعك مع تقرير تحسينات مفصّل.',
                'description_en' => 'A technical review of your website with a detailed improvement report.',
                'price' => 45_000,
                'duration_minutes' => 90,
                'is_featured' => true,
            ],
            [
                'slug' => 'laravel-workshop',
                'category_id' => $training->id,
                'name_ar' => 'ورشة لارافيل',
                'name_en' => 'Laravel workshop',
                'description_ar' => 'ورشة عملية لبناء منتج رقمي متكامل باستخدام لارافيل والذكاء الاصطناعي.',
                'description_en' => 'A hands-on workshop building a complete digital product with Laravel and AI.',
                'price' => 35_000,
                'duration_minutes' => 180,
                'is_featured' => true,
            ],
        ];

        foreach ($services as $index => $data) {
            Service::updateOrCreate(
                ['slug' => $data['slug']],
                $data + ['is_active' => true, 'is_bookable' => true, 'sort_order' => $index],
            );
        }

        $products = [
            [
                'slug' => 'laravel-starter-guide',
                'category_id' => $digital->id,
                'name_ar' => 'دليل البداية مع لارافيل',
                'name_en' => 'Laravel starter guide',
                'description_ar' => 'كتيّب رقمي يشرح بناء أول منتج لك خطوة بخطوة.',
                'description_en' => 'A digital handbook that walks through building your first product.',
                'price' => 5_000,
                'stock' => null, // unlimited: digital download
                'is_featured' => true,
            ],
            [
                'slug' => 'developer-notebook',
                'category_id' => $merch->id,
                'name_ar' => 'دفتر المطوّر',
                'name_en' => 'Developer notebook',
                'description_ar' => 'دفتر ملاحظات بغلاف صلب مخصص للمبرمجين.',
                'description_en' => 'A hardcover notebook made for developers.',
                'price' => 3_500,
                'stock' => 40,
                'is_featured' => true,
            ],
        ];

        foreach ($products as $index => $data) {
            Product::updateOrCreate(
                ['slug' => $data['slug']],
                $data + ['is_active' => true, 'sort_order' => $index],
            );
        }
    }
}
