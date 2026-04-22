<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pilot_project_validation_timeline_tasks')) {
            return;
        }

        Schema::table('pilot_project_validation_timeline_tasks', function (Blueprint $table): void {
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'original_owner')) {
                $table->string('original_owner', 255)->nullable()->after('task_status');
            }
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'original_status')) {
                $table->string('original_status', 32)->nullable()->after('original_owner');
            }
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'pic_actual_owner')) {
                $table->string('pic_actual_owner', 255)->nullable()->after('original_status');
            }
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'pic_start_date')) {
                $table->date('pic_start_date')->nullable()->after('pic_actual_owner');
            }
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'pic_actual_percent')) {
                $table->decimal('pic_actual_percent', 5, 2)->nullable()->after('pic_start_date');
            }
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'pic_progress_note')) {
                $table->text('pic_progress_note')->nullable()->after('pic_actual_percent');
            }
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'evidence_link')) {
                $table->text('evidence_link')->nullable()->after('pic_progress_note');
            }
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'target_date')) {
                $table->date('target_date')->nullable()->after('evidence_link');
            }
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'dependency_blocker')) {
                $table->text('dependency_blocker')->nullable()->after('target_date');
            }
            if (! Schema::hasColumn('pilot_project_validation_timeline_tasks', 'task_progress_percent_normalized')) {
                $table->decimal('task_progress_percent_normalized', 5, 2)->nullable()->after('dependency_blocker');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('pilot_project_validation_timeline_tasks')) {
            return;
        }

        Schema::table('pilot_project_validation_timeline_tasks', function (Blueprint $table): void {
            $dropColumns = [];
            foreach ([
                'original_owner',
                'original_status',
                'pic_actual_owner',
                'pic_start_date',
                'pic_actual_percent',
                'pic_progress_note',
                'evidence_link',
                'target_date',
                'dependency_blocker',
                'task_progress_percent_normalized',
            ] as $column) {
                if (Schema::hasColumn('pilot_project_validation_timeline_tasks', $column)) {
                    $dropColumns[] = $column;
                }
            }

            if ($dropColumns !== []) {
                $table->dropColumn($dropColumns);
            }
        });
    }
};

