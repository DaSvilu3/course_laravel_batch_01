<?php

namespace App\Models;

use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'purchasable_type',
        'purchasable_id',
        'name',
        'unit_price',
        'quantity',
        'total',
        'options',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'integer',
            'quantity' => 'integer',
            'total' => 'integer',
            'options' => 'array',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /** Service, Product, or any other Purchasable. */
    public function purchasable(): MorphTo
    {
        return $this->morphTo();
    }

    public function booking(): HasOne
    {
        return $this->hasOne(Booking::class);
    }

    public function formattedUnitPrice(): string
    {
        return Money::format($this->unit_price);
    }

    public function formattedTotal(): string
    {
        return Money::format($this->total);
    }
}
