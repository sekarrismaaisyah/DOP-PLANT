<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pilot_project_validation_projects', function (Blueprint $table) {
            $table->id();
            $table->string('project_name', 255);
            $table->text('subtitle')->nullable();
            $table->string('pilot_area', 512)->nullable();
            $table->text('support')->nullable();
            $table->string('current_phase', 255)->nullable();
            $table->unsignedTinyInteger('progress')->default(0);
            $table->string('current_period', 255)->nullable();
            $table->text('next_milestone')->nullable();
            $table->timestamps();

            $table->unique('project_name', 'uq_ppv_projects_project_name');
        });

        Schema::create('pilot_project_validation_roadmap_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('pilot_project_validation_projects')
                ->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('period', 255);
            $table->string('phase', 255)->nullable();
            $table->string('status', 32)->default('plan');
            $table->timestamps();

            $table->index(['project_id', 'sort_order'], 'idx_ppv_roadmap_project_sort');
        });

        Schema::create('pilot_project_validation_timeline_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roadmap_period_id')
                ->constrained('pilot_project_validation_roadmap_periods')
                ->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->text('task_text');
            $table->string('task_owner', 255)->nullable();
            $table->string('task_status', 32)->default('plan');
            $table->timestamps();

            $table->index(['roadmap_period_id', 'sort_order'], 'idx_ppv_tasks_period_sort');
        });

        Schema::create('pilot_project_validation_gates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('pilot_project_validation_projects')
                ->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('gate_label', 128);
            $table->string('gate_title', 255)->nullable();
            $table->text('gate_caption')->nullable();
            $table->boolean('hard_gate')->default(false);
            $table->timestamps();

            $table->index(['project_id', 'sort_order'], 'idx_ppv_gates_project_sort');
        });

        Schema::create('pilot_project_validation_metrics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gate_id')
                ->constrained('pilot_project_validation_gates')
                ->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('metric_name', 255);
            $table->string('metric_type', 32)->default('range');
            $table->text('metric_desc')->nullable();
            $table->string('direction', 16)->nullable();
            $table->string('unit', 64)->nullable();
            $table->boolean('critical')->default(false);
            $table->string('metric_value', 64)->nullable();
            $table->decimal('min_value', 14, 4)->nullable();
            $table->decimal('max_value', 14, 4)->nullable();
            $table->decimal('step_value', 14, 4)->nullable();
            $table->decimal('pass_threshold', 14, 4)->nullable();
            $table->decimal('conditional_threshold', 14, 4)->nullable();
            $table->timestamps();

            $table->index(['gate_id', 'sort_order'], 'idx_ppv_metrics_gate_sort');
        });

        Schema::create('pilot_project_validation_history_snapshots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')
                ->constrained('pilot_project_validation_projects')
                ->cascadeOnDelete();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('snapshot_date', 128);
            $table->unsignedTinyInteger('progress')->default(0);
            $table->unsignedSmallInteger('decision_score')->default(0);
            $table->timestamps();

            $table->index(['project_id', 'sort_order'], 'idx_ppv_history_project_sort');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pilot_project_validation_history_snapshots');
        Schema::dropIfExists('pilot_project_validation_metrics');
        Schema::dropIfExists('pilot_project_validation_gates');
        Schema::dropIfExists('pilot_project_validation_timeline_tasks');
        Schema::dropIfExists('pilot_project_validation_roadmap_periods');
        Schema::dropIfExists('pilot_project_validation_projects');
    }
};
