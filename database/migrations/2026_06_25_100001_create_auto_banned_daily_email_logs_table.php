<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_banned_daily_email_logs', function (Blueprint $table) {
            $table->id();
            $table->date('filter_date')->index('idx_ab_daily_email_filter_date');
            $table->string('filter_shift', 32);
            $table->dateTime('scraped_at')->index('idx_ab_daily_email_scraped_at');
            $table->string('recipients', 500);
            $table->unsignedSmallInteger('total_banned')->default(0);
            $table->unsignedSmallInteger('perusahaan_count')->default(0);
            $table->unsignedSmallInteger('site_count')->default(0);
            $table->string('status', 16)->default('sent');
            $table->text('error_message')->nullable();
            $table->dateTime('sent_at');
            $table->timestamps();

            $table->unique(
                ['filter_date', 'filter_shift'],
                'uniq_ab_daily_email_period_shift',
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_banned_daily_email_logs');
    }
};
