<?php

return [

    'user_role' => [
        'admin' => 'Administrator',
        'user' => 'Customer',
    ],

    'catalog_type' => [
        'service' => 'Service',
        'product' => 'Product',
    ],

    'order_status' => [
        'pending' => 'Pending',
        'awaiting_payment' => 'Awaiting payment',
        'paid' => 'Paid',
        'processing' => 'Processing',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ],

    'payment_status' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ],

    'booking_status' => [
        'pending' => 'Pending',
        'confirmed' => 'Confirmed',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'merchant_order_status' => [
        'new' => 'New',
        'confirmed' => 'Confirmed',
        'preparing' => 'Preparing',
        'out_for_delivery' => 'Out for delivery',
        'delivered' => 'Delivered',
        'cancelled' => 'Cancelled',
    ],

    'billing_interval' => [
        'month' => 'Monthly',
        'year' => 'Yearly',
    ],

    'subscription_status' => [
        'pending' => 'Pending',
        'trialing' => 'Trial',
        'active' => 'Active',
        'expired' => 'Expired',
        'canceled' => 'Canceled',
    ],

];
