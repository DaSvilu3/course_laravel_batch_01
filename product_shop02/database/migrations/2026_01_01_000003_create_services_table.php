<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('slug')->unique();
            $table->string('name_ar');
            $table->string('name_en');
            $table->text('description_ar')->nullable();
            $table->text('description_en')->nullable();
            // Prices are stored as integer baisa (1 OMR = 1000 baisa) so we never
            // do float maths on money. Thawani expects baisa too.
            $table->unsignedBigInteger('price')->default(0);
            $table->unsignedInteger('duration_minutes')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_bookable')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
