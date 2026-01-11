<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class TelegramManageWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:webhook 
                            {action=info : Action to perform (info, remove)}
                            {--cctv : Use CCTV bot token instead of default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage Telegram webhook (check info or remove)';

    public function handle(): int
    {
        $action = $this->argument('action');
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

        if ($action === 'remove' || $action === 'delete') {
            return $this->removeWebhook($service);
        } else {
            return $this->showWebhookInfo($service);
        }
    }

    private function showWebhookInfo(TelegramBotService $service): int
    {
        $this->info('📡 Checking webhook information...');
        $this->newLine();

        try {
            $response = $service->getWebhookInfo();

            if (! Arr::get($response, 'ok')) {
                $this->error('✗ Failed to get webhook info: ' . json_encode($response));
                return self::FAILURE;
            }

            $result = Arr::get($response, 'result', []);
            $url = Arr::get($result, 'url', '');
            $hasCustomCertificate = Arr::get($result, 'has_custom_certificate', false);
            $pendingUpdateCount = Arr::get($result, 'pending_update_count', 0);
            $lastErrorDate = Arr::get($result, 'last_error_date');
            $lastErrorMessage = Arr::get($result, 'last_error_message', '');
            $maxConnections = Arr::get($result, 'max_connections');
            $allowedUpdates = Arr::get($result, 'allowed_updates', []);

            if (empty($url)) {
                $this->info('✓ No webhook is configured');
                $this->comment('You can use polling (telegram:listen) without conflicts');
            } else {
                $this->warn('⚠️  Webhook is ACTIVE!');
                $this->newLine();
                $this->line('Webhook URL: ' . $url);
                $this->line('Has Custom Certificate: ' . ($hasCustomCertificate ? 'Yes' : 'No'));
                $this->line('Pending Updates: ' . $pendingUpdateCount);
                
                if ($maxConnections) {
                    $this->line('Max Connections: ' . $maxConnections);
                }
                
                if (! empty($allowedUpdates)) {
                    $this->line('Allowed Updates: ' . implode(', ', $allowedUpdates));
                }

                if ($lastErrorDate) {
                    $this->newLine();
                    $this->error('Last Error Date: ' . date('Y-m-d H:i:s', $lastErrorDate));
                    $this->error('Last Error: ' . $lastErrorMessage);
                }

                $this->newLine();
                $this->warn('⚠️  Having a webhook active will cause conflicts with polling (telegram:listen)');
                $this->info('💡 To remove webhook, run: php artisan telegram:webhook remove');
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    private function removeWebhook(TelegramBotService $service): int
    {
        $this->info('🗑️  Removing webhook...');
        $this->newLine();

        if (! $this->confirm('Are you sure you want to remove the webhook?', true)) {
            $this->info('Cancelled.');
            return self::SUCCESS;
        }

        try {
            $response = $service->deleteWebhook();

            if (! Arr::get($response, 'ok')) {
                $this->error('✗ Failed to remove webhook: ' . json_encode($response));
                return self::FAILURE;
            }

            $this->info('✓ Webhook removed successfully!');
            $this->newLine();
            $this->comment('You can now use polling (telegram:listen) without conflicts');

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

