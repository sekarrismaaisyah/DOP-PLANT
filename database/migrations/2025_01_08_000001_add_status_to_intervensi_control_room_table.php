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
        Schema::table('intervensi_control_room', function (Blueprint $table) {
            $table->string('status')->default('open')->after('issue'); // open, closed
            $table->timestamp('closed_at')->nullable()->after('status');
            $table->string('closed_by')->nullable()->after('closed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('intervensi_control_room', function (Blueprint $table) {
            $table->dropColumn(['status', 'closed_at', 'closed_by']);
        });
    }
};

