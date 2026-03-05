<?php

namespace App\Console\Commands;

use App\Jobs\GeneratePlanningJob;
use App\Models\RosterPlanningJob;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class GenerateRosterPlanning extends Command
{
    protected $signature = 'roster:generate-planning
                            {--start_date= : Tanggal mulai (Y-m-d), default hari ini}
                            {--end_date= : Tanggal selesai (Y-m-d), default hari ini}';

    protected $description = 'Generate planning roster dari DOP & IKK (untuk schedule atau manual).';

    public function handle(): int
    {
        $startDate = $this->option('start_date') ?: now()->toDateString();
        $endDate = $this->option('end_date') ?: now()->toDateString();

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $startDate) || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $endDate)) {
            $this->error('Format tanggal harus Y-m-d.');
            return self::FAILURE;
        }

        if ($startDate > $endDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        $existingJob = RosterPlanningJob::whereIn('status', ['pending', 'processing'])
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->first();

        if ($existingJob) {
            $this->warn("Proses generate untuk periode {$startDate} - {$endDate} sedang berjalan. Dilewati.");
            return self::SUCCESS;
        }

        $jobId = Str::uuid()->toString();

        RosterPlanningJob::create([
            'job_id' => $jobId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'user_id' => null,
        ]);

        GeneratePlanningJob::dispatch($jobId, $startDate, $endDate);

        $this->info("Generate planning untuk {$startDate} - {$endDate} telah didispatch (job_id: {$jobId}).");
        return self::SUCCESS;
    }
}
