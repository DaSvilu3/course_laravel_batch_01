<?php

namespace Database\Factories;

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Enums\PaymentMethod;
use App\Models\Order;
use App\Models\User;
use App\Support\Regions;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    private const NAMES = [
        'أحمد البلوشي', 'محمد الحارثي', 'سالم المعمري', 'خالد الرواحي', 'يوسف الكندي',
        'عبدالله الشحي', 'فاطمة الزدجالي', 'مريم الهنائي', 'عائشة البوسعيدية', 'نورة الغافري',
        'سيف الوهيبي', 'حمد العامري', 'ريّا السيابية', 'بدر الحبسي', 'منى الفارسي',
        'طارق اللواتي', 'هدى المقبالي', 'ناصر الجابري', 'شيخة الرحبي', 'علي البادي',
    ];

    private const ITEMS = [
        'آيفون 15 برو ماكس - 256 جيجا', 'سماعة ايربودز أصلية', 'شاحن سريع 65 واط',
        'عباية سوداء مطرزة - مقاس M', 'عطر عود فاخر 100 مل', 'ساعة سمارت ووتش',
        'كفر جوال شفاف', 'حقيبة يد جلد طبيعي', 'طقم عبايات صيفية', 'باور بانك 20000',
        'سماعة بلوتوث محمولة', 'شاشة حماية زجاجية', 'جهاز تابلت 11 بوصة', 'مبخرة كهربائية',
        'طقم مكياج كامل', 'ساعة يد كلاسيكية', 'نظارة شمسية', 'محفظة رجالية جلد',
    ];

    public function definition(): array
    {
        $wilayat = Arr::random(Regions::wilayatKeys());

        return [
            'user_id' => User::factory(),
            'source' => Arr::random([OrderSource::Form, OrderSource::Form, OrderSource::Manual]),
            'status' => OrderStatus::New,
            'customer_name' => Arr::random(self::NAMES),
            'customer_phone' => Arr::random(['9', '7']).fake()->numerify('#######'),
            'item_description' => Arr::random(self::ITEMS),
            'quantity' => fake()->numberBetween(1, 4),
            'price' => fake()->numberBetween(2, 220) * 1000,
            'currency' => 'OMR',
            'payment_method' => Arr::random([PaymentMethod::Cod, PaymentMethod::Cod, PaymentMethod::Transfer]),
            'country' => 'OM',
            'wilayat' => $wilayat,
            'governorate' => Regions::governorateOfWilayat($wilayat),
            'address' => fake()->boolean(70) ? 'قرب '.Arr::random(['جامع', 'مدرسة', 'محطة', 'مركز']).' '.fake()->numerify('##') : null,
            'notes' => fake()->boolean(30) ? 'يُفضّل التوصيل بعد العصر.' : null,
        ];
    }

    public function status(OrderStatus $status): static
    {
        return $this->state(fn () => [
            'status' => $status,
            'confirmed_at' => in_array($status, [OrderStatus::InProgress, OrderStatus::Completed], true) ? now() : null,
            'completed_at' => $status === OrderStatus::Completed ? now() : null,
        ]);
    }
}
