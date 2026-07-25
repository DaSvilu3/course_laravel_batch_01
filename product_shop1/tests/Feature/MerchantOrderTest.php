<?php

namespace Tests\Feature;

use App\Enums\MerchantOrderStatus;
use App\Models\MerchantOrder;
use App\Models\MerchantOrderEvent;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * The core product: a public intake link that creates trackable orders,
 * gated by the merchant's plan quota.
 */
class MerchantOrderTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_intake_form_is_visible(): void
    {
        $merchant = User::factory()->create(['store_name' => 'متجر النور']);

        $this->get(route('intake.show', $merchant->store_slug))
            ->assertOk()
            ->assertSee('متجر النور');
    }

    public function test_placing_an_order_creates_it_with_a_tracker_code(): void
    {
        $merchant = User::factory()->create();

        $response = $this->post(route('intake.store', $merchant->store_slug), [
            'customer_name' => 'أحمد',
            'customer_phone' => '90000000',
            'item_description' => 'عباية سوداء مقاس M',
            'quantity' => 2,
            'amount' => 15.5,
        ]);

        $order = MerchantOrder::firstOrFail();

        $this->assertSame($merchant->id, $order->user_id);
        $this->assertSame(MerchantOrderStatus::New, $order->status);
        $this->assertStringStartsWith('TLB-', $order->tracker_code);
        $this->assertSame(15_500, $order->amount); // 15.500 OMR -> baisa

        $response->assertRedirect(route('intake.received', $merchant->store_slug))
            ->assertSessionHas('tracker_code', $order->tracker_code);
    }

    public function test_the_free_tier_is_capped_at_ten_orders_per_day(): void
    {
        $merchant = User::factory()->create(); // no subscription => free limit of 10/day

        MerchantOrder::factory()->count(10)->for($merchant)->create();

        $this->assertFalse($merchant->canAcceptOrder());

        $this->post(route('intake.store', $merchant->store_slug), [
            'customer_name' => 'زبون',
            'customer_phone' => '90000000',
            'item_description' => 'طلب إضافي',
            'quantity' => 1,
        ])->assertSessionHas('error');

        $this->assertSame(10, $merchant->merchantOrders()->count());
    }

    public function test_a_customer_can_track_an_order_by_code(): void
    {
        $order = MerchantOrder::factory()
            ->status(MerchantOrderStatus::Preparing)
            ->create();

        $this->get(route('track', $order->tracker_code))
            ->assertOk()
            ->assertSee($order->tracker_code)
            ->assertSee(MerchantOrderStatus::Preparing->label());
    }

    public function test_tracking_an_unknown_code_reports_not_found(): void
    {
        $this->get(route('track', 'TLB-NOPE00'))
            ->assertOk()
            ->assertSee(__('orders.track_not_found'));
    }

    public function test_a_merchant_can_update_their_order_status(): void
    {
        $merchant = User::factory()->create();
        $order = MerchantOrder::factory()->for($merchant)->status(MerchantOrderStatus::New)->create();

        $this->actingAs($merchant)
            ->patch(route('merchant.orders.update', $order), ['status' => MerchantOrderStatus::Confirmed->value])
            ->assertRedirect();

        $this->assertSame(MerchantOrderStatus::Confirmed, $order->fresh()->status);
    }

    public function test_a_merchant_cannot_update_someone_elses_order(): void
    {
        $order = MerchantOrder::factory()->status(MerchantOrderStatus::New)->create();

        $this->actingAs(User::factory()->create())
            ->patch(route('merchant.orders.update', $order), ['status' => MerchantOrderStatus::Cancelled->value])
            ->assertForbidden();

        $this->assertSame(MerchantOrderStatus::New, $order->fresh()->status);
    }

    public function test_the_dashboard_lists_the_merchants_orders(): void
    {
        $merchant = User::factory()->create();
        $order = MerchantOrder::factory()->for($merchant)->create(['customer_name' => 'سالم القحطاني']);

        $this->actingAs($merchant)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee($order->tracker_code)
            ->assertSee('سالم القحطاني');
    }

    public function test_updating_the_status_records_a_history_entry(): void
    {
        $merchant = User::factory()->create();
        $order = MerchantOrder::factory()->for($merchant)->status(MerchantOrderStatus::New)->create();
        $before = $order->events()->count();

        $this->actingAs($merchant)
            ->patch(route('merchant.orders.update', $order), ['status' => MerchantOrderStatus::Confirmed->value]);

        $this->assertSame($before + 1, $order->events()->count());
        $latest = MerchantOrderEvent::where('merchant_order_id', $order->id)->orderByDesc('id')->first();
        $this->assertSame(MerchantOrderStatus::Confirmed, $latest->status);
    }

    public function test_the_order_details_page_shows_the_history(): void
    {
        $merchant = User::factory()->create();
        $order = MerchantOrder::factory()->for($merchant)->status(MerchantOrderStatus::Delivered)->create();

        $this->actingAs($merchant)
            ->get(route('merchant.orders.show', $order))
            ->assertOk()
            ->assertSee($order->tracker_code)
            ->assertSee(__('orders.history_title'))
            ->assertSee(MerchantOrderStatus::Delivered->label());
    }

    public function test_a_merchant_cannot_view_someone_elses_order_details(): void
    {
        $order = MerchantOrder::factory()->create();

        $this->actingAs(User::factory()->create())
            ->get(route('merchant.orders.show', $order))
            ->assertForbidden();
    }

    public function test_the_analytics_page_loads_for_a_merchant(): void
    {
        $merchant = User::factory()->create();
        MerchantOrder::factory()->count(3)->for($merchant)->status(MerchantOrderStatus::Delivered)->create();

        $this->actingAs($merchant)
            ->get(route('merchant.analytics'))
            ->assertOk()
            ->assertSee(__('orders.chart_daily_title'))
            ->assertSee(__('orders.breakdown_title'));
    }

    public function test_a_customer_can_attach_a_photo_to_their_order(): void
    {
        Storage::fake('public');
        $merchant = User::factory()->create();

        $this->post(route('intake.store', $merchant->store_slug), [
            'customer_name' => 'أحمد',
            'customer_phone' => '90000000',
            'item_description' => 'ساعة',
            'quantity' => 1,
            'image' => UploadedFile::fake()->image('item.jpg', 600, 600),
        ])->assertRedirect();

        $order = MerchantOrder::firstOrFail();
        $this->assertNotNull($order->image_path);
        Storage::disk('public')->assertExists($order->image_path);
    }

    public function test_the_intake_form_rejects_a_non_image_file(): void
    {
        Storage::fake('public');
        $merchant = User::factory()->create();

        $this->from(route('intake.show', $merchant->store_slug))
            ->post(route('intake.store', $merchant->store_slug), [
                'customer_name' => 'أحمد',
                'customer_phone' => '90000000',
                'item_description' => 'ساعة',
                'quantity' => 1,
                'image' => UploadedFile::fake()->create('malware.php', 12, 'application/x-php'),
            ])
            ->assertSessionHasErrors('image');

        $this->assertSame(0, MerchantOrder::count());
    }

    public function test_a_merchant_can_upload_a_store_logo(): void
    {
        Storage::fake('public');
        $merchant = User::factory()->create();

        $this->actingAs($merchant)
            ->patch(route('profile.update'), [
                'name' => $merchant->name,
                'email' => $merchant->email,
                'store_name' => 'متجر النور',
                'logo' => UploadedFile::fake()->image('logo.png'),
            ])
            ->assertRedirect(route('profile.edit'));

        $merchant->refresh();
        $this->assertNotNull($merchant->logo_path);
        $this->assertSame('متجر النور', $merchant->store_name);
        Storage::disk('public')->assertExists($merchant->logo_path);
    }
}
