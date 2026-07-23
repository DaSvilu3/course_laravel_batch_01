<?php

namespace Tests\Feature;

use App\Enums\SubscriptionStatus;
use App\Events\SubscriptionStarted;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

/**
 * The SaaS billing flow, driven against the fake gateway.
 */
class SubscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_pricing_page_is_public(): void
    {
        Plan::factory()->create(['name_ar' => 'باقة برو']);

        $this->get(route('plans.index'))->assertOk()->assertSee('باقة برو');
    }

    public function test_subscribing_to_a_paid_plan_redirects_to_the_gateway(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 15_000]);

        $this->actingAs($user)
            ->post(route('billing.subscribe', $plan))
            ->assertRedirect();

        $subscription = Subscription::firstOrFail();
        $this->assertSame(SubscriptionStatus::Pending, $subscription->status);
        $this->assertSame($user->id, $subscription->user_id);

        // Amount snapshotted from the plan.
        $payment = Payment::firstOrFail();
        $this->assertSame(15_000, $payment->amount);
        $this->assertSame('subscription', $payment->payable_type);
    }

    public function test_a_free_plan_activates_without_payment(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->free()->create();

        $this->actingAs($user)
            ->post(route('billing.subscribe', $plan))
            ->assertRedirect(route('billing.index'));

        $this->assertTrue($user->fresh()->subscribed());
        $this->assertSame(0, Payment::count());
    }

    public function test_paying_activates_the_subscription(): void
    {
        Event::fake([SubscriptionStarted::class]);

        $user = User::factory()->create();
        $plan = Plan::factory()->create(['price' => 15_000]);

        $this->actingAs($user)->post(route('billing.subscribe', $plan));

        $payment = Payment::firstOrFail();

        $this->post(route('fake-gateway.pay'), [
            'session' => $payment->session_id,
            'success_url' => $this->signed('checkout.success', $payment),
            'cancel_url' => $this->signed('checkout.cancel', $payment),
        ]);

        $this->actingAs($user)
            ->get($this->signed('checkout.success', $payment))
            ->assertRedirect(route('billing.index'));

        $subscription = Subscription::firstOrFail();
        $this->assertSame(SubscriptionStatus::Active, $subscription->status);
        $this->assertTrue($subscription->isActive());
        $this->assertNotNull($subscription->ends_at);
        $this->assertTrue($user->fresh()->subscribed());

        Event::assertDispatched(SubscriptionStarted::class);
    }

    public function test_a_trial_plan_starts_in_trialing(): void
    {
        $user = User::factory()->create();
        $plan = Plan::factory()->trial(14)->create(['price' => 15_000]);

        $this->actingAs($user)->post(route('billing.subscribe', $plan));
        $payment = Payment::firstOrFail();
        $this->settle($payment);

        $subscription = Subscription::firstOrFail();
        $this->assertSame(SubscriptionStatus::Trialing, $subscription->status);
        $this->assertTrue($subscription->onTrial());
        $this->assertTrue($subscription->isActive());
    }

    public function test_subscribing_supersedes_the_previous_subscription(): void
    {
        $user = User::factory()->create();
        $old = Subscription::factory()->active()->for($user)->create();
        $plan = Plan::factory()->create(['price' => 15_000]);

        $this->actingAs($user)->post(route('billing.subscribe', $plan));
        $this->settle(Payment::firstOrFail());

        $this->assertSame(SubscriptionStatus::Canceled, $old->fresh()->status);
        $this->assertCount(1, $user->fresh()->subscriptions->filter->isActive());
    }

    public function test_renewing_extends_the_term_from_the_current_end(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->active()->for($user)->create();
        $originalEnd = $subscription->ends_at;

        $this->actingAs($user)
            ->post(route('billing.renew', $subscription))
            ->assertRedirect();

        $this->settle($subscription->payments()->latest()->first());

        // Term extended from the old end date, not reset to "now + 1 month".
        $this->assertTrue($subscription->fresh()->ends_at->greaterThan($originalEnd));
    }

    public function test_customer_can_cancel_and_keeps_access_until_term_end(): void
    {
        $user = User::factory()->create();
        $subscription = Subscription::factory()->active()->for($user)->create();

        $this->actingAs($user)
            ->post(route('billing.cancel', $subscription))
            ->assertRedirect(route('billing.index'));

        $subscription->refresh();
        $this->assertNotNull($subscription->canceled_at);
        $this->assertFalse($subscription->willRenew());
        // Still inside the paid term, so access continues.
        $this->assertTrue($subscription->isActive());
    }

    public function test_a_user_cannot_cancel_someone_elses_subscription(): void
    {
        $subscription = Subscription::factory()->active()->create();

        $this->actingAs(User::factory()->create())
            ->post(route('billing.cancel', $subscription))
            ->assertForbidden();
    }

    private function settle(Payment $payment): void
    {
        $this->post(route('fake-gateway.pay'), [
            'session' => $payment->session_id,
            'success_url' => $this->signed('checkout.success', $payment),
            'cancel_url' => $this->signed('checkout.cancel', $payment),
        ]);

        $this->actingAs($payment->user)->get($this->signed('checkout.success', $payment));
    }

    private function signed(string $route, Payment $payment): string
    {
        return URL::signedRoute($route, ['payment' => $payment->id]);
    }
}
