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
     * Human-readable feature lines for the pricing cards.
     * Each: ['label' => string, 'on' => bool].
     */
    public function featureLines(): array
    {
        $daily = (int) $this->feature('daily_orders', 0);
        $monthly = (int) $this->feature('monthly_orders', -1);
        $support = (string) $this->feature('support', 'community');

        return [
            [
                'label' => $daily === -1
                    ? __('billing.feat_daily_unlimited')
                    : __('billing.feat_daily', ['count' => $daily]),
                'on' => true,
            ],
            [
                'label' => $monthly === -1
                    ? __('billing.feat_monthly_unlimited')
                    : __('billing.feat_monthly', ['count' => $monthly]),
                'on' => true,
            ],
            [
                'label' => __('billing.feat_tracking'),
                'on' => (bool) $this->feature('tracking', false),
            ],
            [
                'label' => __('billing.feat_support_'.$support),
                'on' => true,
            ],
        ];
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
