<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class TestTelegramFetch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:test {--limit=10 : Maximum updates to fetch per request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test fetching Telegram messages without saving to database (for testing only)';

    public function handle(): int
    {
        $this->info('Testing Telegram Bot Connection...');
        $this->newLine();

        try {
            // Use CCTV bot token if available, otherwise fallback to default
            try {
                $service = TelegramBotService::makeFromCctvConfig();
                $this->info('✓ Using CCTV Bot Token');
            } catch (\RuntimeException $e) {
                $service = TelegramBotService::makeFromConfig();
                $this->info('✓ Using Default Bot Token');
            }
        } catch (\RuntimeException $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            $this->newLine();
            $this->warn('Please make sure TELEGRAM_BOT_CCTV or TELEGRAM_BOT_TOKEN is set in your .env file');
            
            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Fetching updates from Telegram...');
        $this->newLine();

        $payload = [
            'limit' => (int) $this->option('limit'),
        ];

        try {
            $response = $service->getUpdates($payload);

            if (! Arr::get($response, 'ok')) {
                $this->error('✗ Telegram API responded with failure:');
                $this->line(json_encode($response, JSON_PRETTY_PRINT));
                
                return self::FAILURE;
            }

            $updates = Arr::get($response, 'result', []);

            if (empty($updates)) {
                $this->warn('No new messages found. Try sending a message to your bot first.');
                $this->newLine();
                $this->info('Bot is ready and waiting for messages!');
                
                return self::SUCCESS;
            }

            $this->info('✓ Found ' . count($updates) . ' message(s):');
            $this->newLine();

            foreach ($updates as $index => $update) {
                $message = Arr::get($update, 'message', []);
                $chat = Arr::get($message, 'chat', []);
                $from = Arr::get($message, 'from', []);

                $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                $this->line('Message #' . ($index + 1));
                $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                $this->line('Update ID: ' . Arr::get($update, 'update_id'));
                $this->line('Message ID: ' . Arr::get($message, 'message_id'));
                $this->line('Chat ID: ' . Arr::get($chat, 'id'));
                $this->line('Chat Type: ' . Arr::get($chat, 'type'));
                $this->line('From: ' . Arr::get($from, 'first_name') . ' ' . Arr::get($from, 'last_name'));
                $this->line('Username: @' . Arr::get($from, 'username', 'N/A'));
                $this->line('Text: ' . (Arr::get($message, 'text') ?: '(No text - might be photo, video, etc.)'));
                
                if (Arr::has($message, 'date')) {
                    $date = now()->setTimestamp(Arr::get($message, 'date'));
                    $this->line('Date: ' . $date->format('Y-m-d H:i:s'));
                }
                
                $this->newLine();
            }

            $this->info('✓ Test completed successfully!');
            $this->newLine();
            $this->comment('Note: These messages were NOT saved to database.');
            $this->comment('To save messages, use: php artisan telegram:fetch-updates');

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('✗ Error fetching updates: ' . $e->getMessage());
            $this->newLine();
            $this->line('Full error:');
            $this->line($e->getTraceAsString());
            
            return self::FAILURE;
        }
    }
}

