<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();

            // The merchant who owns (received) this order.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            // Public code the customer uses to follow the order (QD-XXXXXX).
            $table->string('tracker_code')->unique();

            // new / in_progress / completed / cancelled
            $table->string('status')->default('new')->index();

            // Where the order came from: the public intake form or manual entry.
            $table->string('source')->default('manual')->index();

            // The customer as captured on the order.
            $table->string('customer_name');
            $table->string('customer_phone', 32);

            // What was ordered.
            $table->text('item_description');
            $table->unsignedInteger('quantity')->default(1);

            // Money is integer baisa. Price is optional (may be agreed later).
            $table->unsignedBigInteger('price')->nullable();
            $table->char('currency', 3)->default('OMR');
            $table->string('payment_method')->nullable(); // cod / transfer

            // Delivery.
            $table->string('country', 2)->default('OM');
            $table->string('governorate')->nullable();
            $table->text('address')->nullable();
            $table->string('location_note')->nullable();

            $table->text('notes')->nullable();
            $table->string('attachment_path')->nullable();

            // Status timestamps.
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
