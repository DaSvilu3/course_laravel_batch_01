<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('user')->after('email')->index();
            $table->string('phone', 32)->nullable()->after('role');
            $table->boolean('is_active')->default(true)->after('phone');
            $table->timestamp('last_login_at')->nullable()->after('is_active');

            // Store (merchant) profile — powers the public intake link.
            $table->string('store_name')->nullable()->after('last_login_at');
            $table->string('intake_slug')->nullable()->unique()->after('store_name');
            $table->string('whatsapp', 32)->nullable()->after('intake_slug');
            $table->string('store_logo_path')->nullable()->after('whatsapp');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'role', 'phone', 'is_active', 'last_login_at',
                'store_name', 'intake_slug', 'whatsapp', 'store_logo_path',
            ]);
        });
    }
};
