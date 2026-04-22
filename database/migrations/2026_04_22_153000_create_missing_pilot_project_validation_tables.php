<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pilot_project_validation_projects')) {
            Schema::create('pilot_project_validation_projects', function (Blueprint $table): void {
                $table->id();
                $table->string('project_name', 255);
                $table->text('subtitle')->nullable();
                $table->string('pilot_area', 512)->nullable();
                $table->text('support')->nullable();
                $table->string('current_phase', 255)->nullable();
                $table->decimal('progress', 5, 2)->default(0);
                $table->string('current_period', 255)->nullable();
                $table->text('next_milestone')->nullable();
                $table->string('need_support_pic', 255)->nullable();
                $table->timestamps();

                $table->unique('project_name', 'uq_ppv_projects_project_name');
                $table->index('progress', 'idx_ppv_projects_progress');
            });
        }

        if (! Schema::hasTable('pilot_project_validation_roadmap_periods')) {
            Schema::create('pilot_project_validation_roadmap_periods', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('project_id')
                    ->constrained('pilot_project_validation_projects')
                    ->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('display_current_period', 255)->nullable();
                $table->string('period', 255);
                $table->string('phase', 255)->nullable();
                $table->string('status', 32)->default('plan');
                $table->text('period_explanation')->nullable();
                $table->text('planned_objective_outcome')->nullable();
                $table->text('pic_update_summary')->nullable();
                $table->text('pic_risks_dependencies')->nullable();
                $table->string('pic_owner', 255)->nullable();
                $table->date('target_date')->nullable();
                $table->string('reviewer_status', 128)->nullable();
                $table->decimal('period_progress_percent', 5, 2)->nullable();
                $table->timestamps();

                $table->index(['project_id', 'sort_order'], 'idx_ppv_roadmap_project_sort');
                $table->index(['project_id', 'target_date'], 'idx_ppv_roadmap_project_target_date');
            });
        }

        if (! Schema::hasTable('pilot_project_validation_timeline_tasks')) {
            Schema::create('pilot_project_validation_timeline_tasks', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('roadmap_period_id')
                    ->constrained('pilot_project_validation_roadmap_periods')
                    ->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->text('task_text');
                $table->string('task_owner', 255)->nullable();
                $table->string('task_status', 32)->default('plan');
                $table->string('original_owner', 255)->nullable();
                $table->string('original_status', 32)->nullable();
                $table->string('pic_actual_owner', 255)->nullable();
                $table->date('pic_start_date')->nullable();
                $table->decimal('pic_actual_percent', 5, 2)->nullable();
                $table->text('pic_progress_note')->nullable();
                $table->text('evidence_link')->nullable();
                $table->date('target_date')->nullable();
                $table->text('dependency_blocker')->nullable();
                $table->decimal('task_progress_percent_normalized', 5, 2)->nullable();
                $table->timestamps();

                $table->index(['roadmap_period_id', 'sort_order'], 'idx_ppv_tasks_period_sort');
            });
        }

        if (! Schema::hasTable('pilot_project_validation_gates')) {
            Schema::create('pilot_project_validation_gates', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('project_id')
                    ->constrained('pilot_project_validation_projects')
                    ->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('gate_label', 128);
                $table->string('gate_title', 255)->nullable();
                $table->text('gate_caption')->nullable();
                $table->boolean('hard_gate')->default(false);
                $table->text('gate_definition')->nullable();
                $table->text('project_specific_explanation')->nullable();
                $table->text('what_gate_confirms')->nullable();
                $table->text('what_pic_needs_to_fill')->nullable();
                $table->string('pic_status', 128)->nullable();
                $table->text('pic_notes_key_findings')->nullable();
                $table->text('evidence_link_folder')->nullable();
                $table->string('pic_owner', 255)->nullable();
                $table->date('target_close_date')->nullable();
                $table->string('reviewer_status', 128)->nullable();
                $table->timestamps();

                $table->index(['project_id', 'sort_order'], 'idx_ppv_gates_project_sort');
                $table->index(['project_id', 'target_close_date'], 'idx_ppv_gates_project_target_close');
            });
        }

        if (! Schema::hasTable('pilot_project_validation_metrics')) {
            Schema::create('pilot_project_validation_metrics', function (Blueprint $table): void {
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
                $table->text('pic_current_finding')->nullable();
                $table->text('pic_evidence_source')->nullable();
                $table->text('pic_comment')->nullable();
                $table->string('metric_status', 64)->nullable();
                $table->timestamps();

                $table->index(['gate_id', 'sort_order'], 'idx_ppv_metrics_gate_sort');
            });
        }

        if (! Schema::hasTable('pilot_project_validation_history_snapshots')) {
            Schema::create('pilot_project_validation_history_snapshots', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('project_id')
                    ->constrained('pilot_project_validation_projects')
                    ->cascadeOnDelete();
                $table->unsignedInteger('sort_order')->default(0);
                $table->string('snapshot_date', 128);
                $table->decimal('progress', 5, 2)->default(0);
                $table->unsignedSmallInteger('decision_score')->default(0);
                $table->timestamps();

                $table->index(['project_id', 'sort_order'], 'idx_ppv_history_project_sort');
            });
        }
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

