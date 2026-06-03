<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('event_minutes', function (Blueprint $table): void {
            $table->json('issue_sections')->nullable()->after('location');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE minute_issues MODIFY section VARCHAR(64) NOT NULL');
        } else {
            Schema::table('minute_issues', function (Blueprint $table): void {
                $table->string('section', 64)->change();
            });
        }
    }

    public function down(): void
    {
        Schema::table('event_minutes', function (Blueprint $table): void {
            $table->dropColumn('issue_sections');
        });

        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE minute_issues MODIFY section ENUM('enviro','safety','general') NOT NULL");
        }
    }
};
