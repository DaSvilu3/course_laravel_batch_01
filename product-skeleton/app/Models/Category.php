<?php

namespace App\Models;

use App\Enums\CatalogType;
use App\Models\Concerns\HasTranslatedAttributes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory, HasTranslatedAttributes;

    protected $fillable = [
        'type',
        'slug',
        'name_ar',
        'name_en',
        'description_ar',
        'description_en',
        'sort_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'type' => CatalogType::class,
            'is_active' => 'boolean',
        ];
    }

    public function services(): HasMany
    {
        return $this->hasMany(Service::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOfType($query, CatalogType|string $type)
    {
        return $query->where('type', $type instanceof CatalogType ? $type->value : $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
