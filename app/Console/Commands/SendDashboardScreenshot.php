<?php

namespace App\Console\Commands;

use App\Mail\DashboardScreenshotMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDashboardScreenshot extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:screenshot-send
                            {--slot=pagi : Waktu kirim: pagi, siang, sore}
                            {--url= : Override URL dashboard (optional)}
                            {--no-email : Hanya ambil screenshot, tidak kirim email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Screenshot dashboard DOPM dan kirim ke email (untuk jadwal pagi/siang/sore)';

    public function handle(): int
    {
        if (!class_exists(\Spatie\Browsershot\Browsershot::class)) {
            $this->error('Package spatie/browsershot belum terpasang. Jalankan: composer require spatie/browsershot');
            return self::FAILURE;
        }

        $slot = $this->option('slot');
        $validSlots = ['pagi', 'siang', 'sore'];
        if (!in_array($slot, $validSlots, true)) {
            $this->error("Slot harus salah satu: " . implode(', ', $validSlots));
            return self::FAILURE;
        }

        $url = $this->option('url') ?: config('dashboard_screenshot.url');
        $emails = config('dashboard_screenshot.emails');
        $storagePath = config('dashboard_screenshot.storage_path');
        $timeout = config('dashboard_screenshot.timeout');
        $width = config('dashboard_screenshot.width');
        $height = config('dashboard_screenshot.height');

        if (empty($url)) {
            $this->error('DASHBOARD_SCREENSHOT_URL belum di-set di .env');
            return self::FAILURE;
        }

        if (!is_dir($storagePath)) {
            if (!@mkdir($storagePath, 0755, true)) {
                $this->error('Tidak bisa membuat folder: ' . $storagePath);
                return self::FAILURE;
            }
        }

        $filename = 'dopm-dashboard-' . $slot . '-' . now()->format('Y-m-d-His') . '.png';
        $filepath = rtrim($storagePath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $filename;

        $this->info("Mengambil screenshot dari: {$url}");

        try {
            \Spatie\Browsershot\Browsershot::url($url)
                ->windowSize($width, $height)
                ->timeout($timeout)
                ->waitUntilNetworkIdle()
                ->save($filepath);
        } catch (\Throwable $e) {
            $this->error('Browsershot gagal: ' . $e->getMessage());
            return self::FAILURE;
        }

        if (!is_file($filepath) || filesize($filepath) === 0) {
            $this->error('File screenshot tidak terbentuk atau kosong.');
            @unlink($filepath);
            return self::FAILURE;
        }

        $this->info('Screenshot tersimpan: ' . $filepath);

        if ($this->option('no-email')) {
            $this->info('Opsi --no-email: email tidak dikirim.');
            return self::SUCCESS;
        }

        if (empty($emails)) {
            $this->warn('DASHBOARD_SCREENSHOT_EMAILS kosong di .env. Email tidak dikirim.');
            return self::SUCCESS;
        }

        $slotLabel = ucfirst($slot);
        try {
            foreach ($emails as $email) {
                Mail::to($email)->send(new DashboardScreenshotMail($filepath, $slotLabel));
                $this->info("Email terkirim ke: {$email}");
            }
        } catch (\Throwable $e) {
            $this->error('Kirim email gagal: ' . $e->getMessage());
            @unlink($filepath);
            return self::FAILURE;
        }

        @unlink($filepath);
        $this->info('Selesai.');
        return self::SUCCESS;
    }
}
