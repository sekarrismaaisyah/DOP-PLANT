<?php

namespace App\Console\Commands;

use App\Models\DopmAlertIntervensi;
use App\Models\DopmAlertPerIkk;
use App\Services\ClickHouseService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateDopmAlertLog extends Command
{
    protected $signature = 'dopm:alert-snapshot
                            {--date= : Tanggal (Y-m-d), default hari ini WITA}';

    protected $description = 'Simpan snapshot alert DOPM ke dopm_alert_per_ikk. Alert 1/2/3 = jam ke-1/2/3 sejak start_date jika belum ada IPK/OKK lengkap.';

    private const TZ = 'Asia/Makassar';

    public function handle(): int
    {
        $dateOpt = $this->option('date');
        $tz = self::TZ;
        $date = $dateOpt !== null && $dateOpt !== ''
            ? $dateOpt
            : Carbon::now($tz)->toDateString();

        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            $this->error('Format tanggal harus Y-m-d.');
            return self::FAILURE;
        }

        $this->info('[' . Carbon::now($tz)->format('Y-m-d H:i:s') . '] Mulai snapshot alert DOPM untuk tanggal: ' . $date);

        try {
            $ikkList = $this->fetchIkkFromClickHouse($date);

            if (empty($ikkList)) {
                $this->info('Tidak ada IKK (work permit) APPROVED untuk tanggal ' . $date);
                return self::SUCCESS;
            }

            $this->info('Ditemukan ' . count($ikkList) . ' IKK dari ClickHouse.');

            $saved = $this->storeAlerts($ikkList, $date);

            $this->info('Selesai. ' . $saved . ' alert disimpan/diupdate ke dopm_alert_per_ikk.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Error: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return self::FAILURE;
        }
    }

    /**
     * Ambil data IKK (work permit) dari ClickHouse untuk tanggal tertentu.
     */
    private function fetchIkkFromClickHouse(string $date): array
    {
        if (! class_exists(ClickHouseService::class)) {
            $this->warn('ClickHouseService tidak tersedia.');
            return [];
        }

        $clickHouse = app(ClickHouseService::class);

        if (! method_exists($clickHouse, 'query') || ! $clickHouse->isConnected()) {
            $this->warn('ClickHouse tidak terhubung.');
            return [];
        }

        $dateEsc = addslashes($date);

        $sqlWorkPermits = "
            SELECT
                id,
                code,
                name,
                ra_site_name,
                company_name,
                status,
                m_job_id,
                start_date,
                end_date,
                location_name,
                location_detail_name
            FROM hse_automation.ikk_work_permit
            WHERE toDate(start_date) <= toDate('{$dateEsc}')
              AND toDate(end_date)   >= toDate('{$dateEsc}')
              AND status = 'APPROVED'
              AND deleted_at IS NULL
            ORDER BY start_date ASC
        ";

        $wpRows = $clickHouse->query($sqlWorkPermits);

        if (empty($wpRows)) {
            return [];
        }

        $wpIds = array_values(array_unique(array_filter(array_column($wpRows, 'id'))));
        $layersByWp = [];

        if (! empty($wpIds)) {
            $inWpIds = implode(',', array_map(fn ($id) => "'" . addslashes($id) . "'", $wpIds));
            $sqlEmp = "
                SELECT work_permit_id, layer, employee_name, employee_sid
                FROM hse_automation.ikk_work_permit_employee
                WHERE work_permit_id IN ({$inWpIds})
            ";
            $empRows = $clickHouse->query($sqlEmp);

            foreach ($empRows as $er) {
                $wpId = $er['work_permit_id'] ?? null;
                if ($wpId === null || $wpId === '') {
                    continue;
                }
                $layerRaw = $er['layer'] ?? null;
                if ($layerRaw === null || $layerRaw === '') {
                    continue;
                }
                $layerNum = (int) $layerRaw;
                if (! in_array($layerNum, [1, 2, 3, 4], true)) {
                    continue;
                }
                if (! isset($layersByWp[$wpId])) {
                    $layersByWp[$wpId] = [];
                }
                if (! isset($layersByWp[$wpId][$layerNum])) {
                    $layersByWp[$wpId][$layerNum] = [];
                }
                $layersByWp[$wpId][$layerNum][] = [
                    'name' => trim((string) ($er['employee_name'] ?? '')),
                    'sid' => trim((string) ($er['employee_sid'] ?? '')),
                ];
            }
        }

        $jobIds = array_values(array_unique(array_filter(array_column($wpRows, 'm_job_id'))));
        $jobNamesById = [];

        if (! empty($jobIds)) {
            $inJobs = implode(',', array_map(fn ($id) => "'" . addslashes($id) . "'", $jobIds));
            $sqlJobs = "SELECT id, name FROM hse_automation.m_job WHERE id IN ({$inJobs})";
            $jobRows = $clickHouse->query($sqlJobs);
            foreach ($jobRows ?? [] as $jr) {
                $jobId = $jr['id'];
                $jobNamesById[$jobId] = $jr['name'] ?? null;
            }
        }

        $ikkList = [];
        $seenCodes = [];

        foreach ($wpRows as $row) {
            $wpId = $row['id'] ?? null;
            $code = $row['code'] ?? null;

            if ($wpId === null || $code === null || $code === '') {
                continue;
            }

            if (isset($seenCodes[$code])) {
                continue;
            }
            $seenCodes[$code] = true;

            $layers = $layersByWp[$wpId] ?? [];

            $ikkList[] = (object) [
                'id' => $wpId,
                'code' => $code,
                'site' => $row['ra_site_name'] ?? null,
                'jenis_ijin_kerja_khusus' => isset($row['m_job_id']) && $row['m_job_id']
                    ? ($jobNamesById[$row['m_job_id']] ?? null)
                    : null,
                'nama_pekerjaan' => $row['name'] ?? null,
                'perusahaan' => $row['company_name'] ?? null,
                'start_date' => $row['start_date'] ?? null,
                'end_date' => $row['end_date'] ?? null,
                'location_name' => $row['location_name'] ?? null,
                'location_detail_name' => $row['location_detail_name'] ?? null,
                'nama_layer_1' => $this->formatLayerEmployees($layers[1] ?? []),
                'sid_layer_1' => $this->formatLayerSids($layers[1] ?? []),
                'nama_layer_2' => $this->formatLayerEmployees($layers[2] ?? []),
                'sid_layer_2' => $this->formatLayerSids($layers[2] ?? []),
                'nama_layer_3' => $this->formatLayerEmployees($layers[3] ?? []),
                'sid_layer_3' => $this->formatLayerSids($layers[3] ?? []),
                'nama_layer_4' => $this->formatLayerEmployees($layers[4] ?? []),
                'sid_layer_4' => $this->formatLayerSids($layers[4] ?? []),
            ];
        }

        return $ikkList;
    }

    /**
     * Simpan alert ke database.
     * Alert level maksimal 3 (tidak menggunakan durasi).
     * Alert 1 = jam ke-1 sejak start_date, dst.
     */
    private function storeAlerts(array $ikkList, string $tanggal): int
    {
        $tz = self::TZ;
        $now = Carbon::now($tz);
        $jamCek = (int) $now->format('G');
        $saved = 0;

        $maxIntervensiByIkk = DopmAlertIntervensi::getMaxIntervensiLevelByIkk($tanggal);

        foreach ($ikkList as $ikk) {
            $kodeIkk = trim((string) ($ikk->code ?? ''));
            if ($kodeIkk === '') {
                continue;
            }

            $startDateRaw = $ikk->start_date ?? null;
            if ($startDateRaw === null || $startDateRaw === '') {
                continue;
            }

            $startDate = $this->parseDateTime($startDateRaw, $tz);
            if ($startDate === null) {
                continue;
            }
            $startDate = $startDate->setTimezone($tz);

            $endDateRaw = $ikk->end_date ?? null;
            $endDate = $endDateRaw ? $this->parseDateTime($endDateRaw, $tz) : null;
            $endDate = $endDate?->setTimezone($tz);

            $diffMinutes = $startDate->diffInMinutes($now, false);
            if ($diffMinutes < 0) {
                continue;
            }

            $diffHours = $diffMinutes / 60;
            $jamKe = (int) min(3, max(1, floor($diffHours) + 1));

            $alertCheck = DopmAlertPerIkk::checkAlertCondition(
                $kodeIkk,
                $tanggal,
                $ikk->nama_layer_1 ?? null,
                $ikk->nama_layer_2 ?? null,
                $ikk->nama_layer_3 ?? null,
                $ikk->nama_layer_4 ?? null,
                $ikk->sid_layer_1 ?? null,
                $ikk->sid_layer_2 ?? null,
                $ikk->sid_layer_3 ?? null,
                $ikk->sid_layer_4 ?? null
            );

            if (! $alertCheck['should_alert']) {
                continue;
            }

            $snapshot = $this->buildIkkSnapshot($ikk, $startDate, $endDate, $alertCheck['alert_reason']);

            $maxLevel = $jamKe;
            $maxIntervensi = $maxIntervensiByIkk[$kodeIkk] ?? null;
            if ($maxIntervensi !== null) {
                $maxLevel = min($maxLevel, (int) $maxIntervensi);
            }

            for ($level = 1; $level <= $maxLevel; $level++) {
                DopmAlertPerIkk::updateOrCreate(
                    [
                        'tanggal' => $tanggal,
                        'kode_ikk' => $kodeIkk,
                        'alert_level' => $level,
                    ],
                    [
                        'jam_cek' => $jamCek,
                        'ikk_snapshot' => $snapshot,
                    ]
                );
                $saved++;
            }
        }

        return $saved;
    }

    private function buildIkkSnapshot(object $ikk, Carbon $startDate, ?Carbon $endDate, string $alertReason): array
    {
        return [
            'code' => $ikk->code ?? null,
            'start_date_tanggal' => $startDate->format('d/m/Y'),
            'start_date_jam' => $startDate->format('H:i'),
            'end_date_tanggal' => $endDate?->format('d/m/Y'),
            'end_date_jam' => $endDate?->format('H:i'),
            'site' => $ikk->site ?? null,
            'jenis_ijin_kerja_khusus' => $ikk->jenis_ijin_kerja_khusus ?? null,
            'nama_pekerjaan' => $ikk->nama_pekerjaan ?? null,
            'perusahaan' => $ikk->perusahaan ?? null,
            'location_name' => $ikk->location_name ?? null,
            'location_detail_name' => $ikk->location_detail_name ?? null,
            'type' => 'need_action',
            'alert_reason' => $alertReason,
            'max_alert_level' => 3,
            'nama_layer_1' => $ikk->nama_layer_1 ?? null,
            'nama_layer_2' => $ikk->nama_layer_2 ?? null,
            'nama_layer_3' => $ikk->nama_layer_3 ?? null,
            'nama_layer_4' => $ikk->nama_layer_4 ?? null,
            'sid_layer_1' => $ikk->sid_layer_1 ?? null,
            'sid_layer_2' => $ikk->sid_layer_2 ?? null,
            'sid_layer_3' => $ikk->sid_layer_3 ?? null,
            'sid_layer_4' => $ikk->sid_layer_4 ?? null,
        ];
    }

    private function parseDateTime(mixed $value, string $tz): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp((int) $value, $tz);
            }
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->setTimezone($tz);
            }
            return Carbon::parse($value, $tz);
        } catch (\Throwable) {
            return null;
        }
    }

    private function formatLayerEmployees(array $employees): ?string
    {
        if (empty($employees)) {
            return null;
        }
        $parts = [];
        foreach ($employees as $e) {
            $name = trim((string) ($e['name'] ?? ''));
            $sid = trim((string) ($e['sid'] ?? ''));
            $parts[] = $name !== '' ? ($sid !== '' ? $name . ' (' . $sid . ')' : $name) : ($sid !== '' ? $sid : null);
        }
        $parts = array_filter($parts);
        return $parts !== [] ? implode(', ', $parts) : null;
    }

    private function formatLayerSids(array $employees): ?string
    {
        if (empty($employees)) {
            return null;
        }
        $sids = array_filter(array_map(fn ($e) => trim((string) ($e['sid'] ?? '')), $employees));
        return $sids !== [] ? implode(', ', $sids) : null;
    }
}
