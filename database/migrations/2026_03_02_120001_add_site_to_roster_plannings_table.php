<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roster_plannings', function (Blueprint $table) {
            $table->string('site', 255)->nullable()->after('source_id');
        });
    }

    public function down(): void
    {
        Schema::table('roster_plannings', function (Blueprint $table) {
            $table->dropColumn('site');
        });
    }
};
