<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Sync Telegram messages every minute (monitoring CCTV alerts)
        // This command has built-in lock mechanism to prevent conflicts
        // $schedule->command('telegram:sync --once')
        //     ->everyMinute()
        //     ->withoutOverlapping()
        //     ->runInBackground()
        //     ->appendOutputTo(storage_path('logs/telegram-sync.log'));

        // Screenshot dashboard DOPM & kirim email 3x sehari: pagi, siang, sore
        $schedule->command('dashboard:screenshot-send --slot=pagi')
            ->dailyAt('09:00')
            ->appendOutputTo(storage_path('logs/dashboard-screenshot.log'));
        $schedule->command('dashboard:screenshot-send --slot=siang')
            ->dailyAt('14:00')
            ->appendOutputTo(storage_path('logs/dashboard-screenshot.log'));
        $schedule->command('dashboard:screenshot-send --slot=sore')
            ->dailyAt('17:00')
            ->appendOutputTo(storage_path('logs/dashboard-screenshot.log'));

        // Supervisory alert log: perbarui data alert 3x sehari (pagi, siang, sore) timezone Asia/Makassar
        $schedule->command('supervisory:update-alert-log')
            ->timezone('Asia/Makassar')
            ->dailyAt('06:00')
            ->appendOutputTo(storage_path('logs/supervisory-alert.log'));
        $schedule->command('supervisory:update-alert-log')
            ->timezone('Asia/Makassar')
            ->dailyAt('12:00')
            ->appendOutputTo(storage_path('logs/supervisory-alert.log'));
        $schedule->command('supervisory:update-alert-log')
            ->timezone('Asia/Makassar')
            ->dailyAt('18:00')
            ->appendOutputTo(storage_path('logs/supervisory-alert.log'));

        // DOPM Alert: snapshot + Alert 1/2/3 per IKK (jam ke-1/2/3 sejak start_date, belum IPK). Setiap 30 menit WITA agar Alert 1 tercatat ~30 menit setelah IKK mulai.
        // $schedule->command('dopm:alert-snapshot')
        //     ->timezone('Asia/Makassar')
        //     ->everyThirtyMinutes()
        //     ->appendOutputTo(storage_path('logs/dopm-alert-snapshot.log'));
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
