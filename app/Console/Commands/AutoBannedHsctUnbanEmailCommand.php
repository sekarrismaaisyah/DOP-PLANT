<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutoBanned\AutoBannedHsctEmailService;
use Illuminate\Console\Command;

class AutoBannedHsctUnbanEmailCommand extends Command
{
    protected $signature = 'auto-banned:hsct-unban-email
                            {--week= : Filter periode minggu ISO (contoh: W25). Kosongkan = semua minggu}
                            {--year= : Filter tahun ISO. Kosongkan = semua tahun}
                            {--dry-run : Tampilkan daftar pengajuan tanpa kirim email}
                            {--force : Kirim ulang termasuk yang sudah pernah dikirim ke HSECT}';

    protected $description = 'Kirim 1 email batch HSECT berisi semua pengajuan unban yang sudah approved';

    public function handle(AutoBannedHsctEmailService $emailService): int
    {
        $recipients = $emailService->recipients();
        if ($recipients === []) {
            $this->error('AUTO_BANNED_HSCT_EMAILS belum dikonfigurasi di .env');

            return self::FAILURE;
        }

        $week = $this->option('week') !== null ? (string) $this->option('week') : null;
        $year = $this->option('year') !== null ? (string) $this->option('year') : null;
        $force = (bool) $this->option('force');

        $this->info('Penerima: '.implode(', ', $recipients));
        $this->line('Mode: 1 email berisi semua pengajuan unban approved (sudah disetujui SOD)'.($force ? ' — force/resend' : ''));

        $requests = $emailService->resolveApprovedUnbanRequestsForHsct($week, $year, $force);

        if ($requests->isEmpty()) {
            $this->warn('Tidak ada pengajuan unban approved yang eligible.');
            $this->line('Syarat: status = approved, hsct_notified_at masih null (belum pernah dikirim ke HSECT).');

            return self::SUCCESS;
        }

        $this->info("Total pengajuan dalam batch: {$requests->count()}");
        $this->table(
            ['ID', 'SID', 'Karyawan', 'Site', 'Periode', 'Diajukan Oleh', 'Sudah Kirim HSCT?'],
            $requests->map(fn ($row) => [
                (string) $row->id,
                $row->sid,
                $row->karyawan ?? '—',
                $row->site_dedicated ?? '—',
                trim(($row->week ?? '').' '.($row->iso_year ?? '')),
                $row->submitted_by_name ?? '—',
                $row->hsct_notified_at ? $row->hsct_notified_at->format('d M Y H:i') : 'Belum',
            ])->all(),
        );

        if ((bool) $this->option('dry-run')) {
            $this->warn('Dry-run — email tidak dikirim.');

            return self::SUCCESS;
        }

        if (! (bool) $this->option('force') && ! $this->confirm("Kirim 1 email batch berisi {$requests->count()} pengajuan unban ke HSECT?", true)) {
            $this->line('Dibatalkan.');

            return self::SUCCESS;
        }

        $result = $emailService->sendUnbanBatchEmail($week, $year, $force);

        $this->line("[{$result['action']}] {$result['message']}");

        return $result['action'] === 'error' ? self::FAILURE : self::SUCCESS;
    }
}
