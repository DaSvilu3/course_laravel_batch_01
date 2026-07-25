<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

/**
 * Admins are handled by the Gate::before() rule in AppServiceProvider,
 * so these methods only describe what a merchant may do with their own orders.
 */
class OrderPolicy
{
    public function view(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    public function update(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }

    public function delete(User $user, Order $order): bool
    {
        return $user->id === $order->user_id;
    }
}
