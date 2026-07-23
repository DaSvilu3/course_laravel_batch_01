<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\Payments\FakeGatewayController;
use App\Http\Controllers\Payments\PaymentCallbackController;
use App\Http\Controllers\Payments\ThawaniWebhookController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Shop;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public storefront
|--------------------------------------------------------------------------
*/

Route::get('/', Shop\HomeController::class)->name('home');

Route::get('locale/{locale}', LocaleController::class)->name('locale.switch');

Route::get('services', [Shop\ServiceController::class, 'index'])->name('services.index');
Route::get('services/{service}', [Shop\ServiceController::class, 'show'])->name('services.show');

Route::get('products', [Shop\ProductController::class, 'index'])->name('products.index');
Route::get('products/{product}', [Shop\ProductController::class, 'show'])->name('products.show');

// The cart lives in the session, so guests can fill it before logging in.
Route::controller(Shop\CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::post('/', 'store')->name('store');
    Route::delete('clear', 'clear')->name('clear');
    Route::patch('{key}', 'update')->name('update');
    Route::delete('{key}', 'destroy')->name('destroy');
});

/*
|--------------------------------------------------------------------------
| Customer area
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('dashboard', DashboardController::class)->name('dashboard');

    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('checkout', [Shop\CheckoutController::class, 'show'])->name('checkout.show');
    Route::post('checkout', [Shop\CheckoutController::class, 'store'])->name('checkout.store');

    Route::get('orders', [Shop\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [Shop\OrderController::class, 'show'])->name('orders.show');
    Route::post('orders/{order}/pay', [Shop\OrderController::class, 'pay'])->name('orders.pay');
    Route::post('orders/{order}/cancel', [Shop\OrderController::class, 'cancel'])->name('orders.cancel');

    Route::get('bookings', [Shop\BookingController::class, 'index'])->name('bookings.index');
    Route::patch('bookings/{booking}', [Shop\BookingController::class, 'update'])->name('bookings.update');
});

/*
|--------------------------------------------------------------------------
| Payment callbacks
|--------------------------------------------------------------------------
|
| Signed URLs: the gateway sends the customer back here, and the signature
| stops anyone from calling these routes for someone else's payment. The
| status is still confirmed with the gateway API before anything is saved.
|
*/

Route::middleware('signed')->group(function () {
    Route::get('payment/success/{payment}', [PaymentCallbackController::class, 'success'])->name('checkout.success');
    Route::get('payment/cancel/{payment}', [PaymentCallbackController::class, 'cancel'])->name('checkout.cancel');
});

// Server-to-server notification from Thawani (CSRF excluded in bootstrap/app.php).
Route::post('webhooks/thawani', ThawaniWebhookController::class)->name('webhooks.thawani');

// Local sandbox checkout page — only exists while PAYMENT_GATEWAY=fake.
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
    Route::resource('categories', Admin\CategoryController::class)->except('show');
    Route::resource('services', Admin\ServiceController::class)->except('show');
    Route::resource('products', Admin\ProductController::class)->except('show');

    Route::get('orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    Route::get('orders/{order}', [Admin\OrderController::class, 'show'])->name('orders.show');
    Route::patch('orders/{order}', [Admin\OrderController::class, 'update'])->name('orders.update');

    Route::get('payments', [Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::post('payments/{payment}/verify', [Admin\PaymentController::class, 'verify'])->name('payments.verify');

    Route::get('bookings', [Admin\BookingController::class, 'index'])->name('bookings.index');
    Route::patch('bookings/{booking}', [Admin\BookingController::class, 'update'])->name('bookings.update');
});

require __DIR__.'/auth.php';
