<?php

return [

    'user_role' => [
        'admin' => 'Admin',
        'user' => 'Merchant',
    ],

    'order_status' => [
        'new' => 'New',
        'in_progress' => 'In progress',
        'completed' => 'Completed',
        'cancelled' => 'Cancelled',
    ],

    'order_source' => [
        'form' => 'Form',
        'manual' => 'Manual',
    ],

    'payment_method' => [
        'cod' => 'Cash on delivery',
        'transfer' => 'Bank transfer',
    ],

    'payment_status' => [
        'pending' => 'Pending',
        'paid' => 'Paid',
        'failed' => 'Failed',
        'cancelled' => 'Cancelled',
        'refunded' => 'Refunded',
    ],

    'billing_interval' => [
        'month' => 'Monthly',
        'year' => 'Yearly',
    ],

    'subscription_status' => [
        'pending' => 'Pending',
        'trialing' => 'Trialing',
        'active' => 'Active',
        'expired' => 'Expired',
        'canceled' => 'Canceled',
    ],

];
