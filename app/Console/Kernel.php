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
        $schedule->command('telegram:sync --once')
            ->everyMinute()
            ->withoutOverlapping()
            ->runInBackground()
            ->appendOutputTo(storage_path('logs/telegram-sync.log'));

        // Screenshot dashboard DOPM & kirim email 3x sehari: pagi, siang, sore
        $schedule->command('dashboard:screenshot-send --slot=pagi')
            ->dailyAt('07:00')
            ->appendOutputTo(storage_path('logs/dashboard-screenshot.log'));
        $schedule->command('dashboard:screenshot-send --slot=siang')
            ->dailyAt('12:00')
            ->appendOutputTo(storage_path('logs/dashboard-screenshot.log'));
        $schedule->command('dashboard:screenshot-send --slot=sore')
            ->dailyAt('17:00')
            ->appendOutputTo(storage_path('logs/dashboard-screenshot.log'));
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
