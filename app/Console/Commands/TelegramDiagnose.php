<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class TelegramDiagnose extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:diagnose {--cctv : Use CCTV bot token instead of default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Diagnose Telegram bot connection issues and conflicts';

    public function handle(): int
    {
        $this->info('🔍 Diagnosing Telegram Bot Configuration...');
        $this->newLine();

        $useCctv = $this->option('cctv');

        try {
            // Use CCTV bot token if requested, otherwise fallback to default
            if ($useCctv) {
                $service = TelegramBotService::makeFromCctvConfig();
                $this->info('✓ Using CCTV Bot Token');
            } else {
                try {
                    $service = TelegramBotService::makeFromCctvConfig();
                    $this->info('✓ Using CCTV Bot Token (default)');
                } catch (\RuntimeException $e) {
                    $service = TelegramBotService::makeFromConfig();
                    $this->info('✓ Using Default Bot Token');
                }
            }
        } catch (\RuntimeException $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Please make sure TELEGRAM_BOT_CCTV or TELEGRAM_BOT_TOKEN is set in your .env file');
            
            return self::FAILURE;
        }

        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // 1. Test bot connection
        $this->info('1️⃣ Testing bot connection...');
        try {
            $botInfo = $service->getMe();
            if (Arr::get($botInfo, 'ok')) {
                $result = Arr::get($botInfo, 'result', []);
                $this->info('   ✓ Bot is connected');
                $this->line('   Bot Name: ' . Arr::get($result, 'first_name', 'N/A'));
                $this->line('   Bot Username: @' . Arr::get($result, 'username', 'N/A'));
                $this->line('   Bot ID: ' . Arr::get($result, 'id', 'N/A'));
            } else {
                $this->error('   ✗ Failed to get bot info');
                return self::FAILURE;
            }
        } catch (\Throwable $e) {
            $this->error('   ✗ Connection failed: ' . $e->getMessage());
            return self::FAILURE;
        }

        $this->newLine();

        // 2. Check webhook status
        $this->info('2️⃣ Checking webhook status...');
        try {
            $webhookInfo = $service->getWebhookInfo();
            if (Arr::get($webhookInfo, 'ok')) {
                $result = Arr::get($webhookInfo, 'result', []);
                $url = Arr::get($result, 'url', '');
                
                if (empty($url)) {
                    $this->info('   ✓ No webhook configured (OK for polling)');
                } else {
                    $this->warn('   ⚠️  Webhook is ACTIVE!');
                    $this->line('   Webhook URL: ' . $url);
                    $this->line('   Pending Updates: ' . Arr::get($result, 'pending_update_count', 0));
                    $this->newLine();
                    $this->warn('   ⚠️  This will cause 409 Conflict with polling!');
                    $this->info('   💡 Solution: Run "php artisan telegram:webhook remove"');
                }
            }
        } catch (\Throwable $e) {
            $this->error('   ✗ Failed to check webhook: ' . $e->getMessage());
        }

        $this->newLine();

        // 3. Test getUpdates (this will show if there's a conflict)
        $this->info('3️⃣ Testing getUpdates (this may show conflicts)...');
        try {
            $response = $service->getUpdates([
                'limit' => 1,
                'timeout' => 1, // Short timeout for testing
            ]);

            if (! Arr::get($response, 'ok')) {
                $errorCode = Arr::get($response, 'error_code');
                $errorDescription = Arr::get($response, 'description', '');
                
                if ($errorCode === 409) {
                    $this->error('   ✗ Error 409 Conflict detected!');
                    $this->line('   Description: ' . $errorDescription);
                    $this->newLine();
                    $this->warn('   ⚠️  This means another getUpdates is already running!');
                    $this->newLine();
                    $this->info('   Possible causes:');
                    $this->line('   • Another instance of "telegram:listen" is running');
                    $this->line('   • A webhook is active (see above)');
                    $this->line('   • Another script/application is using this bot token');
                    $this->newLine();
                    $this->info('   Solutions:');
                    $this->line('   1. Check for running processes:');
                    $this->line('      Windows: Get-Process | Where-Object {$_.ProcessName -like "*php*"}');
                    $this->line('      Linux/Mac: ps aux | grep "telegram:listen"');
                    $this->line('   2. If webhook is active, remove it:');
                    $this->line('      php artisan telegram:webhook remove');
                    $this->line('   3. Wait a few seconds and try again');
                } else {
                    $this->error('   ✗ Error: ' . json_encode($response));
                }
            } else {
                $updates = Arr::get($response, 'result', []);
                $this->info('   ✓ getUpdates works! (No conflicts detected)');
                $this->line('   Updates available: ' . count($updates));
            }
        } catch (\Throwable $e) {
            $this->error('   ✗ Failed to test getUpdates: ' . $e->getMessage());
        }

        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        // Summary
        $this->info('📋 Summary:');
        $this->line('• sendMessage (mengirim pesan) TIDAK menyebabkan konflik');
        $this->line('• Hanya getUpdates yang bisa bentrok (multiple instances atau webhook)');
        $this->line('• Jika error 409, kemungkinan ada instance lain atau webhook aktif');
        $this->newLine();

        return self::SUCCESS;
    }
}

