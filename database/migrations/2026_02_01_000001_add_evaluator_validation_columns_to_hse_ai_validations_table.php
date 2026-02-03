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
        Schema::table('hse_ai_validations', function (Blueprint $table) {
            // Kolom validasi manual oleh Evaluator
            $table->string('tbc', 50)->nullable()->after('ai_confidence_score')->comment('Validasi TBC BY Evaluator: Valid, Invalid');
            $table->string('gr', 50)->nullable()->after('tbc')->comment('GR: Valid, Potential, Invalid, NonGrRelated');
            $table->text('catatan')->nullable()->after('gr')->comment('Catatan');
            
            // Index untuk kolom baru
            $table->index('tbc');
            $table->index('gr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('hse_ai_validations', function (Blueprint $table) {
            $table->dropIndex(['tbc']);
            $table->dropIndex(['gr']);
            $table->dropColumn(['tbc', 'gr', 'catatan']);
        });
    }
};
