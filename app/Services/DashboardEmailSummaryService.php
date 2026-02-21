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
 * Menggunakan logika yang selaras dengan DOPMController::dashboard().
 */
class DashboardEmailSummaryService
{
    /**
     * Ringkasan metrik untuk template email dashboard-screenshot.
     *
     * @return array{
     *   needVerification: int,
     *   cancelCount: int,
     *   compliance: string,
     *   oakToday: int,
     *   weeklyCount: int,
     *   needAction: int,
     *   warningCount: int,
     *   completeCount: int,
     *   reportDate: string
     * }
     */
    public function getSummaryForDate(string $filterDate): array
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
            $filterDate = now()->toDateString();
        }

        $scopeDate = function ($q) use ($filterDate) {
            $q->whereDate('tanggal_dop', $filterDate)->orWhereDate('timestamp', $filterDate);
        };
        $scopeNotCancel = function ($q) {
            $q->whereNull('status')->orWhereNotIn('status', ['Cancel', 'CANCEL']);
        };

        $totalDopmHarian = (int) Dopm::where($scopeDate)->where($scopeNotCancel)
            ->selectRaw('COUNT(DISTINCT kode_ikk) as cnt')->value('cnt');

        $totalDopmCancelHarian = Dopm::where($scopeDate)
            ->whereIn('status', ['Cancel', 'CANCEL'])
            ->count();

        $mingguStart = Carbon::parse($filterDate)->startOfWeek(Carbon::MONDAY);
        $mingguEnd = $mingguStart->copy()->addDays(6)->endOfDay();
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
                    $mingguStartStr = $mingguStart->format('Y-m-d');
                    $mingguEndStr = $mingguEnd->format('Y-m-d');
                    $sqlOak = "SELECT count() as cnt FROM hse_automation.aaj_vw_car_oak_register_ytd_only";
                    $oakResult = $clickHouse->query($sqlOak);
                    $totalOakHarian = isset($oakResult[0]['cnt']) ? (int) $oakResult[0]['cnt'] : 0;

                    $sqlWeek = "
                        SELECT code FROM hse_automation.ikk_work_permit
                        WHERE toDate(start_date) <= toDate('" . addslashes($mingguEndStr) . "')
                          AND toDate(end_date)   >= toDate('" . addslashes($mingguStartStr) . "')
                          AND status IN ('APPROVED', 'EXPIRED') AND deleted_at IS NULL
                    ";
                    $wpRowsWeek = $clickHouse->query($sqlWeek);
                    $codesAllWeek = [];
                    if (!empty($wpRowsWeek)) {
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
            Log::debug('DashboardEmailSummaryService ClickHouse: ' . $e->getMessage());
        }

        $dopmAdaIpkCount = $dopmListHarian->where('is_ikk_ada_ipk', true)->count();
        $dopmAdaOkkCount = $dopmListHarian->where('is_ikk_ada_okk', true)->count();
        $pctDopmAdaIpk = $totalDopmHarian > 0 ? round($dopmAdaIpkCount / $totalDopmHarian * 100, 1) : 0;
        $pctDopmAdaOkk = $totalDopmHarian > 0 ? round($dopmAdaOkkCount / $totalDopmHarian * 100, 1) : 0;
        $pctDopmOak = $totalDopmHarian > 0 ? min(100.0, round($totalOakHarian / $totalDopmHarian * 100, 1)) : 0;
        $pctPengisianRataRata = round(($pctDopmAdaIpk + $pctDopmAdaOkk + $pctDopmOak) / 3, 1);

        $weeklyCount = $totalIkkClickhouseMingguIni > 0 ? $totalIkkClickhouseMingguIni : $totalDopmMingguIni;

        // Per-site: name, ikk (DOPM count), ipk (IpkIkk count), oak (0 atau dari CH jika ada)
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
            'totalOak' => $totalOakHarian,
        ];
    }
}
