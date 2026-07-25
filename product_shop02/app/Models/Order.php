<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\OrderSource;
use App\Enums\PaymentMethod;
use App\Support\Money;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * An order a merchant received — either through their public intake form or
 * entered by hand. Each one carries a public tracker code the customer follows.
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'tracker_code',
        'status',
        'source',
        'customer_name',
        'customer_phone',
        'item_description',
        'quantity',
        'price',
        'currency',
        'payment_method',
        'country',
        'governorate',
        'wilayat',
        'address',
        'location_note',
        'notes',
        'attachment_path',
        'confirmed_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => OrderStatus::class,
            'source' => OrderSource::class,
            'payment_method' => PaymentMethod::class,
            'quantity' => 'integer',
            'price' => 'integer',
            'confirmed_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            $order->tracker_code ??= self::generateTrackerCode();
        });
    }

    public static function generateTrackerCode(): string
    {
        do {
            $code = 'QD-'.Str::upper(Str::random(6));
        } while (self::where('tracker_code', $code)->exists());

        return $code;
    }

    public function getRouteKeyName(): string
    {
        return 'tracker_code';
    }

    // ---------------------------------------------------------------- relations

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** The merchant who received the order. */
    public function merchant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // ---------------------------------------------------------------- helpers

    public function hasPrice(): bool
    {
        return $this->price !== null && $this->price > 0;
    }

    public function formattedPrice(): string
    {
        return $this->hasPrice() ? Money::format($this->price, $this->currency) : '—';
    }

    public function whatsappLink(): string
    {
        $phone = preg_replace('/[^0-9]/', '', (string) $this->customer_phone);

        return 'https://wa.me/'.$phone;
    }

    // ------------------------------------------------------------------ scopes

    public function scopeStatus(Builder $query, ?string $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (blank($term)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('tracker_code', 'like', "%{$term}%")
                ->orWhere('customer_name', 'like', "%{$term}%")
                ->orWhere('customer_phone', 'like', "%{$term}%")
                ->orWhere('item_description', 'like', "%{$term}%");
        });
    }
}
