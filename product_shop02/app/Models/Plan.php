<?php

namespace App\Models;

use App\Enums\BillingInterval;
use App\Models\Concerns\HasTranslatedAttributes;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory, HasTranslatedAttributes;

    protected $fillable = [
        'slug',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'price',
        'interval',
        'trial_days',
        'features',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'interval' => BillingInterval::class,
            'trial_days' => 'integer',
            'features' => 'array',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    // ---------------------------------------------------------------- features

    /**
     * Read a feature flag / limit. A limit of -1 means unlimited.
     * $plan->feature('max_projects', 0)
     */
    public function feature(string $key, mixed $default = null): mixed
    {
        return data_get($this->features, $key, $default);
    }

    public function isFree(): bool
    {
        return $this->price <= 0;
    }

    public function isUnlimited(string $key): bool
    {
        return (int) $this->feature($key, 0) === -1;
    }

    /**
     * Localized feature bullet lines for the pricing / billing cards, derived
     * from the order quota stored in `features`.
     */
    public function featureLines(): array
    {
        $limit = $this->feature('orders_limit');
        $period = $this->feature('orders_period', 'month');

        if ($limit === null || (int) $limit === -1) {
            $orders = __('billing.orders_unlimited');
        } else {
            $orders = __('billing.orders_limit_'.$period, ['count' => number_format((int) $limit)]);
        }

        $lines = [
            $orders,
            __('billing.tracker_included'),
            __('billing.intake_link_included'),
        ];

        if ($support = $this->feature('support')) {
            $lines[] = __('billing.support_'.$support);
        }

        return $lines;
    }

    // ---------------------------------------------------------------- helpers

    public function formattedPrice(): string
    {
        return $this->isFree() ? __('billing.free') : Money::format($this->price);
    }

    // ------------------------------------------------------------------ scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('price');
    }
}
