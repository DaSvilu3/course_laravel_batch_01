<?php

namespace App\Events;

use App\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Fired once, when an order is confirmed paid.
 *
 * Hook your own side effects onto this: invoices, WhatsApp messages,
 * granting access to a course, notifying the operations team...
 */
class OrderPaid
{
    use Dispatchable, SerializesModels;

    public function __construct(public Order $order) {}
}
