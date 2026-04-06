<?php

namespace App\Jobs;

use App\Models\DailyOperationPlan;
use App\Models\RosterPlanning;
use App\Models\RosterPlanningJob;
use App\Services\ClickHouseService;
use App\Services\SistemRoster\RosterPlanningDopSyncService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GeneratePlanningJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 3600;

    protected string $jobId;
    protected string $startDate;
    protected string $endDate;

    public function __construct(string $jobId, string $startDate, string $endDate)
    {
        $this->jobId = $jobId;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function handle(): void
    {
        $planningJob = RosterPlanningJob::where('job_id', $this->jobId)->first();
        
        if (!$planningJob) {
            Log::error("GeneratePlanningJob: Job record not found for {$this->jobId}");
            return;
        }

        $planningJob->update([
            'status' => 'processing',
            'started_at' => now(),
        ]);

        Log::info("GeneratePlanningJob: Starting job {$this->jobId} for period {$this->startDate} - {$this->endDate}");

        try {
            $dopResult = $this->generateFromDOP(app(RosterPlanningDopSyncService::class));
            
            $planningJob->update([
                'dop_created' => $dopResult['created'],
                'dop_updated' => $dopResult['updated'],
            ]);

            $ikkResult = $this->generateFromIKK();

            $planningJob->update([
                'status' => 'completed',
                'ikk_created' => $ikkResult['created'],
                'ikk_updated' => $ikkResult['updated'],
                'completed_at' => now(),
            ]);

            Log::info("GeneratePlanningJob: Completed job {$this->jobId} - DOP: {$dopResult['created']} created, {$dopResult['updated']} updated | IKK: {$ikkResult['created']} created, {$ikkResult['updated']} updated");

        } catch (\Throwable $e) {
            $planningJob->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            Log::error("GeneratePlanningJob: Failed job {$this->jobId} - " . $e->getMessage());
            throw $e;
        }
    }

    private function generateFromDOP(RosterPlanningDopSyncService $dopSync): array
    {
        $created = 0;
        $updated = 0;

        $start = Carbon::parse($this->startDate)->startOfDay()->toDateString();
        $end = Carbon::parse($this->endDate)->endOfDay()->toDateString();

        $dopCount = DailyOperationPlan::whereBetween('tanggal', [$start, $end])->count();
        Log::info("GeneratePlanningJob DOP: Found {$dopCount} DOP(s) for period {$start} to {$end}");

        DailyOperationPlan::with(['picBerauCoal', 'pengawasMitraKerja'])
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->orderBy('id')
            ->chunk(100, function ($dops) use (&$created, &$updated, $dopSync) {
                foreach ($dops as $dop) {
                    if ($dopSync->syncFromModel($dop)) {
                        $created++;
                    } else {
                        $updated++;
                    }
                }
            });

        Log::info("GeneratePlanningJob DOP: Loaded period {$start} - {$end}, created={$created}, updated={$updated}");

        return ['created' => $created, 'updated' => $updated];
    }

    private function generateFromIKK(): array
    {
        $created = 0;
        $updated = 0;

        try {
            if (! class_exists(ClickHouseService::class)) {
                Log::warning('GeneratePlanningJob IKK: ClickHouseService class not found');
                return ['created' => 0, 'updated' => 0];
            }

            $clickHouse = app(ClickHouseService::class);
            if (! method_exists($clickHouse, 'query') || ! $clickHouse->isConnected()) {
                Log::warning('GeneratePlanningJob IKK: ClickHouse not connected');
                return ['created' => 0, 'updated' => 0];
            }

            $startDateStr = addslashes($this->startDate);
            $endDateStr = addslashes($this->endDate);
            $filterStart = Carbon::parse($this->startDate)->startOfDay();
            $filterEnd = Carbon::parse($this->endDate)->startOfDay();
            $today = Carbon::today();

            /** WKTT privilege ID: digunakan untuk cek IKK yang sudah di-approve WKTT. */
            $wkttPrivilegeId = '7d872114-0924-4c6a-880e-49b3c06b5429';

            // Preload daftar work_permit_id yang sudah di-approve WKTT (APPROVED/EXPIRED) dan overlap dengan periode generate.
            $wkttApprovedIds = [];
            $sqlWktt = "
                SELECT wp.id
                FROM hse_automation.ikk_work_permit AS wp
                INNER JOIN hse_automation.ikk_work_permit_pic AS wp_pic
                    ON toString(wp_pic.work_permit_id) = toString(wp.id)
                    AND (wp_pic.deleted_at IS NULL OR wp_pic.deleted_at = toDateTime(0))
                LEFT JOIN hse_automation.ikk_m_pic AS m
                    ON toString(m.id) = toString(wp_pic.m_pic_id)
                WHERE (wp.deleted_at IS NULL OR wp.deleted_at = toDateTime(0))
                  AND toDate(wp.start_date) <= toDate('{$endDateStr}')
                  AND toDate(wp.end_date)   >= toDate('{$startDateStr}')
                  AND wp.status IN ('APPROVED', 'EXPIRED')
                GROUP BY wp.id
                HAVING sum(
                    if(
                        upper(trim(toString(wp_pic.status))) = 'APPROVED'
                        AND trim(toString(m.m_privilege_id)) = '{$wkttPrivilegeId}',
                        1,
                        0
                    )
                ) > 0
            ";
            try {
                $wkttRows = $clickHouse->query($sqlWktt);
                foreach ($wkttRows ?? [] as $row) {
                    $id = $this->getVal($row, 'id') ?? $this->getVal($row, 'wp.id');
                    if (is_array($id) && isset($id[0])) {
                        $id = $id[0];
                    }
                    if ($id !== null && $id !== '') {
                        $wkttApprovedIds[(string) $id] = true;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('GeneratePlanningJob IKK: gagal memuat daftar WKTT-approved: ' . $e->getMessage());
                $wkttApprovedIds = [];
            }

            $batchSize = 100;
            $offset = 0;
            $hasMore = true;

            while ($hasMore) {
                $sql = "
                    SELECT
                        id,
                        code,
                        status,
                        company_name,
                        m_job_id,
                        start_date,
                        end_date,
                        ra_site_name,
                        location_name,
                        location_detail_name,
                        location_detail_id
                    FROM hse_automation.ikk_work_permit
                    WHERE (deleted_at IS NULL OR deleted_at = toDateTime(0))
                      AND toDate(start_date) <= toDate('{$endDateStr}')
                      AND toDate(end_date)   >= toDate('{$startDateStr}')
                      AND status IN ('APPROVED', 'EXPIRED')
                    ORDER BY id
                    LIMIT {$batchSize} OFFSET {$offset}
                ";

                $rows = $clickHouse->query($sql);
                
                if (empty($rows) || count($rows) === 0) {
                    $hasMore = false;
                    break;
                }

                Log::info("GeneratePlanningJob IKK: Processing batch offset {$offset}, rows: " . count($rows));

                $jobIds = [];
                $wpIds = [];
                foreach ($rows as $row) {
                    $jobId = $this->getVal($row, 'm_job_id');
                    $wpId = $this->getVal($row, 'id') ?? $this->getVal($row, 'wp.id');
                    if (is_array($wpId) && isset($wpId[0])) {
                        $wpId = $wpId[0];
                    }
                    $wpId = $wpId !== null && $wpId !== '' ? (string) $wpId : null;
                    if ($jobId) $jobIds[$jobId] = true;
                    if ($wpId) $wpIds[$wpId] = true;
                }

                $jobMap = $this->fetchJobNames($clickHouse, array_keys($jobIds));
                $employeeMap = $this->fetchEmployees($clickHouse, array_keys($wpIds));

                unset($jobIds, $wpIds);

                foreach ($rows as $row) {
                    $wpId = $this->getVal($row, 'id') ?? $this->getVal($row, 'wp.id');
                    if (is_array($wpId) && isset($wpId[0])) {
                        $wpId = $wpId[0];
                    }
                    $wpId = $wpId !== null && $wpId !== '' ? (string) $wpId : null;
                    $code = $this->getVal($row, 'code');
                    $statusUpper = null;
                    $statusRaw = $this->getVal($row, 'status');
                    if ($statusRaw !== null && $statusRaw !== '') {
                        $statusUpper = strtoupper(trim((string) $statusRaw));
                    }
                    $startDateIkk = $this->parseDate($this->getVal($row, 'start_date'));
                    $endDateIkk = $this->parseDate($this->getVal($row, 'end_date'));
                    $jobId = $this->getVal($row, 'm_job_id');

                    if (!$startDateIkk || !$wpId) {
                        continue;
                    }

                    $ikkStart = $startDateIkk->startOfDay();
                    $ikkEnd = $endDateIkk ? $endDateIkk->startOfDay() : $ikkStart->copy();

                    $layer1 = $employeeMap[$wpId] ?? null;
                    $companyName = trim($this->getVal($row, 'company_name') ?? '');

                    $currentDate = $filterStart->copy();
                    while ($currentDate->lte($filterEnd)) {
                        // Logika filter:
                        // - Jika tanggal planning < hari ini: harus sudah WKTT-approved (APPROVED/EXPIRED + ada di $wkttApprovedIds)
                        // - Jika tanggal planning >= hari ini: cukup status APPROVED.
                        if ($currentDate->lt($today)) {
                            $allowedStatusesPast = ['APPROVED', 'EXPIRED'];
                            if (! in_array($statusUpper, $allowedStatusesPast, true) || ! isset($wkttApprovedIds[$wpId])) {
                                $currentDate->addDay();
                                continue;
                            }
                        } else {
                            if ($statusUpper !== 'APPROVED') {
                                $currentDate->addDay();
                                continue;
                            }
                        }

                        if ($currentDate->gte($ikkStart) && $currentDate->lte($ikkEnd)) {
                            $result = RosterPlanning::updateOrCreate(
                                [
                                    'source_type' => 'IKK',
                                    'source_id' => (string) $wpId,
                                    'tanggal' => $currentDate->toDateString(),
                                ],
                                [
                                    'no_ikk' => $code,
                                    'site' => $this->getVal($row, 'ra_site_name'),
                                    'aktivitas' => $jobMap[$jobId] ?? null,
                                    'lokasi' => $this->getVal($row, 'location_name'),
                                    'detail_lokasi' => $this->getVal($row, 'location_detail_name'),
                                    'id_detail_lokasi' => $this->getVal($row, 'location_detail_id'),
                                    'pengawas_langsung' => $layer1,
                                    'perusahaan_pic' => $companyName ?: null,
                                    'shift' => null,
                                    'kategori_area' => null,
                                    'jenis_sap' => null,
                                ]
                            );
                            // Hanya set status draft untuk record baru; yang sudah assigned tetap tidak tertimpa
                            if ($result->wasRecentlyCreated) {
                                $result->update(['status' => 'draft']);
                                $created++;
                            } else {
                                $updated++;
                            }
                        }
                        $currentDate->addDay();
                    }
                }

                $rowsCount = count($rows);
                unset($rows, $jobMap, $employeeMap);

                $offset += $batchSize;

                if ($rowsCount < $batchSize) {
                    $hasMore = false;
                }
            }

        } catch (\Throwable $e) {
            Log::error('GeneratePlanningJob IKK Error: ' . $e->getMessage());
        }

        return ['created' => $created, 'updated' => $updated];
    }

    private function fetchJobNames($clickHouse, array $jobIds): array
    {
        if (empty($jobIds)) {
            return [];
        }

        $jobIds = array_slice($jobIds, 0, 500);
        $inJobs = implode(',', array_map(fn($id) => "'" . addslashes((string) $id) . "'", $jobIds));
        $sql = "SELECT id, name FROM hse_automation.ikk_m_job WHERE id IN ({$inJobs})";
        $rows = $clickHouse->query($sql);

        $map = [];
        foreach ($rows ?? [] as $row) {
            $id = $this->getVal($row, 'id');
            $name = $this->getVal($row, 'name');
            if ($id) {
                $map[$id] = $name;
            }
        }
        return $map;
    }

    private function fetchEmployees($clickHouse, array $wpIds): array
    {
        if (empty($wpIds)) {
            return [];
        }

        $wpIds = array_slice($wpIds, 0, 500);
        $inWpIds = implode(',', array_map(fn($id) => "'" . addslashes((string) $id) . "'", $wpIds));
        $sql = "
            SELECT work_permit_id, employee_name
            FROM hse_automation.ikk_work_permit_employee
            WHERE work_permit_id IN ({$inWpIds})
              AND layer = '1'
              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
        ";
        $rows = $clickHouse->query($sql);

        $map = [];
        foreach ($rows ?? [] as $row) {
            $wpId = $this->getVal($row, 'work_permit_id');
            $name = $this->getVal($row, 'employee_name');
            if ($wpId && $name) {
                if (!isset($map[$wpId])) {
                    $map[$wpId] = $name;
                } else {
                    $map[$wpId] .= ', ' . $name;
                }
            }
        }
        return $map;
    }

    private function getVal(array $row, string $key)
    {
        if (!isset($row[$key])) {
            return null;
        }
        $val = $row[$key];
        return is_array($val) && isset($val[0]) ? $val[0] : $val;
    }

    private function parseDate($value): ?Carbon
    {
        if ($value === null || $value === '' || $value === '0000-00-00' || $value === '1970-01-01') {
            return null;
        }
        try {
            return is_numeric($value) ? Carbon::createFromTimestamp((int) $value) : Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('GeneratePlanningJob failed: ' . $exception->getMessage(), [
            'job_id' => $this->jobId,
            'trace' => $exception->getTraceAsString(),
        ]);

        $planningJob = RosterPlanningJob::where('job_id', $this->jobId)->first();
        if ($planningJob) {
            $planningJob->update([
                'status' => 'failed',
                'error_message' => $exception->getMessage(),
                'completed_at' => now(),
            ]);
        }
    }
}
