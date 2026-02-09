<?php

namespace App\Jobs;

use App\Models\IpkIkk;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetException;

class ImportIpkIkkJob implements ShouldQueue
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
        if (!file_exists($fullPath)) {
            Log::warning('ImportIpkIkkJob file not found: ' . $fullPath);
            return;
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
        } catch (SpreadsheetException $e) {
            Log::error('ImportIpkIkkJob spreadsheet: ' . $e->getMessage());
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
                Log::info('ImportIpkIkkJob: No data rows');
                return;
            }

            $header = array_map(function ($c) {
                return trim(str_replace(["\r", "\n"], '', (string) $c));
            }, $rows[0]);

            $allowedColumns = $this->getTableColumns();
            $colIndex = [];
            foreach ($header as $i => $h) {
                $key = $this->headerToColumn($h);
                if ($key !== null && in_array($key, $allowedColumns, true)) {
                    $colIndex[$key] = $i;
                }
            }

            $processedCount = 0;
            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $allowedColumns = $this->getTableColumns();
                $data = [];
                foreach ($colIndex as $column => $i) {
                    if (!in_array($column, $allowedColumns, true)) {
                        continue;
                    }
                    $val = isset($row[$i]) ? trim((string) $row[$i]) : null;
                    if ($val === '') {
                        $val = null;
                    }
                    $data[$column] = $val;
                }

                if (empty($data['kode_ikk'] ?? '') && empty($data['nama_pengawas'] ?? '') && empty($data['nama_pekerjaan'] ?? '')) {
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
                    IpkIkk::create($data);
                    $processedCount++;
                } catch (\Exception $e) {
                    Log::warning('ImportIpkIkkJob row ' . ($index + 1) . ': ' . $e->getMessage());
                }
            }

            Log::info("ImportIpkIkkJob completed. Processed {$processedCount} records.");
        } catch (\Exception $e) {
            Log::error('ImportIpkIkkJob: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            throw $e;
        }
    }

    private function getTableColumns(): array
    {
        $cols = Schema::getColumnListing('ipk_ikk');
        return array_diff($cols, ['id', 'created_at', 'updated_at']);
    }

    /** Map header (or normalized name) to actual DB column (short names). */
    private const HEADER_TO_COLUMN = [
        'timestamp' => 'ts',
        'durasi_pekerjaan_jam' => 'durasi_jam',
        'kategori_jenis_ijin_kerja_khusus' => 'kategori_ijk',
    ];

    /**
     * Map Excel header text to database column name (snake_case, short names).
     */
    private function headerToColumn(?string $h): ?string
    {
        if ($h === null || $h === '') {
            return null;
        }
        $h = preg_replace('/\s+/', ' ', trim($h));
        $snake = str_replace(' ', '_', strtolower($h));
        $snake = preg_replace('/[^a-z0-9_]/', '', $snake);
        if ($snake === '' || $snake === 'id') {
            return null;
        }
        return self::HEADER_TO_COLUMN[$snake] ?? $snake;
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ImportIpkIkkJob failed: ' . $exception->getMessage(), [
            'path' => $this->relativePath,
        ]);
    }
}
