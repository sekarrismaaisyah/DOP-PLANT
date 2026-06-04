<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hira_improvement_scurve_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('company', 255)->default('Bukit Makmur');
            $table->unsignedSmallInteger('period_year')->default(2026);
            $table->string('improvement_plan', 500);
            $table->string('task_name', 255);
            $table->date('planned_date')->nullable();
            $table->date('actual_date')->nullable();
            $table->string('status', 32)->default('Open');
            $table->text('note')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index(['company', 'period_year']);
            $table->index('improvement_plan');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hira_improvement_scurve_tasks');
    }
};
