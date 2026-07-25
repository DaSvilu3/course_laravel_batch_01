<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Service;
use App\Support\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_add_items_to_the_cart(): void
    {
        $service = Service::factory()->create(['price' => 25_000]);

        $this->post(route('cart.store'), [
            'type' => 'service',
            'id' => $service->id,
            'quantity' => 2,
        ])->assertRedirect(route('cart.index'));

        $this->assertEquals(50_000, app(Cart::class)->subtotal());
    }

    public function test_quantity_can_be_updated_and_removed(): void
    {
        $product = Product::factory()->create(['price' => 10_000, 'stock' => 10]);

        $this->post(route('cart.store'), ['type' => 'product', 'id' => $product->id]);

        $this->patch(route('cart.update', 'product:'.$product->id), ['quantity' => 3]);
        $this->assertEquals(30_000, app(Cart::class)->subtotal());

        $this->delete(route('cart.destroy', 'product:'.$product->id));
        $this->assertTrue(app(Cart::class)->isEmpty());
    }

    public function test_out_of_stock_products_are_rejected(): void
    {
        $product = Product::factory()->outOfStock()->create();

        $this->from(route('products.show', $product))
            ->post(route('cart.store'), ['type' => 'product', 'id' => $product->id])
            ->assertSessionHasErrors('cart');

        $this->assertTrue(app(Cart::class)->isEmpty());
    }

    public function test_prices_come_from_the_database_not_the_request(): void
    {
        $service = Service::factory()->create(['price' => 25_000]);

        // A tampered "price" field is simply ignored.
        $this->post(route('cart.store'), [
            'type' => 'service',
            'id' => $service->id,
            'price' => 1,
        ]);

        $this->assertEquals(25_000, app(Cart::class)->subtotal());
    }

    public function test_cart_page_renders(): void
    {
        $service = Service::factory()->create();
        $this->post(route('cart.store'), ['type' => 'service', 'id' => $service->id]);

        $this->get(route('cart.index'))->assertOk()->assertSee($service->name_ar);
    }
}
