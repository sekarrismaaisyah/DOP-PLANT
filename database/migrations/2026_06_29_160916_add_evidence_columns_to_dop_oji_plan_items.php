<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            $table->string('evidence_1')->nullable()->after('group_leader_sid');
            $table->string('evidence_2')->nullable()->after('evidence_1');
            $table->string('evidence_3')->nullable()->after('evidence_2');
            $table->string('evidence_4')->nullable()->after('evidence_3');
            $table->string('evidence_5')->nullable()->after('evidence_4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dop_oji_plan_items', function (Blueprint $table) {
            $table->dropColumn([
                'evidence_1',
                'evidence_2',
                'evidence_3',
                'evidence_4',
                'evidence_5',
            ]);
        });
    }
};