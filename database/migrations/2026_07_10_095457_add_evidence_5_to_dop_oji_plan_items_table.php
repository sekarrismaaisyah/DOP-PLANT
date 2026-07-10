<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            // Menambahkan kolom evidence_5 setelah evidence_4
            $table->string('evidence_5')->nullable()->after('evidence_4');
        });
    }

    public function down()
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            $table->dropColumn('evidence_5');
        });
    }
};
