<?php

namespace App\Console\Commands;

use App\Services\SupervisoryAlertService;
use Illuminate\Console\Command;

class UpdateSupervisoryAlertLog extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'supervisory:update-alert-log
                            {--date= : Tanggal (Y-m-d), default hari ini Asia/Makassar}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update log alert Supervisory (layer Pengawasan Berjarak) per area kerja; jadwal pagi/siang/sore';

    public function handle(SupervisoryAlertService $service): int
    {
        $dateOpt = $this->option('date');
        $date = $dateOpt
            ? \Carbon\Carbon::parse($dateOpt, 'Asia/Makassar')
            : now('Asia/Makassar');

        $this->info('Memperbarui supervisory alert log untuk tanggal: ' . $date->format('Y-m-d'));

        $result = $service->updateAlertLogForDate($date);

        $this->info('Area diproses: ' . $result['areas_processed']);
        $this->info('Alert disimpan (HIGH/MEDIUM): ' . $result['saved']);

        if (! empty($result['errors'])) {
            foreach ($result['errors'] as $err) {
                $this->warn('  - ' . $err);
            }
        }

        $this->info('Selesai.');
        return self::SUCCESS;
    }
}
