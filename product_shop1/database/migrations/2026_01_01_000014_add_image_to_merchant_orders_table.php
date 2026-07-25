<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * An optional customer-supplied photo of the item they want (e.g. a
 * screenshot from Instagram). Stored on the public disk; only the path is kept.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('merchant_orders', function (Blueprint $table) {
            $table->string('image_path')->nullable()->after('notes');
        });
    }

    public function down(): void
    {
        Schema::table('merchant_orders', function (Blueprint $table) {
            $table->dropColumn('image_path');
        });
    }
};
