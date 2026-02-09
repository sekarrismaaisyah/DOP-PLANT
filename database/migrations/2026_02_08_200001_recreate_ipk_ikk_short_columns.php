<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Recreate ipk_ikk with shortened column names.
 * PERHATIAN: Migration ini akan DROP tabel ipk_ikk dan membuat ulang. Data lama akan hilang.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('ipk_ikk');

        Schema::create('ipk_ikk', function (Blueprint $table) {
            $table->id();
            $table->timestamp('ts')->nullable();
            $table->text('nama_pengawas')->nullable();
            $table->string('kode_sid', 100)->nullable();
            $table->string('kode_ikk', 100)->nullable();
            $table->text('nama_perusahaan')->nullable();
            $table->text('site')->nullable();
            $table->string('durasi_jam', 50)->nullable();
            $table->text('cctv_terekam')->nullable();
            $table->text('kategori_ijk')->nullable();
            // Personil & pengawas (1-6)
            $table->text('personil_fit_1')->nullable();
            $table->text('personil_daftar_ikk_1')->nullable();
            $table->text('pengawas_komit_1')->nullable();
            $table->text('personil_fit_2')->nullable();
            $table->text('personil_daftar_ikk_2')->nullable();
            $table->text('pengawas_komit_2')->nullable();
            $table->text('personil_fit_3')->nullable();
            $table->text('personil_daftar_ikk_3')->nullable();
            $table->text('pengawas_komit_3')->nullable();
            $table->text('personil_fit_4')->nullable();
            $table->text('personil_daftar_ikk_4')->nullable();
            $table->text('pengawas_komit_4')->nullable();
            $table->text('personil_fit_5')->nullable();
            $table->text('personil_daftar_ikk_5')->nullable();
            $table->text('pengawas_komit_5')->nullable();
            $table->text('personil_fit_6')->nullable();
            $table->text('personil_daftar_ikk_6')->nullable();
            $table->text('pengawas_komit_6')->nullable();
            // PPE & checklist: TEXT agar row size MySQL < 65535 (utf8mb4)
            $table->text('helm_chain')->nullable();
            $table->text('sarung_grip')->nullable();
            $table->text('sepatu_safety')->nullable();
            $table->text('rompi_pantul')->nullable();
            $table->text('fbh_lanyard')->nullable();
            $table->text('tali_lanyard')->nullable();
            $table->text('tali_webbing')->nullable();
            $table->text('anchor_kuat')->nullable();
            $table->text('double_hook')->nullable();
            $table->text('carabiner')->nullable();
            $table->text('d_rings')->nullable();
            $table->text('helm_chain_2')->nullable();
            $table->text('sarung_grip_2')->nullable();
            $table->text('sepatu_safety_2')->nullable();
            $table->text('rompi_pantul_2')->nullable();
            $table->text('appron_layak')->nullable();
            $table->text('welding_mask')->nullable();
            $table->text('face_shield')->nullable();
            $table->text('helm_chain_3')->nullable();
            $table->text('sarung_grip_3')->nullable();
            $table->text('sepatu_safety_3')->nullable();
            $table->text('rompi_pantul_3')->nullable();
            $table->text('respirator')->nullable();
            $table->text('life_line')->nullable();
            $table->text('helm_chain_4')->nullable();
            $table->text('sarung_grip_4')->nullable();
            $table->text('sepatu_safety_4')->nullable();
            $table->text('rompi_pantul_4')->nullable();
            $table->text('life_vest')->nullable();
            $table->text('kaca_mata')->nullable();
            $table->text('helm_chain_5')->nullable();
            $table->text('sarung_grip_5')->nullable();
            $table->text('sepatu_safety_5')->nullable();
            $table->text('rompi_pantul_5')->nullable();
            $table->text('helm_chain_6')->nullable();
            $table->text('sarung_grip_6')->nullable();
            $table->text('sepatu_safety_6')->nullable();
            $table->text('rompi_pantul_6')->nullable();
            $table->text('anchor_benar')->nullable();
            $table->text('radio_hp')->nullable();
            $table->text('rambu_barikade')->nullable();
            $table->text('unit_sko')->nullable();
            $table->text('support_tools_1')->nullable();
            $table->text('tabung_berdiri')->nullable();
            $table->text('welding_screen')->nullable();
            $table->text('mesin_las_sko')->nullable();
            $table->text('support_tools_2')->nullable();
            $table->text('apar_mudah')->nullable();
            $table->text('gas_detektor')->nullable();
            $table->text('scba')->nullable();
            $table->text('blower')->nullable();
            $table->text('thermometer')->nullable();
            $table->text('perahu_dayung')->nullable();
            $table->text('pijakan_pompa')->nullable();
            $table->text('jembatan_apung')->nullable();
            $table->text('permesinan_sko')->nullable();
            $table->text('ringbuoy')->nullable();
            $table->text('support_tools_3')->nullable();
            $table->text('assessment_geoteknik')->nullable();
            $table->text('material_swl')->nullable();
            $table->text('radio_ok')->nullable();
            $table->text('rambu_barikade_2')->nullable();
            $table->text('alat_angkat_sko')->nullable();
            $table->text('alat_bantu_angkat')->nullable();
            $table->text('alat_bantu_inspeksi')->nullable();
            $table->text('penampung_tumpahan')->nullable();
            $table->text('tray_drum')->nullable();
            // Kerja harian & IK-JSA (1-6)
            $table->text('kerja_dop_1')->nullable();
            $table->text('kerja_ik_jsa_1')->nullable();
            $table->text('ik_jsa_sosialisasi_1')->nullable();
            $table->text('kerja_dop_2')->nullable();
            $table->text('kerja_ik_jsa_2')->nullable();
            $table->text('ik_jsa_sosialisasi_2')->nullable();
            $table->text('kerja_dop_3')->nullable();
            $table->text('kerja_ik_jsa_3')->nullable();
            $table->text('ik_jsa_sosialisasi_3')->nullable();
            $table->text('kerja_dop_4')->nullable();
            $table->text('kerja_ik_jsa_4')->nullable();
            $table->text('ik_jsa_sosialisasi_4')->nullable();
            $table->text('msds_b3')->nullable();
            $table->text('kerja_dop_5')->nullable();
            $table->text('kerja_ik_jsa_5')->nullable();
            $table->text('ik_jsa_sosialisasi_5')->nullable();
            $table->text('rencana_angkat')->nullable();
            $table->text('kerja_dop_6')->nullable();
            $table->text('kerja_ik_jsa_6')->nullable();
            $table->text('ik_jsa_sosialisasi_6')->nullable();
            // Darurat (1-6)
            $table->text('darurat_1')->nullable();
            $table->text('lokasi_komunikasi_1')->nullable();
            $table->text('apar_baik_1')->nullable();
            $table->text('p3k_1')->nullable();
            $table->text('spill_kit_1')->nullable();
            $table->text('darurat_2')->nullable();
            $table->text('lokasi_komunikasi_2')->nullable();
            $table->text('apar_baik_2')->nullable();
            $table->text('p3k_2')->nullable();
            $table->text('spill_kit_2')->nullable();
            $table->text('darurat_3')->nullable();
            $table->text('lokasi_komunikasi_3')->nullable();
            $table->text('apar_baik_3')->nullable();
            $table->text('p3k_3')->nullable();
            $table->text('spill_kit_3')->nullable();
            $table->text('darurat_4')->nullable();
            $table->text('lokasi_komunikasi_4')->nullable();
            $table->text('apar_baik_4')->nullable();
            $table->text('p3k_4')->nullable();
            $table->text('spill_kit_4')->nullable();
            $table->text('darurat_5')->nullable();
            $table->text('lokasi_komunikasi_5')->nullable();
            $table->text('apar_baik_5')->nullable();
            $table->text('p3k_5')->nullable();
            $table->text('spill_kit_5')->nullable();
            $table->text('darurat_6')->nullable();
            $table->text('lokasi_komunikasi_6')->nullable();
            $table->text('apar_baik_6')->nullable();
            $table->text('p3k_6')->nullable();
            $table->text('spill_kit_6')->nullable();
            // Cuaca & lingkungan (1-6)
            $table->text('cuaca_cerah_1')->nullable();
            $table->text('angin_tenang_1')->nullable();
            $table->text('jalur_bebas_1')->nullable();
            $table->text('area_bebas_1')->nullable();
            $table->text('penerangan_1')->nullable();
            $table->text('cuaca_cerah_2')->nullable();
            $table->text('angin_tenang_2')->nullable();
            $table->text('jalur_bebas_2')->nullable();
            $table->text('area_bebas_2')->nullable();
            $table->text('penerangan_2')->nullable();
            $table->text('cuaca_cerah_3')->nullable();
            $table->text('angin_tenang_3')->nullable();
            $table->text('jalur_bebas_3')->nullable();
            $table->text('area_bebas_3')->nullable();
            $table->text('penerangan_3')->nullable();
            $table->text('cuaca_cerah_4')->nullable();
            $table->text('angin_tenang_4')->nullable();
            $table->text('jalur_bebas_4')->nullable();
            $table->text('area_bebas_4')->nullable();
            $table->text('penerangan_4')->nullable();
            $table->text('cuaca_cerah_5')->nullable();
            $table->text('angin_tenang_5')->nullable();
            $table->text('jalur_bebas_5')->nullable();
            $table->text('area_bebas_5')->nullable();
            $table->text('penerangan_5')->nullable();
            $table->text('cuaca_cerah_6')->nullable();
            $table->text('angin_tenang_6')->nullable();
            $table->text('jalur_bebas_6')->nullable();
            $table->text('area_bebas_6')->nullable();
            $table->text('penerangan_6')->nullable();
            // Akhir
            $table->text('jenis_pengawasan')->nullable();
            $table->string('status_pekerjaan', 100)->nullable();
            $table->text('alasan_ditunda')->nullable();
            $table->text('evidence_bukti')->nullable();
            $table->text('kategori_hold')->nullable();
            $table->text('detail_lokasi')->nullable();
            $table->text('nama_pekerjaan')->nullable();
            $table->string('kode_ikk_2', 100)->nullable();
            $table->text('keterangan_batal')->nullable();
            $table->text('l1_okk_awal')->nullable();
            $table->text('l1_okk_tengah')->nullable();
            $table->text('l1_okk_akhir')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ipk_ikk');
    }
};
