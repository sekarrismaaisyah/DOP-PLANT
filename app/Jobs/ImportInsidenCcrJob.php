<?php

namespace App\Jobs;

use App\Models\InsidenCcr;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetException;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportInsidenCcrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected string $relativePath;

    // Mapping kolom Excel ke database (sesuai urutan di Excel)
    protected array $columns = [
        'ccr_id',                        // 0
        'no_kecelakaan',                 // 1
        'ccr_jenis_insiden',             // 2
        'ccr_waktu_pelaporan',           // 3
        'ccr_waktu_insiden',             // 4
        'ccr_kronologi',                 // 5
        'ccr_nama_call_taker',           // 6
        'ccr_perusahaan_call_taker',     // 7
        'ccr_nama_pelapor',              // 8
        'ccr_perusahaan_pelapor',        // 9
        'ccr_lokasi_perusahaan',         // 10
        'ccr_site',                      // 11
        'ccr_lokasi',                    // 12
        'ccr_detil_lokasi',              // 13
        'ccr_keterangan_lokasi',         // 14
        'ccr_status',                    // 15
        'ccr_pic_investigasi',           // 16
        'ccr_pic_investigasi_perusahaan', // 17
        'ket_not_investigasi',           // 18
    ];

    protected array $datetimeColumns = [
        'ccr_waktu_pelaporan',
        'ccr_waktu_insiden',
    ];

    /**
     * Create a new job instance.
     */
    public function __construct(string $relativePath)
    {
        $this->relativePath = $relativePath;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $fullPath = storage_path('app/' . $this->relativePath);

        if (! file_exists($fullPath)) {
            Log::warning('ImportInsidenCcrJob file missing: ' . $fullPath);
            return;
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
        } catch (SpreadsheetException $e) {
            Log::error('ImportInsidenCcrJob unable to read file: ' . $e->getMessage());
            @unlink($fullPath);
            return;
        }

        @unlink($fullPath);

        $sheet = $spreadsheet->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);

        if (count($rows) <= 1) {
            return;
        }

        $batch = [];
        $batchSize = 500;
        $insertedCount = 0;

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

            foreach ($this->columns as $colIndex => $columnName) {
                $value = $row[$colIndex] ?? null;
                $payload[$columnName] = $this->normalizeValue($value, $columnName);
            }

            $payload['created_at'] = now();
            $payload['updated_at'] = now();
            $batch[] = $payload;

            if (count($batch) >= $batchSize) {
                InsidenCcr::insert($batch);
                $insertedCount += count($batch);
                $batch = [];
            }
        }

        if (! empty($batch)) {
            InsidenCcr::insert($batch);
            $insertedCount += count($batch);
        }

        Log::info("ImportInsidenCcrJob completed. Inserted: {$insertedCount} records.");
    }

    protected function normalizeValue($value, string $column)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Handle datetime columns
        if (in_array($column, $this->datetimeColumns, true)) {
            return $this->parseDateTime($value);
        }

        return trim((string) $value);
    }

    protected function parseDateTime($value)
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
                    return $date->format('Y-m-d H:i:s');
                }
            }

            $stringValue = trim((string) $value);

            // Format: dd/mm/yyyy HH:ii (Indonesian format)
            // Example: 31/01/2026 16:36
            if (preg_match('/^(\d{1,2})\/(\d{1,2})\/(\d{4})\s+(\d{1,2}):(\d{2})$/', $stringValue, $matches)) {
                $part1 = (int) $matches[1]; // Could be day or month
                $part2 = (int) $matches[2]; // Could be month or day
                $year = (int) $matches[3];
                $hour = (int) $matches[4];
                $minute = (int) $matches[5];

                // Determine if format is dd/mm/yyyy or mm/dd/yyyy
                // If first part > 12, it must be day (dd/mm/yyyy format)
                if ($part1 > 12) {
                    $day = $part1;
                    $month = $part2;
                } elseif ($part2 > 12) {
                    // If second part > 12, it must be day (mm/dd/yyyy format)
                    $day = $part2;
                    $month = $part1;
                } else {
                    // Both are <= 12, assume Indonesian format dd/mm/yyyy
                    $day = $part1;
                    $month = $part2;
                }

                // Validate date components
                if ($month >= 1 && $month <= 12 && $day >= 1 && $day <= 31) {
                    return sprintf('%04d-%02d-%02d %02d:%02d:00', $year, $month, $day, $hour, $minute);
                }
            }

            // Try parsing format: yyyy-mm-dd HH:ii:ss
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}:\d{2}$/', $stringValue)) {
                return $stringValue;
            }

            // Try parsing format: yyyy-mm-dd HH:ii
            if (preg_match('/^\d{4}-\d{2}-\d{2}\s+\d{2}:\d{2}$/', $stringValue)) {
                return $stringValue . ':00';
            }

            // Try Carbon createFromFormat with explicit format
            $formats = [
                'd/m/Y H:i',
                'd/m/Y H:i:s',
                'm/d/Y H:i',
                'm/d/Y H:i:s',
                'Y-m-d H:i:s',
                'Y-m-d H:i',
            ];

            foreach ($formats as $format) {
                $parsed = Carbon::createFromFormat($format, $stringValue);
                if ($parsed !== false && $parsed instanceof Carbon) {
                    $parsedYear = (int) $parsed->format('Y');
                    if ($parsedYear >= 1900 && $parsedYear <= 2100) {
                        return $parsed->format('Y-m-d H:i:s');
                    }
                }
            }

            // Last fallback
            return Carbon::parse($stringValue)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            Log::warning("ImportInsidenCcrJob: Failed to parse datetime '{$value}': " . $e->getMessage());
            return null;
        }
    }
}
