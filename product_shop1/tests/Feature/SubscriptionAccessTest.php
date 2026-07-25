<?php

namespace Tests\Feature;

use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriptionAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_subscribed_middleware_blocks_non_subscribers(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('members'))
            ->assertRedirect(route('plans.index'));
    }

    public function test_an_active_subscriber_may_enter(): void
    {
        $user = User::factory()->create();
        Subscription::factory()->active()->for($user)->create();

        $this->actingAs($user)->get(route('members'))->assertOk();
    }

    public function test_admins_always_pass(): void
    {
        $this->actingAs(User::factory()->admin()->create())
            ->get(route('members'))
            ->assertOk();
    }

    public function test_plan_scoped_gate(): void
    {
        $pro = Plan::factory()->create(['slug' => 'pro']);
        $free = Plan::factory()->create(['slug' => 'free', 'price' => 0]);

        $onPro = User::factory()->create();
        Subscription::factory()->active()->for($onPro)->for($pro)->create();

        $onFree = User::factory()->create();
        Subscription::factory()->active()->for($onFree)->for($free)->create();

        $this->assertTrue($onPro->onPlan('pro'));
        $this->assertFalse($onFree->onPlan('pro'));
    }

    public function test_feature_flags_and_limits_come_from_the_plan(): void
    {
        $plan = Plan::factory()->create([
            'features' => ['max_projects' => 20, 'api_access' => true],
        ]);
        $user = User::factory()->create();
        Subscription::factory()->active()->for($user)->for($plan)->create();

        $this->assertSame(20, $user->planFeature('max_projects'));
        $this->assertTrue($user->hasFeature('api_access'));
        $this->assertFalse($user->hasFeature('white_label'));

        // No subscription => the default is returned.
        $this->assertSame(0, User::factory()->create()->planFeature('max_projects', 0));
    }
}
