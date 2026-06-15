<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_banned_unban_requests', function (Blueprint $table) {
            $table->id();
            $table->string('sid', 64);
            $table->string('karyawan', 255);
            $table->string('perusahaan', 255)->nullable();
            $table->string('site_dedicated', 255)->nullable();
            $table->string('banned_reason', 255)->nullable();
            $table->string('status_banned_ref', 255)->nullable();
            $table->text('alasan_pengajuan');
            $table->string('status', 32)->default('pending');
            $table->string('week', 8)->nullable();
            $table->string('iso_year', 8)->nullable();
            $table->unsignedBigInteger('submitted_by_id')->nullable();
            $table->string('submitted_by_name', 255);
            $table->unsignedBigInteger('reviewed_by_id')->nullable();
            $table->string('reviewed_by_name', 255)->nullable();
            $table->dateTime('reviewed_at')->nullable();
            $table->text('catatan_review')->nullable();
            $table->timestamps();

            $table->index('sid', 'idx_auto_banned_unban_requests_sid');
            $table->index('status', 'idx_auto_banned_unban_requests_status');
            $table->index(['site_dedicated', 'status'], 'idx_auto_banned_unban_requests_site_status');
            $table->index('created_at', 'idx_auto_banned_unban_requests_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_banned_unban_requests');
    }
};
