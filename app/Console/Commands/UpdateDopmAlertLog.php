<?php

namespace App\Console\Commands;

use App\Http\Controllers\DOPMIKK\DOPMController;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class UpdateDopmAlertLog extends Command
{
    protected $signature = 'dopm:alert-snapshot
                            {--date= : Tanggal (Y-m-d), default hari ini}';

    protected $description = 'Simpan snapshot alert DOPM (Need Action / Warning) per jam untuk tanggal hari ini; panggil dashboard agar data IKK+matriks terisi lalu simpan ke dopm_alert_log';

    public function handle(): int
    {
        $dateOpt = $this->option('date');
        $date = $dateOpt ? $dateOpt : now()->toDateString();
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error('Format tanggal harus Y-m-d.');
            return self::FAILURE;
        }

        $this->info('Memicu dashboard DOPM untuk tanggal: ' . $date . ' (snapshot per jam akan tersimpan).');

        $request = Request::create(
            '/dopmikk/dopm/dashboard',
            'GET',
            ['date' => $date, 'site' => '']
        );

        try {
            $controller = app(DOPMController::class);
            $controller->dashboard($request);
            $this->info('Selesai. Cek tabel dopm_alert_log untuk jam ' . now()->format('G') . '.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
