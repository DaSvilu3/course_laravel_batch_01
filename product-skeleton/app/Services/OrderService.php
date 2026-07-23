<?php

namespace App\Services;

use App\Enums\BookingStatus;
use App\Enums\OrderStatus;
use App\Events\OrderPaid;
use App\Models\Booking;
use App\Models\Order;
use App\Models\Service;
use App\Models\User;
use App\Support\Cart;
use App\Support\CartItem;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * Turn the cart into an order. Prices are re-read from the models here, so
     * whatever was posted from the browser cannot influence the total.
     */
    public function createFromCart(User $user, Cart $cart, array $customer = []): Order
    {
        $items = $cart->items();

        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['cart' => __('shop.cart_empty')]);
        }

        $unavailable = $items->reject(fn (CartItem $item) => $item->isAvailable());

        if ($unavailable->isNotEmpty()) {
            throw ValidationException::withMessages([
                'cart' => __('shop.items_unavailable', ['items' => $unavailable->map->name()->join('، ')]),
            ]);
        }

        return DB::transaction(function () use ($user, $items, $customer, $cart) {
            $order = Order::create([
                'user_id' => $user->id,
                'status' => OrderStatus::Pending,
                'currency' => config('payments.currency', 'OMR'),
                'customer_name' => $customer['customer_name'] ?? $user->name,
                'customer_email' => $customer['customer_email'] ?? $user->email,
                'customer_phone' => $customer['customer_phone'] ?? $user->phone,
                'notes' => $customer['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $order->items()->create([
                    'purchasable_type' => $item->purchasable->purchasableType(),
                    'purchasable_id' => $item->purchasable->getKey(),
                    'name' => $item->name(),
                    'unit_price' => $item->unitPrice(),
                    'quantity' => $item->quantity,
                    'total' => $item->total(),
                    'options' => $item->options ?: null,
                ]);
            }

            $order->load('items')->recalculate()->save();

            $cart->clear();

            return $order;
        });
    }

    /**
     * Called once money has actually been received. Safe to call twice.
     */
    public function markAsPaid(Order $order): Order
    {
        if ($order->isPaid()) {
            return $order;
        }

        DB::transaction(function () use ($order) {
            $order->forceFill([
                'status' => OrderStatus::Paid,
                'paid_at' => now(),
            ])->save();

            foreach ($order->items()->with('purchasable')->get() as $item) {
                $item->purchasable?->afterPurchase($item->quantity);

                if ($item->purchasable instanceof Service && $item->purchasable->is_bookable) {
                    $this->createBookingFor($order, $item);
                }
            }
        });

        OrderPaid::dispatch($order->fresh(['items', 'user']));

        return $order;
    }

    public function cancel(Order $order): Order
    {
        if ($order->isPaid()) {
            return $order;
        }

        $order->update(['status' => OrderStatus::Cancelled]);

        return $order;
    }

    /**
     * Paid services become bookings. If the customer already picked a slot at
     * checkout it is stored in the line options, otherwise the booking waits
     * to be scheduled.
     */
    private function createBookingFor(Order $order, $item): void
    {
        if ($item->booking()->exists()) {
            return;
        }

        $startsAt = $item->options['starts_at'] ?? null;

        Booking::create([
            'user_id' => $order->user_id,
            'service_id' => $item->purchasable_id,
            'order_item_id' => $item->id,
            'status' => BookingStatus::Pending,
            'starts_at' => $startsAt,
            'ends_at' => $startsAt && $item->purchasable->duration_minutes
                ? Carbon::parse($startsAt)->addMinutes($item->purchasable->duration_minutes)
                : null,
        ]);
    }
}
