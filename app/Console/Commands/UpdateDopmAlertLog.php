<?php

namespace App\Console\Commands;

use App\Http\Controllers\DOPMIKK\DOPMController;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Http\Request;

class UpdateDopmAlertLog extends Command
{
    protected $signature = 'dopm:alert-snapshot
                            {--date= : Tanggal (Y-m-d), default hari ini WITA}';

    protected $description = 'Simpan snapshot alert DOPM (dopm_alert_log + dopm_alert_per_ikk). IKK dari ClickHouse; Alert 1/2/3 = jam ke-1/2/3 sejak start_date jika belum ada IPK. Panggil dashboard agar data terisi.';

    public function handle(): int
    {
        $dateOpt = $this->option('date');
        $tz = 'Asia/Makassar';
        $date = $dateOpt !== null && $dateOpt !== ''
            ? $dateOpt
            : Carbon::now($tz)->toDateString();
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error('Format tanggal harus Y-m-d.');
            return self::FAILURE;
        }

        $this->info('Memicu dashboard DOPM untuk tanggal: ' . $date . ' (WITA). Snapshot + Alert 1/2/3 per IKK akan tersimpan.');

        $request = Request::create(
            '/dopmikk/dopm/dashboard',
            'GET',
            ['date' => $date, 'site' => '']
        );

        try {
            $controller = app(DOPMController::class);
            $controller->dashboard($request);
            $this->info('Selesai. Cek dopm_alert_log dan dopm_alert_per_ikk.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}
