<?php

return [

    'user_role' => [
        'admin' => 'مدير',
        'user' => 'عميل',
    ],

    'catalog_type' => [
        'service' => 'خدمة',
        'product' => 'منتج',
    ],

    'order_status' => [
        'pending' => 'قيد الانتظار',
        'awaiting_payment' => 'بانتظار الدفع',
        'paid' => 'مدفوع',
        'processing' => 'قيد التنفيذ',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
        'refunded' => 'مسترجع',
    ],

    'payment_status' => [
        'pending' => 'قيد الانتظار',
        'paid' => 'مدفوع',
        'failed' => 'فشل',
        'cancelled' => 'ملغي',
        'refunded' => 'مسترجع',
    ],

    'booking_status' => [
        'pending' => 'قيد الانتظار',
        'confirmed' => 'مؤكد',
        'completed' => 'مكتمل',
        'cancelled' => 'ملغي',
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
