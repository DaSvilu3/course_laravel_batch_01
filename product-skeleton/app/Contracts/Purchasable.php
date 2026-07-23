<?php

namespace App\Contracts;

/**
 * Anything that can be put in the cart and sold.
 *
 * Service and Product both implement this. To sell something new
 * (a subscription, a course, an event ticket) implement this interface,
 * add the model to the morph map in AppServiceProvider, and the cart,
 * checkout and Thawani flow work with it unchanged.
 */
interface Purchasable
{
    /** Primary key of the model. */
    public function getKey();

    /** Morph alias, e.g. "service" or "product". */
    public function purchasableType(): string;

    /** Display name, already translated to the current locale. */
    public function purchasableName(): string;

    /** Unit price in baisa (1 OMR = 1000 baisa). */
    public function purchasableUnitPrice(): int;

    /** Can the given quantity be bought right now? */
    public function isPurchasable(int $quantity = 1): bool;

    /** Public URL of the item's detail page. */
    public function purchasableUrl(): string;

    public function purchasableImageUrl(): ?string;

    /** Called after a successful payment (e.g. decrement stock). */
    public function afterPurchase(int $quantity): void;
}
