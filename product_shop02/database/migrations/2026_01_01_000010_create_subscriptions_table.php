<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('plan_id')->constrained()->cascadeOnDelete();

            $table->string('status')->default('pending')->index();

            // Snapshot of what was bought, so a later price/plan change never
            // rewrites history.
            $table->string('plan_name');
            $table->unsignedBigInteger('price');
            $table->string('interval');
            $table->char('currency', 3)->default('OMR');

            $table->timestamp('trial_ends_at')->nullable();
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable()->index(); // current term end / renewal date
            $table->timestamp('canceled_at')->nullable();       // set = will not renew
            $table->timestamp('renewal_reminded_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
