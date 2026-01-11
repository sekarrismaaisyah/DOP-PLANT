<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class TelegramKillProcesses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:kill-processes {--force : Force kill without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Find and kill running telegram:listen processes to resolve 409 conflicts';

    public function handle(): int
    {
        $this->info('🔍 Searching for telegram:listen processes...');
        $this->newLine();

        $isWindows = strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';

        if ($isWindows) {
            return $this->handleWindows();
        } else {
            return $this->handleUnix();
        }
    }

    private function handleWindows(): int
    {
        // Get all PHP processes
        $processes = [];
        $output = [];
        $returnCode = 0;
        
        exec('tasklist /FI "IMAGENAME eq php.exe" /FO CSV /NH 2>nul', $output, $returnCode);

        if ($returnCode !== 0 || empty($output)) {
            $this->warn('No PHP processes found or unable to check processes.');
            $this->newLine();
            $this->info('You can manually check with: tasklist | findstr php');
            return self::SUCCESS;
        }

        $phpProcesses = [];
        foreach ($output as $line) {
            $line = trim($line);
            if (empty($line)) continue;
            
            // Parse CSV line
            $parts = str_getcsv($line);
            if (count($parts) >= 2) {
                $processName = $parts[0] ?? '';
                $pid = $parts[1] ?? '';
                
                if ($processName === 'php.exe' && !empty($pid)) {
                    $phpProcesses[] = $pid;
                }
            }
        }

        if (empty($phpProcesses)) {
            $this->info('✓ No PHP processes found running.');
            $this->newLine();
            $this->comment('If you still get 409 error, try:');
            $this->line('  1. Wait 10-30 seconds');
            $this->line('  2. Check if another application is using the bot token');
            $this->line('  3. Restart your computer if needed');
            return self::SUCCESS;
        }

        $this->warn('Found ' . count($phpProcesses) . ' PHP process(es):');
        foreach ($phpProcesses as $pid) {
            $this->line('  • PID: ' . $pid);
        }
        $this->newLine();

        $this->warn('⚠️  Note: These might be other Laravel processes (queue, scheduler, etc.)');
        $this->warn('⚠️  Killing them might stop other important processes!');
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to kill all PHP processes? (This might affect other Laravel commands)', false)) {
                $this->info('Cancelled. You can manually kill processes using:');
                $this->line('  taskkill /PID <PID> /F');
                return self::SUCCESS;
            }
        }

        $killed = 0;
        foreach ($phpProcesses as $pid) {
            $this->line("Killing process PID: {$pid}...");
            exec("taskkill /PID {$pid} /F 2>nul", $killOutput, $killReturn);
            
            if ($killReturn === 0) {
                $this->info("  ✓ Process {$pid} killed");
                $killed++;
            } else {
                $this->warn("  ✗ Failed to kill process {$pid} (might need admin rights)");
            }
        }

        $this->newLine();
        if ($killed > 0) {
            $this->info("✓ Killed {$killed} process(es)");
            $this->newLine();
            $this->comment('Wait 5-10 seconds, then try: php artisan telegram:listen --save');
        } else {
            $this->warn('No processes were killed. You might need to run as Administrator.');
            $this->newLine();
            $this->info('Manual steps:');
            $this->line('  1. Open Task Manager (Ctrl+Shift+Esc)');
            $this->line('  2. Find "php.exe" processes');
            $this->line('  3. Right-click and "End Task"');
            $this->line('  4. Or use command: taskkill /PID <PID> /F');
        }

        return self::SUCCESS;
    }

    private function handleUnix(): int
    {
        $output = [];
        exec("ps aux | grep 'telegram:listen' | grep -v grep", $output);

        if (empty($output)) {
            $this->info('✓ No telegram:listen processes found.');
            $this->newLine();
            $this->comment('If you still get 409 error, try:');
            $this->line('  1. Wait 10-30 seconds');
            $this->line('  2. Check if another application is using the bot token');
            return self::SUCCESS;
        }

        $pids = [];
        foreach ($output as $line) {
            if (preg_match('/^\s*\S+\s+(\d+)/', $line, $matches)) {
                $pids[] = $matches[1];
            }
        }

        if (empty($pids)) {
            $this->info('✓ No telegram:listen processes found.');
            return self::SUCCESS;
        }

        $this->warn('Found ' . count($pids) . ' telegram:listen process(es):');
        foreach ($pids as $pid) {
            $this->line('  • PID: ' . $pid);
        }
        $this->newLine();

        if (!$this->option('force')) {
            if (!$this->confirm('Do you want to kill these processes?', true)) {
                $this->info('Cancelled.');
                return self::SUCCESS;
            }
        }

        $killed = 0;
        foreach ($pids as $pid) {
            $this->line("Killing process PID: {$pid}...");
            exec("kill -9 {$pid} 2>&1", $killOutput, $killReturn);
            
            if ($killReturn === 0) {
                $this->info("  ✓ Process {$pid} killed");
                $killed++;
            } else {
                $this->warn("  ✗ Failed to kill process {$pid}");
            }
        }

        $this->newLine();
        if ($killed > 0) {
            $this->info("✓ Killed {$killed} process(es)");
            $this->newLine();
            $this->comment('Wait 5-10 seconds, then try: php artisan telegram:listen --save');
        }

        return self::SUCCESS;
    }
}

