<?php

namespace App\Enums;

/**
 * A category belongs to one side of the catalog: services or products.
 */
enum CatalogType: string
{
    case Service = 'service';
    case Product = 'product';

    public function label(): string
    {
        return __('enums.catalog_type.'.$this->value);
    }

    public static function options(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn (self $t) => [$t->value => $t->label()])
            ->all();
    }
}
