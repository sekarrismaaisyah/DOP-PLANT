<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('pilot_project_validation_roadmap_periods')) {
            return;
        }

        Schema::table('pilot_project_validation_roadmap_periods', function (Blueprint $table): void {
            if (! Schema::hasColumn('pilot_project_validation_roadmap_periods', 'display_current_period')) {
                $table->string('display_current_period', 255)->nullable()->after('sort_order');
            }
            if (! Schema::hasColumn('pilot_project_validation_roadmap_periods', 'period_explanation')) {
                $table->text('period_explanation')->nullable()->after('status');
            }
            if (! Schema::hasColumn('pilot_project_validation_roadmap_periods', 'planned_objective_outcome')) {
                $table->text('planned_objective_outcome')->nullable()->after('period_explanation');
            }
            if (! Schema::hasColumn('pilot_project_validation_roadmap_periods', 'pic_update_summary')) {
                $table->text('pic_update_summary')->nullable()->after('planned_objective_outcome');
            }
            if (! Schema::hasColumn('pilot_project_validation_roadmap_periods', 'pic_risks_dependencies')) {
                $table->text('pic_risks_dependencies')->nullable()->after('pic_update_summary');
            }
            if (! Schema::hasColumn('pilot_project_validation_roadmap_periods', 'pic_owner')) {
                $table->string('pic_owner', 255)->nullable()->after('pic_risks_dependencies');
            }
            if (! Schema::hasColumn('pilot_project_validation_roadmap_periods', 'target_date')) {
                $table->date('target_date')->nullable()->after('pic_owner');
            }
            if (! Schema::hasColumn('pilot_project_validation_roadmap_periods', 'reviewer_status')) {
                $table->string('reviewer_status', 128)->nullable()->after('target_date');
            }
            if (! Schema::hasColumn('pilot_project_validation_roadmap_periods', 'period_progress_percent')) {
                $table->decimal('period_progress_percent', 5, 2)->nullable()->after('reviewer_status');
            }
        });
    }

    public function down(): void
    {
        // Tidak drop kolom pada migration sync agar aman untuk environment existing.
    }
};

