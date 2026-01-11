<?php

namespace App\Console\Commands;

use App\Services\TelegramBotService;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class TelegramStopListen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:stop-listen {--force : Force stop without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Stop all telegram:listen processes and clear conflicts';

    public function handle(): int
    {
        $this->info('🛑 Stopping telegram:listen processes...');
        $this->newLine();

        $stopped = 0;

        // 1. Find and kill telegram:listen processes
        $this->info('1️⃣ Searching for telegram:listen processes...');
        
        if (PHP_OS_FAMILY === 'Windows') {
            $output = [];
            exec('tasklist /FI "IMAGENAME eq php.exe" /FO CSV /NH 2>nul', $output);
            
            $pids = [];
            foreach ($output as $line) {
                $line = trim($line);
                if (empty($line)) continue;
                
                $parts = str_getcsv($line);
                if (count($parts) >= 2) {
                    $pid = $parts[1] ?? '';
                    if (!empty($pid)) {
                        // Get command line to check if it's telegram:listen
                        $cmdOutput = [];
                        exec("wmic process where ProcessId={$pid} get CommandLine 2>nul", $cmdOutput);
                        $cmdLine = implode(' ', $cmdOutput);
                        
                        if (stripos($cmdLine, 'telegram:listen') !== false) {
                            $pids[] = $pid;
                        }
                    }
                }
            }

            if (empty($pids)) {
                $this->comment('   ✓ No telegram:listen processes found');
            } else {
                foreach ($pids as $pid) {
                    $this->line("   Killing process PID: {$pid}...");
                    exec("taskkill /PID {$pid} /F 2>nul", $killOutput, $killReturn);
                    
                    if ($killReturn === 0) {
                        $this->info("   ✓ Process {$pid} stopped");
                        $stopped++;
                    } else {
                        $this->warn("   ✗ Failed to stop process {$pid}");
                    }
                }
            }
        } else {
            // Linux/Mac
            $output = [];
            exec("ps aux | grep 'telegram:listen' | grep -v grep", $output);
            
            $pids = [];
            foreach ($output as $line) {
                if (preg_match('/^\s*\S+\s+(\d+)/', $line, $matches)) {
                    $pids[] = $matches[1];
                }
            }

            if (empty($pids)) {
                $this->comment('   ✓ No telegram:listen processes found');
            } else {
                foreach ($pids as $pid) {
                    $this->line("   Killing process PID: {$pid}...");
                    exec("kill -9 {$pid} 2>&1", $killOutput, $killReturn);
                    
                    if ($killReturn === 0) {
                        $this->info("   ✓ Process {$pid} stopped");
                        $stopped++;
                    } else {
                        $this->warn("   ✗ Failed to stop process {$pid}");
                    }
                }
            }
        }

        $this->newLine();

        // 2. Remove lock files
        $this->info('2️⃣ Cleaning up lock files...');
        $lockFiles = [
            storage_path('framework/schedule-telegram-listen.lock'),
            storage_path('framework/telegram-sync.lock'),
        ];

        foreach ($lockFiles as $lockFile) {
            if (file_exists($lockFile)) {
                @unlink($lockFile);
                $this->info("   ✓ Removed: " . basename($lockFile));
            }
        }

        $this->newLine();

        // 3. Check webhook status
        $this->info('3️⃣ Checking webhook status...');
        try {
            $service = TelegramBotService::makeFromCctvConfig();
        } catch (\RuntimeException $e) {
            try {
                $service = TelegramBotService::makeFromConfig();
            } catch (\RuntimeException $e2) {
                $this->warn('   ⚠️  Could not check webhook (bot token not configured)');
                $this->newLine();
                $this->info("✓ Stopped {$stopped} process(es)");
                $this->comment('Wait 10-15 seconds before running telegram:sync again');
                return self::SUCCESS;
            }
        }

        try {
            $webhookInfo = $service->getWebhookInfo();
            if (Arr::get($webhookInfo, 'ok')) {
                $url = Arr::get($webhookInfo, 'result.url', '');
                if (!empty($url)) {
                    $this->warn('   ⚠️  Webhook is active: ' . $url);
                    $this->comment('   This may cause conflicts. Remove it with: php artisan telegram:webhook remove');
                } else {
                    $this->info('   ✓ No webhook configured');
                }
            }
        } catch (\Throwable $e) {
            $this->warn('   ⚠️  Could not check webhook: ' . $e->getMessage());
        }

        $this->newLine();
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        if ($stopped > 0) {
            $this->info("✓ Successfully stopped {$stopped} process(es)");
        } else {
            $this->info('✓ No telegram:listen processes were running');
        }

        $this->newLine();
        $this->comment('💡 Wait 10-15 seconds for Telegram API to clear the connection');
        $this->comment('   Then try: php artisan telegram:sync --once');

        return self::SUCCESS;
    }
}

