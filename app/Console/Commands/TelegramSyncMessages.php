<?php

namespace App\Console\Commands;

use App\Models\TelegramMessage;
use App\Services\TelegramBotService;
use App\Services\TelegramCctvAlertParser;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class TelegramSyncMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:sync 
                            {--limit=100 : Maximum updates to fetch per request}
                            {--once : Run once and exit (for scheduled tasks)}
                            {--interval=60 : Interval in seconds when running continuously}
                            {--cctv : Use CCTV bot token instead of default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Telegram messages to database (safe for scheduled tasks, no conflicts)';

    private string $lockFile;

    public function handle(): int
    {
        // Create lock file to prevent multiple instances
        $this->lockFile = storage_path('framework/telegram-sync.lock');
        
        // Check if another instance is running
        if (file_exists($this->lockFile)) {
            $lockPid = (int) file_get_contents($this->lockFile);
            
            // Check if process is still running
            if ($this->isProcessRunning($lockPid)) {
                $this->warn('⚠️  Another instance is already running (PID: ' . $lockPid . ')');
                $this->comment('Skipping this run to prevent conflicts...');
                return self::SUCCESS;
            } else {
                // Stale lock file, remove it
                @unlink($this->lockFile);
            }
        }

        // Create lock file
        file_put_contents($this->lockFile, getmypid());

        // Register shutdown function to clean up lock file
        register_shutdown_function(function () {
            if (file_exists($this->lockFile)) {
                @unlink($this->lockFile);
            }
        });

        try {
            $useCctv = $this->option('cctv');
            
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
            
            // Clean up lock file
            if (file_exists($this->lockFile)) {
                @unlink($this->lockFile);
            }
            
            return self::FAILURE;
        }

        $runOnce = $this->option('once');
        $interval = (int) $this->option('interval');

        if ($runOnce) {
            $this->info('🔄 Syncing messages (one-time run)...');
            $this->newLine();
            return $this->syncMessages($service);
        } else {
            $this->info("🔄 Syncing messages continuously (interval: {$interval}s)...");
            $this->info('Press Ctrl+C to stop');
            $this->newLine();

            while (true) {
                $result = $this->syncMessages($service);
                
                if ($result !== self::SUCCESS) {
                    // Clean up lock file on error
                    if (file_exists($this->lockFile)) {
                        @unlink($this->lockFile);
                    }
                    return $result;
                }

                $this->line("⏳ Waiting {$interval} seconds before next sync...");
                sleep($interval);
            }
        }
    }

    private function syncMessages(TelegramBotService $service): int
    {
        try {
            // Get last update ID from database
            $lastUpdateId = TelegramMessage::whereNotNull('update_id')->max('update_id') ?? 0;

            $payload = [
                'limit' => (int) $this->option('limit'),
                'timeout' => 0, // Use 0 timeout (no long polling) to minimize conflicts
            ];

            if ($lastUpdateId > 0) {
                $payload['offset'] = $lastUpdateId + 1;
            }

            // Try to get updates with retry mechanism
            $maxRetries = 3;
            $retryDelay = 5; // seconds
            $response = null;
            $lastError = null;

            for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
                $response = $service->getUpdates($payload);

                if (Arr::get($response, 'ok')) {
                    break; // Success, exit retry loop
                }

                $errorCode = Arr::get($response, 'error_code');
                
                if ($errorCode === 409) {
                    $lastError = '409 Conflict';
                    
                    if ($attempt < $maxRetries) {
                        $this->warn("⚠️  Conflict detected (attempt {$attempt}/{$maxRetries})...");
                        $this->comment("   Waiting {$retryDelay} seconds before retry...");
                        sleep($retryDelay);
                        continue; // Retry
                    } else {
                        $this->warn('⚠️  Conflict detected after ' . $maxRetries . ' attempts');
                        $this->comment('This might be due to:');
                        $this->line('  • Another application using the same bot token');
                        $this->line('  • Telegram API still processing previous request');
                        $this->line('  • Scheduled task running in background');
                        $this->newLine();
                        $this->comment('💡 Solutions:');
                        $this->line('  1. Wait 30-60 seconds and try again');
                        $this->line('  2. Check if schedule:work is running: php artisan schedule:work');
                        $this->line('  3. Check for other applications using this bot token');
                        return self::SUCCESS; // Return success to allow retry later
                    }
                } else {
                    // Other error, don't retry
                    $this->error('✗ Telegram API error: ' . json_encode($response));
                    return self::FAILURE;
                }
            }

            if (! Arr::get($response, 'ok')) {
                $this->error('✗ Failed after ' . $maxRetries . ' attempts: ' . ($lastError ?? 'Unknown error'));
                return self::FAILURE;
            }

            $updates = Arr::get($response, 'result', []);

            if (empty($updates)) {
                $this->comment('✓ No new messages');
                return self::SUCCESS;
            }

            $saved = 0;
            $skipped = 0;

            foreach ($updates as $update) {
                try {
                    $message = Arr::get($update, 'message', []);
                    $chat = Arr::get($message, 'chat', []);
                    $from = Arr::get($message, 'from', []);

                    $updateId = Arr::get($update, 'update_id');
                    $messageId = Arr::get($message, 'message_id');
                    $chatId = Arr::get($chat, 'id');
                    $text = Arr::get($message, 'text');
                    $isFromBot = Arr::get($from, 'is_bot', false);

                    // Skip if update_id is null
                    if (empty($updateId)) {
                        $skipped++;
                        continue;
                    }

                    // Parse CCTV alert if it's a CCTV offline alert message
                    $parsedData = null;
                    if (!empty($text) && TelegramCctvAlertParser::isCctvAlert($text)) {
                        $parsedData = TelegramCctvAlertParser::parse($text);
                        if ($parsedData) {
                            $this->line("📡 CCTV Alert detected: Site={$parsedData['site']}, Offline={$parsedData['offline_count']}, Online={$parsedData['online_count']}, Units=" . count($parsedData['units']));
                            
                            // Add parsed data to raw_payload
                            $update['cctv_alert_parsed'] = $parsedData;
                        }
                    }

                    TelegramMessage::updateOrCreate(
                        ['update_id' => $updateId],
                        [
                            'message_id' => $messageId,
                            'chat_id' => $chatId,
                            'chat_type' => Arr::get($chat, 'type'),
                            'username' => Arr::get($from, 'username'),
                            'first_name' => Arr::get($from, 'first_name'),
                            'last_name' => Arr::get($from, 'last_name'),
                            'text' => $text,
                            'is_from_bot' => $isFromBot,
                            'bot_id' => $isFromBot ? Arr::get($from, 'id') : null,
                            'raw_payload' => $update, // Contains parsed CCTV alert data if applicable
                            'message_date' => Arr::has($message, 'date')
                                ? now()->setTimestamp(Arr::get($message, 'date'))
                                : null,
                        ]
                    );

                    $saved++;
                } catch (\Throwable $e) {
                    $this->warn('Failed to save update ' . Arr::get($update, 'update_id') . ': ' . $e->getMessage());
                    $skipped++;
                }
            }

            if ($saved > 0) {
                $this->info("✓ Synced {$saved} message(s) to database");
            }
            
            if ($skipped > 0) {
                $this->comment("  ({$skipped} skipped)");
            }

            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('✗ Error syncing messages: ' . $e->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * Check if a process is still running
     */
    private function isProcessRunning(int $pid): bool
    {
        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            exec("tasklist /FI \"PID eq {$pid}\" 2>nul", $output);
            return !empty($output) && count($output) > 1;
        } else {
            return posix_kill($pid, 0);
        }
    }
}

