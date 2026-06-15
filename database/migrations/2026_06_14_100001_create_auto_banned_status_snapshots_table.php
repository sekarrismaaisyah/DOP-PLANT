<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_banned_status_snapshots', function (Blueprint $table) {
            $table->id();
            $table->string('sid', 64);
            $table->string('week', 8);
            $table->string('iso_year', 8);
            $table->string('karyawan', 255)->nullable();
            $table->string('perusahaan', 255)->nullable();
            $table->string('site_dedicated', 255)->nullable();
            $table->string('banned_reason', 255)->nullable();
            $table->string('system_status', 32)->default('passed');
            $table->string('scrap_status_raw', 255)->nullable();
            $table->unsignedBigInteger('scr_row_id')->nullable();
            $table->string('ban_status', 48)->default('open_banned');
            $table->string('treatment_status', 48)->default('none');
            $table->string('verification_status', 48)->default('none');
            $table->string('hsct_sync_status', 32)->default('not_required');
            $table->dateTime('first_seen_at');
            $table->dateTime('last_seen_at');
            $table->dateTime('status_changed_at')->nullable();
            $table->dateTime('banned_detected_at')->nullable();
            $table->dateTime('hsct_sent_at')->nullable();
            $table->dateTime('hsct_confirmed_at')->nullable();
            $table->dateTime('treatment_submitted_at')->nullable();
            $table->dateTime('verification_done_at')->nullable();
            $table->dateTime('unban_opened_at')->nullable();
            $table->dateTime('unban_closed_at')->nullable();
            $table->dateTime('scr_scraped_at')->nullable();
            $table->timestamps();

            $table->unique(['sid', 'week', 'iso_year'], 'uq_auto_banned_snap_sid_period');
            $table->index('system_status', 'idx_auto_banned_snap_system_status');
            $table->index('ban_status', 'idx_auto_banned_snap_ban_status');
            $table->index('hsct_sync_status', 'idx_auto_banned_snap_hsct_sync');
            $table->index(['week', 'iso_year'], 'idx_auto_banned_snap_period');
            $table->index('last_seen_at', 'idx_auto_banned_snap_last_seen');
            $table->index(['site_dedicated', 'system_status'], 'idx_auto_banned_snap_site_system');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_banned_status_snapshots');
    }
};
