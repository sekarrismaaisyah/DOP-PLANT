<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\ValidasiTbcImportLog;
use App\Services\PeerPressure\ValidasiTbcImportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ImportValidasiTbcJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /** Satu percobaan: file dihapus setelah run; impor ulang dari UI jika perlu. */
    public int $tries = 1;

    public int $timeout = 3600;

    public function __construct(
        private readonly string $relativePath,
        private readonly int $importLogId
    ) {}

    public function handle(ValidasiTbcImportService $importService): void
    {
        $log = ValidasiTbcImportLog::query()->find($this->importLogId);

        if (! Str::startsWith($this->relativePath, 'imports/validasi-tbc/')) {
            Log::warning('ImportValidasiTbcJob: rejected path', ['path' => $this->relativePath]);
            $this->markLogFailed($log, 'Path file tidak valid.');

            return;
        }

        $fullPath = storage_path('app/' . $this->relativePath);

        if (! is_file($fullPath)) {
            Log::warning('ImportValidasiTbcJob: file not found', ['path' => $fullPath]);
            $this->markLogFailed($log, 'File sementara tidak ditemukan (mungkin sudah diproses atau kedaluwarsa).');

            return;
        }

        try {
            $count = $importService->importFromSpreadsheetPath($fullPath);
            if ($log) {
                $log->update([
                    'status' => 'completed',
                    'rows_imported' => $count,
                    'error_message' => null,
                ]);
            }
            Log::info('ImportValidasiTbcJob selesai', [
                'baris' => $count,
                'file' => $this->relativePath,
                'import_log_id' => $this->importLogId,
            ]);
        } catch (\InvalidArgumentException $e) {
            $this->markLogFailed($log, $e->getMessage());
            Log::warning('ImportValidasiTbcJob: data/template tidak valid — ' . $e->getMessage());
            @unlink($fullPath);
            throw $e;
        } catch (Throwable $e) {
            $this->markLogFailed($log, $e->getMessage());
            Log::error('ImportValidasiTbcJob gagal: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            @unlink($fullPath);
            throw $e;
        }

        @unlink($fullPath);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('ImportValidasiTbcJob failed permanently', [
            'path' => $this->relativePath,
            'import_log_id' => $this->importLogId,
            'message' => $exception?->getMessage(),
        ]);

        $log = ValidasiTbcImportLog::query()->find($this->importLogId);
        if ($log && ! $log->isCompleted()) {
            $msg = $exception?->getMessage() ?? 'Import gagal tanpa pesan detail.';
            $log->update([
                'status' => 'failed',
                'error_message' => mb_substr($msg, 0, 65000),
            ]);
        }

        $fullPath = storage_path('app/' . $this->relativePath);
        if (is_file($fullPath)) {
            @unlink($fullPath);
        }
    }

    private function markLogFailed(?ValidasiTbcImportLog $log, string $message): void
    {
        if (! $log) {
            return;
        }
        if ($log->isCompleted()) {
            return;
        }
        $log->update([
            'status' => 'failed',
            'error_message' => mb_substr($message, 0, 65000),
        ]);
    }
}
