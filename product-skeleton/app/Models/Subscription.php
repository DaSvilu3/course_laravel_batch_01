<?php

namespace App\Models;

use App\Contracts\Payable;
use App\Enums\BillingInterval;
use App\Enums\SubscriptionStatus;
use App\Services\SubscriptionService;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Subscription extends Model implements Payable
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'plan_name',
        'price',
        'interval',
        'currency',
        'trial_ends_at',
        'starts_at',
        'ends_at',
        'canceled_at',
        'renewal_reminded_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => SubscriptionStatus::class,
            'price' => 'integer',
            'interval' => BillingInterval::class,
            'trial_ends_at' => 'datetime',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'canceled_at' => 'datetime',
            'renewal_reminded_at' => 'datetime',
        ];
    }

    // ---------------------------------------------------------------- relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    // ------------------------------------------------------------------ status

    /** Currently entitled to the plan? (paid and inside the term) */
    public function isActive(): bool
    {
        return $this->status->grantsAccess()
            && ($this->ends_at === null || $this->ends_at->isFuture());
    }

    public function onTrial(): bool
    {
        return $this->status === SubscriptionStatus::Trialing
            && $this->trial_ends_at?->isFuture();
    }

    /** Cancelled but still inside the paid term. */
    public function onGracePeriod(): bool
    {
        return $this->canceled_at !== null && $this->isActive();
    }

    public function willRenew(): bool
    {
        return $this->canceled_at === null
            && in_array($this->status, [SubscriptionStatus::Active, SubscriptionStatus::Trialing], true);
    }

    // --------------------------------------------------------------- payable

    public function payments(): MorphMany
    {
        return $this->morphMany(Payment::class, 'payable')->latest();
    }

    public function paymentOwner(): User
    {
        return $this->user;
    }

    public function paymentReference(): string
    {
        return 'SUB-'.$this->id;
    }

    public function paymentTotal(): int
    {
        return (int) $this->price;
    }

    public function paymentCurrency(): string
    {
        return $this->currency;
    }

    public function paymentLineItems(): array
    {
        return [[
            'name' => $this->plan_name.' ('.$this->interval->label().')',
            'quantity' => 1,
            'unit_amount' => (int) $this->price,
        ]];
    }

    public function paymentMetadata(): array
    {
        return [
            'type' => 'subscription',
            'subscription_id' => $this->id,
            'plan' => $this->plan_name,
            'customer_email' => (string) $this->user?->email,
        ];
    }

    public function paymentReturnUrl(): string
    {
        return route('billing.index');
    }

    public function isSettled(): bool
    {
        // Always false: a subscription can be paid again to renew / extend, so
        // checkout is never blocked. activate() extends the term correctly.
        return false;
    }

    public function handleCheckoutStarted(Payment $payment): void
    {
        // The subscription stays pending until payment settles; nothing to do.
    }

    public function handlePaymentPaid(Payment $payment): void
    {
        app(SubscriptionService::class)->activate($this);
    }

    public function handlePaymentFailed(Payment $payment): void
    {
        // Nothing to roll back: the subscription simply stays pending/expired
        // until the customer completes a payment.
    }

    // ---------------------------------------------------------------- helpers

    public function formattedPrice(): string
    {
        return Money::format($this->price, $this->currency);
    }

    // ------------------------------------------------------------------ scopes

    public function scopeActive($query)
    {
        return $query->whereIn('status', [
            SubscriptionStatus::Active->value,
            SubscriptionStatus::Trialing->value,
        ]);
    }
}
