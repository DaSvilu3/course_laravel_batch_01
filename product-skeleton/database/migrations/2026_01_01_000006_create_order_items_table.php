<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();

            // Polymorphic link to whatever was bought (Service, Product, or any
            // future model implementing App\Contracts\Purchasable).
            $table->nullableMorphs('purchasable');

            // Name + price are copied here so an old order still reads correctly
            // after the catalog changes.
            $table->string('name');
            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('quantity')->default(1);
            $table->unsignedBigInteger('total');
            $table->json('options')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
