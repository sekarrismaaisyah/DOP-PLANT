<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutoBanned\AutoBannedBanVerifyService;
use Illuminate\Console\Command;

class AutoBannedVerifyBannedCommand extends Command
{
    protected $signature = 'auto-banned:verify-banned
                            {--week= : Minggu ISO (contoh: W24)}
                            {--year= : Tahun ISO}';

    protected $description = 'Verifikasi hourly: cek SID terkirim HSECT sudah terbanned di tabel scrape verifikasi';

    public function handle(AutoBannedBanVerifyService $verifyService): int
    {
        if (! $verifyService->snapshotsTableAvailable()) {
            $this->error('Tabel auto_banned_status_snapshots belum tersedia. Jalankan migration.');

            return self::FAILURE;
        }

        if (! $verifyService->verifyTableAvailable()) {
            $this->warn('Tabel verifikasi belum dikonfigurasi. Set AUTO_BANNED_VERIFY_TABLE di .env');

            return self::SUCCESS;
        }

        $week = $this->option('week') !== null ? (string) $this->option('week') : null;
        $year = $this->option('year') !== null ? (string) $this->option('year') : null;

        $result = $verifyService->verify($week, $year);

        $this->info($result['message']);
        $this->line("Checked: {$result['checked']} | Confirmed: {$result['confirmed']} | Skipped: {$result['skipped']}");

        return self::SUCCESS;
    }
}
