<?php

namespace App\Jobs;

use App\Mail\PlanningSummaryMail;
use App\Models\RosterPlanning;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendPlanningSummaryEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    /**
     * @param array<int, string> $emails
     */
    public function __construct(
        public array $emails,
        public string $tanggal,
        public string $rosterTable,
        public string $siteLabel
    ) {}

    public function handle(): void
    {
        $plannings = RosterPlanning::query()
            ->with('karyawans')
            ->where('tanggal', $this->tanggal)
            ->where('source_type', 'Roster')
            ->where('source_id', 'like', $this->rosterTable . '_%')
            ->orderBy('lokasi')
            ->orderBy('detail_lokasi')
            ->get();

        $summary = $plannings->map(function (RosterPlanning $p) {
            return [
                'lokasi' => $p->lokasi ?? '-',
                'detail_lokasi' => $p->detail_lokasi ?? '-',
                'aktivitas' => $p->aktivitas ?? '-',
                'karyawans' => $p->karyawans->pluck('nama_karyawan')->values()->all(),
            ];
        })->values()->all();

        foreach ($this->emails as $email) {
            try {
                Mail::to($email)->send(new PlanningSummaryMail(
                    $summary,
                    $this->tanggal,
                    $this->siteLabel
                ));
            } catch (\Throwable $e) {
                Log::warning('SendPlanningSummaryEmailJob: Failed to send to ' . $email . ' - ' . $e->getMessage());
            }
        }
    }
}
