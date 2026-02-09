<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('okk')) {
            Schema::create('okk', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
            });
        }

        if (Schema::hasColumn('okk', 'ts')) {
            return;
        }

        Schema::table('okk', function (Blueprint $table) {
            $table->timestamp('ts')->nullable()->after('id');
            $table->text('nama_pengawas')->nullable();
            $table->string('kode_sid', 100)->nullable();
            $table->string('kode_ikk', 100)->nullable();
            $table->text('nama_perusahaan')->nullable();
            $table->text('site')->nullable();
            $table->text('jenis_ijk')->nullable();
            // Nama kolom max 64 karakter (MySQL limit), pakai TEXT untuk hindari row size
            $table->text('fbh_anchor_kuat')->nullable();
            $table->text('hook_perpindahan')->nullable();
            $table->text('hook_pekerjaan')->nullable();
            $table->text('personil_tidak_tersengat')->nullable();
            $table->text('bangunan_tidak_rebah')->nullable();
            $table->text('barang_bawaan_tidak_jatuh')->nullable();
            $table->text('platform_material_besi')->nullable();
            $table->text('tidak_kotor_licin')->nullable();
            $table->text('posisi_ergonomis')->nullable();
            $table->text('pekerja_fokus')->nullable();
            $table->text('pekerja_fbh_lanyard')->nullable();
            $table->text('personil_pelampung')->nullable();
            $table->text('personil_platform_memadai')->nullable();
            $table->text('pencahayaan_cukup')->nullable();
            $table->text('oksigen_cukup')->nullable();
            $table->text('tidak_material_terbakar')->nullable();
            $table->text('sumber_bahaya_dikendalikan')->nullable();
            $table->text('material_swl')->nullable();
            $table->text('material_pengikatan')->nullable();
            $table->text('tidak_manusia_bawah_swing')->nullable();
            $table->text('unit_tidak_rebah')->nullable();
            $table->text('rencana_pengangkatan')->nullable();
            $table->text('pengendalian_kebakaran')->nullable();
            $table->text('tidak_las_dekat_terbakar')->nullable();
            $table->text('potensi_tersetrum')->nullable();
            $table->text('potensi_terjepit')->nullable();
            $table->text('personil_terdaftar_ikk')->nullable();
            $table->text('verifikasi_ipk')->nullable();
            $table->text('personil_apd_benar')->nullable();
            $table->text('personil_peralatan_benar')->nullable();
            $table->text('personil_prosedur_ik_jsa')->nullable();
            $table->text('cuaca_cerah')->nullable();
            $table->text('angin_tenang')->nullable();
            $table->text('layer_pengawas')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('okk', function (Blueprint $table) {
            $columns = [
                'ts', 'nama_pengawas', 'kode_sid', 'kode_ikk', 'nama_perusahaan', 'site', 'jenis_ijk',
                'fbh_anchor_kuat', 'hook_perpindahan', 'hook_pekerjaan', 'personil_tidak_tersengat',
                'bangunan_tidak_rebah', 'barang_bawaan_tidak_jatuh', 'platform_material_besi', 'tidak_kotor_licin',
                'posisi_ergonomis', 'pekerja_fokus', 'pekerja_fbh_lanyard', 'personil_pelampung', 'personil_platform_memadai',
                'pencahayaan_cukup', 'oksigen_cukup', 'tidak_material_terbakar', 'sumber_bahaya_dikendalikan',
                'material_swl', 'material_pengikatan', 'tidak_manusia_bawah_swing', 'unit_tidak_rebah',
                'rencana_pengangkatan', 'pengendalian_kebakaran', 'tidak_las_dekat_terbakar', 'potensi_tersetrum',
                'potensi_terjepit', 'personil_terdaftar_ikk', 'verifikasi_ipk', 'personil_apd_benar',
                'personil_peralatan_benar', 'personil_prosedur_ik_jsa', 'cuaca_cerah', 'angin_tenang', 'layer_pengawas',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('okk', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
