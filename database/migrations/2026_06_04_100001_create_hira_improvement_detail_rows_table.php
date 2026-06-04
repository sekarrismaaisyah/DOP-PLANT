<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hira_improvement_detail_rows', function (Blueprint $table) {
            $table->id();
            $table->string('company', 255)->default('Bukit Makmur');
            $table->unsignedSmallInteger('period_year')->default(2026);
            $table->string('improvement_plan', 500);
            $table->string('section', 255)->default('');
            $table->string('activity', 255)->default('');
            $table->string('sub_activity', 255)->default('');
            $table->string('sub_sub_activity', 255)->default('');
            $table->string('rnr', 8)->default('R');
            $table->string('site', 64)->default('HO');
            $table->string('faktor', 16)->default('Men');
            $table->text('hazard')->nullable();
            $table->text('event_potential')->nullable();
            $table->unsignedTinyInteger('kep_awal')->default(1);
            $table->unsignedTinyInteger('konseq_awal')->default(1);
            $table->string('tp_awal', 16)->default('ADM');
            $table->text('existing_control')->nullable();
            $table->string('owner_existing', 255)->nullable();
            $table->string('control_level', 128)->default('L3 - Mendeteksi + Intervensi Manusia');
            $table->string('exposure_type', 32)->default('Unit');
            $table->decimal('exposure_before_value', 14, 2)->default(0);
            $table->decimal('exposure_control_value', 14, 2)->default(0);
            $table->unsignedTinyInteger('kep_sisa')->default(1);
            $table->unsignedTinyInteger('konseq_sisa')->default(1);
            $table->string('target_risk', 32)->default('Medium');
            $table->string('tp_lanjutan', 16)->default('ADM');
            $table->string('owner_lanjutan', 255)->nullable();
            $table->date('start_date')->nullable();
            $table->date('target_date')->nullable();
            $table->string('status', 64)->default('Not Started');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['company', 'period_year']);
            $table->index('improvement_plan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hira_improvement_detail_rows');
    }
};
