<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutoBanned\AutoBannedScrapPollService;
use Illuminate\Console\Command;

class AutoBannedPollScrapCommand extends Command
{
    protected $signature = 'auto-banned:poll-scrap
                            {--week= : Filter minggu ISO (contoh: W24)}
                            {--year= : Filter tahun ISO}';

    protected $description = 'Polling data scraping auto banned dan deteksi perubahan status Pass/Not Pass';

    public function handle(AutoBannedScrapPollService $pollService): int
    {
        if (! $pollService->scrTableAvailable()) {
            $this->warn('Tabel scr_auto_banned_tbc_sap belum tersedia.');

            return self::SUCCESS;
        }

        if (! $pollService->snapshotsTableAvailable()) {
            $this->error('Tabel auto_banned_status_snapshots belum tersedia. Jalankan migration terlebih dahulu.');

            return self::FAILURE;
        }

        $week = $this->option('week') !== null ? (string) $this->option('week') : null;
        $year = $this->option('year') !== null ? (string) $this->option('year') : null;

        $result = $pollService->poll($week, $year);

        $this->info(sprintf(
            'Poll selesai: %d baris diproses, %d snapshot baru, %d perubahan status.',
            $result['rows_processed'],
            $result['new_snapshots'],
            $result['status_changes'],
        ));

        return self::SUCCESS;
    }
}
