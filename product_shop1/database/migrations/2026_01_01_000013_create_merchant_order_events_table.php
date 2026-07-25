<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * The status history of a merchant order: one row per status the order has
 * been in, in order. Powers the order details timeline and public tracking.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('merchant_order_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_order_id')->constrained()->cascadeOnDelete();
            $table->string('status');
            $table->timestamp('created_at')->nullable();

            $table->index(['merchant_order_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('merchant_order_events');
    }
};
