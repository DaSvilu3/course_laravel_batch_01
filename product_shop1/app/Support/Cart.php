<?php

namespace App\Support;

use App\Contracts\Purchasable;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Contracts\Session\Session;
use Illuminate\Support\Collection;

/**
 * A session backed shopping cart that works with any Purchasable model.
 *
 * The session only ever holds ["service:3" => ["type" => "service", "id" => 3,
 * "quantity" => 2, "options" => [...]]] — identifiers and quantities. Names and
 * prices are looked up from the database every time the cart is read.
 */
class Cart
{
    public const SESSION_KEY = 'cart.items';

    /** Morph alias => model class. Extend this when you add a Purchasable. */
    public const TYPES = [
        'service' => Service::class,
        'product' => Product::class,
    ];

    public function __construct(private readonly Session $session) {}

    public function add(Purchasable $purchasable, int $quantity = 1, array $options = []): void
    {
        $quantity = max(1, $quantity);
        $key = $this->keyFor($purchasable);
        $items = $this->raw();

        $items[$key] = [
            'type' => $purchasable->purchasableType(),
            'id' => $purchasable->getKey(),
            'quantity' => ($items[$key]['quantity'] ?? 0) + $quantity,
            'options' => $options ?: ($items[$key]['options'] ?? []),
        ];

        $this->persist($items);
    }

    public function update(string $key, int $quantity): void
    {
        $items = $this->raw();

        if (! isset($items[$key])) {
            return;
        }

        if ($quantity < 1) {
            unset($items[$key]);
        } else {
            $items[$key]['quantity'] = $quantity;
        }

        $this->persist($items);
    }

    public function remove(string $key): void
    {
        $items = $this->raw();
        unset($items[$key]);
        $this->persist($items);
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    /**
     * @return Collection<string, CartItem>
     */
    public function items(): Collection
    {
        $raw = collect($this->raw());

        // Group by type so we run one query per model instead of one per line.
        $models = $raw->groupBy('type')->flatMap(function (Collection $lines, string $type) {
            $class = self::TYPES[$type] ?? null;

            if ($class === null) {
                return [];
            }

            return $class::query()
                ->whereIn('id', $lines->pluck('id'))
                ->get()
                ->mapWithKeys(fn ($model) => [$type.':'.$model->getKey() => $model]);
        });

        return $raw
            ->map(function (array $line, string $key) use ($models) {
                $model = $models->get($key);

                return $model ? new CartItem($key, $model, (int) $line['quantity'], $line['options'] ?? []) : null;
            })
            ->filter()
            ->values()
            ->keyBy(fn (CartItem $item) => $item->key);
    }

    public function subtotal(): int
    {
        return (int) $this->items()->sum(fn (CartItem $item) => $item->total());
    }

    public function formattedSubtotal(): string
    {
        return Money::format($this->subtotal());
    }

    /** Number of distinct lines. */
    public function count(): int
    {
        return count($this->raw());
    }

    /** Total number of units. */
    public function quantity(): int
    {
        return (int) collect($this->raw())->sum('quantity');
    }

    public function isEmpty(): bool
    {
        return $this->count() === 0;
    }

    /** Lines that can no longer be bought (deactivated or out of stock). */
    public function unavailableItems(): Collection
    {
        return $this->items()->reject(fn (CartItem $item) => $item->isAvailable());
    }

    public function keyFor(Purchasable $purchasable): string
    {
        return $purchasable->purchasableType().':'.$purchasable->getKey();
    }

    /** Resolve "service:3" back to the model. */
    public static function resolve(string $type, int|string $id): ?Purchasable
    {
        $class = self::TYPES[$type] ?? null;

        return $class ? $class::find($id) : null;
    }

    private function raw(): array
    {
        return (array) $this->session->get(self::SESSION_KEY, []);
    }

    private function persist(array $items): void
    {
        $this->session->put(self::SESSION_KEY, $items);
    }
}
