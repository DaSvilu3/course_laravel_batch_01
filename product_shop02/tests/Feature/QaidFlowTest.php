<?php

namespace Tests\Feature;

use App\Enums\OrderSource;
use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use Database\Seeders\PlanSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QaidFlowTest extends TestCase
{
    use RefreshDatabase;

    private function merchant(array $attrs = []): User
    {
        return User::factory()->create(array_merge([
            'store_name' => 'متجر تجريبي',
            'intake_slug' => 'test-store',
        ], $attrs));
    }

    public function test_public_pages_render(): void
    {
        $this->seed(PlanSeeder::class);

        $this->get('/')->assertOk();
        $this->get(route('pricing'))->assertOk();
        $this->get(route('track.index'))->assertOk();
    }

    public function test_registration_creates_a_merchant_with_intake_slug(): void
    {
        $this->seed(PlanSeeder::class);

        $response = $this->post('/register', [
            'name' => 'يوسف',
            'store_name' => 'متجر النخبة',
            'whatsapp' => '91234567',
            'email' => 'y@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $user = User::where('email', 'y@example.com')->first();
        $this->assertNotNull($user);
        $this->assertNotEmpty($user->intake_slug);
        $this->assertSame('متجر النخبة', $user->store_name);
    }

    public function test_public_intake_creates_order_with_tracker_code(): void
    {
        $this->seed(PlanSeeder::class);
        $merchant = $this->merchant();

        $this->get(route('intake.show', ['slug' => $merchant->intake_slug]))->assertOk();

        $response = $this->post(route('intake.store', ['slug' => $merchant->intake_slug]), [
            'customer_name' => 'زبون',
            'customer_phone' => '90000000',
            'item_description' => 'آيفون 15',
            'quantity' => 1,
            'wilayat' => 'muscat',
            'price' => '12.500',
            'payment_method' => 'cod',
        ]);

        $order = Order::first();
        $this->assertNotNull($order);
        $this->assertSame($merchant->id, $order->user_id);
        $this->assertSame(OrderSource::Form, $order->source);
        $this->assertSame('muscat', $order->wilayat);
        $this->assertSame('muscat', $order->governorate);
        $this->assertSame(12500, $order->price);
        $response->assertRedirect(route('track.show', ['code' => $order->tracker_code]));
    }

    public function test_tracking_shows_status(): void
    {
        $merchant = $this->merchant();
        $order = Order::factory()->for($merchant, 'user')->create(['status' => OrderStatus::InProgress]);

        $this->get(route('track.show', ['code' => $order->tracker_code]))
            ->assertOk()
            ->assertSee($order->tracker_code);
    }

    public function test_quota_blocks_orders_over_the_free_limit(): void
    {
        $this->seed(PlanSeeder::class);
        $free = Plan::where('slug', 'free')->first();
        $limit = (int) $free->feature('orders_limit'); // 10 / day
        $merchant = $this->merchant();

        Order::factory()->count($limit)->for($merchant, 'user')->create();

        $response = $this->post(route('intake.store', ['slug' => $merchant->intake_slug]), [
            'customer_name' => 'زبون',
            'customer_phone' => '90000000',
            'item_description' => 'طلب زائد',
            'quantity' => 1,
        ]);

        $response->assertSessionHasErrors('quota');
        $this->assertSame($limit, Order::count());
    }

    public function test_merchant_dashboard_and_orders_render(): void
    {
        $this->seed(PlanSeeder::class);
        $merchant = $this->merchant();
        Order::factory()->count(3)->for($merchant, 'user')->create();

        $this->actingAs($merchant);
        $this->get(route('dashboard'))->assertOk();
        $this->get(route('orders.index'))->assertOk();
        $this->get(route('orders.create'))->assertOk();
        $this->get(route('orders.show', Order::first()))->assertOk();
    }

    public function test_merchant_can_update_and_delete_only_their_orders(): void
    {
        $merchant = $this->merchant();
        $other = $this->merchant(['intake_slug' => 'other-store', 'email' => 'other@example.com']);
        $order = Order::factory()->for($merchant, 'user')->create(['status' => OrderStatus::New]);

        // Owner can advance the status.
        $this->actingAs($merchant)
            ->patch(route('orders.update', $order), ['status' => 'in_progress'])
            ->assertRedirect();
        $this->assertSame(OrderStatus::InProgress, $order->fresh()->status);

        // A different merchant cannot.
        $this->actingAs($other)
            ->patch(route('orders.update', $order), ['status' => 'completed'])
            ->assertForbidden();
    }
}
