<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->string('meeting_level', 32)->default('site')->after('site_id');
            $table->json('target_companies')->nullable()->after('meeting_level');
            $table->json('target_positions')->nullable()->after('target_companies');
            $table->json('target_departments')->nullable()->after('target_positions');
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->dropColumn(['meeting_level', 'target_companies', 'target_positions', 'target_departments']);
        });
    }
};
