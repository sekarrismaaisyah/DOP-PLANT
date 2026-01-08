<?php

namespace App\Console\Commands;

use App\Models\TelegramMessage;
use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class TelegramGetHistory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:history 
                            {--from-bot : Get messages sent BY bot}
                            {--to-bot : Get messages sent TO bot}
                            {--chat-id= : Filter by chat ID}
                            {--limit=50 : Maximum messages to display}
                            {--all : Get all messages (both from and to bot)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get message history from database (messages sent by or to bot)';

    public function handle(): int
    {
        $this->info('📜 Telegram Message History');
        $this->newLine();

        try {
            // Get bot info to identify bot messages
            try {
                $service = TelegramBotService::makeFromCctvConfig();
                $botInfo = $service->getMe();
            } catch (\RuntimeException $e) {
                $service = TelegramBotService::makeFromConfig();
                $botInfo = $service->getMe();
            }

            $botId = Arr::get($botInfo, 'result.id');
            $botUsername = Arr::get($botInfo, 'result.username');

            $this->info("🤖 Bot Info: @{$botUsername} (ID: {$botId})");
            $this->newLine();

            // Build query
            $query = TelegramMessage::query();

            // Filter by chat ID if provided
            if ($this->option('chat-id')) {
                $query->where('chat_id', $this->option('chat-id'));
            }

            // Filter by message type
            if ($this->option('from-bot')) {
                $query->where('is_from_bot', true);
                $this->info('📤 Showing messages sent BY bot');
            } elseif ($this->option('to-bot')) {
                $query->where('is_from_bot', false);
                $this->info('📥 Showing messages sent TO bot');
            } elseif ($this->option('all')) {
                $this->info('📨 Showing ALL messages');
            } else {
                // Default: show messages TO bot
                $query->where('is_from_bot', false);
                $this->info('📥 Showing messages sent TO bot (default)');
                $this->comment('Use --from-bot to see messages sent by bot');
                $this->comment('Use --all to see all messages');
            }

            $limit = (int) $this->option('limit');
            $messages = $query->latest('message_date')
                ->limit($limit)
                ->get();

            if ($messages->isEmpty()) {
                $this->warn('No messages found matching the criteria.');
                $this->newLine();
                $this->comment('Note: Bot API can only retrieve messages sent TO the bot.');
                $this->comment('Messages sent BY the bot are only saved if you use sendMessage() with save option.');
                
                return self::SUCCESS;
            }

            $this->newLine();
            $this->info("Found {$messages->count()} message(s):");
            $this->newLine();

            foreach ($messages as $index => $message) {
                $direction = $message->is_from_bot ? '📤 FROM BOT' : '📥 TO BOT';
                
                $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                $this->line("Message #" . ($index + 1) . " - {$direction}");
                $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
                $this->line("Update ID: {$message->update_id}");
                $this->line("Message ID: {$message->message_id}");
                $this->line("Chat ID: {$message->chat_id}");
                $this->line("Chat Type: {$message->chat_type}");
                
                if ($message->is_from_bot) {
                    $this->line("From: Bot (@{$botUsername})");
                } else {
                    $name = trim(($message->first_name ?? '') . ' ' . ($message->last_name ?? ''));
                    $this->line("From: {$name}");
                    $this->line("Username: @" . ($message->username ?? 'N/A'));
                }
                
                $this->line("Text: " . ($message->text ?: '(No text)'));
                
                if ($message->message_date) {
                    $this->line("Date: " . $message->message_date->format('Y-m-d H:i:s'));
                }
                
                $this->newLine();
            }

            $this->info('✓ History displayed successfully!');

            return self::SUCCESS;

        } catch (\Throwable $e) {
            $this->error('✗ Error: ' . $e->getMessage());
            $this->newLine();
            
            return self::FAILURE;
        }
    }
}

