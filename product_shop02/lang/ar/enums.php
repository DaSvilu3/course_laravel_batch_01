<?php

return [

    'user_role' => [
        'admin' => 'مدير',
        'user' => 'تاجر',
    ],

    'order_status' => [
        'new' => 'جديد',
        'in_progress' => 'قيد العمل',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
    ],

    'order_source' => [
        'form' => 'نموذج',
        'manual' => 'يدوي',
    ],

    'payment_method' => [
        'cod' => 'نقدي عند الاستلام',
        'transfer' => 'تحويل بنكي',
    ],

    'payment_status' => [
        'pending' => 'قيد الانتظار',
        'paid' => 'مدفوع',
        'failed' => 'فشل',
        'cancelled' => 'ملغي',
        'refunded' => 'مسترجع',
    ],

    'billing_interval' => [
        'month' => 'شهري',
        'year' => 'سنوي',
    ],

    'subscription_status' => [
        'pending' => 'قيد الانتظار',
        'trialing' => 'فترة تجريبية',
        'active' => 'نشط',
        'expired' => 'منتهي',
        'canceled' => 'ملغي',
    ],

];
