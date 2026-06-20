<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('tableau_flow_history')) {
            return;
        }

        Schema::create('tableau_flow_history', function (Blueprint $table): void {
            $table->id();
            $table->dateTime('logged_at')->index('idx_tableau_flow_history_logged_at');
            $table->string('status_code', 64)->index('idx_tableau_flow_history_status_code');
            $table->string('flow_name', 255)->index('idx_tableau_flow_history_flow_name');
            $table->string('output_name', 255);
            $table->string('status_detail', 255);
            $table->string('trigger_type', 64)->index('idx_tableau_flow_history_trigger_type');
            $table->string('flow_url', 512);
            $table->timestamp('created_at')->nullable()->index('idx_tableau_flow_history_created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tableau_flow_history');
    }
};
