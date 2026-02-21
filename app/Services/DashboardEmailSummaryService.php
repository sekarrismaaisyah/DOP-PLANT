<?php

namespace App\Services;

use App\Http\Controllers\DOPMIKK\DOPMController;
use App\Models\Dopm;
use App\Models\IpkIkk;
use App\Models\Okk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Mengambil ringkasan metrik dashboard DOPM/IKK untuk keperluan email daily report.
 * Mengutamakan data dari ClickHouse (IKK = ikk_work_permit, IPK = ipk_assessment, OKK = okk_assessment, OAK = aaj_vw_car_oak_register_ytd_only).
 * Fallback ke MySQL (Dopm, IpkIkk, Okk) bila ClickHouse tidak tersedia atau tidak ada data.
 */
class DashboardEmailSummaryService
{
    public function getSummaryForDate(string $filterDate): array
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
            $filterDate = now()->toDateString();
        }

        $mingguStart = Carbon::parse($filterDate)->startOfWeek(Carbon::MONDAY);
        $mingguEnd = $mingguStart->copy()->addDays(6)->endOfDay();

        $fromClickHouse = $this->fetchSummaryFromClickHouse($filterDate, $mingguStart, $mingguEnd);
        if ($fromClickHouse !== null) {
            return $fromClickHouse;
        }

        return $this->fetchSummaryFromMySQL($filterDate, $mingguStart, $mingguEnd);
    }

    /**
     * Ringkasan dari ClickHouse: IKK (ikk_work_permit), IPK (ipk_assessment), OKK (okk_assessment), OAK.
     * Return null jika ClickHouse tidak dipakai (tidak connected atau tidak ada data IKK).
     */
    private function fetchSummaryFromClickHouse(string $filterDate, Carbon $mingguStart, Carbon $mingguEnd): ?array
    {
        try {
            if (!class_exists(ClickHouseService::class)) {
                return null;
            }
            $ch = app(ClickHouseService::class);
            if (!method_exists($ch, 'query') || !$ch->isConnected()) {
                return null;
            }

            $dateStr = addslashes($filterDate);
            $dateEsc = addslashes($filterDate);
            $mingguStartStr = $mingguStart->format('Y-m-d');
            $mingguEndStr = $mingguEnd->format('Y-m-d');

            // 1) IKK (work permit) harian: query sama seperti dashboard (date range + APPROVED)
            //    lalu filter sampai jam: start_date <= now < end_date (sama seperti dashboard)
            $sqlWp = "
                SELECT id, code, ra_site_name, start_date, end_date, status
                FROM hse_automation.ikk_work_permit
                WHERE toDate(start_date) <= toDate('{$dateStr}')
                  AND toDate(end_date)   >= toDate('{$dateStr}')
                  AND deleted_at IS NULL
                ORDER BY start_date ASC
            ";
            $wpRows = $ch->query($sqlWp);
            if (empty($wpRows)) {
                return null;
            }

            $now = Carbon::now(config('app.timezone'));
            $byCode = [];
            foreach ($wpRows as $row) {
                $rawStatus = $this->chVal($row, 'status');
                $statusUpper = $rawStatus !== null ? strtoupper(trim((string) $rawStatus)) : null;
                if ($statusUpper === 'DRAFT') {
                    continue;
                }
                if (in_array($statusUpper, ['EXPIRED', 'PENDING', 'REJECTED'], true)) {
                    continue;
                }
                $startDateRaw = $this->chVal($row, 'start_date');
                if ($startDateRaw !== null && $startDateRaw !== '') {
                    $startDate = $this->parseClickHouseDate($startDateRaw);
                    if ($startDate !== null && $startDate->gt($now)) {
                        continue;
                    }
                }
                $endDateRaw = $this->chVal($row, 'end_date');
                if ($endDateRaw !== null && $endDateRaw !== '') {
                    $endDate = $this->parseClickHouseDate($endDateRaw);
                    if ($endDate === null || $endDate->lte($now)) {
                        continue;
                    }
                }
                if ($statusUpper !== 'APPROVED') {
                    continue;
                }
                $code = $this->chVal($row, 'code');
                if ($code === null || $code === '') {
                    continue;
                }
                $code = is_object($code) ? (string) $code : $code;
                if (!isset($byCode[$code])) {
                    $byCode[$code] = [
                        'id' => $this->chVal($row, 'id'),
                        'code' => $code,
                        'site' => trim((string) ($this->chVal($row, 'ra_site_name') ?? '')) ?: 'Lainnya',
                    ];
                }
            }
            $ikkList = array_values($byCode);
            if (empty($ikkList)) {
                return null;
            }

            $wpIds = array_values(array_unique(array_filter(array_column($ikkList, 'id'))));
            $wpIdsEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $wpIds));
            $codeToSite = [];
            $wpIdToCode = [];
            foreach ($ikkList as $item) {
                $codeToSite[$item['code']] = $item['site'];
                if (isset($item['id'])) {
                    $wpIdToCode[$item['id']] = $item['code'];
                }
            }

            // 2) IPK count per work_permit_id (untuk tanggal filterDate)
            $ipkCountByWp = [];
            $sqlIpkCnt = "
                SELECT work_permit_id, count() AS cnt FROM hse_automation.ipk_assessment
                WHERE work_permit_id IN ({$wpIdsEsc}) AND toDate(start_date) = toDate('{$dateEsc}')
                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                GROUP BY work_permit_id
            ";
            foreach ($ch->query($sqlIpkCnt) ?? [] as $r) {
                $wpId = $this->chVal($r, 'work_permit_id');
                if ($wpId !== null && $wpId !== '') {
                    $ipkCountByWp[$wpId] = (int) ($this->chVal($r, 'cnt') ?? 0);
                }
            }

            // 3) OKK count per work_permit_id (status SUBMITTED, created_at = filterDate)
            $okkCountByWp = [];
            $sqlOkkCnt = "
                SELECT work_permit_id, count() AS cnt FROM hse_automation.okk_assessment
                WHERE work_permit_id IN ({$wpIdsEsc}) AND upper(trim(toString(status))) = 'SUBMITTED'
                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0)) AND toDate(created_at) = toDate('{$dateEsc}')
                GROUP BY work_permit_id
            ";
            foreach ($ch->query($sqlOkkCnt) ?? [] as $r) {
                $wpId = $this->chVal($r, 'work_permit_id');
                if ($wpId !== null && $wpId !== '') {
                    $okkCountByWp[$wpId] = (int) ($this->chVal($r, 'cnt') ?? 0);
                }
            }

            // 4) IKK cancel (IPK: SUBMITTED + job_status NOT_STARTED)
            $sqlCancel = "
                SELECT work_permit_id FROM hse_automation.ipk_assessment
                WHERE work_permit_id IN ({$wpIdsEsc})
                  AND toDate(start_date) = toDate('{$dateEsc}')
                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                  AND upper(trim(toString(status))) = 'SUBMITTED' AND upper(trim(toString(job_status))) = 'NOT_STARTED'
            ";
            $cancelWpIds = [];
            foreach ($ch->query($sqlCancel) ?? [] as $r) {
                $wpId = $this->chVal($r, 'work_permit_id');
                if ($wpId !== null && isset($wpIdToCode[$wpId])) {
                    $cancelWpIds[$wpIdToCode[$wpId]] = true;
                }
            }

            // 5) Agregasi per site: ikk (count unik code), ipk (sum count), okk (sum count)
            $siteData = [];
            $hasIpkByCode = [];
            $hasOkkByCode = [];
            foreach ($ikkList as $item) {
                $code = $item['code'];
                if (isset($cancelWpIds[$code])) {
                    continue;
                }
                $site = $item['site'];
                if (!isset($siteData[$site])) {
                    $siteData[$site] = ['ikk' => 0, 'ipk' => 0, 'okk' => 0];
                }
                $siteData[$site]['ikk']++;
                $wpId = $item['id'] ?? null;
                $ipkCnt = $wpId !== null ? ($ipkCountByWp[$wpId] ?? 0) : 0;
                $okkCnt = $wpId !== null ? ($okkCountByWp[$wpId] ?? 0) : 0;
                $siteData[$site]['ipk'] += $ipkCnt;
                $siteData[$site]['okk'] += $okkCnt;
                $hasIpkByCode[$code] = $ipkCnt > 0;
                $hasOkkByCode[$code] = $okkCnt > 0;
            }

            $sites = [];
            foreach ($siteData as $siteName => $data) {
                $sites[] = [
                    'name' => $siteName,
                    'ikk' => $data['ikk'],
                    'oak' => 0,
                    'ipk' => $data['ipk'],
                ];
            }
            usort($sites, fn ($a, $b) => strnatcasecmp($a['name'], $b['name']));

            $totalIkkCh = array_sum(array_column($sites, 'ikk'));
            $totalIpkCh = array_sum(array_column($sites, 'ipk'));

            // 6) Matriks (Hijau/Kuning/Merah) per code untuk needAction, warningCount, completeCount
            $needAction = 0;
            $warningCount = 0;
            $completeCount = 0;
            foreach ($ikkList as $item) {
                if (isset($cancelWpIds[$item['code']])) {
                    continue;
                }
                $hasIpk = $hasIpkByCode[$item['code']] ?? false;
                $hasOkk = $hasOkkByCode[$item['code']] ?? false;
                $matriks = DOPMController::hitungStatusMatriks($hasIpk, $hasOkk);
                if ($matriks === 'Merah') {
                    $needAction++;
                } elseif ($matriks === 'Kuning') {
                    $warningCount++;
                } else {
                    $completeCount++;
                }
            }

            $totalPekerjaanBatalCh = 0;
            $sqlBatalCnt = "
                SELECT count() AS cnt FROM hse_automation.ipk_assessment
                WHERE work_permit_id IN ({$wpIdsEsc}) AND toDate(start_date) = toDate('{$dateEsc}')
                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                  AND upper(trim(toString(status))) = 'SUBMITTED' AND upper(trim(toString(job_status))) = 'NOT_STARTED'
            ";
            $batalRow = $ch->query($sqlBatalCnt);
            if (!empty($batalRow)) {
                $totalPekerjaanBatalCh = (int) ($this->chVal($batalRow[0], 'cnt') ?? 0);
            }

            // 7) OAK total
            $totalOakHarian = 0;
            $sqlOak = "SELECT count() AS cnt FROM hse_automation.aaj_vw_car_oak_register_ytd_only";
            $oakResult = $ch->query($sqlOak);
            if (!empty($oakResult)) {
                $totalOakHarian = (int) ($this->chVal($oakResult[0], 'cnt') ?? 0);
            }

            // 8) IKK minggu ini (weeklyCount)
            $totalIkkClickhouseMingguIni = 0;
            $sqlWeek = "
                SELECT code FROM hse_automation.ikk_work_permit
                WHERE toDate(start_date) <= toDate('" . addslashes($mingguEndStr) . "')
                  AND toDate(end_date)   >= toDate('" . addslashes($mingguStartStr) . "')
                  AND status IN ('APPROVED', 'EXPIRED') AND deleted_at IS NULL
            ";
            $wpRowsWeek = $ch->query($sqlWeek);
            if (!empty($wpRowsWeek)) {
                $codesAllWeek = [];
                foreach ($wpRowsWeek as $row) {
                    $code = $this->chVal($row, 'code');
                    if ($code !== null && $code !== '') {
                        $codesAllWeek[$code] = true;
                    }
                }
                $totalIkkClickhouseMingguIni = count($codesAllWeek);
            }

            $pctIpk = $totalIkkCh > 0 ? round(array_sum(array_values($hasIpkByCode)) / $totalIkkCh * 100, 1) : 0;
            $pctOkk = $totalIkkCh > 0 ? round(array_sum(array_values($hasOkkByCode)) / $totalIkkCh * 100, 1) : 0;
            $pctOak = $totalIkkCh > 0 ? min(100.0, round($totalOakHarian / $totalIkkCh * 100, 1)) : 0;
            $pctPengisianRataRata = round(($pctIpk + $pctOkk + $pctOak) / 3, 1);

            return [
                'needVerification' => $needAction,
                'cancelCount' => $totalPekerjaanBatalCh,
                'compliance' => $pctPengisianRataRata . '%',
                'oakToday' => $totalOakHarian,
                'weeklyCount' => $totalIkkClickhouseMingguIni > 0 ? $totalIkkClickhouseMingguIni : $totalIkkCh,
                'needAction' => $needAction,
                'warningCount' => $warningCount,
                'completeCount' => $completeCount,
                'reportDate' => $filterDate,
                'sites' => $sites,
                'totalIkk' => $totalIkkCh,
                'totalOak' => $totalOakHarian,
                'totalIpk' => $totalIpkCh,
            ];
        } catch (\Throwable $e) {
            Log::debug('DashboardEmailSummaryService ClickHouse: ' . $e->getMessage());
            return null;
        }
    }

    private function chVal(array $row, string $key): mixed
    {
        $keyLower = strtolower($key);
        foreach ($row as $k => $v) {
            if (strtolower((string) $k) === $keyLower) {
                return $v;
            }
        }
        return $row[$key] ?? null;
    }

    /**
     * Parse nilai datetime dari ClickHouse ke Carbon (sama logika seperti DOPMController::parseEndDate).
     */
    private function parseClickHouseDate(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        $tz = config('app.timezone', 'UTC');
        try {
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp((int) $value, $tz);
            }
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->setTimezone($tz);
            }
            return Carbon::parse($value, $tz);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Fallback: ringkasan dari MySQL (Dopm, IpkIkk, Okk) + OAK dari ClickHouse bila ada.
     */
    private function fetchSummaryFromMySQL(string $filterDate, Carbon $mingguStart, Carbon $mingguEnd): array
    {
        $scopeDate = function ($q) use ($filterDate) {
            $q->whereDate('tanggal_dop', $filterDate)->orWhereDate('timestamp', $filterDate);
        };
        $scopeNotCancel = function ($q) {
            $q->whereNull('status')->orWhereNotIn('status', ['Cancel', 'CANCEL']);
        };

        $totalDopmHarian = (int) Dopm::where($scopeDate)->where($scopeNotCancel)
            ->selectRaw('COUNT(DISTINCT kode_ikk) as cnt')->value('cnt');

        $scopeWeek = function ($q) use ($mingguStart, $mingguEnd) {
            $q->whereBetween('tanggal_dop', [$mingguStart, $mingguEnd])
                ->orWhereBetween('timestamp', [$mingguStart, $mingguEnd]);
        };
        $totalDopmMingguIni = Dopm::where($scopeWeek)->where($scopeNotCancel)->count();

        $totalPekerjaanBatalHarian = IpkIkk::whereDate('ts', $filterDate)
            ->whereIn('status_pekerjaan', ['Batal', 'BATAL'])
            ->count();

        $dopmListHarian = Dopm::where($scopeDate)
            ->where($scopeNotCancel)
            ->orderBy('tanggal_dop')
            ->orderBy('id')
            ->get();

        $kodeIkks = $dopmListHarian->pluck('kode_ikk')->filter()->unique()->values()->all();
        $hasIpkByKode = [];
        $hasOkkByKode = [];
        if (!empty($kodeIkks)) {
            $ipkKodes = IpkIkk::whereIn('kode_ikk', $kodeIkks)
                ->whereDate('ts', $filterDate)
                ->select('kode_ikk')
                ->distinct()
                ->pluck('kode_ikk')
                ->flip()
                ->all();
            $okkKodes = Okk::whereIn('kode_ikk', $kodeIkks)
                ->whereDate('ts', $filterDate)
                ->select('kode_ikk')
                ->distinct()
                ->pluck('kode_ikk')
                ->flip()
                ->all();
            foreach ($kodeIkks as $k) {
                $hasIpkByKode[$k] = isset($ipkKodes[$k]);
                $hasOkkByKode[$k] = isset($okkKodes[$k]);
            }
        }

        $needAction = 0;
        $warningCount = 0;
        $completeCount = 0;
        foreach ($dopmListHarian as $dopm) {
            $k = $dopm->kode_ikk;
            $hasIpk = ($k === null || $k === '') ? null : ($hasIpkByKode[$k] ?? false);
            $hasOkk = ($k === null || $k === '') ? null : ($hasOkkByKode[$k] ?? false);
            $dopm->is_ikk_ada_ipk = $hasIpk;
            $dopm->is_ikk_ada_okk = $hasOkk;
            $matriks = DOPMController::hitungStatusMatriks($hasIpk, $hasOkk);
            if ($matriks === 'Merah') {
                $needAction++;
            } elseif ($matriks === 'Kuning') {
                $warningCount++;
            } else {
                $completeCount++;
            }
        }

        $totalOakHarian = 0;
        $totalIkkClickhouseMingguIni = 0;
        try {
            if (class_exists(ClickHouseService::class)) {
                $clickHouse = app(ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    $sqlOak = "SELECT count() as cnt FROM hse_automation.aaj_vw_car_oak_register_ytd_only";
                    $oakResult = $clickHouse->query($sqlOak);
                    $totalOakHarian = isset($oakResult[0]['cnt']) ? (int) $oakResult[0]['cnt'] : 0;
                    $mingguStartStr = $mingguStart->format('Y-m-d');
                    $mingguEndStr = $mingguEnd->format('Y-m-d');
                    $sqlWeek = "
                        SELECT code FROM hse_automation.ikk_work_permit
                        WHERE toDate(start_date) <= toDate('" . addslashes($mingguEndStr) . "')
                          AND toDate(end_date)   >= toDate('" . addslashes($mingguStartStr) . "')
                          AND status IN ('APPROVED', 'EXPIRED') AND deleted_at IS NULL
                    ";
                    $wpRowsWeek = $clickHouse->query($sqlWeek);
                    if (!empty($wpRowsWeek)) {
                        $codesAllWeek = [];
                        foreach ($wpRowsWeek as $row) {
                            $code = isset($row['code']) ? trim((string) $row['code']) : '';
                            if ($code !== '') {
                                $codesAllWeek[$code] = true;
                            }
                        }
                        $totalIkkClickhouseMingguIni = count($codesAllWeek);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::debug('DashboardEmailSummaryService ClickHouse fallback: ' . $e->getMessage());
        }

        $dopmAdaIpkCount = $dopmListHarian->where('is_ikk_ada_ipk', true)->count();
        $dopmAdaOkkCount = $dopmListHarian->where('is_ikk_ada_okk', true)->count();
        $pctDopmAdaIpk = $totalDopmHarian > 0 ? round($dopmAdaIpkCount / $totalDopmHarian * 100, 1) : 0;
        $pctDopmAdaOkk = $totalDopmHarian > 0 ? round($dopmAdaOkkCount / $totalDopmHarian * 100, 1) : 0;
        $pctDopmOak = $totalDopmHarian > 0 ? min(100.0, round($totalOakHarian / $totalDopmHarian * 100, 1)) : 0;
        $pctPengisianRataRata = round(($pctDopmAdaIpk + $pctDopmAdaOkk + $pctDopmOak) / 3, 1);

        $weeklyCount = $totalIkkClickhouseMingguIni > 0 ? $totalIkkClickhouseMingguIni : $totalDopmMingguIni;

        $summaryBySite = [];
        foreach ($dopmListHarian as $dopm) {
            $site = trim($dopm->site_ijin_kerja_khusus ?? '') ?: 'Lainnya';
            if (!isset($summaryBySite[$site])) {
                $summaryBySite[$site] = ['ikk' => 0, 'kode_ikks' => []];
            }
            $summaryBySite[$site]['ikk']++;
            $k = $dopm->kode_ikk;
            if ($k !== null && $k !== '') {
                $summaryBySite[$site]['kode_ikks'][$k] = true;
            }
        }
        $sites = [];
        foreach ($summaryBySite as $siteName => $data) {
            $kodeIkksSite = array_keys($data['kode_ikks']);
            $ipkCount = empty($kodeIkksSite)
                ? 0
                : IpkIkk::whereDate('ts', $filterDate)->whereIn('kode_ikk', $kodeIkksSite)->count();
            $sites[] = [
                'name' => $siteName,
                'ikk' => $data['ikk'],
                'oak' => 0,
                'ipk' => $ipkCount,
            ];
        }
        usort($sites, fn ($a, $b) => strnatcasecmp($a['name'], $b['name']));

        $totalIpkHarian = empty($kodeIkks)
            ? IpkIkk::whereDate('ts', $filterDate)->count()
            : IpkIkk::whereDate('ts', $filterDate)->whereIn('kode_ikk', $kodeIkks)->count();

        return [
            'needVerification' => $needAction,
            'cancelCount' => $totalPekerjaanBatalHarian,
            'compliance' => $pctPengisianRataRata . '%',
            'oakToday' => $totalOakHarian,
            'weeklyCount' => $weeklyCount,
            'needAction' => $needAction,
            'warningCount' => $warningCount,
            'completeCount' => $completeCount,
            'reportDate' => $filterDate,
            'sites' => $sites,
            'totalIkk' => $totalDopmHarian,
            'totalOak' => $totalOakHarian,
            'totalIpk' => $totalIpkHarian,
        ];
    }
}
