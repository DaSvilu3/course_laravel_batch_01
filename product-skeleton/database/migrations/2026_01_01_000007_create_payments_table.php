<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            // Polymorphic: the payable is an Order, a Subscription, or anything
            // implementing App\Contracts\Payable.
            $table->morphs('payable');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('gateway')->default('thawani');
            $table->string('status')->default('pending')->index();

            // Thawani checkout session id + the URL we send the customer to.
            $table->string('session_id')->nullable()->index();
            $table->text('checkout_url')->nullable();
            // Thawani invoice number, returned once the payment succeeds.
            $table->string('reference')->nullable()->index();

            $table->unsignedBigInteger('amount'); // baisa
            $table->char('currency', 3)->default('OMR');

            // Raw gateway responses, kept for debugging and reconciliation.
            $table->json('payload')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
