<?php

namespace App\Jobs;

use App\Models\DopmWeeklyImport;
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

class ImportDopmWeeklyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600;

    protected string $relativePath;

    public function __construct(string $relativePath)
    {
        $this->relativePath = $relativePath;
    }

    public function handle(): void
    {
        $fullPath = storage_path('app/' . $this->relativePath);
        if (! file_exists($fullPath)) {
            Log::warning('ImportDopmWeeklyJob file not found: ' . $fullPath);
            return;
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
        } catch (SpreadsheetException $e) {
            Log::error('ImportDopmWeeklyJob spreadsheet error: ' . $e->getMessage());
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
                Log::info('ImportDopmWeeklyJob: no data rows');
                return;
            }

            $processed = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $data = [
                    'row_no' => $this->toInt($row[0] ?? null),
                    'kode_ikk' => $this->val($row, 1),
                    'tanggal' => $this->parseDate($row[2] ?? null),
                    'site' => $this->val($row, 3),
                    'jenis_ijin_kerja_khusus' => $this->val($row, 4),
                    'nama_pekerjaan' => $this->val($row, 5),
                    'perusahaan' => $this->val($row, 6),
                    'status_wp' => $this->val($row, 7),
                    'pic_approver' => $this->val($row, 8),
                    'nama_layer_1' => $this->val($row, 9),
                    'sid_layer_1' => $this->val($row, 10),
                    'nama_layer_2' => $this->val($row, 11),
                    'sid_layer_2' => $this->val($row, 12),
                    'nama_layer_3' => $this->val($row, 13),
                    'sid_layer_3' => $this->val($row, 14),
                    'nama_layer_4' => $this->val($row, 15),
                    'sid_layer_4' => $this->val($row, 16),
                    'start_date' => $this->parseDate($row[17] ?? null),
                    'end_date' => $this->parseDate($row[18] ?? null),
                    'location' => $this->val($row, 19),
                    'location_detail' => $this->val($row, 20),
                    'ada_ipk' => $this->toBooleanFlag($row[21] ?? null),
                    'kode_ipk' => $this->nullableDash($this->val($row, 22)),
                    'detail_ipk' => $this->nullableDash($this->val($row, 23)),
                    'ada_okk' => $this->toBooleanFlag($row[24] ?? null),
                    'kode_okk' => $this->nullableDash($this->val($row, 25)),
                    'detail_okk' => $this->nullableDash($this->val($row, 26)),
                ];

                if (empty($data['kode_ikk']) && empty($data['nama_pekerjaan'])) {
                    continue;
                }

                try {
                    if (! empty($data['kode_ikk'])) {
                        DopmWeeklyImport::updateOrCreate(
                            ['kode_ikk' => $data['kode_ikk'], 'start_date' => $data['start_date']],
                            $data
                        );
                    } else {
                        DopmWeeklyImport::create($data);
                    }
                    $processed++;
                } catch (\Throwable $e) {
                    Log::warning('ImportDopmWeeklyJob row ' . ($index + 1) . ': ' . $e->getMessage());
                }
            }

            Log::info('ImportDopmWeeklyJob completed.', ['processed' => $processed]);
        } catch (\Throwable $e) {
            Log::error('ImportDopmWeeklyJob failed: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    private function val(array $row, int $index): ?string
    {
        $v = $row[$index] ?? null;
        if ($v === null || $v === '') {
            return null;
        }

        return trim((string) $v);
    }

    private function parseDate($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(ExcelDate::excelToDateTimeObject((float) $value))->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }

        $str = trim((string) $value);
        $formats = ['d/m/Y', 'd-m-Y', 'Y-m-d'];
        foreach ($formats as $format) {
            try {
                return Carbon::createFromFormat($format, $str)->format('Y-m-d');
            } catch (\Throwable $e) {
            }
        }

        try {
            return Carbon::parse($str)->format('Y-m-d');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function toBooleanFlag($value): bool
    {
        $normalized = strtolower(trim((string) ($value ?? '')));
        return in_array($normalized, ['ya', 'y', 'yes', 'true', '1'], true);
    }

    private function toInt($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function nullableDash(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        return trim($value) === '-' ? null : $value;
    }
}
