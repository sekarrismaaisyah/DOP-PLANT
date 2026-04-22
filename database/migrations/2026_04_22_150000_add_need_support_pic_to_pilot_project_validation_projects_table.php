<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('pilot_project_validation_projects', 'need_support_pic')) {
            Schema::table('pilot_project_validation_projects', function (Blueprint $table): void {
                $table->string('need_support_pic', 255)
                    ->nullable()
                    ->after('next_milestone');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pilot_project_validation_projects', 'need_support_pic')) {
            Schema::table('pilot_project_validation_projects', function (Blueprint $table): void {
                $table->dropColumn('need_support_pic');
            });
        }
    }
};

