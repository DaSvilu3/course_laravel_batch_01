<?php

namespace App\Models;

use App\Contracts\Purchasable;
use App\Models\Concerns\HasTranslatedAttributes;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Service extends Model implements Purchasable
{
    use HasFactory, HasTranslatedAttributes, SoftDeletes;

    protected $fillable = [
        'category_id',
        'slug',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'price',
        'duration_minutes',
        'image_path',
        'is_bookable',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'duration_minutes' => 'integer',
            'is_bookable' => 'boolean',
            'is_active' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ---------------------------------------------------------------- relations

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function orderItems(): MorphMany
    {
        return $this->morphMany(OrderItem::class, 'purchasable');
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    // ------------------------------------------------------------- purchasable

    public function purchasableType(): string
    {
        return 'service';
    }

    public function purchasableName(): string
    {
        return (string) $this->translate('name');
    }

    public function purchasableUnitPrice(): int
    {
        return (int) $this->price;
    }

    public function isPurchasable(int $quantity = 1): bool
    {
        return $this->is_active && $quantity > 0;
    }

    public function purchasableUrl(): string
    {
        return route('services.show', $this);
    }

    public function purchasableImageUrl(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    public function afterPurchase(int $quantity): void
    {
        // Services have no stock. Bookings are created by the checkout flow.
    }

    // ------------------------------------------------------------------ scopes

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    // ---------------------------------------------------------------- helpers

    public function formattedPrice(): string
    {
        return Money::format($this->price);
    }
}
