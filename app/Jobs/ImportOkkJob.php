<?php

namespace App\Jobs;

use App\Models\Okk;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetException;

class ImportOkkJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600;

    /** Map header (snake dari Excel) ke nama kolom DB (singkat, max 64 char) */
    private const HEADER_TO_COLUMN = [
        'timestamp' => 'ts',
        'jenis_ijin_kerja_khusus' => 'jenis_ijk',
        'full_body_harness_dikaitkan_sempurna_pada_anchor_poin_yang_kuat' => 'fbh_anchor_kuat',
        'hook_dikaitkan_pada_saat_perpindahan_personil' => 'hook_perpindahan',
        'hook_dikaitkan_pada_saat_pekerjaan_berlangsung' => 'hook_pekerjaan',
        'personil_tidak_ada_potensi_tersengat_aliran_listrik' => 'personil_tidak_tersengat',
        'bangunan_tempat_berpijak_tidak_berpotensi_rebah_baik_seluruh_maupun_sebagian_akibat_kegagalan_komponen_maupun_beban' => 'bangunan_tidak_rebah',
        'semua_barang_bawaan_tidak_ada_potensi_jatuh' => 'barang_bawaan_tidak_jatuh',
        'platform_papan_pijakan_terbuat_dari_material_besi' => 'platform_material_besi',
        'tidak_kotor_dan_licin' => 'tidak_kotor_licin',
        'posisi_pekerja_ergonomis_saat_melakukan_pekerjaaan' => 'posisi_ergonomis',
        'pekerja_tidak_melakukan_aktivitas_lain_selain_pekerjaan' => 'pekerja_fokus',
        'pekerja_menggunakan_full_body_harness_double_lanyard' => 'pekerja_fbh_lanyard',
        'personil_menggunakan_pelampung' => 'personil_pelampung',
        'personil_bekerja_diatas_platform_yang_memadai' => 'personil_platform_memadai',
        'bekerja_pada_pencahayaan_cukup' => 'pencahayaan_cukup',
        'bekerja_pada_kondisi_oksigen_cukup' => 'oksigen_cukup',
        'tidak_terdapat_material_mudah_terbakar' => 'tidak_material_terbakar',
        'sumber_bahaya_lain_dikendalikan' => 'sumber_bahaya_dikendalikan',
        'material_yang_diangkat_diangkut_sesuai_dengan_swl_unit' => 'material_swl',
        'material_yang_diangkat_angkut_dilakukan_pengikatan_sesuai_dengan_metode' => 'material_pengikatan',
        'tidak_terdapat_manusia_yang_berada_dibawah_radius_swing_putar_alat_angkat' => 'tidak_manusia_bawah_swing',
        'unit_angkat_angkut_tidak_berpotensi_rebah_atau_amblas' => 'unit_tidak_rebah',
        'personil_menjalankan_rencana_pengangkatan_yang_sudah_ditetapkan' => 'rencana_pengangkatan',
        'personil_mampu_melakukan_pengendalian_ketika_terjadi_kebakaran' => 'pengendalian_kebakaran',
        'tidak_melakukan_pengelasan_dekat_dengan_bahan_mudah_terbakar' => 'tidak_las_dekat_terbakar',
        'potensi_tersetrum_sudah_dikendalikan' => 'potensi_tersetrum',
        'potensi_bagian_tubuh_terjepit_sudah_dikendalikan' => 'potensi_terjepit',
        'semua_personil_yang_terlibat_terdaftar_dalam_list_ijin_kerja_khusus' => 'personil_terdaftar_ikk',
        'verifikasi_ipk_sesuai_dengan_persyaratan_awal' => 'verifikasi_ipk',
        'personil_menggunakan_apd_dengan_benar' => 'personil_apd_benar',
        'personil_menggunakan_peralatan_dengan_benar' => 'personil_peralatan_benar',
        'personil_melaksanakan_pekerjaan_sesuai_dengan_prosedur_ik_jsa' => 'personil_prosedur_ik_jsa',
        'kondisi_cuaca_cerah_tidak_sedang_gerimis_atau_hujan' => 'cuaca_cerah',
        'kondisi_angin_tenang_tidak_kencang' => 'angin_tenang',
    ];

    protected string $relativePath;

    public function __construct(string $relativePath)
    {
        $this->relativePath = $relativePath;
    }

    public function handle(): void
    {
        $fullPath = storage_path('app/' . $this->relativePath);
        if (!file_exists($fullPath)) {
            Log::warning('ImportOkkJob file not found: ' . $fullPath);
            return;
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
        } catch (SpreadsheetException $e) {
            Log::error('ImportOkkJob spreadsheet: ' . $e->getMessage());
            @unlink($fullPath);
            throw $e;
        }

        try {
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
            @unlink($fullPath);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $sheet);

            if (count($rows) <= 1) {
                Log::info('ImportOkkJob: No data rows');
                return;
            }

            $header = array_map(function ($c) {
                return trim(str_replace(["\r", "\n"], '', (string) $c));
            }, $rows[0]);

            $allowedColumns = $this->getTableColumns();
            $colIndex = [];
            foreach ($header as $i => $h) {
                $key = $this->headerToColumn($h);
                if ($key === null) {
                    continue;
                }
                $dbColumn = self::HEADER_TO_COLUMN[$key] ?? $key;
                if (in_array($dbColumn, $allowedColumns, true)) {
                    $colIndex[$dbColumn] = $i;
                }
            }

            $processedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $data = [];
                foreach ($colIndex as $column => $i) {
                    $val = isset($row[$i]) ? trim((string) $row[$i]) : null;
                    if ($val === '') {
                        $val = null;
                    }
                    $data[$column] = $val;
                }

                if (empty($data['kode_ikk'] ?? '') && empty($data['nama_pengawas'] ?? '')) {
                    continue;
                }

                if (!empty($data['ts'])) {
                    try {
                        $data['ts'] = Carbon::parse($data['ts']);
                    } catch (\Exception $e) {
                        $data['ts'] = null;
                    }
                }

                $data = array_intersect_key($data, array_flip($allowedColumns));

                try {
                    Okk::create($data);
                    $processedCount++;
                } catch (\Exception $e) {
                    Log::warning('ImportOkkJob row ' . ($index + 1) . ': ' . $e->getMessage());
                }
            }

            Log::info("ImportOkkJob completed. Processed {$processedCount} records.");
        } catch (\Exception $e) {
            Log::error('ImportOkkJob: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    private function getTableColumns(): array
    {
        $cols = Schema::getColumnListing('okk');
        return array_diff($cols, ['id', 'created_at', 'updated_at']);
    }

    private function headerToColumn(?string $h): ?string
    {
        if ($h === null || $h === '') {
            return null;
        }
        $h = preg_replace('/\s+/', ' ', trim($h));
        $h = str_replace(['/', '(', ')'], '_', $h);
        $h = preg_replace('/_+/', '_', $h);
        $snake = str_replace(' ', '_', strtolower($h));
        $snake = preg_replace('/[^a-z0-9_]/', '', $snake);
        if ($snake === '' || $snake === 'id') {
            return null;
        }
        return $snake;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ImportOkkJob failed: ' . $exception->getMessage(), [
            'path' => $this->relativePath,
        ]);
    }
}
