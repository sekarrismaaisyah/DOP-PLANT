<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class TelegramListen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:listen 
                            {--timeout=50 : Long polling timeout in seconds (max 50)}
                            {--save : Save messages to database}
                            {--limit=100 : Maximum updates to fetch per request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Listen for Telegram messages continuously using long polling';

    private int $lastUpdateId = 0;
    private bool $shouldSave = false;

    public function handle(): int
    {
        $this->info('🤖 Telegram Bot Listener Started');
        $this->newLine();

        $this->shouldSave = $this->option('save');
        $timeout = min((int) $this->option('timeout'), 50); // Max 50 seconds for Telegram API

        try {
            // Use CCTV bot token if available, otherwise fallback to default
            try {
                $service = TelegramBotService::makeFromCctvConfig($timeout + 10); // Add buffer for timeout
                $this->info('✓ Using CCTV Bot Token');
            } catch (\RuntimeException $e) {
                $service = TelegramBotService::makeFromConfig($timeout + 10);
                $this->info('✓ Using Default Bot Token');
            }
        } catch (\RuntimeException $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Please make sure TELEGRAM_BOT_CCTV or TELEGRAM_BOT_TOKEN is set in your .env file');
            
            return self::FAILURE;
        }

        // Get last update ID from database if saving
        if ($this->shouldSave) {
            $this->lastUpdateId = \App\Models\TelegramMessage::max('update_id') ?? 0;
        }

        $this->newLine();
        $this->info("📡 Listening for messages (timeout: {$timeout}s)...");
        $this->info('Press Ctrl+C to stop');
        $this->newLine();

        if ($this->shouldSave) {
            $this->comment('💾 Messages will be saved to database');
        } else {
            $this->comment('ℹ️  Messages will NOT be saved (use --save to enable)');
        }
        $this->newLine();

        // Continuous loop
        while (true) {
            try {
                $this->line('⏳ Waiting for new messages...');
                
                $payload = [
                    'limit' => (int) $this->option('limit'),
                    'timeout' => $timeout,
                ];

                if ($this->lastUpdateId > 0) {
                    $payload['offset'] = $this->lastUpdateId + 1;
                }

                $response = $service->getUpdates($payload);

                if (! Arr::get($response, 'ok')) {
                    $this->error('✗ Telegram API error: ' . json_encode($response));
                    $this->newLine();
                    sleep(5); // Wait 5 seconds before retry
                    continue;
                }

                $updates = Arr::get($response, 'result', []);

                if (! empty($updates)) {
                    $this->newLine();
                    $this->info('📨 Received ' . count($updates) . ' message(s):');
                    $this->newLine();

                    foreach ($updates as $update) {
                        $this->processUpdate($update, $service);
                        $this->lastUpdateId = max($this->lastUpdateId, Arr::get($update, 'update_id', 0));
                    }

                    $this->newLine();
                } else {
                    // No new messages, just continue waiting
                    $this->line('⏳ No new messages, continuing to wait...');
                }

            } catch (\Throwable $e) {
                $this->newLine();
                $this->error('✗ Error: ' . $e->getMessage());
                $this->newLine();
                sleep(5); // Wait 5 seconds before retry
            }
        }

        return self::SUCCESS;
    }

    private function processUpdate(array $update, TelegramBotService $service): void
    {
        $message = Arr::get($update, 'message', []);
        $chat = Arr::get($message, 'chat', []);
        $from = Arr::get($message, 'from', []);

        $updateId = Arr::get($update, 'update_id');
        $messageId = Arr::get($message, 'message_id');
        $chatId = Arr::get($chat, 'id');
        $text = Arr::get($message, 'text', '(No text - might be photo, video, etc.)');
        $fromName = trim(Arr::get($from, 'first_name', '') . ' ' . Arr::get($from, 'last_name', ''));
        $username = Arr::get($from, 'username', 'N/A');

        // Display message
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("📩 Update ID: {$updateId}");
        $this->line("👤 From: {$fromName} (@{$username})");
        $this->line("💬 Text: {$text}");
        
        if (Arr::has($message, 'date')) {
            $date = now()->setTimestamp(Arr::get($message, 'date'));
            $this->line("🕐 Date: " . $date->format('Y-m-d H:i:s'));
        }
        
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Save to database if enabled
        if ($this->shouldSave) {
            try {
                $isFromBot = Arr::get($from, 'is_bot', false);
                
                \App\Models\TelegramMessage::updateOrCreate(
                    ['update_id' => $updateId],
                    [
                        'message_id' => $messageId,
                        'chat_id' => $chatId,
                        'chat_type' => Arr::get($chat, 'type'),
                        'username' => $username !== 'N/A' ? $username : null,
                        'first_name' => Arr::get($from, 'first_name'),
                        'last_name' => Arr::get($from, 'last_name'),
                        'text' => $text !== '(No text - might be photo, video, etc.)' ? $text : null,
                        'is_from_bot' => $isFromBot,
                        'bot_id' => $isFromBot ? Arr::get($from, 'id') : null,
                        'raw_payload' => $update,
                        'message_date' => Arr::has($message, 'date')
                            ? now()->setTimestamp(Arr::get($message, 'date'))
                            : null,
                    ]
                );
                $this->info('💾 Saved to database');
            } catch (\Throwable $e) {
                $this->error('✗ Failed to save: ' . $e->getMessage());
            }
        }
    }
}

