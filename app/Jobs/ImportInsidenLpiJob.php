<?php

namespace App\Jobs;

use App\Models\InsidenLpi;
use App\Models\InsidenLpiLayer;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportInsidenLpiJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $relativePath;
    protected ?int $insidenCcrId;

    // Mapping kolom Excel ke database (sesuai urutan di Excel)
    protected array $columns = [
        'no_kecelakaan',              // 0
        'kode_be_investigasi',        // 1
        'status_lpi',                 // 2
        'target_penyelesaian_lpi',    // 3
        'actual_penyelesaian_lpi',    // 4
        'ketepatan_waktu_lpi',        // 5
        'tanggal',                    // 6
        'bulan',                      // 7
        'tahun',                      // 8
        'minggu_ke',                  // 9
        'hari',                       // 10
        'jam',                        // 11
        'menit',                      // 12
        'shift',                      // 13
        'perusahaan',                 // 14
        'latitude',                   // 15
        'longitude',                  // 16
        'departemen',                 // 17
        'site',                       // 18
        'lokasi',                     // 19
        'sublokasi',                  // 20
        'lokasi_spesifik',            // 21
        'lokasi_validasi_hsecm',      // 22
        'pja',                        // 23
        'insiden_dalam_site_mining',  // 24
        'kategori',                   // 25
        'injury_status',              // 26
        'kronologis',                 // 27
        'high_potential',             // 28
        'alat_terlibat',              // 29
        'nama',                       // 30
        'jabatan',                    // 31
        'shift_kerja_ke',             // 32
        'hari_kerja_ke',              // 33
        'npk',                        // 34
        'umur',                       // 35
        'range_umur',                 // 36
        'masa_kerja_perusahaan_tahun', // 37
        'masa_kerja_perusahaan_bulan', // 38
        'range_masa_kerja_perusahaan', // 39
        'masa_kerja_bc_tahun',        // 40
        'masa_kerja_bc_bulan',        // 41
        'range_masa_kerja_bc',        // 42
        'bagian_luka',                // 43
        'loss_cost',                  // 44
        'saksi_langsung',             // 45
        'atasan_langsung',            // 46
        'jabatan_atasan_langsung',    // 47
        'kontak',                     // 48
        'detail_kontak',              // 49
        'sumber_kecelakaan',          // 50
        'layer',                      // 51
        'jenis_item_ipls',            // 52
        'detail_layer',               // 53
        'keterangan_layer',           // 54
        'id_lokasi_insiden',          // 55
        'id_pja_insiden',             // 56
    ];

    // Kolom yang merupakan data layer (akan dipisah ke tabel terpisah)
    protected array $layerColumns = [
        'layer',
        'jenis_item_ipls',
        'detail_layer',
        'keterangan_layer',
    ];

    protected array $dateColumns = [
        'target_penyelesaian_lpi',
        'actual_penyelesaian_lpi',
    ];

    protected array $integerColumns = [
        'tanggal',
        'bulan',
        'tahun',
        'minggu_ke',
        'jam',
        'menit',
        'hari_kerja_ke',
        'umur',
        'masa_kerja_perusahaan_tahun',
        'masa_kerja_perusahaan_bulan',
        'masa_kerja_bc_tahun',
        'masa_kerja_bc_bulan',
    ];

    protected array $decimalColumns = [
        'latitude',
        'longitude',
        'loss_cost',
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(string $relativePath, ?int $insidenCcrId = null)
    {
        $this->relativePath = $relativePath;
        $this->insidenCcrId = $insidenCcrId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fullPath = storage_path('app/' . $this->relativePath);

        if (! file_exists($fullPath)) {
            Log::warning('ImportInsidenLpiJob file missing: ' . $fullPath);
            return;
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
        } catch (SpreadsheetException $e) {
            Log::error('ImportInsidenLpiJob unable to read file: ' . $e->getMessage());
            @unlink($fullPath);
            return;
        }

        @unlink($fullPath);

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (count($rows) <= 1) {
            return;
        }

        // Group rows by no_kecelakaan
        $groupedData = [];
        
        foreach ($rows as $index => $row) {
            if ($index === 0) {
                continue; // Skip header row
            }

            // Skip empty rows
            $hasData = false;
            foreach ($row as $cell) {
                if ($cell !== null && trim((string) $cell) !== '') {
                    $hasData = true;
                    break;
                }
            }
            if (!$hasData) {
                continue;
            }

            $payload = [];
            $layerData = [];

            foreach ($this->columns as $colIndex => $columnName) {
                $value = $row[$colIndex] ?? null;
                $normalizedValue = $this->normalizeValue($value, $columnName);
                
                if (in_array($columnName, $this->layerColumns)) {
                    $layerData[$columnName] = $normalizedValue;
                } else {
                    $payload[$columnName] = $normalizedValue;
                }
            }

            $noKecelakaan = $payload['no_kecelakaan'] ?? 'unknown_' . $index;
            
            if (!isset($groupedData[$noKecelakaan])) {
                $groupedData[$noKecelakaan] = [
                    'main' => $payload,
                    'layers' => [],
                ];
            } else {
                // Merge non-empty values from subsequent rows
                foreach ($payload as $key => $value) {
                    if ($value !== null && $value !== '' && empty($groupedData[$noKecelakaan]['main'][$key])) {
                        $groupedData[$noKecelakaan]['main'][$key] = $value;
                    }
                }
            }
            
            // Add layer if it has data
            if (!empty($layerData['layer']) || !empty($layerData['jenis_item_ipls']) || 
                !empty($layerData['detail_layer']) || !empty($layerData['keterangan_layer'])) {
                $groupedData[$noKecelakaan]['layers'][] = $layerData;
            }
        }

        // Insert data
        $insertedCount = 0;
        $layersCount = 0;

        DB::transaction(function () use ($groupedData, &$insertedCount, &$layersCount) {
            foreach ($groupedData as $noKecelakaan => $data) {
                $mainData = $data['main'];
                
                // Add foreign key if provided
                if ($this->insidenCcrId) {
                    $mainData['insiden_ccr_id'] = $this->insidenCcrId;
                }

                $mainData['created_at'] = now();
                $mainData['updated_at'] = now();
                
                $insiden = InsidenLpi::create($mainData);
                $insertedCount++;

                // Insert layers
                foreach ($data['layers'] as $layerData) {
                    $layerData['insiden_lpi_id'] = $insiden->id;
                    $layerData['created_at'] = now();
                    $layerData['updated_at'] = now();
                    InsidenLpiLayer::create($layerData);
                    $layersCount++;
                }
            }
        });

        Log::info("ImportInsidenLpiJob completed. Inserted: {$insertedCount} records with {$layersCount} layers.");
    }

    protected function normalizeValue($value, string $column)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Handle date columns
        if (in_array($column, $this->dateColumns, true)) {
            return $this->parseDate($value);
        }

        // Handle integer columns
        if (in_array($column, $this->integerColumns, true)) {
            $cleaned = preg_replace('/[^0-9\-]/', '', (string) $value);
            return $cleaned !== '' ? (int) $cleaned : null;
        }

        // Handle decimal columns
        if (in_array($column, $this->decimalColumns, true)) {
            // Remove currency symbols and thousands separators
            $cleaned = preg_replace('/[^0-9.\-]/', '', str_replace(',', '.', (string) $value));
            return $cleaned !== '' ? (float) $cleaned : null;
        }

        return trim((string) $value);
    }

    protected function parseDate($value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            // Check if value is numeric (Excel date serial number)
            if (is_numeric($value) && $value > 1) {
                $date = ExcelDate::excelToDateTimeObject($value);
                $year = (int) $date->format('Y');
                if ($year >= 1900 && $year <= 2100) {
                    return $date->format('Y-m-d');
                }
            }

            $stringValue = trim((string) $value);

            // Format: d/m/yyyy or dd/mm/yyyy
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})$/', $stringValue, $matches)) {
                $part1 = (int) $matches[1];
                $part2 = (int) $matches[2];
                $year = (int) $matches[3];

                // Determine format
                if ($part1 > 12) {
                    $day = $part1;
                    $month = $part2;
                } elseif ($part2 > 12) {
                    $day = $part2;
                    $month = $part1;
                } else {
                    // Default to d/m/Y (Indonesian format)
                    $day = $part1;
                    $month = $part2;
                }

                if ($month >= 1 && $month <= 12 && $day >= 1 && $day <= 31) {
                    return sprintf('%04d-%02d-%02d', $year, $month, $day);
                }
            }

            // Try Carbon parse
            return Carbon::parse($stringValue)->format('Y-m-d');
        } catch (\Throwable $e) {
            Log::warning("ImportInsidenLpiJob: Failed to parse date '{$value}'");
            return null;
        }
    }
}
