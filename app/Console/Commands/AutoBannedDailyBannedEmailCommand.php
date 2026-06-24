<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutoBanned\AutoBannedDailyBannedEmailService;
use Illuminate\Console\Command;

class AutoBannedDailyBannedEmailCommand extends Command
{
    protected $signature = 'auto-banned:daily-banned-email
                            {--date= : Tanggal filter (YYYY-MM-DD)}
                            {--shift= : Shift (contoh: "Shift 1")}
                            {--force : Paksa kirim meski sudah pernah dikirim}';

    protected $description = 'Kirim notifikasi email daily banned dengan ringkasan per Perusahaan & Site + lampiran Excel';

    public function handle(AutoBannedDailyBannedEmailService $emailService): int
    {
        $date = $this->option('date') !== null ? (string) $this->option('date') : null;
        $shift = $this->option('shift') !== null ? (string) $this->option('shift') : null;
        $force = (bool) $this->option('force');

        if (! $emailService->scrTableAvailable()) {
            $this->error('Tabel scr_daily_banned belum tersedia.');

            return self::FAILURE;
        }

        if ($date !== null || $shift !== null) {
            $batch = $emailService->resolveBatch($date, $shift);
            if ($batch === null) {
                $this->warn('Tidak ada data untuk periode yang diminta.');

                return self::SUCCESS;
            }

            $this->info("Periode: {$batch['filter_date']} · {$batch['filter_shift']} · scrape {$batch['scraped_at']}");
            $result = $emailService->sendForBatch($batch, $force);
        } else {
            $result = $emailService->runScheduled($force);
        }

        $this->line("[{$result['action']}] {$result['message']}");

        return $result['action'] === 'error' ? self::FAILURE : self::SUCCESS;
    }
}
