<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_page_lists_featured_items(): void
    {
        $service = Service::factory()->featured()->create();
        $product = Product::factory()->featured()->create();

        $this->get('/')
            ->assertOk()
            ->assertSee($service->name_ar)
            ->assertSee($product->name_ar);
    }

    public function test_service_and_product_listings_render(): void
    {
        Service::factory(3)->create();
        Product::factory(3)->create();

        $this->get(route('services.index'))->assertOk();
        $this->get(route('products.index'))->assertOk();
    }

    public function test_detail_pages_render(): void
    {
        $service = Service::factory()->create();
        $product = Product::factory()->create();

        $this->get(route('services.show', $service))->assertOk()->assertSee($service->name_ar);
        $this->get(route('products.show', $product))->assertOk()->assertSee($product->name_ar);
    }

    public function test_inactive_items_are_hidden(): void
    {
        $service = Service::factory()->inactive()->create();

        $this->get(route('services.show', $service))->assertNotFound();
        $this->get(route('services.index'))->assertOk()->assertDontSee($service->name_ar);
    }

    public function test_locale_can_be_switched(): void
    {
        $service = Service::factory()->create();

        $this->get(route('locale.switch', 'en'));

        $this->get(route('services.show', $service))
            ->assertOk()
            ->assertSee($service->name_en);
    }

    public function test_customer_dashboard_requires_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));

        $this->actingAs(User::factory()->create())
            ->get(route('dashboard'))
            ->assertOk();
    }

    public function test_deactivated_user_is_logged_out(): void
    {
        $user = User::factory()->inactive()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
