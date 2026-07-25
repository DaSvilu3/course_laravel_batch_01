<?php

namespace App\Models;

use App\Enums\PaymentStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'payable_type',
        'payable_id',
        'user_id',
        'gateway',
        'status',
        'session_id',
        'checkout_url',
        'reference',
        'amount',
        'currency',
        'payload',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => PaymentStatus::class,
            'amount' => 'integer',
            'payload' => 'array',
            'paid_at' => 'datetime',
        ];
    }

    /** The thing being paid for: Order | Subscription | … */
    public function payable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Convenience for the shop views: the payable, when it is an Order. */
    public function getOrderAttribute(): ?Order
    {
        return $this->payable instanceof Order ? $this->payable : null;
    }

    public function isPaid(): bool
    {
        return $this->status === PaymentStatus::Paid;
    }

    public function reference(): string
    {
        return $this->reference ?: ($this->payable?->paymentReference() ?? '#'.$this->id);
    }

    public function formattedAmount(): string
    {
        return Money::format($this->amount, $this->currency);
    }

    public function scopePending($query)
    {
        return $query->where('status', PaymentStatus::Pending->value);
    }
}
