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
        if (Schema::hasTable('ipk_ikk')) {
            return;
        }

        Schema::create('ipk_ikk', function (Blueprint $table) {
            $table->id();
            $table->timestamp('timestamp')->nullable();
            $table->string('nama_pengawas', 500)->nullable();
            $table->string('kode_sid', 100)->nullable();
            $table->string('kode_ikk', 100)->nullable();
            $table->string('nama_perusahaan', 500)->nullable();
            $table->string('site', 255)->nullable();
            $table->string('durasi_pekerjaan_jam', 50)->nullable();
            $table->string('apakah_kegiatan_terekam_menggunakan_cctv', 100)->nullable();
            $table->string('kategori_jenis_ijin_kerja_khusus', 255)->nullable();
            $table->text('semua_personil_yang_terlibat_fit_untuk_melakukan_pekerjaan_ikk')->nullable();
            $table->text('semua_personil_yang_terlibat_pekerjaan_masuk_ikk')->nullable();
            $table->text('pengawas_berkomitmen')->nullable();
            $table->text('semua_personil_yang_terlibat_fit_untuk_melakukan_pekerjaan_2')->nullable();
            $table->text('semua_personil_yang_terlibat_pekerjaan_ikk_2')->nullable();
            $table->text('pengawas_berkomitmen_selalu_berada_dilokasi_pekerjaan_selama_kegiatan_berlangsung_2')->nullable();
            $table->text('semua_personil_yang_terlibat_fit_untuk_melakukan_pekerjaan_3')->nullable();
            $table->text('semua_personil_yang_terlibat_pekerjaan_masuk_dalam_daftar_ijin_kerja_khusus_3')->nullable();
            $table->text('pengawas_berkomitmen_selalu_berada_dilokasi_pekerjaan_selama_kegiatan_berlangsung_3')->nullable();
            $table->text('semua_personil_yang_terlibat_fit_untuk_melakukan_pekerjaan_4')->nullable();
            $table->text('semua_personil_yang_terlibat_pekerjaan_masuk_dalam_daftar_ijin_kerja_khusus_4')->nullable();
            $table->text('pengawas_berkomitmen_selalu_berada_dilokasi_pekerjaan_selama_kegiatan_berlangsung_4')->nullable();
            $table->text('semua_personil_yang_terlibat_fit_untuk_melakukan_pekerjaan_5')->nullable();
            $table->text('semua_personil_yang_terlibat_pekerjaan_masuk_dalam_daftar_ijin_kerja_khusus_5')->nullable();
            $table->text('pengawas_berkomitmen_selalu_berada_dilokasi_pekerjaan_selama_kegiatan_berlangsung_5')->nullable();
            $table->text('semua_personil_yang_terlibat_fit_untuk_melakukan_pekerjaan_6')->nullable();
            $table->text('semua_personil_yang_terlibat_pekerjaan_masuk_dalam_daftar_ijin_kerja_khusus_6')->nullable();
            $table->text('pengawas_berkomitmen_selalu_berada_dilokasi_pekerjaan_selama_kegiatan_berlangsung_6')->nullable();
            // PPE & equipment columns
            $table->string('helm_dilengkapi_dengan_chain_strip_tali_dagu', 100)->nullable();
            $table->string('sarung_tangan_dengan_grip_anti_slip', 100)->nullable();
            $table->string('sepatu_safety_atau_yang_sesuai', 100)->nullable();
            $table->string('rompi_pantul_jika_seragam_tidak_dilengkapi_reflektor', 100)->nullable();
            $table->string('full_body_harness_double_lanyard', 100)->nullable();
            $table->string('tali_lanyard_kondisi_baik_dan_layak', 100)->nullable();
            $table->string('tali_webbing_kondisi_baik_dan_layak', 100)->nullable();
            $table->string('terdapat_anchor_point_yang_sesuai_dan_kuat', 100)->nullable();
            $table->string('double_hook_dalam_kondisi_baik', 100)->nullable();
            $table->string('carabiner_dalam_kondisi_baik', 100)->nullable();
            $table->string('d_rings_dalam_kondisi_baik', 100)->nullable();
            $table->string('helm_dengan_chain_strip_tali_dagu', 100)->nullable();
            $table->string('sarung_tangan_dengan_grip_anti_slip_2', 100)->nullable();
            $table->string('sepatu_safety_atau_yang_sesuai_2', 100)->nullable();
            $table->string('rompi_pantul_jika_seragam_tidak_dilengkapi_reflektor_2', 100)->nullable();
            $table->string('appron_dalam_kondisi_baik_dan_layak_pakai', 100)->nullable();
            $table->string('tersedia_welding_mask_kondisi_baik_dan_layak_pakai', 100)->nullable();
            $table->string('tersedia_face_shield_kondisi_baik_dan_layak_pakai', 100)->nullable();
            $table->string('helm_dengan_chain_strip_tali_dagu_2', 100)->nullable();
            $table->string('sarung_tangan_dengan_grip_anti_slip_3', 100)->nullable();
            $table->string('tersedia_sepatu_safety_atau_yang_sesuai', 100)->nullable();
            $table->string('rompi_pantul_jika_seragam_tidak_dilengkapi_reflektor_3', 100)->nullable();
            $table->string('tersedia_respirator_yang_baik', 100)->nullable();
            $table->string('terdapat_life_line', 100)->nullable();
            $table->string('helm_dengan_chain_strip_tali_dagu_3', 100)->nullable();
            $table->string('sarung_tangan_dengan_grip_anti_slip_4', 100)->nullable();
            $table->string('tersedia_sepatu_safety_atau_yang_sesuai_2', 100)->nullable();
            $table->string('rompi_pantul_jika_seragam_tidak_dilengkapi_reflektor_4', 100)->nullable();
            $table->string('tersedia_life_vest_atau_baju_pelampung', 100)->nullable();
            $table->string('terdapat_kaca_mata_yang_sesuai', 100)->nullable();
            $table->string('helm_dengan_chain_strip_tali_dagu_4', 100)->nullable();
            $table->string('tersedia_sarung_tangan_grip_anti_slip', 100)->nullable();
            $table->string('sepatu_safety_atau_yang_sesuai_3', 100)->nullable();
            $table->string('rompi_pantul_jika_seragam_tidak_dilengkapi_reflektor_5', 100)->nullable();
            $table->string('helm_dengan_chain_strip_tali_dagu_5', 100)->nullable();
            $table->string('sarung_tangan_dengan_grip_anti_slip_5', 100)->nullable();
            $table->string('tersedia_sepatu_safety_atau_yang_sesuai_3', 100)->nullable();
            $table->string('rompi_pantul_jika_seragam_tidak_dilengkapi_reflektor_6', 100)->nullable();
            $table->string('terdapat_anchor_point_yang_berfungsi_dengan_benar', 100)->nullable();
            $table->string('tersedia_radio_atau_alat_komunikasi_hp_yang_berfungsi_dengan_baik', 100)->nullable();
            $table->string('tersedia_rambu_dan_demarkasi_barikade_sesuai_dengan_jenis_pekerjaan', 100)->nullable();
            $table->string('semua_unit_permesinan_memiliki_sko_yang_aktif_dan_dilakukan_pemeriksaan_sebelum_digunakan', 100)->nullable();
            $table->string('semua_support_tools_dilakukan_pemeriksaan_sebelum_digunakan', 100)->nullable();
            $table->string('semua_tabung_dalam_kondisi_berdiri_diikat_dan_terdapat_penutup', 100)->nullable();
            $table->string('tersedia_welding_screen_tabir_las_pada_sekeliling_area_pekerjaan_panas', 100)->nullable();
            $table->string('semua_mesin_las_memiliki_sko_yang_aktif_dan_dilakukan_pemeriksaan_sebelum_digunakan', 100)->nullable();
            $table->string('semua_support_tools_dilakukan_pemeriksaan_sebelum_digunakan_2', 100)->nullable();
            $table->string('tersedia_apar_yang_mudah_dijangkau_dan_dilakukan_pemeriksaan', 100)->nullable();
            $table->string('gas_detektor_sudah_dilakukan_kalibrasi_dan_pemeriksaan_sebelum_digunakan', 100)->nullable();
            $table->string('scba_berfungsi_dengan_baik_dan_dilakukan_pemeriksaan_sebelum_digunakan', 100)->nullable();
            $table->string('tersedia_blower_exhaust_fan', 100)->nullable();
            $table->string('terdapat_thermometer_terkalibrasi_dan_berfungsi_dengan_baik', 100)->nullable();
            $table->string('terdapat_perahu_standar_dilengkapi_dengan_sepasang_dayung', 100)->nullable();
            $table->string('pijakan_menuju_pompa_kondisi_baik', 100)->nullable();
            $table->string('terdapat_akses_jembatan_apung_dilengkapi_dengan_pagar_pengaman_jika_menggunakan', 100)->nullable();
            $table->string('permesinan_peralatan_memiliki_sko_aktif_dan_dilakukan_pemeriksaan_sebelum_digunakan', 100)->nullable();
            $table->string('terdapat_ringbuoy_dilengkapi_tali_minimal_20_meter', 100)->nullable();
            $table->string('semua_support_tools_dilakukan_pemeriksaan_sebelum_digunakan_3', 100)->nullable();
            $table->string('kegiatan_menggunakan_unit_a2b_excavator_dozer_wajib_terdapat_assessment_geoteknik', 100)->nullable();
            $table->string('material_yang_akan_diangkat_angkut_sesuai_dengan_swl_unit', 100)->nullable();
            $table->string('radio_komunikasi_berfungsi_dengan_baik', 100)->nullable();
            $table->string('tersedia_rambu_dan_demarkasi_barikade_sesuai_dengan_jenis_pekerjaan_2', 100)->nullable();
            $table->string('alat_angkat_dan_angkut_memiliki_sko_yang_aktif_dan_dilakukan_pemeriksaan_sebelum_digunakan', 100)->nullable();
            $table->string('semua_alat_bantu_angkat_dilakukan_pemeriksaan_sebelum_digunakan', 100)->nullable();
            $table->string('alat_bantu_kerja_sudah_dilakukan_inspeksi', 100)->nullable();
            $table->string('terdapat_alat_penampung_tumpahan', 100)->nullable();
            $table->string('terdapat_tray_drum_penampung', 100)->nullable();
            $table->string('pekerjaan_terdaftar_dalam_rencana_kerja_harian_dop', 100)->nullable();
            $table->string('pekerjaan_wajib_dilengkapi_dengan_ik_jsa_sesuai_jenis_pekerjaan', 100)->nullable();
            $table->string('ik_jsa_sudah_disosialisasikan_kepada_seluruh_personil', 100)->nullable();
            $table->string('pekerjaan_terdaftar_dalam_rencana_kerja_harian_dop_2', 100)->nullable();
            $table->string('pekerjaan_wajib_dilengkapi_dengan_ik_jsa_sesuai_jenis_pekerjaan_2', 100)->nullable();
            $table->string('ik_jsa_sudah_disosialisasikan_kepada_seluruh_personil_2', 100)->nullable();
            $table->string('pekerjaan_terdaftar_dalam_rencana_kerja_harian_dop_3', 100)->nullable();
            $table->string('pekerjaan_wajib_dilengkapi_dengan_ik_jsa_sesuai_jenis_pekerjaan_3', 100)->nullable();
            $table->string('ik_jsa_sudah_disosialisasikan_kepada_seluruh_personil_3', 100)->nullable();
            $table->string('pekerjaan_terdaftar_dalam_rencana_kerja_harian_dop_4', 100)->nullable();
            $table->string('pekerjaan_wajib_dilengkapi_dengan_ik_jsa_sesuai_jenis_pekerjaan_4', 100)->nullable();
            $table->string('ik_jsa_sudah_disosialisasikan_kepada_seluruh_personil_4', 100)->nullable();
            $table->string('tersedia_lembar_msds_sesuai_b3_yang_digunakan', 100)->nullable();
            $table->string('pekerjaan_terdaftar_dalam_rencana_kerja_harian_dop_5', 100)->nullable();
            $table->string('pekerjaan_wajib_dilengkapi_dengan_ik_jsa_sesuai_jenis_pekerjaan_5', 100)->nullable();
            $table->string('ik_jsa_sudah_disosialisasikan_kepada_seluruh_personil_5', 100)->nullable();
            $table->string('terdapat_rencana_pengangkatan_dan_pengangkutan_yang_sudah_disosialisasikan', 100)->nullable();
            $table->string('pekerjaan_terdaftar_dalam_rencana_kerja_harian_dop_6', 100)->nullable();
            $table->string('pekerjaan_wajib_dilengkapi_dengan_ik_jsa_sesuai_jenis_pekerjaan_6', 100)->nullable();
            $table->string('ik_jsa_sudah_disosialisasikan_kepada_seluruh_personil_6', 100)->nullable();
            $table->string('pengawas_dan_personil_memahami_kondisi_darurat', 100)->nullable();
            $table->string('lokasi_kerja_terjangkau_alat_komunikasi_baik_radio_maupun_handphone', 100)->nullable();
            $table->string('tersedia_apar_dalam_kondisi_baik', 100)->nullable();
            $table->string('tersedia_p3k_minimal_di_unit_kendaraan_pengawas_atau_tempat_berkumpul', 100)->nullable();
            $table->string('tersedia_spill_kit', 100)->nullable();
            $table->string('pengawas_dan_personil_memahami_kondisi_darurat_2', 100)->nullable();
            $table->string('lokasi_kerja_terjangkau_alat_komunikasi_baik_radio_maupun_handphone_2', 100)->nullable();
            $table->string('tersedia_apar_dalam_kondisi_baik_2', 100)->nullable();
            $table->string('tersedia_p3k_minimal_di_unit_kendaraan_pengawas_atau_tempat_berkumpul_2', 100)->nullable();
            $table->string('tersedia_spill_kit_2', 100)->nullable();
            $table->string('pengawas_dan_personil_memahami_kondisi_darurat_3', 100)->nullable();
            $table->string('lokasi_kerja_terjangkau_alat_komunikasi_baik_radio_maupun_handphone_3', 100)->nullable();
            $table->string('tersedia_apar_dalam_kondisi_baik_3', 100)->nullable();
            $table->string('tersedia_p3k_minimal_di_unit_kendaraan_pengawas_atau_tempat_berkumpul_3', 100)->nullable();
            $table->string('tersedia_spill_kit_3', 100)->nullable();
            $table->string('pengawas_dan_personil_memahami_kondisi_darurat_4', 100)->nullable();
            $table->string('lokasi_kerja_terjangkau_alat_komunikasi_baik_radio_maupun_handphone_4', 100)->nullable();
            $table->string('tersedia_apar_dalam_kondisi_baik_4', 100)->nullable();
            $table->string('tersedia_p3k_minimal_di_unit_kendaraan_pengawas_atau_tempat_berkumpul_4', 100)->nullable();
            $table->string('tersedia_spill_kit_4', 100)->nullable();
            $table->string('pengawas_dan_personil_memahami_kondisi_darurat_5', 100)->nullable();
            $table->string('lokasi_kerja_terjangkau_alat_komunikasi_baik_radio_maupun_handphone_5', 100)->nullable();
            $table->string('tersedia_apar_dalam_kondisi_baik_5', 100)->nullable();
            $table->string('tersedia_p3k_minimal_di_unit_kendaraan_pengawas_atau_tempat_berkumpul_5', 100)->nullable();
            $table->string('tersedia_spill_kit_5', 100)->nullable();
            $table->string('pengawas_dan_personil_memahami_kondisi_darurat_6', 100)->nullable();
            $table->string('lokasi_kerja_terjangkau_alat_komunikasi_baik_radio_maupun_handphone_6', 100)->nullable();
            $table->string('tersedia_apar_dalam_kondisi_baik_6', 100)->nullable();
            $table->string('tersedia_p3k_minimal_di_unit_kendaraan_pengawas_atau_tempat_berkumpul_6', 100)->nullable();
            $table->string('tersedia_spill_kit_6', 100)->nullable();
            $table->string('kondisi_cuaca_cerah_tidak_sedang_hujan_gerimis', 100)->nullable();
            $table->string('kondisi_angin_tenang_tidak_kencang', 100)->nullable();
            $table->string('jalur_kerja_bebas_hambatan_rintangan', 100)->nullable();
            $table->string('tidak_terdapat_material_bahan_yang_menghalangi_di_sekitar_area_pekerjaan', 100)->nullable();
            $table->string('penerangan_cukup_jika_dilakukan_pada_malam_hari', 100)->nullable();
            $table->string('kondisi_cuaca_cerah_tidak_sedang_gerimis_atau_hujan', 100)->nullable();
            $table->string('kondisi_angin_tenang_tidak_kencang_2', 100)->nullable();
            $table->string('jalur_area_kerja_bebas_hambatan_rintangan', 100)->nullable();
            $table->string('area_sekitar_tempat_kerja_bebas_dari_material_bahan_yang_menghalangi', 100)->nullable();
            $table->string('penerangan_cukup_jika_dilakukan_pada_malam_hari_2', 100)->nullable();
            $table->string('kondisi_cuaca_cerah_tidak_sedang_gerimis_atau_hujan_2', 100)->nullable();
            $table->string('kondisi_angin_tenang_tidak_kencang_3', 100)->nullable();
            $table->string('jalur_area_kerja_bebas_hambatan_rintangan_2', 100)->nullable();
            $table->string('area_sekitar_tempat_kerja_bebas_dari_material_bahan_yang_menghalangi_2', 100)->nullable();
            $table->string('penerangan_cukup_jika_dilakukan_pada_malam_hari_3', 100)->nullable();
            $table->string('kondisi_cuaca_cerah_tidak_sedang_gerimis_atau_hujan_3', 100)->nullable();
            $table->string('kondisi_angin_tenang_tidak_kencang_4', 100)->nullable();
            $table->string('jalur_area_kerja_bebas_hambatan_rintangan_3', 100)->nullable();
            $table->string('area_sekitar_tempat_kerja_bebas_dari_material_bahan_yang_menghalangi_3', 100)->nullable();
            $table->string('penerangan_cukup_jika_dilakukan_pada_malam_hari_4', 100)->nullable();
            $table->string('kondisi_cuaca_cerah_tidak_sedang_gerimis_atau_hujan_4', 100)->nullable();
            $table->string('kondisi_angin_tenang_tidak_kencang_5', 100)->nullable();
            $table->string('jalur_area_kerja_bebas_hambatan_rintangan_4', 100)->nullable();
            $table->string('area_sekitar_tempat_kerja_bebas_dari_material_bahan_yang_menghalangi_4', 100)->nullable();
            $table->string('penerangan_cukup_jika_dilakukan_pada_malam_hari_5', 100)->nullable();
            $table->string('kondisi_cuaca_cerah_tidak_sedang_gerimis_atau_hujan_5', 100)->nullable();
            $table->string('kondisi_angin_tenang_tidak_kencang_6', 100)->nullable();
            $table->string('jalur_area_kerja_bebas_hambatan_rintangan_5', 100)->nullable();
            $table->string('area_sekitar_tempat_kerja_bebas_dari_material_bahan_yang_menghalangi_5', 100)->nullable();
            $table->string('penerangan_cukup_jika_dilakukan_pada_malam_hari_6', 100)->nullable();
            $table->string('jenis_pengawasan_layer_2_3_4', 255)->nullable();
            $table->string('status_pekerjaan', 100)->nullable();
            $table->text('alasan_pekerjaan_ditunda')->nullable();
            $table->string('evidence_bukti', 500)->nullable();
            $table->string('kategori_jenis_ijin_kerja_khusus_hold', 255)->nullable();
            $table->text('detail_lokasi')->nullable();
            $table->string('nama_pekerjaan', 500)->nullable();
            $table->string('kode_ikk_2', 100)->nullable();
            $table->text('keterangan_pembatalan')->nullable();
            $table->string('l1_id_okk_awal', 100)->nullable();
            $table->string('l1_id_okk_tengah', 100)->nullable();
            $table->string('l1_id_okk_akhir', 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ipk_ikk');
    }
};
