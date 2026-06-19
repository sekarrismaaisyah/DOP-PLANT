<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutoBanned\AutoBannedOverviewService;
use App\Services\AutoBanned\AutoBannedScrapPollService;
use Illuminate\Console\Command;

class AutoBannedPollScrapCommand extends Command
{
    protected $signature = 'auto-banned:poll-scrap
                            {--week= : Filter minggu ISO (contoh: W24)}
                            {--year= : Filter tahun ISO}';

    protected $description = 'Polling data scraping auto banned dan deteksi perubahan status Pass/Not Pass';

    public function handle(
        AutoBannedScrapPollService $pollService,
        AutoBannedOverviewService $overviewService,
    ): int {
        if (! $pollService->scrTableAvailable()) {
            $this->warn('Tabel scr_auto_banned_tbc_sap belum tersedia.');

            return self::SUCCESS;
        }

        if (! $pollService->snapshotsTableAvailable()) {
            $this->error('Tabel auto_banned_status_snapshots belum tersedia. Jalankan migration terlebih dahulu.');

            return self::FAILURE;
        }

        $weekOption = $this->option('week') !== null ? (string) $this->option('week') : '';
        $yearOption = $this->option('year') !== null ? (string) $this->option('year') : '';
        $period = $overviewService->resolvePeriod([
            'week' => $weekOption,
            'year' => $yearOption,
        ]);

        $result = $pollService->poll($period['week'], $period['year']);

        if ($result['skipped'] ?? false) {
            $this->line('[skip] '.($result['message'] ?? 'Poll dilewati.'));

            return self::SUCCESS;
        }

        $this->info(sprintf(
            'Poll selesai (%s %s): %d baris diproses, %d snapshot baru, %d perubahan status.',
            $period['week'],
            $period['year'],
            $result['rows_processed'],
            $result['new_snapshots'],
            $result['status_changes'],
        ));

        return self::SUCCESS;
    }
}
