<?php

namespace App\Jobs;

use App\Services\LokasiNonKritisService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class GenerateLokasiNonKritisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 600;

    protected string $tanggal;

    public function __construct(string $tanggal)
    {
        $this->tanggal = $tanggal;
    }

    public function handle(): void
    {
        Log::info("GenerateLokasiNonKritisJob: Starting for tanggal {$this->tanggal}");

        try {
            $service = app(LokasiNonKritisService::class);
            $result = $service->generate($this->tanggal);
            Log::info("GenerateLokasiNonKritisJob: Completed for {$this->tanggal}, kritis={$result['kritis']}, non_kritis={$result['non_kritis']}");
        } catch (\Throwable $e) {
            Log::error('GenerateLokasiNonKritisJob: ' . $e->getMessage(), [
                'tanggal' => $this->tanggal,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GenerateLokasiNonKritisJob failed: ' . $exception->getMessage(), [
            'tanggal' => $this->tanggal,
        ]);
    }
}
