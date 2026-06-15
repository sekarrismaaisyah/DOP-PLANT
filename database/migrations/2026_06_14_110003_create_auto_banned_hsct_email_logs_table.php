<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_banned_hsct_email_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('auto_banned_hsct_campaigns')->cascadeOnDelete();
            $table->string('email_type', 32);
            $table->unsignedSmallInteger('reminder_number')->default(1);
            $table->string('week', 8);
            $table->string('iso_year', 8);
            $table->string('recipients', 500);
            $table->unsignedSmallInteger('total_in_list')->default(0);
            $table->unsignedSmallInteger('pending_count')->default(0);
            $table->unsignedSmallInteger('confirmed_count')->default(0);
            $table->json('payload')->nullable();
            $table->string('status', 16)->default('sent');
            $table->text('error_message')->nullable();
            $table->dateTime('sent_at');
            $table->timestamps();

            $table->index('email_type', 'idx_auto_banned_hsct_email_type');
            $table->index('sent_at', 'idx_auto_banned_hsct_email_sent_at');
            $table->index(['week', 'iso_year'], 'idx_auto_banned_hsct_email_period');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_banned_hsct_email_logs');
    }
};
