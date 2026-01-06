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
        Schema::create('cctv_p2h_checklist', function (Blueprint $table) {
            $table->id();
            $table->string('control_room'); // Control room yang di-P2H
            $table->date('tanggal_pemeriksaan'); // Tanggal pemeriksaan
            $table->string('shift'); // Shift (1, 2, 3, atau lainnya)
            $table->json('jenis_cctv')->nullable(); // Array jenis CCTV: Mining Eyes, Mining Eyes Analytics, Plant, Support
            $table->string('nama_pengawas'); // Nama pengawas yang melakukan P2H
            
            // Section A: Pemeriksaan Fisik (9 items)
            // Format: JSON dengan struktur {item_name: {jumlah: int, ketersediaan: 'ada'|'tidak_ada', kondisi: 'baik'|'rusak'}}
            $table->json('pemeriksaan_fisik')->nullable();
            
            // Section B: Pemeriksaan Fungsi (8 items)
            // Format: JSON dengan struktur {item_name: {status: 'baik'|'rusak'|'tidak_ada'}}
            $table->json('pemeriksaan_fungsi')->nullable();
            
            // Detail checklist per CCTV (optional, untuk tracking per CCTV)
            $table->json('detail_cctv')->nullable(); // Array of {cctv_id, nama_cctv, status, catatan}
            
            $table->text('catatan_lain')->nullable(); // Catatan lain-lain
            $table->string('status')->default('draft'); // draft, completed, verified
            $table->timestamps();
            
            // Index untuk performa query
            $table->index(['control_room', 'tanggal_pemeriksaan', 'shift']);
            $table->index('tanggal_pemeriksaan');
            $table->index('nama_pengawas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cctv_p2h_checklist');
    }
};

