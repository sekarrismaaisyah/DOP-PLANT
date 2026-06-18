<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fatigue_management_program_monitoring', function (Blueprint $table) {
            $table->id();

            $table->string('program_key', 64);
            $table->string('partner_key', 32);
            $table->unsignedSmallInteger('year');
            $table->string('iso_week', 8);

            $table->string('evidence_status', 32)->default('belum_upload');
            $table->string('evidence_file_path', 500)->nullable();
            $table->string('evidence_original_name', 255)->nullable();
            $table->text('evidence_notes')->nullable();
            $table->timestamp('evidence_uploaded_at')->nullable();

            $table->string('evaluation_status', 32)->default('menunggu_evidence');
            $table->unsignedTinyInteger('evaluation_score')->nullable();
            $table->text('evaluation_notes')->nullable();
            $table->string('evaluated_by', 255)->nullable();
            $table->timestamp('evaluated_at')->nullable();

            $table->string('pic_name', 255)->nullable();

            $table->timestamps();

            $table->unique(
                ['program_key', 'partner_key', 'year', 'iso_week'],
                'fatigue_mgmt_monitoring_unique_period'
            );
            $table->index(['year', 'iso_week'], 'fatigue_mgmt_monitoring_year_week_index');
            $table->index('partner_key', 'fatigue_mgmt_monitoring_partner_index');
            $table->index('program_key', 'fatigue_mgmt_monitoring_program_index');
            $table->index('evidence_status', 'fatigue_mgmt_monitoring_evidence_status_index');
            $table->index('evaluation_status', 'fatigue_mgmt_monitoring_evaluation_status_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fatigue_management_program_monitoring');
    }
};
