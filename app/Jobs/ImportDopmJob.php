<?php

namespace App\Jobs;

use App\Models\Dopm;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Exception as SpreadsheetException;

class ImportDopmJob implements ShouldQueue
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
            Log::warning('ImportDopmJob file not found: ' . $fullPath);
            return;
        }

        try {
            $spreadsheet = IOFactory::load($fullPath);
        } catch (SpreadsheetException $e) {
            Log::error('ImportDopmJob spreadsheet error: ' . $e->getMessage());
            @unlink($fullPath);
            throw $e;
        }

        try {
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, false);
            @unlink($fullPath);
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet, $sheet);

            $totalRows = count($rows);
            Log::info("ImportDopmJob: Processing {$totalRows} rows");

            if ($totalRows <= 1) {
                Log::info('ImportDopmJob: No data rows (only header or empty)');
                return;
            }

            $processedCount = 0;
            $batchSize = 200;

            foreach ($rows as $index => $row) {
                if ($index === 0) {
                    continue;
                }

                $timestamp = isset($row[1]) ? $this->parseDate($row[1]) : null;
                $tanggalSelesai = isset($row[6]) ? $this->parseDate($row[6]) : null;
                $tanggalDop = isset($row[8]) ? $this->parseDate($row[8]) : null;

                // Urutan kolom (25 kolom): ID, Timestamp, Site, Perusahaan, Jenis, Kode IKK, Tanggal Selesai Ijin, Nama Pekerjaan, Tanggal DOP, SID Layer 1, Nama Layer 1, Shift, Jam Mulai, Jam Akhir, Status Pengiriman Notif, Status, Deskripsi, SID Layer 2, Nama Layer 2, SID Layer 3, Nama Layer 3, SID Layer 4, Nama Layer 4, Jenis Pengawasan, Detail Lokasi
                $hasNewColumns = count($row) >= 25;
                $data = [
                    'id_dop' => $this->val($row, 0),
                    'timestamp' => $timestamp,
                    'site_ijin_kerja_khusus' => $this->val($row, 2),
                    'perusahaan_ijin_kerja_khusus' => $this->val($row, 3),
                    'jenis_ijin_kerja_khusus' => $this->val($row, 4),
                    'kode_ikk' => $this->val($row, 5),
                    'tanggal_selesai_ijin' => $tanggalSelesai,
                    'nama_pekerjaan' => $this->val($row, 7),
                    'tanggal_dop' => $tanggalDop,
                    'sid_layer_1' => $hasNewColumns ? $this->val($row, 9) : null,
                    'nama_layer_1' => $hasNewColumns ? $this->val($row, 10) : null,
                    'shift' => $hasNewColumns ? $this->val($row, 11) : null,
                    'jam_mulai' => $hasNewColumns ? $this->val($row, 12) : null,
                    'jam_akhir' => $hasNewColumns ? $this->val($row, 13) : null,
                    'status_pengiriman_notif' => $hasNewColumns ? $this->val($row, 14) : $this->val($row, 9),
                    'status' => $hasNewColumns ? $this->val($row, 15) : $this->val($row, 10),
                    'deskripsi_atau_alasan_cancel' => $hasNewColumns ? $this->val($row, 16) : $this->val($row, 11),
                    'sid_layer_2' => $hasNewColumns ? $this->val($row, 17) : $this->val($row, 12),
                    'nama_layer_2' => $hasNewColumns ? $this->val($row, 18) : $this->val($row, 13),
                    'sid_layer_3' => $hasNewColumns ? $this->val($row, 19) : $this->val($row, 14),
                    'nama_layer_3' => $hasNewColumns ? $this->val($row, 20) : $this->val($row, 15),
                    'sid_layer_4' => $hasNewColumns ? $this->val($row, 21) : $this->val($row, 16),
                    'nama_layer_4' => $hasNewColumns ? $this->val($row, 22) : $this->val($row, 17),
                    'jenis_pengawasan_layer' => $hasNewColumns ? $this->val($row, 23) : $this->val($row, 18),
                    'detail_lokasi' => $hasNewColumns ? $this->val($row, 24) : $this->val($row, 19),
                ];

                if (empty($data['id_dop']) && empty($data['kode_ikk']) && empty($data['nama_pekerjaan'])) {
                    continue;
                }

                try {
                    // Jika kode_ikk sudah ada di database, update data; jika belum, buat baru
                    if (!empty($data['kode_ikk'])) {
                        Dopm::updateOrCreate(
                            ['kode_ikk' => $data['kode_ikk']],
                            $data
                        );
                    } else {
                        Dopm::create($data);
                    }
                    $processedCount++;
                } catch (\Exception $e) {
                    Log::warning('ImportDopmJob row ' . ($index + 1) . ': ' . $e->getMessage());
                }
            }

            Log::info("ImportDopmJob completed. Processed {$processedCount} records.");
        } catch (\Exception $e) {
            Log::error('ImportDopmJob error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
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
        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('Y-m-d');
        }
        try {
            return Carbon::parse(trim((string) $value))->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('ImportDopmJob failed: ' . $exception->getMessage(), [
            'path' => $this->relativePath,
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
