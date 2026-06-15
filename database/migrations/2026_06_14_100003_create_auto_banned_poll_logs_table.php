<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('auto_banned_poll_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('rows_processed')->default(0);
            $table->unsignedInteger('new_snapshots')->default(0);
            $table->unsignedInteger('status_changes')->default(0);
            $table->dateTime('poll_started_at');
            $table->dateTime('poll_finished_at')->nullable();
            $table->string('status', 16)->default('running');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index('poll_started_at', 'idx_auto_banned_poll_started');
            $table->index('status', 'idx_auto_banned_poll_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('auto_banned_poll_logs');
    }
};
