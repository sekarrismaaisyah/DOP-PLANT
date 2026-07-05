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
        Schema::table('dop_safety_plan_items', function (Blueprint $table) {
            // Menambahkan kolom approval_status setelah kolom pja_bc dengan nilai default 'draft' atau 'waiting_lce'
            $table->string('approval_status', 50)
                  ->default('waiting_lce')
                  ->after('pja_bc');
            
            // Menambahkan indeks agar query massal (whereIn) berjalan sangat cepat
            $table->index('approval_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dop_safety_plan_items', function (Blueprint $table) {
            $table->dropIndex(['approval_status']);
            $table->dropColumn('approval_status');
        });
    }
};