<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutoBanned\AutoBannedHsctEmailService;
use Illuminate\Console\Command;

class AutoBannedHsctEmailCommand extends Command
{
    protected $signature = 'auto-banned:hsct-email
                            {--week= : Minggu ISO (contoh: W24)}
                            {--year= : Tahun ISO}
                            {--type=auto : auto|initial|reminder}
                            {--force : Paksa kirim meski sudah dikirim hari ini}';

    protected $description = 'Kirim email HSECT: awal (Selasa) list semua banned, reminder harian untuk yang belum banned';

    public function handle(AutoBannedHsctEmailService $emailService): int
    {
        $week = $this->option('week') !== null ? (string) $this->option('week') : null;
        $year = $this->option('year') !== null ? (string) $this->option('year') : null;
        $type = (string) $this->option('type');
        $force = (bool) $this->option('force');

        if (! $emailService->tablesAvailable()) {
            $this->error('Tabel campaign email belum tersedia. Jalankan migration.');

            return self::FAILURE;
        }

        $period = $emailService->resolvePeriod($week, $year);
        $this->info("Periode: {$period['week']} {$period['year']}");

        $result = match ($type) {
            'initial' => $emailService->sendInitialEmail($period['week'], $period['year'], $force),
            'reminder' => $this->runReminder($emailService, $period['week'], $period['year'], $force),
            default => $emailService->runScheduled($period['week'], $period['year'], $force),
        };

        $this->line("[{$result['action']}] {$result['message']}");

        return $result['action'] === 'error' ? self::FAILURE : self::SUCCESS;
    }

    private function runReminder(AutoBannedHsctEmailService $emailService, string $week, string $year, bool $force): array
    {
        $campaign = $emailService->findCampaign($week, $year);
        if ($campaign === null) {
            return ['action' => 'error', 'message' => 'Campaign belum ada. Kirim email awal dulu.', 'sent' => false];
        }

        return $emailService->sendReminderEmail($campaign, $force);
    }
}
