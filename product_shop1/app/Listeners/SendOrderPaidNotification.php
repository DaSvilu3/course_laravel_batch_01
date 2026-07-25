<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Notifications\OrderPaidNotification;
use Illuminate\Support\Facades\Log;

class SendOrderPaidNotification
{
    public function handle(OrderPaid $event): void
    {
        $order = $event->order;

        Log::info('Order paid', ['order' => $order->number, 'total' => $order->total]);

        $order->user?->notify(new OrderPaidNotification($order));
    }
}
