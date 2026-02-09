<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('dopm', 'id_dop')) {
            return;
        }

        Schema::table('dopm', function (Blueprint $table) {
            $table->string('id_dop', 100)->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('dopm', function (Blueprint $table) {
            $table->dropColumn('id_dop');
        });
    }
};
