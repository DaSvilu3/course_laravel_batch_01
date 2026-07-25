<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();

            // Price in baisa. 0 = a free plan (no checkout).
            $table->unsignedBigInteger('price')->default(0);
            $table->string('interval')->default('month'); // month | year
            $table->unsignedInteger('trial_days')->default(0);

            // Feature flags + limits, e.g. {"max_projects": 10, "api": true}.
            // Read with $plan->feature('max_projects'). -1 means unlimited.
            $table->json('features')->nullable();

            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
