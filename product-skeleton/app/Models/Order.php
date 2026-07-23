<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\OrderStatus;
use App\Services\OrderService;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Str;

class Order extends Model implements Payable
{
    use HasFactory;

    protected $fillable = [
        'number',
        'user_id',
        'status',
        'subtotal',
        'discount',
        'tax',
        'total',
        'currency',
        'customer_name',
        'customer_email',
        'customer_phone',
        'notes',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'subtotal' => 'integer',
            'discount' => 'integer',
            'tax' => 'integer',
            'total' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->number ??= self::generateNumber();
        });
    }

    public static function generateNumber(): string
    {
        return 'ORD-'.now()->format('Ymd').'-'.Str::upper(Str::random(6));
    }

    // ---------------------------------------------------------------- relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable')->latest();
    }

    public function latestPayment()
    {
        return $this->morphOne(Payment::class, 'payable')->latestOfMany();
    }

    public function bookings(): HasMany
    {
        return $this->hasManyThrough(Booking::class, OrderItem::class, 'order_id', 'order_item_id');
    }

    // ---------------------------------------------------------------- helpers

    public function isPaid(): bool
    {
        return $this->status->isPaid();
    }

    public function isPayable(): bool
    {
        return in_array($this->status, [OrderStatus::Pending, OrderStatus::AwaitingPayment], true);
    }

    public function formattedTotal(): string
    {
        return Money::format($this->total, $this->currency);
    }

    /** Recalculate money columns from the line items. */
    public function recalculate(): self
    {
        $subtotal = (int) $this->items()->sum('total');

        $this->subtotal = $subtotal;
        $this->total = max(0, $subtotal - $this->discount + $this->tax);

        return $this;
    }

    // --------------------------------------------------------------- payable

    public function paymentOwner(): User
    {
        return $this->user;
    }

    public function paymentReference(): string
    {
        return $this->number;
    }

    public function paymentTotal(): int
    {
        return (int) $this->total;
    }

    public function paymentCurrency(): string
    {
        return $this->currency;
    }

    public function paymentLineItems(): array
    {
        // A discount/tax cannot be sent as a negative line, so when one exists
        // we collapse the order into a single line for the amount actually due.
        if ($this->discount > 0 || $this->tax > 0) {
            return [[
                'name' => __('payments.order_line', ['number' => $this->number]),
                'quantity' => 1,
                'unit_amount' => (int) $this->total,
            ]];
        }

        return $this->items->map(fn (OrderItem $item) => [
            'name' => $item->name,
            'quantity' => (int) $item->quantity,
            'unit_amount' => (int) $item->unit_price,
        ])->values()->all();
    }

    public function paymentMetadata(): array
    {
        return [
            'type' => 'order',
            'order_id' => $this->id,
            'order_number' => $this->number,
            'customer_email' => (string) $this->customer_email,
        ];
    }

    public function paymentReturnUrl(): string
    {
        return route('orders.show', $this);
    }

    public function isSettled(): bool
    {
        return $this->isPaid();
    }

    public function handleCheckoutStarted(Payment $payment): void
    {
        $this->update(['status' => OrderStatus::AwaitingPayment]);
    }

    public function handlePaymentPaid(Payment $payment): void
    {
        app(OrderService::class)->markAsPaid($this);
    }

    public function handlePaymentFailed(Payment $payment): void
    {
        // Put the order back so the customer can retry from the order page.
        if (! $this->isPaid()) {
            $this->update(['status' => OrderStatus::Pending]);
        }
    }

    // ------------------------------------------------------------------ scopes

    public function scopePaid($query)
    {
        return $query->whereIn('status', [
            OrderStatus::Paid->value,
            OrderStatus::Processing->value,
            OrderStatus::Completed->value,
        ]);
    }
}
