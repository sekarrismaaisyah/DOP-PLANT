<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Satu no_kecelakaan bisa punya banyak tag (one-to-many).
     */
    public function up(): void
    {
        Schema::create('insiden_tabel_tags', function (Blueprint $table) {
            $table->id();
            $table->string('no_kecelakaan');
            $table->string('tag');
            $table->timestamps();
            $table->index('no_kecelakaan');
        });

        // Jika kolom tag masih ada di group_meta (migrasi lama), pindahkan ke tabel tags lalu hapus kolom.
        if (Schema::hasTable('insiden_tabel_group_meta') && Schema::hasColumn('insiden_tabel_group_meta', 'tag')) {
            $rows = \DB::table('insiden_tabel_group_meta')->whereNotNull('tag')->where('tag', '!=', '')->get();
            foreach ($rows as $row) {
                \DB::table('insiden_tabel_tags')->insert([
                    'no_kecelakaan' => $row->no_kecelakaan,
                    'tag' => $row->tag,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
            Schema::table('insiden_tabel_group_meta', function (Blueprint $table) {
                $table->dropColumn('tag');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('insiden_tabel_tags');
    }
};
