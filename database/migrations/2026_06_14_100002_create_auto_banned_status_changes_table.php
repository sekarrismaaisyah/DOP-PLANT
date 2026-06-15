<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_banned_status_changes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('snapshot_id')->constrained('auto_banned_status_snapshots')->cascadeOnDelete();
            $table->string('sid', 64);
            $table->string('week', 8);
            $table->string('iso_year', 8);
            $table->string('from_system_status', 32)->nullable();
            $table->string('to_system_status', 32);
            $table->string('change_type', 48);
            $table->string('scrap_status_raw', 255)->nullable();
            $table->unsignedBigInteger('scr_row_id')->nullable();
            $table->dateTime('detected_at');
            $table->dateTime('scr_scraped_at')->nullable();
            $table->timestamps();

            $table->index('sid', 'idx_auto_banned_changes_sid');
            $table->index('detected_at', 'idx_auto_banned_changes_detected');
            $table->index(['week', 'iso_year'], 'idx_auto_banned_changes_period');
            $table->index('change_type', 'idx_auto_banned_changes_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_banned_status_changes');
    }
};
