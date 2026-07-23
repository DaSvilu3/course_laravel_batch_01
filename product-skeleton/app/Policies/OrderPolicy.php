<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

/**
 * Admins are handled by the Gate::before() rule in AppServiceProvider,
 * so these methods only describe what a normal customer may do.
 */
class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    public function pay(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && $order->isPayable();
    }

    public function cancel(User $user, Order $order): bool
    {
        return $user->id === $order->user_id && ! $order->isPaid();
    }
}
