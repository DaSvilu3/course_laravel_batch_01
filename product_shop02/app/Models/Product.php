<?php

namespace App\Models;

use App\Contracts\Purchasable;
use App\Models\Concerns\HasTranslatedAttributes;
use App\Support\Money;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model implements Purchasable
{
    use HasFactory, HasTranslatedAttributes, SoftDeletes;

    protected $fillable = [
        'category_id',
        'slug',
        'sku',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'price',
        'stock',
        'image_path',
        'is_active',
        'is_featured',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'stock' => 'integer',
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

    // ------------------------------------------------------------- purchasable

    public function purchasableType(): string
    {
        return 'product';
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
        if (! $this->is_active || $quantity < 1) {
            return false;
        }

        // A null stock means "unlimited" (digital / made to order).
        return $this->stock === null || $this->stock >= $quantity;
    }

    public function purchasableUrl(): string
    {
        return route('products.show', $this);
    }

    public function purchasableImageUrl(): ?string
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }

    public function afterPurchase(int $quantity): void
    {
        if ($this->stock !== null) {
            $this->decrement('stock', min($quantity, $this->stock));
        }
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

    public function scopeInStock($query)
    {
        return $query->where(fn ($q) => $q->whereNull('stock')->orWhere('stock', '>', 0));
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
