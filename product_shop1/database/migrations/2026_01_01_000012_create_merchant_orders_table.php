<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Merchant orders: manual orders a merchant receives from a customer through
 * the public intake form. Each one carries a shareable tracker code the
 * customer uses to follow its status. This is separate from the shop `orders`
 * table (which is payable / cart-based).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('tracker_code')->unique();
            $table->string('status')->default('new')->index();

            // The customer who placed the order (captured on the intake form).
            $table->string('customer_name');
            $table->string('customer_phone', 32);
            $table->string('customer_location')->nullable();

            // What they ordered.
            $table->text('item_description');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('amount')->nullable(); // integer baisa, optional
            $table->text('notes')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_orders');
    }
};
