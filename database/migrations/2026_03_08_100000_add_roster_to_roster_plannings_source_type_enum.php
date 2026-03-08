<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // MySQL: ubah enum source_type agar menerima 'Roster'
        DB::statement("ALTER TABLE roster_plannings MODIFY COLUMN source_type ENUM('IKK', 'DOP', 'Roster') NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE roster_plannings MODIFY COLUMN source_type ENUM('IKK', 'DOP') NOT NULL");
    }
};
