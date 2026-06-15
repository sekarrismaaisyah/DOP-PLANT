<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_banned_hsct_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('week', 8);
            $table->string('iso_year', 8);
            $table->string('status', 32)->default('active');
            $table->unsignedSmallInteger('total_items')->default(0);
            $table->unsignedSmallInteger('confirmed_items')->default(0);
            $table->unsignedSmallInteger('reminder_count')->default(0);
            $table->dateTime('initial_sent_at')->nullable();
            $table->dateTime('last_reminder_at')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['week', 'iso_year'], 'uq_auto_banned_hsct_campaign_period');
            $table->index('status', 'idx_auto_banned_hsct_campaign_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_banned_hsct_campaigns');
    }
};
