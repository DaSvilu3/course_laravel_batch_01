<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Merchant profile fields. Every user is a merchant who receives orders
 * through a public intake link at /o/{store_slug}.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('store_name')->nullable()->after('name');
            $table->string('store_slug')->nullable()->unique()->after('store_name');
            $table->string('whatsapp', 32)->nullable()->after('phone');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['store_name', 'store_slug', 'whatsapp']);
        });
    }
};
