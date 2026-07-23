<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Events\OrderPaid;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * Drives the whole purchase flow against the fake gateway
 * (PAYMENT_GATEWAY=fake is set in phpunit.xml).
 */
class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    private function fillCart(): Service
    {
        $service = Service::factory()->create(['price' => 25_000]);

        $this->post(route('cart.store'), ['type' => 'service', 'id' => $service->id]);

        return $service;
    }

    public function test_checkout_requires_login(): void
    {
        $this->fillCart();

        $this->get(route('checkout.show'))->assertRedirect(route('login'));
    }

    public function test_checkout_creates_an_order_and_redirects_to_the_gateway(): void
    {
        $user = User::factory()->create();
        $service = $this->fillCart();

        $response = $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Ahmed',
            'customer_email' => 'ahmed@example.com',
            'customer_phone' => '90000000',
        ]);

        $order = Order::firstOrFail();

        $this->assertSame($user->id, $order->user_id);
        $this->assertSame(25_000, $order->total);
        $this->assertSame(OrderStatus::AwaitingPayment, $order->status);
        $this->assertSame($service->name_ar, $order->items->first()->name);

        $payment = Payment::firstOrFail();
        $this->assertSame('fake', $payment->gateway);
        $this->assertSame(25_000, $payment->amount);

        $response->assertRedirect($payment->checkout_url);

        // The cart is emptied once the order exists.
        $this->assertDatabaseCount('order_items', 1);
    }

    public function test_empty_cart_cannot_be_checked_out(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('checkout.show'))
            ->assertRedirect(route('cart.index'));
    }

    public function test_successful_payment_marks_the_order_paid(): void
    {
        Event::fake([OrderPaid::class]);

        $user = User::factory()->create();
        $this->fillCart();

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Ahmed',
            'customer_email' => 'ahmed@example.com',
        ]);

        $payment = Payment::firstOrFail();

        // The sandbox checkout page marks the session as paid…
        $this->post(route('fake-gateway.pay'), [
            'session' => $payment->session_id,
            'success_url' => $this->signedCallback('checkout.success', $payment),
            'cancel_url' => $this->signedCallback('checkout.cancel', $payment),
        ]);

        // …and the signed callback verifies it with the gateway.
        $this->actingAs($user)
            ->get($this->signedCallback('checkout.success', $payment))
            ->assertRedirect(route('orders.show', $payment->order));

        $this->assertSame(PaymentStatus::Paid, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Paid, $payment->order->fresh()->status);
        $this->assertNotNull($payment->order->fresh()->paid_at);

        Event::assertDispatched(OrderPaid::class);
    }

    public function test_cancelled_payment_leaves_the_order_payable(): void
    {
        $user = User::factory()->create();
        $this->fillCart();

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Ahmed',
            'customer_email' => 'ahmed@example.com',
        ]);

        $payment = Payment::firstOrFail();

        $this->post(route('fake-gateway.cancel'), [
            'session' => $payment->session_id,
            'success_url' => $this->signedCallback('checkout.success', $payment),
            'cancel_url' => $this->signedCallback('checkout.cancel', $payment),
        ]);

        $this->actingAs($user)->get($this->signedCallback('checkout.cancel', $payment));

        $this->assertSame(PaymentStatus::Cancelled, $payment->fresh()->status);
        $this->assertSame(OrderStatus::Pending, $payment->order->fresh()->status);
        $this->assertTrue($payment->order->fresh()->isPayable());
    }

    public function test_callback_without_a_valid_signature_is_rejected(): void
    {
        $user = User::factory()->create();
        $this->fillCart();

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Ahmed',
            'customer_email' => 'ahmed@example.com',
        ]);

        $payment = Payment::firstOrFail();

        $this->actingAs($user)
            ->get(route('checkout.success', $payment))
            ->assertForbidden();
    }

    public function test_paid_service_becomes_a_booking(): void
    {
        $user = User::factory()->create();
        $service = Service::factory()->create(['price' => 25_000, 'is_bookable' => true]);

        $this->post(route('cart.store'), ['type' => 'service', 'id' => $service->id]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Ahmed',
            'customer_email' => 'ahmed@example.com',
        ]);

        $payment = Payment::firstOrFail();
        $this->post(route('fake-gateway.pay'), [
            'session' => $payment->session_id,
            'success_url' => $this->signedCallback('checkout.success', $payment),
            'cancel_url' => $this->signedCallback('checkout.cancel', $payment),
        ]);
        $this->actingAs($user)->get($this->signedCallback('checkout.success', $payment));

        $this->assertDatabaseHas('bookings', [
            'user_id' => $user->id,
            'service_id' => $service->id,
        ]);
    }

    public function test_paid_product_stock_is_decremented(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 10_000, 'stock' => 5]);

        $this->post(route('cart.store'), ['type' => 'product', 'id' => $product->id, 'quantity' => 2]);

        $this->actingAs($user)->post(route('checkout.store'), [
            'customer_name' => 'Ahmed',
            'customer_email' => 'ahmed@example.com',
        ]);

        $payment = Payment::firstOrFail();
        $this->post(route('fake-gateway.pay'), [
            'session' => $payment->session_id,
            'success_url' => $this->signedCallback('checkout.success', $payment),
            'cancel_url' => $this->signedCallback('checkout.cancel', $payment),
        ]);
        $this->actingAs($user)->get($this->signedCallback('checkout.success', $payment));

        $this->assertSame(3, $product->fresh()->stock);
    }

    public function test_customers_cannot_view_someone_elses_order(): void
    {
        $order = Order::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('orders.show', $order))
            ->assertForbidden();
    }

    public function test_admins_can_view_any_order(): void
    {
        $order = Order::factory()->create();

        $this->actingAs(User::factory()->admin()->create())
            ->get(route('orders.show', $order))
            ->assertOk();
    }

    private function signedCallback(string $route, Payment $payment): string
    {
        return URL::signedRoute($route, ['payment' => $payment->id]);
    }
}
