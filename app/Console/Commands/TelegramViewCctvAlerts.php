<?php

namespace App\Console\Commands;

use App\Models\TelegramMessage;
use Illuminate\Console\Command;

class TelegramViewCctvAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'telegram:cctv-alerts 
                            {--limit=10 : Number of alerts to show}
                            {--site= : Filter by site}
                            {--latest : Show only latest alert}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'View parsed CCTV offline alerts from Telegram messages';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $site = $this->option('site');
        $latest = $this->option('latest');

        $this->info('📡 CCTV Offline Alerts');
        $this->newLine();

        $query = TelegramMessage::cctvAlerts()
            ->orderBy('message_date', 'desc');

        if ($latest) {
            $query->limit(1);
        } else {
            $query->limit($limit);
        }

        if ($site) {
            // Filter by site in parsed data
            $alerts = $query->get()->filter(function ($message) use ($site) {
                $data = $message->getCctvAlertData();
                return $data && strtolower($data['site'] ?? '') === strtolower($site);
            });
        } else {
            $alerts = $query->get();
        }

        if ($alerts->isEmpty()) {
            $this->warn('No CCTV alerts found.');
            return self::SUCCESS;
        }

        foreach ($alerts as $message) {
            $data = $message->getCctvAlertData();
            
            if (!$data) {
                continue;
            }

            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->info("📡 Alert ID: {$message->id}");
            $this->line("Site: {$data['site']}");
            $this->line("Alert Date: {$data['alert_date']}");
            $this->line("Offline: {$data['offline_count']} | Online: {$data['online_count']}");
            $this->line("Message Date: " . $message->message_date?->format('Y-m-d H:i:s'));
            $this->newLine();

            if (!empty($data['units'])) {
                $this->line('Units:');
                $this->table(
                    ['Unit Code', 'Unit Name', 'Last Connect'],
                    array_map(function ($unit) {
                        return [
                            $unit['unit_code'],
                            $unit['unit_name'],
                            $unit['last_connect'] ?? '-',
                        ];
                    }, array_slice($data['units'], 0, 10)) // Show first 10 units
                );

                if (count($data['units']) > 10) {
                    $this->comment('... and ' . (count($data['units']) - 10) . ' more units');
                }
            }

            $this->newLine();
        }

        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->info("Total alerts shown: " . $alerts->count());

        return self::SUCCESS;
    }
}

