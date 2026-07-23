<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
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

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class)->latest();
    }

    public function latestPayment()
    {
        return $this->hasOne(Payment::class)->latestOfMany();
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
