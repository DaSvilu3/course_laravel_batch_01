<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCatalogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->admin()->create();
    }

    public function test_admin_can_create_a_service_with_a_price_in_rials(): void
    {
        $this->actingAs($this->admin())->post(route('admin.services.store'), [
            'name_ar' => 'استشارة',
            'name_en' => 'Consultation',
            'price' => '12.500',
            'duration_minutes' => 60,
            'is_active' => 1,
        ])->assertRedirect(route('admin.services.index'));

        $service = Service::firstOrFail();

        // 12.500 OMR is stored as 12500 baisa.
        $this->assertSame(12_500, $service->price);
        $this->assertSame('consultation', $service->slug);
    }

    public function test_admin_can_update_a_product(): void
    {
        $product = Product::factory()->create(['price' => 1_000]);

        $this->actingAs($this->admin())->put(route('admin.products.update', $product), [
            'name_ar' => $product->name_ar,
            'name_en' => $product->name_en,
            'slug' => $product->slug,
            'price' => '7.250',
            'stock' => 12,
            'is_active' => 1,
        ])->assertRedirect(route('admin.products.index'));

        $this->assertSame(7_250, $product->fresh()->price);
        $this->assertSame(12, $product->fresh()->stock);
    }

    public function test_deleting_a_service_is_a_soft_delete(): void
    {
        $service = Service::factory()->create();

        $this->actingAs($this->admin())
            ->delete(route('admin.services.destroy', $service));

        $this->assertSoftDeleted($service);
    }

    public function test_customers_cannot_create_services(): void
    {
        $this->actingAs(User::factory()->create())
            ->post(route('admin.services.store'), ['name_ar' => 'x', 'name_en' => 'x', 'price' => 1])
            ->assertForbidden();

        $this->assertDatabaseCount('services', 0);
    }

    public function test_admin_can_manage_categories(): void
    {
        $this->actingAs($this->admin())->post(route('admin.categories.store'), [
            'type' => 'service',
            'name_ar' => 'استشارات',
            'name_en' => 'Consulting',
            'is_active' => 1,
        ])->assertRedirect(route('admin.categories.index'));

        $this->assertDatabaseHas('categories', ['slug' => 'consulting', 'type' => 'service']);
    }

    public function test_the_last_admin_cannot_be_demoted_or_deleted(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->put(route('admin.users.update', $admin), [
            'name' => $admin->name,
            'email' => $admin->email,
            'role' => 'user',
        ])->assertSessionHasErrors('role');

        $this->assertTrue($admin->fresh()->isAdmin());

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $admin))
            ->assertSessionHasErrors('user');
    }

    public function test_admin_forms_render(): void
    {
        $admin = $this->admin();
        $category = Category::factory()->create();
        $service = Service::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($admin)->get(route('admin.services.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.services.edit', $service))->assertOk();
        $this->actingAs($admin)->get(route('admin.products.create'))->assertOk();
        $this->actingAs($admin)->get(route('admin.products.edit', $product))->assertOk();
        $this->actingAs($admin)->get(route('admin.categories.edit', $category))->assertOk();
        $this->actingAs($admin)->get(route('admin.users.create'))->assertOk();
    }
}
