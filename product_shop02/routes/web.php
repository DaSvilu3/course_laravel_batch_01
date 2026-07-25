<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Billing;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderTrackController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\Payments\FakeGatewayController;
use App\Http\Controllers\Payments\PaymentCallbackController;
use App\Http\Controllers\Payments\ThawaniWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PublicOrderController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public marketing site
|--------------------------------------------------------------------------
*/

Route::get('/', [PageController::class, 'landing'])->name('home');
Route::get('pricing', [PageController::class, 'pricing'])->name('pricing');
Route::get('privacy', [PageController::class, 'privacy'])->name('privacy');

Route::get('locale/{locale}', LocaleController::class)->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Public order intake (a merchant's shareable form)
|--------------------------------------------------------------------------
*/

Route::get('o/{slug}', [PublicOrderController::class, 'show'])->name('intake.show');
Route::post('o/{slug}', [PublicOrderController::class, 'store'])->name('intake.store');

/*
|--------------------------------------------------------------------------
| Public order tracking (by tracker code)
|--------------------------------------------------------------------------
*/

Route::get('track', [OrderTrackController::class, 'index'])->name('track.index');
Route::post('track', [OrderTrackController::class, 'lookup'])->name('track.lookup');
Route::get('track/{code}', [OrderTrackController::class, 'show'])->name('track.show');

/*
|--------------------------------------------------------------------------
| Merchant area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // The merchant's own orders.
    Route::get('orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/create', [OrderController::class, 'create'])->name('orders.create');
    Route::post('orders', [OrderController::class, 'store'])->name('orders.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [OrderController::class, 'update'])->name('orders.update');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');

    // Billing / SaaS subscriptions.
    Route::get('billing', [Billing\SubscriptionController::class, 'index'])->name('billing.index');
    Route::post('billing/subscribe/{plan}', [Billing\SubscriptionController::class, 'store'])->name('billing.subscribe');
    Route::post('billing/{subscription}/renew', [Billing\SubscriptionController::class, 'renew'])->name('billing.renew');
    Route::post('billing/{subscription}/cancel', [Billing\SubscriptionController::class, 'cancel'])->name('billing.cancel');

    // Example subscriber-only area (any active plan).
    Route::get('members', fn () => view('members'))->middleware('subscribed')->name('members');
});

/*
|--------------------------------------------------------------------------
| Payment callbacks (subscriptions)
|--------------------------------------------------------------------------
*/

Route::middleware('signed')->group(function () {
    Route::get('payment/success/{payment}', [PaymentCallbackController::class, 'success'])->name('checkout.success');
    Route::get('payment/cancel/{payment}', [PaymentCallbackController::class, 'cancel'])->name('checkout.cancel');
});

Route::post('webhooks/thawani', ThawaniWebhookController::class)->name('webhooks.thawani');

if (config('payments.default') === 'fake') {
    Route::controller(FakeGatewayController::class)->prefix('fake-gateway')->name('fake-gateway.')->group(function () {
        Route::get('/', 'show')->name('show');
        Route::post('pay', 'pay')->name('pay');
        Route::post('cancel', 'cancel')->name('cancel');
    });
}

/*
|--------------------------------------------------------------------------
| Admin panel
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', Admin\DashboardController::class)->name('dashboard');

    Route::resource('users', Admin\UserController::class)->except('show');
    Route::resource('plans', Admin\PlanController::class)->except('show');

    Route::get('subscriptions', [Admin\SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions/{subscription}/cancel', [Admin\SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');

    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');

    Route::get('payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/{payment}/verify', [Admin\PaymentController::class, 'verify'])->name('payments.verify');
});

require __DIR__.'/auth.php';
