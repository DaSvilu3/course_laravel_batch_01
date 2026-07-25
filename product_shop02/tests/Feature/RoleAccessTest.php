<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RoleAccessTest extends TestCase
{
    use RefreshDatabase;

    public static function adminRoutes(): array
    {
        return [
            ['admin.dashboard'],
            ['admin.users.index'],
            ['admin.categories.index'],
            ['admin.services.index'],
            ['admin.products.index'],
            ['admin.orders.index'],
            ['admin.payments.index'],
            ['admin.bookings.index'],
        ];
    }

    #[DataProvider('adminRoutes')]
    public function test_admin_can_open_every_admin_page(string $route): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route($route))
            ->assertOk();
    }

    #[DataProvider('adminRoutes')]
    public function test_customers_are_forbidden_from_admin_pages(string $route): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route($route))
            ->assertForbidden();
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get(route('admin.dashboard'))->assertRedirect(route('login'));
    }

    public function test_role_helpers(): void
    {
        $admin = User::factory()->admin()->create();
        $customer = User::factory()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertFalse($customer->isAdmin());
        $this->assertTrue($customer->hasRole('user'));
    }
}
