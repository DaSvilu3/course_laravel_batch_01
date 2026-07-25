<?php

namespace App\Models;

use App\Enums\MerchantOrderStatus;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * A manual order a merchant receives through their public intake link.
 * Carries a shareable tracker code the customer uses to follow its status.
 */
class MerchantOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tracker_code',
        'status',
        'customer_name',
        'customer_phone',
        'customer_location',
        'item_description',
        'quantity',
        'amount',
        'notes',
        'image_path',
    ];

    protected function casts(): array
    {
        return [
            'status' => MerchantOrderStatus::class,
            'quantity' => 'integer',
            'amount' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (MerchantOrder $order) {
            $order->tracker_code ??= self::generateTrackerCode();
            $order->status ??= MerchantOrderStatus::New;
        });
    }

    /** A short, human-friendly, unique code like "TLB-9F3K2A". */
    public static function generateTrackerCode(): string
    {
        do {
            $code = 'TLB-'.Str::upper(Str::random(6));
        } while (self::where('tracker_code', $code)->exists());

        return $code;
    }

    // ---------------------------------------------------------------- relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(MerchantOrderEvent::class)->orderBy('created_at')->orderBy('id');
    }

    // ---------------------------------------------------------------- helpers

    public function formattedAmount(): ?string
    {
        return $this->amount === null ? null : Money::format((int) $this->amount);
    }

    public function imageUrl(): ?string
    {
        return $this->image_path ? \Illuminate\Support\Facades\Storage::disk('public')->url($this->image_path) : null;
    }

    /** Append a history entry (defaults to the order's current status). */
    public function logStatus(?MerchantOrderStatus $status = null, ?Carbon $at = null): MerchantOrderEvent
    {
        return $this->events()->create([
            'status' => $status ?? $this->status,
            'created_at' => $at ?? now(),
        ]);
    }

    /** Move the order to a new status and record it in the history. */
    public function changeStatus(MerchantOrderStatus $status): bool
    {
        if ($this->status === $status) {
            return false;
        }

        $this->update(['status' => $status]);
        $this->logStatus($status);

        return true;
    }

    // ------------------------------------------------------------------ scopes

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month);
    }
}
