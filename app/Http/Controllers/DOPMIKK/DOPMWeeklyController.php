<?php

namespace App\Http\Controllers\DOPMIKK;

use App\Http\Controllers\Controller;
use App\Jobs\ImportDopmJob;
use App\Models\Dopm;
use App\Models\IpkIkk;
use App\Models\Okk;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DOPMWeeklyController extends Controller
{
    /**
     * Apakah untuk tanggal tertentu data IPK/OKK diambil dari ClickHouse (true)
     * atau MySQL (false). Berdasarkan config dopm.ipk_okk_clickhouse_cutoff_date.
     */
    public static function useClickHouseForIpkOkk(string $filterDate): bool
    {
        $cutoff = config('dopm.ipk_okk_clickhouse_cutoff_date', '2025-02-20');
        try {
            $d = Carbon::parse($filterDate)->startOfDay();
            $c = Carbon::parse($cutoff)->startOfDay();

            return $d->gte($c);
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Dashboard statistik mingguan DOPM, IKK, OKK, OAK.
     * Filter utama tetap menggunakan tanggal terpilih, tetapi
     * data IKK yang ditampilkan mencakup status APPROVED & EXPIRED.
     */
    public function dashboard(Request $request): View
    {
        $filterDate = $request->get('date', now()->toDateString());
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
            $filterDate = now()->toDateString();
        }
        // Normalisasi: null / kosong / spasi = Semua Site (jangan filter by site)
        $filterSite = trim((string) ($request->query('site') ?? ''));

        // Week filter: parse dari request atau default ke week saat ini
        // Format: YYYY-WXX (contoh: 2025-W09)
        $filterWeek = $request->get('week', '');
        if ($filterWeek === '' || !preg_match('/^\d{4}-W\d{2}$/', $filterWeek)) {
            // Default ke week dari filterDate
            $filterWeek = Carbon::parse($filterDate)->format('o-\WW');
        }
        
        // Parse week number dan tahun
        preg_match('/^(\d{4})-W(\d{2})$/', $filterWeek, $weekMatches);
        $weekYear = (int) ($weekMatches[1] ?? now()->year);
        $weekNumber = (int) ($weekMatches[2] ?? now()->weekOfYear);
        
        // Hitung tanggal Senin (start) dan Minggu (end) dari week yang dipilih
        $weekStartDate = Carbon::now()->setISODate($weekYear, $weekNumber, 1)->startOfDay(); // 1 = Senin
        $weekEndDate = $weekStartDate->copy()->addDays(6)->endOfDay(); // Minggu
        
        // Generate daftar week untuk dropdown (dari minggu 1 tahun ini sampai minggu saat ini + 2)
        $currentYear = now()->year;
        $currentWeek = now()->weekOfYear;
        $weekList = [];
        for ($w = 1; $w <= min(53, $currentWeek + 2); $w++) {
            $wStart = Carbon::now()->setISODate($currentYear, $w, 1);
            $wEnd = $wStart->copy()->addDays(6);
            $weekList[] = [
                'value' => $currentYear . '-W' . str_pad($w, 2, '0', STR_PAD_LEFT),
                'label' => 'Week ' . $w . ' (' . $wStart->format('d M') . ' - ' . $wEnd->format('d M') . ')',
            ];
        }

        // Scope tanggal & status (exclude Cancel)
        $scopeDate = function ($q) use ($filterDate) {
            $q->whereDate('tanggal_dop', $filterDate)->orWhereDate('timestamp', $filterDate);
        };
        $scopeNotCancel = function ($q) {
            $q->whereNull('status')->orWhereNotIn('status', ['Cancel', 'CANCEL']);
        };
        $scopeSite = function ($q) use ($filterSite) {
            if ($filterSite === '' || $filterSite === null) {
                return;
            }
            if ($filterSite === 'Lainnya') {
                $q->where(function ($q2) {
                    $q2->whereNull('site_ijin_kerja_khusus')
                        ->orWhereRaw("TRIM(COALESCE(site_ijin_kerja_khusus, '')) = ''");
                });
            } else {
                $q->whereRaw("TRIM(COALESCE(site_ijin_kerja_khusus, '')) = ?", [$filterSite]);
            }
        };

        // Daftar site untuk dropdown (tanggal terpilih, bukan Cancel) — query distinct saja, tidak load semua baris
        $siteListRaw = Dopm::where($scopeDate)
            ->where($scopeNotCancel)
            ->selectRaw("TRIM(COALESCE(site_ijin_kerja_khusus, '')) as site_val")
            ->distinct()
            ->pluck('site_val');
        $siteList = $siteListRaw->map(fn ($s) => $s !== '' ? $s : 'Lainnya')->unique()->sort()->values()->all();

        // Data harian: hitung unik per kode_ikk (distinct) per tanggal (+ optional site)
        $totalDopmHarian = (int) Dopm::where($scopeDate)->where($scopeNotCancel)->when($filterSite !== '', $scopeSite)->selectRaw('COUNT(DISTINCT kode_ikk) as cnt')->value('cnt');

        // DOPM dengan status Cancel di hari ini
        $totalDopmCancelHarian = Dopm::where($scopeDate)
            ->whereIn('status', ['Cancel', 'CANCEL'])
            ->when($filterSite !== '', $scopeSite)
            ->count();

        // Total DOPM minggu ini (Senin–Minggu), tanpa status Cancel
        $mingguStart = Carbon::parse($filterDate)->startOfWeek(Carbon::MONDAY);
        $mingguEnd = $mingguStart->copy()->addDays(6)->endOfDay();
        $scopeWeek = function ($q) use ($mingguStart, $mingguEnd) {
            $q->whereBetween('tanggal_dop', [$mingguStart, $mingguEnd])
                ->orWhereBetween('timestamp', [$mingguStart, $mingguEnd]);
        };
        $totalDopmMingguIni = Dopm::where($scopeWeek)->where($scopeNotCancel)->when($filterSite !== '', $scopeSite)->count();

        // Total IKK ClickHouse minggu ini (APPROVED + EXPIRED) + data per hari untuk chart
        $totalIkkClickhouseMingguIni = 0;
        $chartIkkClickhousePerHariMinggu = [0, 0, 0, 0, 0, 0, 0];
        try {
            if (class_exists(\App\Services\ClickHouseService::class)) {
                /** @var \App\Services\ClickHouseService $clickHouse */
                $clickHouse = app(\App\Services\ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    $mingguStartStr = $mingguStart->format('Y-m-d');
                    $mingguEndStr = $mingguEnd->format('Y-m-d');
                    $siteFilterClause = '';
                    if ($filterSite !== '' && $filterSite !== null) {
                        if ($filterSite === 'Lainnya') {
                            $siteFilterClause = " AND trim(COALESCE(ra_site_name, '')) = ''";
                        } else {
                            $siteFilterClause = " AND trim(COALESCE(ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                        }
                    }
                    $sqlWeek = "
                        SELECT code, start_date, end_date
                        FROM hse_automation.ikk_work_permit
                        WHERE toDate(start_date) <= toDate('" . addslashes($mingguEndStr) . "')
                          AND toDate(end_date)   >= toDate('" . addslashes($mingguStartStr) . "')
                          AND status IN ('APPROVED', 'EXPIRED')
                          AND deleted_at IS NULL
                          {$siteFilterClause}
                    ";
                    $wpRowsWeek = $clickHouse->query($sqlWeek);
                    $codesAllWeek = [];
                    $codesPerDay = array_fill(0, 7, []);
                    if (!empty($wpRowsWeek)) {
                        foreach ($wpRowsWeek as $row) {
                            $code = isset($row['code']) ? trim((string) $row['code']) : '';
                            if ($code === '') {
                                continue;
                            }
                            $codesAllWeek[$code] = true;
                            $startDate = self::parseEndDate(self::getClickHouseRowValue($row, 'start_date'));
                            $endDate = self::parseEndDate(self::getClickHouseRowValue($row, 'end_date'));
                            if ($startDate === null || $endDate === null) {
                                continue;
                            }
                            for ($i = 0; $i < 7; $i++) {
                                $dayStart = $mingguStart->copy()->addDays($i)->startOfDay();
                                $dayEnd = $mingguStart->copy()->addDays($i)->endOfDay();
                                if ($startDate->lte($dayEnd) && $endDate->gte($dayStart)) {
                                    $codesPerDay[$i][$code] = true;
                                }
                            }
                        }
                        $totalIkkClickhouseMingguIni = count($codesAllWeek);
                        for ($i = 0; $i < 7; $i++) {
                            $chartIkkClickhousePerHariMinggu[$i] = count($codesPerDay[$i]);
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Dashboard IKK ClickHouse week: ' . $e->getMessage());
        }

        // === STATISTIK WEEKLY: berdasarkan week yang dipilih (distinct work permit by start_date) ===
        $totalIkkWeekly = 0;
        $ikkAdaIpkCountWeekly = 0;
        $ikkAdaOkkCountWeekly = 0;
        $pctIkkAdaIpkWeekly = 0;
        $pctIkkAdaOkkWeekly = 0;
        $pctComplianceWeekly = 0;
        $totalPekerjaanBatalWeekly = 0;
        
        try {
            if (class_exists(\App\Services\ClickHouseService::class)) {
                $clickHouse = app(\App\Services\ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    $weekStartStr = $weekStartDate->format('Y-m-d');
                    $weekEndStr = $weekEndDate->format('Y-m-d');
                    
                    $siteFilterClauseWeekly = '';
                    if ($filterSite !== '' && $filterSite !== null) {
                        if ($filterSite === 'Lainnya') {
                            $siteFilterClauseWeekly = " AND trim(COALESCE(ra_site_name, '')) = ''";
                        } else {
                            $siteFilterClauseWeekly = " AND trim(COALESCE(ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                        }
                    }
                    
                    // Query IKK dengan start_date dalam rentang week (distinct by code)
                    // Hanya yang sudah di-approve oleh WKTT
                    $sqlWeeklyIkk = "
                        SELECT DISTINCT wp.code, wp.id
                        FROM hse_automation.ikk_work_permit AS wp
                        INNER JOIN hse_automation.ikk_work_permit_pic AS wp_pic
                            ON toString(wp_pic.work_permit_id) = toString(wp.id)
                            AND (wp_pic.deleted_at IS NULL OR wp_pic.deleted_at = toDateTime(0))
                        LEFT JOIN hse_automation.ikk_m_pic AS m
                            ON toString(m.id) = toString(wp_pic.m_pic_id)
                        WHERE (wp.deleted_at IS NULL OR wp.deleted_at = toDateTime(0))
                            AND toDate(wp.start_date) >= toDate('{$weekStartStr}')
                            AND toDate(wp.start_date) <= toDate('{$weekEndStr}')
                            {$siteFilterClauseWeekly}
                        GROUP BY wp.code, wp.id
                        HAVING sum(if(upper(trim(toString(wp_pic.status))) = 'APPROVED'
                            AND trim(toString(m.m_privilege_id)) = '7d872114-0924-4c6a-880e-49b3c06b5429', 1, 0)) > 0
                    ";
                    $weeklyIkkRows = $clickHouse->query($sqlWeeklyIkk);
                    $weeklyIkkCodes = [];
                    $weeklyIkkIds = [];
                    if (!empty($weeklyIkkRows)) {
                        foreach ($weeklyIkkRows as $row) {
                            $code = isset($row['code']) ? trim((string) $row['code']) : '';
                            $id = isset($row['id']) ? trim((string) $row['id']) : '';
                            if ($code !== '') {
                                $weeklyIkkCodes[$code] = true;
                            }
                            if ($id !== '') {
                                $weeklyIkkIds[$id] = $code;
                            }
                        }
                    }
                    $totalIkkWeekly = count($weeklyIkkCodes);
                    
                    // Query IPK yang ada dalam rentang week (by work_permit_id)
                    if (!empty($weeklyIkkIds)) {
                        $wpIdsWeeklyEsc = implode(',', array_map(fn($id) => "'" . addslashes($id) . "'", array_keys($weeklyIkkIds)));
                        $sqlWeeklyIpk = "
                            SELECT DISTINCT work_permit_id
                            FROM hse_automation.ipk_assessment
                            WHERE work_permit_id IN ({$wpIdsWeeklyEsc})
                              AND toDate(start_date) >= toDate('{$weekStartStr}')
                              AND toDate(start_date) <= toDate('{$weekEndStr}')
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                        ";
                        $weeklyIpkRows = $clickHouse->query($sqlWeeklyIpk);
                        $ipkWpIds = [];
                        if (!empty($weeklyIpkRows)) {
                            foreach ($weeklyIpkRows as $r) {
                                $wpId = isset($r['work_permit_id']) ? trim((string) $r['work_permit_id']) : '';
                                if ($wpId !== '' && isset($weeklyIkkIds[$wpId])) {
                                    $ipkWpIds[$weeklyIkkIds[$wpId]] = true;
                                }
                            }
                        }
                        $ikkAdaIpkCountWeekly = count($ipkWpIds);
                        
                        // Query OKK yang ada dalam rentang week (by work_permit_id)
                        $sqlWeeklyOkk = "
                            SELECT DISTINCT work_permit_id
                            FROM hse_automation.okk_assessment
                            WHERE work_permit_id IN ({$wpIdsWeeklyEsc})
                              AND toDate(created_at) >= toDate('{$weekStartStr}')
                              AND toDate(created_at) <= toDate('{$weekEndStr}')
                              AND upper(trim(toString(status))) = 'SUBMITTED'
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                        ";
                        $weeklyOkkRows = $clickHouse->query($sqlWeeklyOkk);
                        $okkWpIds = [];
                        if (!empty($weeklyOkkRows)) {
                            foreach ($weeklyOkkRows as $r) {
                                $wpId = isset($r['work_permit_id']) ? trim((string) $r['work_permit_id']) : '';
                                if ($wpId !== '' && isset($weeklyIkkIds[$wpId])) {
                                    $okkWpIds[$weeklyIkkIds[$wpId]] = true;
                                }
                            }
                        }
                        $ikkAdaOkkCountWeekly = count($okkWpIds);
                        
                        // Query IKK yang cancel dalam rentang week
                        $sqlWeeklyCancel = "
                            SELECT COUNT(DISTINCT work_permit_id) as cnt
                            FROM hse_automation.ipk_assessment
                            WHERE work_permit_id IN ({$wpIdsWeeklyEsc})
                              AND toDate(start_date) >= toDate('{$weekStartStr}')
                              AND toDate(start_date) <= toDate('{$weekEndStr}')
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                              AND upper(trim(toString(status))) = 'SUBMITTED' 
                              AND upper(trim(toString(job_status))) = 'NOT_STARTED'
                        ";
                        $cancelRows = $clickHouse->query($sqlWeeklyCancel);
                        $totalPekerjaanBatalWeekly = (int) ($cancelRows[0]['cnt'] ?? 0);
                    }
                    
                    // Hitung persentase
                    $pctIkkAdaIpkWeekly = $totalIkkWeekly > 0 ? round(($ikkAdaIpkCountWeekly / $totalIkkWeekly) * 100, 1) : 0;
                    $pctIkkAdaOkkWeekly = $totalIkkWeekly > 0 ? round(($ikkAdaOkkCountWeekly / $totalIkkWeekly) * 100, 1) : 0;
                    $pctComplianceWeekly = round(($pctIkkAdaIpkWeekly + $pctIkkAdaOkkWeekly) / 2, 1);
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Dashboard weekly stats: ' . $e->getMessage());
        }

        // IPK-IKK dan OKK harian: setelah dapat DOPM list, hitung dari kode_ikk yang ada di list (agar konsisten dengan filter site)
        $totalIkkHarian = IpkIkk::whereDate('ts', $filterDate)->count();
        $totalOkkHarian = Okk::whereDate('ts', $filterDate)->count();

        // IPK-IKK dengan status_pekerjaan Batal di hari ini
        $totalPekerjaanBatalHarian = IpkIkk::whereDate('ts', $filterDate)
            ->whereIn('status_pekerjaan', ['Batal', 'BATAL'])
            ->count();

        // Daftar DOPM untuk tanggal terpilih (+ optional site) — kolom yang dipakai view & summary
        $dopmListHarian = Dopm::where($scopeDate)
            ->where($scopeNotCancel)
            ->when($filterSite !== '', $scopeSite)
            ->orderBy('tanggal_dop')
            ->orderBy('id')
            ->select([
                'id', 'id_dop', 'kode_ikk', 'site_ijin_kerja_khusus', 'jenis_ijin_kerja_khusus', 'tanggal_dop', 'timestamp', 'status',
                'nama_pekerjaan', 'perusahaan_ijin_kerja_khusus',
                'nama_layer_1', 'nama_layer_2', 'nama_layer_3', 'nama_layer_4',
                'sid_layer_1', 'sid_layer_2', 'sid_layer_3', 'sid_layer_4',
            ])
            ->get();

        // Hitung total IKK/OKK hanya dari kode_ikk yang ada di DOPM terfilter (konsisten dengan filter site)
        $kodeIkksAll = $dopmListHarian->pluck('kode_ikk')->filter()->unique()->values()->all();
        if ($filterSite !== '' && !empty($kodeIkksAll)) {
            $totalIkkHarian = IpkIkk::whereDate('ts', $filterDate)->whereIn('kode_ikk', $kodeIkksAll)->count();
            $totalOkkHarian = Okk::whereDate('ts', $filterDate)->whereIn('kode_ikk', $kodeIkksAll)->count();
        }

        // Status matriks per DOPM: Is IKK ada IPK, Is IKK ada OKK (by kode_ikk + filterDate)
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

        foreach ($dopmListHarian as $dopm) {
            $k = $dopm->kode_ikk;
            $hasIpk = ($k === null || $k === '') ? null : ($hasIpkByKode[$k] ?? false);
            $hasOkk = ($k === null || $k === '') ? null : ($hasOkkByKode[$k] ?? false);
            $dopm->status_matriks = self::hitungStatusMatriks($hasIpk, $hasOkk);
            $dopm->is_ikk_ada_ipk = $hasIpk;
            $dopm->is_ikk_ada_okk = $hasOkk;
        }

        // Total OAK harian dihitung nanti dari IKK (lokasi + detail lokasi), sama seperti di modal detail
        $totalOakHarian = 0;

        // Persentase IKK yang ada IPK / ada OKK (dasar: jumlah IKK unik dari DOPM tanggal terpilih)
        $totalIkkUnikHarian = count($kodeIkks);
        $ikkAdaIpkCount = $totalIkkUnikHarian > 0 ? count(array_filter($hasIpkByKode)) : 0;
        $ikkAdaOkkCount = $totalIkkUnikHarian > 0 ? count(array_filter($hasOkkByKode)) : 0;
        $pctIkkAdaIpk = $totalIkkUnikHarian > 0 ? round($ikkAdaIpkCount / $totalIkkUnikHarian * 100, 1) : 0;
        $pctIkkAdaOkk = $totalIkkUnikHarian > 0 ? round($ikkAdaOkkCount / $totalIkkUnikHarian * 100, 1) : 0;

        // Presentase dari total DOPM: berapa banyak DOPM yang IKK-nya punya IPK / OKK (basis = total DOPM, bukan IKK unik)
        $dopmAdaIpkCount = $dopmListHarian->where('is_ikk_ada_ipk', true)->count();
        $dopmAdaOkkCount = $dopmListHarian->where('is_ikk_ada_okk', true)->count();
        $pctDopmAdaIpk = $totalDopmHarian > 0 ? round($dopmAdaIpkCount / $totalDopmHarian * 100, 1) : 0;
        $pctDopmAdaOkk = $totalDopmHarian > 0 ? round($dopmAdaOkkCount / $totalDopmHarian * 100, 1) : 0;
        // OAK: rasio laporan OAK vs DOPM (min 100%), lalu dijadikan persen untuk dirata-ratakan dengan IPK & OKK
        $pctDopmOak = $totalDopmHarian > 0 ? min(100.0, round($totalOakHarian / $totalDopmHarian * 100, 1)) : 0;
        // Satu presentase gabungan: rata-rata IPK, OKK & OAK
        $pctPengisianRataRata = round(($pctDopmAdaIpk + $pctDopmAdaOkk + $pctDopmOak) / 3, 1);

        // Summary harian per site: jumlah per jenis IJK + status Hijau/Kuning/Merah
        $summaryBySite = [];
        $allJenis = [];
        foreach ($dopmListHarian as $dopm) {
            $site = trim($dopm->site_ijin_kerja_khusus ?? '') ?: 'Lainnya';
            $jenis = trim($dopm->jenis_ijin_kerja_khusus ?? '') ?: '-';
            $matriks = $dopm->status_matriks ?? 'Merah';

            if (!isset($summaryBySite[$site])) {
                $summaryBySite[$site] = [
                    'jenis' => [],
                    'hijau' => 0,
                    'kuning' => 0,
                    'merah' => 0,
                ];
            }
            $summaryBySite[$site]['hijau'] += ($matriks === 'Hijau' ? 1 : 0);
            $summaryBySite[$site]['kuning'] += ($matriks === 'Kuning' ? 1 : 0);
            $summaryBySite[$site]['merah'] += ($matriks === 'Merah' ? 1 : 0);
            $summaryBySite[$site]['jenis'][$jenis] = ($summaryBySite[$site]['jenis'][$jenis] ?? 0) + 1;
            $allJenis[$jenis] = true;
        }
        foreach ($summaryBySite as $site => &$row) {
            $row['total'] = $row['hijau'] + $row['kuning'] + $row['merah'];
        }
        unset($row);
        ksort($summaryBySite, SORT_NATURAL);
        $summaryJenisKeys = array_keys($allJenis);
        usort($summaryJenisKeys, function ($a, $b) {
            return strnatcasecmp($a, $b);
        });

        // Chart DOPM vs IPK vs OKK per jenis_ijin_kerja_khusus — satu kali query IPK/OKK count per kode_ikk
        $chartJenisLabels = [];
        $chartDopmPerJenis = [];
        $chartIpkPerJenis = [];
        $chartOkkPerJenis = [];
        $ipkCountByKodeDopm = [];
        $okkCountByKodeDopm = [];
        if (!empty($kodeIkks)) {
            $ipkCountByKodeDopm = IpkIkk::whereDate('ts', $filterDate)
                ->whereIn('kode_ikk', $kodeIkks)
                ->selectRaw('kode_ikk, count(*) as cnt')
                ->groupBy('kode_ikk')
                ->pluck('cnt', 'kode_ikk')
                ->all();
            $okkCountByKodeDopm = Okk::whereDate('ts', $filterDate)
                ->whereIn('kode_ikk', $kodeIkks)
                ->selectRaw('kode_ikk, count(*) as cnt')
                ->groupBy('kode_ikk')
                ->pluck('cnt', 'kode_ikk')
                ->all();
        }
        foreach ($summaryJenisKeys as $jenis) {
            $chartJenisLabels[] = self::singkatJenisIjin($jenis);
            $dopmPerJenis = $dopmListHarian->filter(function ($d) use ($jenis) {
                $j = trim($d->jenis_ijin_kerja_khusus ?? '') ?: '-';
                return $j === $jenis;
            });
            $chartDopmPerJenis[] = $dopmPerJenis->count();
            $kodeIkksJenis = $dopmPerJenis->pluck('kode_ikk')->filter()->unique()->values()->all();
            $chartIpkPerJenis[] = empty($kodeIkksJenis) ? 0 : array_sum(array_map(fn ($c) => (int) ($ipkCountByKodeDopm[$c] ?? 0), $kodeIkksJenis));
            $chartOkkPerJenis[] = empty($kodeIkksJenis) ? 0 : array_sum(array_map(fn ($c) => (int) ($okkCountByKodeDopm[$c] ?? 0), $kodeIkksJenis));
        }

        // Data IKK (work permit) dari ClickHouse untuk tampilan weekly
        $ikkClickhouseListHarian = [];
        try {
            if (class_exists(\App\Services\ClickHouseService::class)) {
                /** @var \App\Services\ClickHouseService $clickHouse */
                $clickHouse = app(\App\Services\ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    // Weekly: WP yang rentang (start_date–end_date) mencakup tanggal filter; IKK 1–3 akan muncul saat filter 1, 2, atau 3
                    $dateStr = addslashes($filterDate);
                    $siteFilterClause = '';
                    if ($filterSite !== '' && $filterSite !== null) {
                        if ($filterSite === 'Lainnya') {
                            $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = ''";
                        } else {
                            $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                        }
                    }

                    $sqlWorkPermits = "
                        SELECT
                            wp.id AS id,
                            wp.code AS code,
                            wp.name AS name,
                            wp.ra_site_name AS ra_site_name,
                            wp.company_name AS company_name,
                            wp.status AS status,
                            wp.m_job_id AS m_job_id,
                            wp.start_date AS start_date,
                            wp.end_date AS end_date,
                            wp.location_name AS location_name,
                            wp.location_detail_name AS location_detail_name,
                            groupUniqArray(if(trim(toString(m.m_privilege_id)) = '7d872114-0924-4c6a-880e-49b3c06b5429', ifNull(m.employee_name, concat('UNKNOWN:', toString(wp_pic.m_pic_id))), null)) AS approver_names,
                            count() AS total_pic
                        FROM hse_automation.ikk_work_permit AS wp
                        INNER JOIN hse_automation.ikk_work_permit_pic AS wp_pic
                            ON toString(wp_pic.work_permit_id) = toString(wp.id)
                            AND (wp_pic.deleted_at IS NULL OR wp_pic.deleted_at = toDateTime(0))
                        LEFT JOIN hse_automation.ikk_m_pic AS m
                            ON toString(m.id) = toString(wp_pic.m_pic_id)
                        WHERE (wp.deleted_at IS NULL OR wp.deleted_at = toDateTime(0))
                            AND toDate(wp.start_date) <= toDate('{$dateStr}')
                            AND toDate(wp.end_date)   >= toDate('{$dateStr}')
                            {$siteFilterClause}
                        GROUP BY
                            wp.id, wp.code, wp.name, wp.ra_site_name, wp.company_name,
                            wp.status, wp.m_job_id,
                            wp.start_date, wp.end_date, wp.location_name, wp.location_detail_name
                        HAVING
                            sum(if(upper(trim(toString(wp_pic.status))) = 'APPROVED'
                                AND trim(toString(m.m_privilege_id)) = '7d872114-0924-4c6a-880e-49b3c06b5429', 1, 0)) > 0
                        ORDER BY wp.start_date ASC
                    ";

                    $wpRows = $clickHouse->query($sqlWorkPermits);

                    // Log hasil query: jumlah baris, struktur kolom, dan sample data
                    $rowCount = is_array($wpRows) ? count($wpRows) : 0;
                    $firstRow = $wpRows[0] ?? null;
                    $sampleRows = [];
                    if (is_array($wpRows) && !empty($wpRows)) {
                        foreach (array_slice($wpRows, 0, 3) as $i => $r) {
                            $sampleRows[] = is_array($r) ? $r : (array) $r;
                        }
                    }
                    \Illuminate\Support\Facades\Log::debug('ClickHouse work permit query result', [
                        'query' => 'sqlWorkPermits (wp + wp_pic + m_pic, HAVING all PIC APPROVED)',
                        'params' => [
                            'date' => $filterDate,
                            'site' => $filterSite,
                        ],
                        'row_count' => $rowCount,
                        'column_keys' => $firstRow !== null ? array_keys(is_array($firstRow) ? $firstRow : (array) $firstRow) : [],
                        'sample_rows' => $sampleRows,
                    ]);

                    if (!empty($wpRows)) {
                        // Map job_id -> job_name
                        $jobIds = array_values(array_unique(array_filter(array_column($wpRows, 'm_job_id'))));
                        $jobNamesById = [];
                        if (!empty($jobIds)) {
                            $inJobs = implode(',', array_map(function ($id) {
                                return "'" . addslashes($id) . "'";
                            }, $jobIds));
                            $sqlJobs = "
                                SELECT id, name
                                FROM hse_automation.ikk_m_job
                                WHERE id IN ({$inJobs})
                            ";
                            $jobRows = $clickHouse->query($sqlJobs);
                            foreach ($jobRows as $jr) {
                                if (!isset($jr['id'])) {
                                    continue;
                                }
                                $jobId = $jr['id'];
                                $jobNamesById[$jobId] = $jr['name'] ?? null;
                            }
                        }

                        // Ambil employee per work permit untuk layer 1/2/3/4
                        $wpIds = array_values(array_unique(array_column($wpRows, 'id')));
                        $layersByWp = [];
                        if (!empty($wpIds)) {
                            $inWpIds = implode(',', array_map(function ($id) {
                                return "'" . addslashes($id) . "'";
                            }, $wpIds));
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
                                if (!in_array($layerNum, [1, 2, 3, 4], true)) {
                                    continue;
                                }
                                if (!isset($layersByWp[$wpId])) {
                                    $layersByWp[$wpId] = [];
                                }
                                if (!isset($layersByWp[$wpId][$layerNum])) {
                                    $layersByWp[$wpId][$layerNum] = [];
                                }
                                $layersByWp[$wpId][$layerNum][] = [
                                    'name' => trim((string) ($er['employee_name'] ?? '')),
                                    'sid' => trim((string) ($er['employee_sid'] ?? '')),
                                ];
                            }
                        }

                        foreach ($wpRows as $row) {
                            $wpId = $row['id'] ?? null;
                            if ($wpId === null || $wpId === '') {
                                continue;
                            }
                            $layers = $layersByWp[$wpId] ?? [];
                            $namaLayer1 = self::formatLayerEmployees($layers[1] ?? []);
                            $sidLayer1 = self::formatLayerSids($layers[1] ?? []);
                            $namaLayer2 = self::formatLayerEmployees($layers[2] ?? []);
                            $sidLayer2 = self::formatLayerSids($layers[2] ?? []);
                            $namaLayer3 = self::formatLayerEmployees($layers[3] ?? []);
                            $sidLayer3 = self::formatLayerSids($layers[3] ?? []);
                            $namaLayer4 = self::formatLayerEmployees($layers[4] ?? []);
                            $sidLayer4 = self::formatLayerSids($layers[4] ?? []);

                            // Simpan raw status dari ClickHouse (APPROVED/EXPIRED/dll) untuk logika
                            $rawStatus = $row['status'] ?? null;
                            $statusUpper = $rawStatus !== null ? strtoupper(trim((string) $rawStatus)) : null;

                            // Sumber kebenaran "approved" ada di permit_pic (HAVING query).
                            // Hanya skip REJECTED; jangan skip DRAFT/PENDING agar baris yang PIC-nya approved tetap tampil.
                            if ($statusUpper === 'REJECTED') {
                                continue;
                            }

                            // Label status untuk UI
                            $statusLabel = $rawStatus;
                            if ($statusUpper === 'APPROVED') {
                                $statusLabel = 'Berlaku';
                            } elseif ($statusUpper === 'EXPIRED') {
                                $statusLabel = 'Kadaluarsa';
                            }

                            $ikkClickhouseListHarian[] = (object) [
                                'id' => $wpId,
                                'code' => $row['code'] ?? null,
                                'site' => $row['ra_site_name'] ?? null,
                                'jenis_ijin_kerja_khusus' => isset($row['m_job_id']) && $row['m_job_id']
                                    ? ($jobNamesById[$row['m_job_id']] ?? null)
                                    : null,
                                'nama_pekerjaan' => $row['name'] ?? null,
                                'perusahaan' => $row['company_name'] ?? null,
                                'raw_status' => $statusUpper,
                                'status' => $statusLabel,
                                // status_matriks akan diisi ulang berdasarkan IPK/OKK/OAK di bawah
                                'status_matriks' => null,
                                'nama_layer_1' => $namaLayer1,
                                'sid_layer_1' => $sidLayer1,
                                'nama_layer_2' => $namaLayer2,
                                'sid_layer_2' => $sidLayer2,
                                'nama_layer_3' => $namaLayer3,
                                'sid_layer_3' => $sidLayer3,
                                'nama_layer_4' => $namaLayer4,
                                'sid_layer_4' => $sidLayer4,
                                'start_date' => $row['start_date'] ?? null,
                                'end_date' => $row['end_date'] ?? null,
                                'location_name' => self::getClickHouseRowValue($row, 'location_name'),
                                'location_detail_name' => self::getClickHouseRowValue($row, 'location_detail_name'),
                                'pic_approver_name' => self::formatApproverNames(self::getClickHouseRowValue($row, 'approver_names')),
                                'pic_approver_sid' => null,
                                'pic_approve_timestamp' => null,
                            ];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Dashboard IKK ClickHouse skip: ' . $e->getMessage());
        }

        // Satu query sudah mengembalikan hanya WP yang semua PIC-nya APPROVED; hitung total & dedupe by code
        if (empty($ikkClickhouseListHarian)) {
            $totalWorkPermitApprovedHarian = 0;
            $ikkClickhouseListHarian = [];
        } else {
            // Hitung approved pakai raw_status (bukan label "Berlaku")
            $approvedCodes = [];
            foreach ($ikkClickhouseListHarian as $ikk) {
                if (($ikk->raw_status ?? null) === 'APPROVED') {
                    $c = $ikk->code ?? null;
                    if ($c !== null && $c !== '') {
                        $approvedCodes[] = is_object($c) ? (string) $c : $c;
                    }
                }
            }
            $totalWorkPermitApprovedHarian = count(array_unique($approvedCodes));

            $byCode = [];
            foreach ($ikkClickhouseListHarian as $ikk) {
                $c = $ikk->code ?? '';
                if ($c !== '' && $c !== null && !isset($byCode[$c])) {
                    $byCode[$c] = $ikk;
                }
            }
            $ikkClickhouseListHarian = array_values($byCode);
        }

        \Illuminate\Support\Facades\Log::debug('IKK list size', [
            'count' => count($ikkClickhouseListHarian),
            'approved_count' => $totalWorkPermitApprovedHarian,
        ]);

        // Status pekerjaan & daftar IKK batal: dari MySQL atau ClickHouse sesuai cutoff
        $useChIpkOkk = self::useClickHouseForIpkOkk($filterDate);
        $cancelKodeIkk = [];
        if (!empty($ikkClickhouseListHarian)) {
            $idToCode = [];
            foreach ($ikkClickhouseListHarian as $ikk) {
                $id = $ikk->id ?? null;
                $code = $ikk->code ?? null;
                if ($id !== null && $code !== null && $code !== '') {
                    $idToCode[$id] = $code;
                }
            }

            if ($useChIpkOkk && class_exists(\App\Services\ClickHouseService::class)) {
                $ch = app(\App\Services\ClickHouseService::class);
                if (method_exists($ch, 'query') && $ch->isConnected() && !empty($idToCode)) {
                    $wpIds = array_keys($idToCode);
                    $wpIdsEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $wpIds));
                    $dateEsc = addslashes($filterDate);
                    $sqlStatus = "
                        SELECT work_permit_id, argMax(status, created_at) AS status, argMax(job_status, created_at) AS job_status
                        FROM hse_automation.ipk_assessment
                        WHERE work_permit_id IN ({$wpIdsEsc})
                          AND toDate(start_date) = toDate('{$dateEsc}')
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                        GROUP BY work_permit_id
                    ";
                    $statusRows = $ch->query($sqlStatus);
                    $statusByCode = [];
                    foreach ($statusRows ?? [] as $r) {
                        $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                        $code = $idToCode[$wpId] ?? null;
                        if ($code !== null) {
                            $st = self::getClickHouseRowValue($r, 'job_status');
                            $statusByCode[$code] = $st;
                        }
                    }
                    foreach ($ikkClickhouseListHarian as $ikk) {
                        $ikk->status_pekerjaan = $statusByCode[$ikk->code ?? ''] ?? null;
                    }
                    // IKK cancel: status = SUBMITTED AND job_status = NOT_STARTED (definisi IKK cancel dari IPK ClickHouse)
                    $sqlCancel = "
                        SELECT work_permit_id
                        FROM hse_automation.ipk_assessment
                        WHERE work_permit_id IN ({$wpIdsEsc})
                          AND toDate(start_date) = toDate('{$dateEsc}')
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                          AND upper(trim(toString(status))) = 'SUBMITTED' AND upper(trim(toString(job_status))) = 'NOT_STARTED'
                    ";
                    $cancelRows = $ch->query($sqlCancel);
                    $cancelKodeIkk = [];
                    foreach ($cancelRows ?? [] as $r) {
                        $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                        if (isset($idToCode[$wpId])) {
                            $cancelKodeIkk[] = $idToCode[$wpId];
                        }
                    }
                    $cancelKodeIkk = array_values(array_unique($cancelKodeIkk));
                }
            }

            if (!$useChIpkOkk) {
                $kodeIkksForStatus = array_values(array_unique(array_filter(array_map(function ($ikk) {
                    return $ikk->code ?? null;
                }, $ikkClickhouseListHarian))));
                if (!empty($kodeIkksForStatus)) {
                    $statusRows = IpkIkk::whereIn('kode_ikk', $kodeIkksForStatus)
                        ->select(['kode_ikk', 'status_pekerjaan', 'ts'])
                        ->orderByDesc('ts')
                        ->get();
                    $statusByKode = [];
                    foreach ($statusRows as $row) {
                        $kode = $row->kode_ikk;
                        if ($kode !== null && $kode !== '' && !isset($statusByKode[$kode])) {
                            $statusByKode[$kode] = $row->status_pekerjaan;
                        }
                    }
                    foreach ($ikkClickhouseListHarian as $ikk) {
                        $code = $ikk->code ?? null;
                        $ikk->status_pekerjaan = ($code !== null && isset($statusByKode[$code]))
                            ? $statusByKode[$code]
                            : null;
                    }
                }
                $cancelKodeIkk = IpkIkk::whereDate('ts', $filterDate)
                    ->whereIn('status_pekerjaan', ['Batal', 'BATAL', 'Cancel', 'CANCEL'])
                    ->pluck('kode_ikk')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            }
        }

        // Kode work permit (non-cancel) untuk pre-load dan persentase
        $workPermitCodes = array_values(array_unique(array_filter(array_map(function ($ikk) use ($cancelKodeIkk) {
            $c = $ikk->code ?? '';
            if ($c === '' || $c === null) {
                return null;
            }
            if (in_array($c, $cancelKodeIkk, true)) {
                return null;
            }
            return $c;
        }, $ikkClickhouseListHarian))));

        // Pre-load IPK/OKK/OAK sekali untuk semua IKK (hindari N+1 di hitungStatusMatriksLengkapDenganAlasan)
        $ipkByKode = [];
        $okkByKode = [];
        $oakDataByLocation = [];
        if (!empty($workPermitCodes)) {
            if ($useChIpkOkk && !empty($ikkClickhouseListHarian) && class_exists(\App\Services\ClickHouseService::class)) {
                $ch = app(\App\Services\ClickHouseService::class);
                if (method_exists($ch, 'query') && $ch->isConnected()) {
                    $wpIdToCode = [];
                    foreach ($ikkClickhouseListHarian as $ikk) {
                        $id = $ikk->id ?? null;
                        $code = $ikk->code ?? null;
                        if ($id !== null && $code !== null && $code !== '' && in_array($code, $workPermitCodes, true)) {
                            $wpIdToCode[$id] = $code;
                        }
                    }
                    $wpIds = array_keys($wpIdToCode);
                    if (!empty($wpIds)) {
                        $wpIdsEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $wpIds));
                        $dateEsc = addslashes($filterDate);
                        $sqlIpk = "
                            SELECT id, work_permit_id, code, status, job_status, start_date, created_at, supervisor_id, cctv
                            FROM hse_automation.ipk_assessment
                            WHERE work_permit_id IN ({$wpIdsEsc})
                              AND toDate(start_date) = toDate('{$dateEsc}')
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                            ORDER BY created_at DESC
                        ";
                        $ipkRowsCh = $ch->query($sqlIpk);
                        foreach ($ipkRowsCh ?? [] as $r) {
                            $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                            $code = $wpIdToCode[$wpId] ?? null;
                            if ($code !== null && !isset($ipkByKode[$code])) {
                                $obj = (object) [
                                    'kode_ikk' => $code,
                                    'durasi_jam' => null,
                                    'status_pekerjaan' => self::getClickHouseRowValue($r, 'job_status'),
                                ];
                                $ipkByKode[$code] = $obj;
                            }
                        }
                        $sqlOkk = "
                            SELECT id, work_permit_id, code, status, created_at, supervisor_id, indirect_supervisor_id
                            FROM hse_automation.okk_assessment
                            WHERE work_permit_id IN ({$wpIdsEsc})
                              AND upper(trim(toString(status))) = 'SUBMITTED'
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                              AND toDate(created_at) = toDate('{$dateEsc}')
                            ORDER BY created_at ASC
                        ";
                        $okkRowsCh = $ch->query($sqlOkk);
                        $okkSupervisorIdsBatch = [];
                        $okkIndirectSupervisorIdsBatch = [];
                        foreach ($okkRowsCh ?? [] as $r) {
                            $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                            $code = $wpIdToCode[$wpId] ?? null;
                            if ($code !== null) {
                                if (!isset($okkByKode[$code])) {
                                    $okkByKode[$code] = collect();
                                }
                                $ts = self::getClickHouseRowValue($r, 'created_at');
                                $tsStr = self::formatClickHouseTsForAppTz($ts);
                                $supId = self::getClickHouseRowValue($r, 'supervisor_id');
                                $indirectSupId = self::getClickHouseRowValue($r, 'indirect_supervisor_id');
                                if ($supId !== null && $supId !== '') {
                                    $okkSupervisorIdsBatch[] = $supId;
                                }
                                if ($indirectSupId !== null && $indirectSupId !== '') {
                                    $okkIndirectSupervisorIdsBatch[] = $indirectSupId;
                                }
                                $okkByKode[$code]->push((object) [
                                    'supervisor_id' => $supId,
                                    'indirect_supervisor_id' => $indirectSupId,
                                    'ts' => $tsStr,
                                    'nama_pengawas' => null,
                                    'layer_pengawas' => null,
                                    'kode_sid' => null,
                                ]);
                            }
                        }
                        // Enrich OKK: kode_sid & layer dari ikk_work_permit_employee; OKK L2 up jika indirect_supervisor_id NOT NULL, layer dari row yang id = indirect_supervisor_id
                        if (!empty($okkSupervisorIdsBatch) || !empty($okkIndirectSupervisorIdsBatch)) {
                            try {
                                $supIdsUniq = array_values(array_unique(array_filter($okkSupervisorIdsBatch)));
                                $indirectIdsUniq = array_values(array_unique(array_filter($okkIndirectSupervisorIdsBatch)));
                                $allEmpIdsBatch = array_values(array_unique(array_merge($supIdsUniq, $indirectIdsUniq)));
                                $supEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $allEmpIdsBatch));
                                $sqlEmpOkk = "
                                    SELECT id, employee_name, employee_sid, layer
                                    FROM hse_automation.ikk_work_permit_employee
                                    WHERE id IN ({$supEsc})
                                      AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                                ";
                                $empOkkRows = $ch->query($sqlEmpOkk);
                                $employeeInfoOkkBatch = [];
                                foreach ($empOkkRows ?? [] as $er) {
                                    $eId = self::getClickHouseRowValue($er, 'id');
                                    if ($eId !== null) {
                                        $layerRaw = self::getClickHouseRowValue($er, 'layer');
                                        $employeeInfoOkkBatch[(string) $eId] = [
                                            'employee_name' => self::getClickHouseRowValue($er, 'employee_name'),
                                            'employee_sid' => self::getClickHouseRowValue($er, 'employee_sid'),
                                            'layer' => $layerRaw !== null && $layerRaw !== '' ? (string) $layerRaw : null,
                                        ];
                                    }
                                }
                                foreach ($okkByKode as $code => $okkList) {
                                    foreach ($okkList as $obj) {
                                        $sid = $obj->supervisor_id ?? null;
                                        $indirectSid = $obj->indirect_supervisor_id ?? null;
                                        // Layer & data employee: indirect_supervisor_id NULL = Layer 1 + data dari supervisor_id; NOT NULL = Layer 2+ + data dari indirect employee
                                        if ($indirectSid === null || $indirectSid === '') {
                                            $obj->layer_pengawas = '1';
                                            if ($sid !== null && isset($employeeInfoOkkBatch[(string) $sid])) {
                                                $info = $employeeInfoOkkBatch[(string) $sid];
                                                $obj->nama_pengawas = $info['employee_name'] ?? null;
                                                $obj->kode_sid = $info['employee_sid'] ?? null;
                                            }
                                        } else {
                                            $infoIndirect = $employeeInfoOkkBatch[(string) $indirectSid] ?? null;
                                            $layerVal = $infoIndirect !== null && isset($infoIndirect['layer']) && $infoIndirect['layer'] !== null && $infoIndirect['layer'] !== '' ? (string) $infoIndirect['layer'] : '2';
                                            $obj->layer_pengawas = $layerVal;
                                            if ($infoIndirect !== null) {
                                                $obj->nama_pengawas = $infoIndirect['employee_name'] ?? null;
                                                $obj->kode_sid = $infoIndirect['employee_sid'] ?? null;
                                            } elseif ($sid !== null && isset($employeeInfoOkkBatch[(string) $sid])) {
                                                $info = $employeeInfoOkkBatch[(string) $sid];
                                                $obj->nama_pengawas = $info['employee_name'] ?? null;
                                                $obj->kode_sid = $info['employee_sid'] ?? null;
                                            }
                                        }
                                        unset($obj->indirect_supervisor_id);
                                    }
                                }
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::debug('Dashboard weekly batch OKK enrich (matriks): ' . $e->getMessage());
                            }
                        }
                    }
                }
            }
            if (empty($ipkByKode) && empty($okkByKode)) {
                $ipkRows = IpkIkk::whereIn('kode_ikk', $workPermitCodes)
                    ->whereDate('ts', $filterDate)
                    ->orderByDesc('ts')
                    ->get();
                foreach ($ipkRows as $row) {
                    $k = $row->kode_ikk;
                    if (($k !== null && $k !== '') && !isset($ipkByKode[$k])) {
                        $ipkByKode[$k] = $row;
                    }
                }
                $okkRows = Okk::whereIn('kode_ikk', $workPermitCodes)
                    ->whereDate('ts', $filterDate)
                    ->orderBy('ts')
                    ->get();
                foreach ($okkRows as $row) {
                    $k = $row->kode_ikk;
                    if ($k === null || $k === '') {
                        continue;
                    }
                    if (!isset($okkByKode[$k])) {
                        $okkByKode[$k] = collect();
                    }
                    $okkByKode[$k]->push($row);
                }
            }
            // OAK: unik (location, detail_location) dari IKK non-cancel
            $locationPairsForOak = [];
            foreach ($ikkClickhouseListHarian as $ikk) {
                $code = $ikk->code ?? null;
                if ($code !== null && in_array($code, $cancelKodeIkk, true)) {
                    continue;
                }
                $loc = trim((string) ($ikk->location_name ?? ''));
                $det = trim((string) ($ikk->location_detail_name ?? ''));
                if ($loc !== '' && $det !== '') {
                    $locationPairsForOak[$loc . '|' . $det] = [$loc, $det];
                }
            }
            $locationPairsForOak = array_values($locationPairsForOak);
            if (!empty($locationPairsForOak)) {
                try {
                    if (class_exists(\App\Services\ClickHouseService::class)) {
                        $ch = app(\App\Services\ClickHouseService::class);
                        if (method_exists($ch, 'query') && $ch->isConnected()) {
                            $dateEsc = addslashes($filterDate);
                            foreach (['observee' => 'dic_mitra', 'observe' => 'bc'] as $tipe => $key) {
                                $conditions = [];
                                foreach ($locationPairsForOak as $p) {
                                    $locEsc = addslashes($p[0]);
                                    $detEsc = addslashes($p[1]);
                                    $conditions[] = "(lower(trim(toString(location))) = lower('{$locEsc}') AND lower(trim(toString(detail_location))) = lower('{$detEsc}'))";
                                }
                                $whereLoc = implode(' OR ', $conditions);
                                $sql = "SELECT location, detail_location FROM hse_automation.aaj_vw_car_oak_register_ytd_only"
                                    . " WHERE toDate(submit_date) = '{$dateEsc}'"
                                    . " AND lower(trim(toString(tipe))) = '" . addslashes($tipe) . "'"
                                    . " AND ({$whereLoc})"
                                    . " LIMIT 1000";
                                $res = $ch->query($sql);
                                foreach ($res ?? [] as $r) {
                                    $loc = trim((string) ($r['location'] ?? ''));
                                    $det = trim((string) ($r['detail_location'] ?? ''));
                                    if ($loc !== '' && $det !== '') {
                                        $k = $loc . '|' . $det;
                                        if (!isset($oakDataByLocation[$k])) {
                                            $oakDataByLocation[$k] = ['dic_mitra' => false, 'bc' => false];
                                        }
                                        $oakDataByLocation[$k][$key] = true;
                                    }
                                }
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::debug('Dashboard OAK pre-load: ' . $e->getMessage());
                }
            }
        }

        // Hitung status_matriks untuk IKK ClickHouse berdasarkan matriks lengkap (IPK + OKK + OAK),
        // dengan mengecualikan IKK yang sudah cancel. Pakai versi optimized dengan data yang sudah di-load.
        $chartMatriksLabels = ['Hijau', 'Kuning', 'Merah'];
        $chartIzinKerjaPerMatriks = [0, 0, 0];

        if (!empty($ikkClickhouseListHarian)) {
            $matriksIndex = [
                'Hijau' => 0,
                'Kuning' => 1,
                'Merah' => 2,
            ];

            foreach ($ikkClickhouseListHarian as $ikk) {
                $code = $ikk->code ?? null;
                $locationName = $ikk->location_name ?? null;
                $locationDetailName = $ikk->location_detail_name ?? null;

                if ($code !== null && in_array($code, $cancelKodeIkk, true)) {
                    $ikk->status_matriks = null;
                    continue;
                }

                $matriksResult = \App\Http\Controllers\DOPMIKK\DOPMController::hitungStatusMatriksLengkapDenganAlasanOptimized(
                    $code,
                    $locationName,
                    $locationDetailName,
                    $filterDate,
                    $ikk->nama_layer_1 ?? null,
                    $ikk->nama_layer_2 ?? null,
                    $ikk->nama_layer_3 ?? null,
                    $ikk->nama_layer_4 ?? null,
                    $ipkByKode[$code] ?? null,
                    $okkByKode[$code] ?? collect(),
                    $oakDataByLocation,
                    $ikk->sid_layer_1 ?? null,
                    $ikk->sid_layer_2 ?? null,
                    $ikk->sid_layer_3 ?? null,
                    $ikk->sid_layer_4 ?? null
                );

                $ikk->status_matriks = $matriksResult['status'] ?? 'Merah';
                $ikk->alasan_matriks = $matriksResult['alasan'] ?? 'Tidak diketahui';

                $status = $ikk->status_matriks ?? 'Merah';
                $status = in_array($status, ['Hijau', 'Kuning', 'Merah'], true) ? $status : 'Merah';

                $idx = $matriksIndex[$status];
                $chartIzinKerjaPerMatriks[$idx] = ($chartIzinKerjaPerMatriks[$idx] ?? 0) + 1;
            }
        }

        // Persentase IKK ada IPK / IKK ada OKK (workPermitCodes sudah dihitung di atas)
        if (!empty($workPermitCodes)) {
            if ($useChIpkOkk) {
                $ipkKodesWp = array_flip(array_keys($ipkByKode));
                $okkKodesWp = array_flip(array_keys($okkByKode));
            } else {
                $ipkKodesWp = IpkIkk::whereIn('kode_ikk', $workPermitCodes)
                    ->whereDate('ts', $filterDate)
                    ->select('kode_ikk')
                    ->distinct()
                    ->pluck('kode_ikk')
                    ->flip()
                    ->all();
                $okkKodesWp = Okk::whereIn('kode_ikk', $workPermitCodes)
                    ->whereDate('ts', $filterDate)
                    ->select('kode_ikk')
                    ->distinct()
                    ->pluck('kode_ikk')
                    ->flip()
                    ->all();
            }
            $totalIkkUnikHarian = count($workPermitCodes);
            $ikkAdaIpkCount = count(array_intersect_key($ipkKodesWp, array_flip($workPermitCodes)));
            $ikkAdaOkkCount = count(array_intersect_key($okkKodesWp, array_flip($workPermitCodes)));
            $pctIkkAdaIpk = $totalIkkUnikHarian > 0 ? round($ikkAdaIpkCount / $totalIkkUnikHarian * 100, 1) : 0;
            $pctIkkAdaOkk = $totalIkkUnikHarian > 0 ? round($ikkAdaOkkCount / $totalIkkUnikHarian * 100, 1) : 0;
        } else {
            $totalIkkUnikHarian = 0;
            $ikkAdaIpkCount = 0;
            $ikkAdaOkkCount = 0;
            $pctIkkAdaIpk = 0;
            $pctIkkAdaOkk = 0;
        }

        // Compliance IKK = rata-rata (pctIkkAdaIpk + pctIkkAdaOkk) / 2 — satu sumber kebenaran untuk view
        $pctPengisianRataRataIkk = round(((float) ($pctIkkAdaIpk ?? 0) + (float) ($pctIkkAdaOkk ?? 0)) / 2, 1);

        // Chart per jenis: 100% data IKK (work permit) — kategori & nilai dari ClickHouse IKK, bukan DOPM
        $chartJenisLabels = [];
        $chartIkkPerJenis = [];
        $chartIpkPerJenis = [];
        $chartOkkPerJenis = [];
        $chartIzinKerjaPerJenis = [];
        $chartMatriksPerJenis = [];
        $chartJenisLabelsFull = [];
        $jenisFromIkk = [];
        foreach ($ikkClickhouseListHarian as $ikk) {
            $j = trim((string) ($ikk->jenis_ijin_kerja_khusus ?? '')) ?: '-';
            $jenisFromIkk[$j] = true;
        }
        $chartJenisKeysFromIkk = array_keys($jenisFromIkk);
        usort($chartJenisKeysFromIkk, function ($a, $b) {
            return strnatcasecmp($a, $b);
        });
        // Satu kali query hitung IPK/OKK per kode_ikk (untuk semua jenis), hindari N+1
        $ipkCountByKode = [];
        $okkCountByKode = [];
        if (!empty($workPermitCodes)) {
            if ($useChIpkOkk && !empty($ikkClickhouseListHarian) && class_exists(\App\Services\ClickHouseService::class)) {
                $ch = app(\App\Services\ClickHouseService::class);
                if (method_exists($ch, 'query') && $ch->isConnected()) {
                    $wpIdToCode = [];
                    foreach ($ikkClickhouseListHarian as $ikk) {
                        $id = $ikk->id ?? null;
                        $code = $ikk->code ?? null;
                        if ($id !== null && $code !== null && in_array($code, $workPermitCodes, true)) {
                            $wpIdToCode[$id] = $code;
                        }
                    }
                    $wpIds = array_keys($wpIdToCode);
                    if (!empty($wpIds)) {
                        $wpIdsEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $wpIds));
                        $dateEsc = addslashes($filterDate);
                        $sqlIpkCnt = "
                            SELECT work_permit_id, count() AS cnt
                            FROM hse_automation.ipk_assessment
                            WHERE work_permit_id IN ({$wpIdsEsc})
                              AND toDate(start_date) = toDate('{$dateEsc}')
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                            GROUP BY work_permit_id
                        ";
                        $ipkCntRows = $ch->query($sqlIpkCnt);
                        foreach ($ipkCntRows ?? [] as $r) {
                            $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                            $code = $wpIdToCode[$wpId] ?? null;
                            if ($code !== null) {
                                $ipkCountByKode[$code] = (int) (self::getClickHouseRowValue($r, 'cnt') ?? 0);
                            }
                        }
                        $sqlOkkCnt = "
                            SELECT work_permit_id, count() AS cnt
                            FROM hse_automation.okk_assessment
                            WHERE work_permit_id IN ({$wpIdsEsc})
                              AND upper(trim(toString(status))) = 'SUBMITTED'
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                              AND toDate(created_at) = toDate('{$dateEsc}')
                            GROUP BY work_permit_id
                        ";
                        $okkCntRows = $ch->query($sqlOkkCnt);
                        foreach ($okkCntRows ?? [] as $r) {
                            $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                            $code = $wpIdToCode[$wpId] ?? null;
                            if ($code !== null) {
                                $okkCountByKode[$code] = (int) (self::getClickHouseRowValue($r, 'cnt') ?? 0);
                            }
                        }
                    }
                }
            }
            if (empty($ipkCountByKode) && empty($okkCountByKode)) {
                $ipkCountByKode = IpkIkk::whereDate('ts', $filterDate)
                    ->whereIn('kode_ikk', $workPermitCodes)
                    ->selectRaw('kode_ikk, count(*) as cnt')
                    ->groupBy('kode_ikk')
                    ->pluck('cnt', 'kode_ikk')
                    ->all();
                $okkCountByKode = Okk::whereDate('ts', $filterDate)
                    ->whereIn('kode_ikk', $workPermitCodes)
                    ->selectRaw('kode_ikk, count(*) as cnt')
                    ->groupBy('kode_ikk')
                    ->pluck('cnt', 'kode_ikk')
                    ->all();
            }
        }
        foreach ($chartJenisKeysFromIkk as $jenis) {
            $chartJenisLabels[] = self::singkatJenisIjin($jenis);
            $chartJenisLabelsFull[] = $jenis;
            $ikkPerJenis = array_filter($ikkClickhouseListHarian, function ($ikk) use ($jenis, $cancelKodeIkk) {
                $j = trim((string) ($ikk->jenis_ijin_kerja_khusus ?? '')) ?: '-';
                $c = $ikk->code ?? null;
                if ($c !== null && in_array($c, $cancelKodeIkk, true)) {
                    return false;
                }
                return $j === $jenis;
            });

            $izinCount = count($ikkPerJenis);
            $chartIkkPerJenis[] = $izinCount;
            $chartIzinKerjaPerJenis[] = $izinCount;

            $statusScore = -1;
            foreach ($ikkPerJenis as $ikkRow) {
                $st = $ikkRow->status_matriks ?? 'Merah';
                if ($st === 'Merah') {
                    $score = 2;
                } elseif ($st === 'Kuning') {
                    $score = 1;
                } else {
                    $score = 0;
                }
                if ($score > $statusScore) {
                    $statusScore = $score;
                }
            }
            if ($statusScore <= 0) {
                $chartMatriksPerJenis[] = 'Hijau';
            } elseif ($statusScore === 1) {
                $chartMatriksPerJenis[] = 'Kuning';
            } else {
                $chartMatriksPerJenis[] = 'Merah';
            }

            $codesJenis = array_values(array_unique(array_filter(array_map(function ($ikk) {
                $c = $ikk->code ?? '';
                return $c !== '' && $c !== null ? $c : null;
            }, $ikkPerJenis))));
            $chartIpkPerJenis[] = empty($codesJenis) ? 0 : array_sum(array_map(fn ($c) => (int) ($ipkCountByKode[$c] ?? 0), $codesJenis));
            $chartOkkPerJenis[] = empty($codesJenis) ? 0 : array_sum(array_map(fn ($c) => (int) ($okkCountByKode[$c] ?? 0), $codesJenis));
        }

        // Total OAK harian: dari IKK (lokasi + detail lokasi), sama konsep dengan modal — match OAK by location & detail_location
        $locationPairs = [];
        foreach ($ikkClickhouseListHarian as $ikk) {
            // IKK cancel tidak dihitung ke OAK harian untuk matriks
            $code = $ikk->code ?? null;
            if ($code !== null && in_array($code, $cancelKodeIkk, true)) {
                continue;
            }
            $loc = trim((string) ($ikk->location_name ?? ''));
            $det = trim((string) ($ikk->location_detail_name ?? ''));
            if ($loc !== '' && $det !== '') {
                $key = $loc . '|' . $det;
                $locationPairs[$key] = [$loc, $det];
            }
        }
        $locationPairs = array_values($locationPairs);
        if (!empty($locationPairs)) {
            try {
                if (class_exists(\App\Services\ClickHouseService::class)) {
                    $clickHouse = app(\App\Services\ClickHouseService::class);
                    if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                        $dateEsc = addslashes($filterDate);
                        $conditions = [];
                        foreach ($locationPairs as $pair) {
                            $locEsc = addslashes($pair[0]);
                            $detEsc = addslashes($pair[1]);
                            $conditions[] = "(lower(trim(toString(location))) = lower('{$locEsc}') AND lower(trim(toString(detail_location))) = lower('{$detEsc}'))";
                        }
                        $whereLoc = implode(' OR ', $conditions);
                        $sqlOakCount = "SELECT count() as cnt FROM hse_automation.aaj_vw_car_oak_register_ytd_only"
                            . " WHERE toDate(submit_date) = '{$dateEsc}'"
                            . " AND lower(trim(toString(tipe))) = 'observer'"
                            . " AND ({$whereLoc})";
                        $oakCountResult = $clickHouse->query($sqlOakCount);
                        $totalOakHarian = isset($oakCountResult[0]['cnt']) ? (int) $oakCountResult[0]['cnt'] : 0;
                        $pctDopmOak = $totalDopmHarian > 0 ? min(100.0, round($totalOakHarian / $totalDopmHarian * 100, 1)) : 0;
                        $pctPengisianRataRata = round(($pctDopmAdaIpk + $pctDopmAdaOkk + $pctDopmOak) / 3, 1);
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::debug('Dashboard OAK harian by IKK location: ' . $e->getMessage());
            }
        }

        // Compliance per hari untuk kalender (bulan yang berisi filterDate)
        $complianceByDay = [];
        $monthStart = Carbon::parse($filterDate)->startOfMonth()->format('Y-m-d');
        $monthEnd = Carbon::parse($filterDate)->endOfMonth()->format('Y-m-d');
        try {
            if (class_exists(\App\Services\ClickHouseService::class)) {
                $clickHouse = app(\App\Services\ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    $siteFilterClause = '';
                    if ($filterSite !== '' && $filterSite !== null) {
                        if ($filterSite === 'Lainnya') {
                            $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = ''";
                        } else {
                            $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                        }
                    }
                    // Kalender: IKK yang rentang (start_date–end_date) mencakup bulan, sama konsep dengan kartu (rentang mencakup tanggal)
                    $sqlMonth = "
                        SELECT wp.id AS id, wp.code AS code, wp.start_date AS start_date, wp.end_date AS end_date
                        FROM hse_automation.ikk_work_permit AS wp
                        INNER JOIN hse_automation.ikk_work_permit_pic AS wp_pic
                            ON toString(wp_pic.work_permit_id) = toString(wp.id)
                            AND (wp_pic.deleted_at IS NULL OR wp_pic.deleted_at = toDateTime(0))
                        LEFT JOIN hse_automation.ikk_m_pic AS m
                            ON toString(m.id) = toString(wp_pic.m_pic_id)
                        WHERE (wp.deleted_at IS NULL OR wp.deleted_at = toDateTime(0))
                            AND toDate(wp.start_date) <= toDate('" . addslashes($monthEnd) . "')
                            AND toDate(wp.end_date)   >= toDate('" . addslashes($monthStart) . "')
                            {$siteFilterClause}
                        GROUP BY wp.id, wp.code, wp.start_date, wp.end_date
                        HAVING sum(if(upper(trim(toString(wp_pic.status))) = 'APPROVED'
                            AND trim(toString(m.m_privilege_id)) = '7d872114-0924-4c6a-880e-49b3c06b5429', 1, 0)) > 0
                    ";
                    $wpRowsMonth = $clickHouse->query($sqlMonth);
                    $codesPerDay = [];
                    $monthIdToCode = [];
                    if (! empty($wpRowsMonth)) {
                        $monthStartCarbon = Carbon::parse($monthStart)->startOfDay();
                        $monthEndCarbon = Carbon::parse($monthEnd)->endOfDay();
                        $daysInMonth = (int) $monthEndCarbon->day;
                        foreach ($wpRowsMonth as $row) {
                            $id = self::getClickHouseRowValue($row, 'id');
                            $code = isset($row['code']) ? trim((string) $row['code']) : '';
                            if ($id !== null && $code !== '') {
                                $monthIdToCode[$id] = $code;
                            }
                            if ($code === '') {
                                continue;
                            }
                            $startDate = self::parseEndDate(self::getClickHouseRowValue($row, 'start_date'));
                            $endDate = self::parseEndDate(self::getClickHouseRowValue($row, 'end_date'));
                            if ($startDate === null || $endDate === null) {
                                continue;
                            }
                            // Bandingkan berdasarkan tanggal saja (startOfDay) agar IKK yang start jam 07:00 tetap masuk untuk hari itu
                            $startDay = $startDate->copy()->startOfDay();
                            $endDay = $endDate->copy()->startOfDay();
                            // Untuk setiap hari di bulan, masukkan code jika rentang (start–end) mencakup hari itu
                            for ($day = 1; $day <= $daysInMonth; $day++) {
                                $dayDate = $monthStartCarbon->copy()->addDays($day - 1)->startOfDay();
                                if ($startDay->lte($dayDate) && $endDay->gte($dayDate)) {
                                    $d = $dayDate->format('Y-m-d');
                                    if (! isset($codesPerDay[$d])) {
                                        $codesPerDay[$d] = [];
                                    }
                                    $codesPerDay[$d][$code] = true;
                                }
                            }
                        }
                        foreach ($codesPerDay as $d => $codes) {
                            $codesPerDay[$d] = array_keys($codes);
                        }
                    }

                    $cancelPerDay = [];
                    $cancelRows = IpkIkk::whereIn('status_pekerjaan', ['Batal', 'BATAL', 'Cancel', 'CANCEL'])
                        ->whereBetween('ts', [$monthStart, $monthEnd])
                        ->get(['ts', 'kode_ikk']);
                    foreach ($cancelRows as $r) {
                        $d = Carbon::parse($r->ts)->format('Y-m-d');
                        $k = trim((string) ($r->kode_ikk ?? ''));
                        if ($k !== '') {
                            if (! isset($cancelPerDay[$d])) {
                                $cancelPerDay[$d] = [];
                            }
                            $cancelPerDay[$d][$k] = true;
                        }
                    }

                    // Compliance per hari: IKK yang punya IPK/OKK **pada tanggal yang sama** (selaras dengan summary tanggal terpilih)
                    $codesWithIpkByDay = [];
                    $codesWithOkkByDay = [];
                    $cutoffDtCompliance = Carbon::parse(config('dopm.ipk_okk_clickhouse_cutoff_date', '2025-02-20'))->startOfDay();
                    $cutoffStrCompliance = $cutoffDtCompliance->format('Y-m-d');
                    if (!empty($monthIdToCode) && class_exists(\App\Services\ClickHouseService::class)) {
                        $chMonth = app(\App\Services\ClickHouseService::class);
                        if (method_exists($chMonth, 'query') && $chMonth->isConnected()) {
                            $wpIdsMonth = array_keys($monthIdToCode);
                            $wpIdsMonthEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $wpIdsMonth));
                            $monthStartEsc = addslashes($monthStart);
                            $monthEndEsc = addslashes($monthEnd);
                            // ClickHouse IPK: per tanggal (start_date) — kode yang punya IPK pada tanggal itu
                            $sqlIpkMonth = "
                                SELECT toDate(start_date) AS d, work_permit_id
                                FROM hse_automation.ipk_assessment
                                WHERE work_permit_id IN ({$wpIdsMonthEsc})
                                  AND toDate(start_date) >= toDate('{$monthStartEsc}')
                                  AND toDate(start_date) <= toDate('{$monthEndEsc}')
                                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                            ";
                            $ipkMonthRows = $chMonth->query($sqlIpkMonth);
                            foreach ($ipkMonthRows ?? [] as $r) {
                                $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                                $dateVal = self::getClickHouseRowValue($r, 'd');
                                if ($wpId === null || $dateVal === null) {
                                    continue;
                                }
                                $d = $dateVal instanceof \Carbon\Carbon ? $dateVal->format('Y-m-d') : (is_string($dateVal) ? $dateVal : null);
                                if ($d === null) {
                                    continue;
                                }
                                $code = $monthIdToCode[$wpId] ?? null;
                                if ($code !== null) {
                                    if (!isset($codesWithIpkByDay[$d])) {
                                        $codesWithIpkByDay[$d] = [];
                                    }
                                    $codesWithIpkByDay[$d][$code] = true;
                                }
                            }
                            // ClickHouse OKK: per tanggal (created_at) — kode yang punya OKK SUBMITTED pada tanggal itu
                            $sqlOkkMonth = "
                                SELECT toDate(created_at) AS d, work_permit_id
                                FROM hse_automation.okk_assessment
                                WHERE work_permit_id IN ({$wpIdsMonthEsc})
                                  AND upper(trim(toString(status))) = 'SUBMITTED'
                                  AND toDate(created_at) >= toDate('{$monthStartEsc}')
                                  AND toDate(created_at) <= toDate('{$monthEndEsc}')
                                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                            ";
                            $okkMonthRows = $chMonth->query($sqlOkkMonth);
                            foreach ($okkMonthRows ?? [] as $r) {
                                $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                                $dateVal = self::getClickHouseRowValue($r, 'd');
                                if ($wpId === null || $dateVal === null) {
                                    continue;
                                }
                                $d = $dateVal instanceof \Carbon\Carbon ? $dateVal->format('Y-m-d') : (is_string($dateVal) ? $dateVal : null);
                                if ($d === null) {
                                    continue;
                                }
                                $code = $monthIdToCode[$wpId] ?? null;
                                if ($code !== null) {
                                    if (!isset($codesWithOkkByDay[$d])) {
                                        $codesWithOkkByDay[$d] = [];
                                    }
                                    $codesWithOkkByDay[$d][$code] = true;
                                }
                            }
                        }
                    }
                    // MySQL (sebelum cutoff): IPK/OKK per tanggal dalam bulan, merge ke per-hari
                    $ipkRows = IpkIkk::whereBetween('ts', [$monthStart, $monthEnd])
                        ->where('ts', '<', $cutoffStrCompliance)
                        ->get(['ts', 'kode_ikk']);
                    foreach ($ipkRows as $row) {
                        $d = Carbon::parse($row->ts)->format('Y-m-d');
                        $k = trim((string) ($row->kode_ikk ?? ''));
                        if ($k !== '') {
                            if (!isset($codesWithIpkByDay[$d])) {
                                $codesWithIpkByDay[$d] = [];
                            }
                            $codesWithIpkByDay[$d][$k] = true;
                        }
                    }
                    $okkRows = Okk::whereBetween('ts', [$monthStart, $monthEnd])
                        ->where('ts', '<', $cutoffStrCompliance)
                        ->get(['ts', 'kode_ikk']);
                    foreach ($okkRows as $row) {
                        $d = Carbon::parse($row->ts)->format('Y-m-d');
                        $k = trim((string) ($row->kode_ikk ?? ''));
                        if ($k !== '') {
                            if (!isset($codesWithOkkByDay[$d])) {
                                $codesWithOkkByDay[$d] = [];
                            }
                            $codesWithOkkByDay[$d][$k] = true;
                        }
                    }

                    $daysInMonth = (int) Carbon::parse($monthEnd)->day;
                    for ($day = 1; $day <= $daysInMonth; $day++) {
                        $d = Carbon::parse($monthStart)->addDays($day - 1)->format('Y-m-d');
                        $codes = $codesPerDay[$d] ?? [];
                        $cancelCodes = isset($cancelPerDay[$d]) ? array_keys($cancelPerDay[$d]) : [];
                        $codes = array_values(array_diff($codes, $cancelCodes));
                        $total = count($codes);
                        if ($total === 0) {
                            $complianceByDay[$d] = null;
                            continue;
                        }
                        $ipkCount = 0;
                        $okkCount = 0;
                        $ipkByDay = $codesWithIpkByDay[$d] ?? [];
                        $okkByDay = $codesWithOkkByDay[$d] ?? [];
                        foreach ($codes as $c) {
                            if (isset($ipkByDay[$c])) {
                                $ipkCount++;
                            }
                            if (isset($okkByDay[$c])) {
                                $okkCount++;
                            }
                        }
                        $pctIpk = $total > 0 ? ($ipkCount / $total * 100) : 0;
                        $pctOkk = $total > 0 ? ($okkCount / $total * 100) : 0;
                        $complianceByDay[$d] = round(($pctIpk + $pctOkk) / 2, 1);
                    }

                    // Selaraskan kalender dengan kartu: untuk tanggal filter gunakan nilai yang sama (pctPengisianRataRataIkk)
                    if (isset($pctPengisianRataRataIkk) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterDate)) {
                        $complianceByDay[$filterDate] = $pctPengisianRataRataIkk;
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Dashboard complianceByDay: ' . $e->getMessage());
        }

        return view('dopmikk.dopm.dashboard-weekly', [
            'filterDate' => $filterDate,
            'filterSite' => $filterSite,
            'siteList' => $siteList,
            // Weekly filter data
            'filterWeek' => $filterWeek,
            'weekNumber' => $weekNumber,
            'weekYear' => $weekYear,
            'weekStartDate' => $weekStartDate->format('d M'),
            'weekEndDate' => $weekEndDate->format('d M Y'),
            'weekList' => $weekList,
            // Weekly statistics (distinct work permit)
            'totalIkkWeekly' => $totalIkkWeekly,
            'ikkAdaIpkCountWeekly' => $ikkAdaIpkCountWeekly,
            'ikkAdaOkkCountWeekly' => $ikkAdaOkkCountWeekly,
            'pctIkkAdaIpkWeekly' => $pctIkkAdaIpkWeekly,
            'pctIkkAdaOkkWeekly' => $pctIkkAdaOkkWeekly,
            'pctComplianceWeekly' => $pctComplianceWeekly,
            'totalPekerjaanBatalWeekly' => $totalPekerjaanBatalWeekly,
            'totalDopmHarian' => $totalDopmHarian,
            'totalWorkPermitApprovedHarian' => $totalWorkPermitApprovedHarian,
            'totalDopmCancelHarian' => $totalDopmCancelHarian,
            'totalDopmMingguIni' => $totalDopmMingguIni,
            'totalIkkClickhouseMingguIni' => $totalIkkClickhouseMingguIni,
            'chartIkkClickhousePerHariMinggu' => $chartIkkClickhousePerHariMinggu,
            'totalPekerjaanBatalHarian' => $totalPekerjaanBatalHarian,
            'totalIkkHarian' => $totalIkkHarian,
            'totalOkkHarian' => $totalOkkHarian,
            'totalOakHarian' => $totalOakHarian,
            'dopmListHarian' => $dopmListHarian,
            'totalIkkUnikHarian' => $totalIkkUnikHarian,
            'pctIkkAdaIpk' => $pctIkkAdaIpk,
            'pctIkkAdaOkk' => $pctIkkAdaOkk,
            'pctPengisianRataRataIkk' => $pctPengisianRataRataIkk,
            'pctDopmAdaIpk' => $pctDopmAdaIpk,
            'pctDopmAdaOkk' => $pctDopmAdaOkk,
            'pctDopmOak' => $pctDopmOak,
            'pctPengisianRataRata' => $pctPengisianRataRata,
            'ikkAdaIpkCount' => $ikkAdaIpkCount,
            'ikkAdaOkkCount' => $ikkAdaOkkCount,
            'summaryBySite' => $summaryBySite,
            'summaryJenisKeys' => $summaryJenisKeys,
            'chartJenisLabels' => $chartJenisLabels,
            'chartJenisLabelsFull' => $chartJenisLabelsFull,
            'chartDopmPerJenis' => $chartDopmPerJenis,
            'chartIkkPerJenis' => $chartIkkPerJenis,
            'chartIpkPerJenis' => $chartIpkPerJenis,
            'chartOkkPerJenis' => $chartOkkPerJenis,
            'chartIzinKerjaPerJenis' => $chartIzinKerjaPerJenis,
            'chartMatriksPerJenis' => $chartMatriksPerJenis,
            'chartMatriksLabels' => $chartMatriksLabels,
            'chartIzinKerjaPerMatriks' => $chartIzinKerjaPerMatriks,
            'ikkClickhouseListHarian' => $ikkClickhouseListHarian,
            'complianceByDay' => $complianceByDay,
        ]);
    }

    /**
     * API data untuk modal detail DOPM: IPK-IKK, OKK, OAK (by kode_ikk dan konteks).
     */
    public function getDetailModalData(Request $request): JsonResponse
    {
        $kodeIkk = $request->input('kode_ikk', '');
        $jenisIjin = $request->input('jenis_ijin_kerja_khusus', '');
        $namaLayer2 = $request->input('nama_layer_2', '');
        $namaLayer3 = $request->input('nama_layer_3', '');
        $namaLayer4 = $request->input('nama_layer_4', '');
        $sidLayer2 = $request->input('sid_layer_2', '');
        $sidLayer3 = $request->input('sid_layer_3', '');
        $sidLayer4 = $request->input('sid_layer_4', '');
        $locationName = $request->input('location_name', '');
        $locationDetailName = $request->input('location_detail_name', '');

        $ipkIkk = [];
        $okk = [];
        $oak = [];
        $ipkSource = 'mysql';
        $okkSource = 'mysql';

        $tanggalDop = trim((string) $request->input('tanggal_dop', ''));
        $filterDateModal = $tanggalDop !== '' ? (function () use ($tanggalDop) {
            try {
                return Carbon::parse($tanggalDop)->format('Y-m-d');
            } catch (\Throwable $e) {
                return date('Y-m-d');
            }
        })() : date('Y-m-d');
        $useChModal = self::useClickHouseForIpkOkk($filterDateModal);
        $workPermitId = trim((string) $request->input('work_permit_id', ''));

        // Hanya gunakan ClickHouse untuk IPK/OKK jika tanggal_dop >= cutoff. Di bawah cutoff selalu MySQL (ipk_ikk, okk).
        if ($kodeIkk !== '' && $kodeIkk !== null) {
            if ($useChModal && class_exists(\App\Services\ClickHouseService::class)) {
                $ch = app(\App\Services\ClickHouseService::class);
                if (method_exists($ch, 'query') && $ch->isConnected()) {
                    $wpId = $workPermitId !== '' ? $workPermitId : null;
                    if ($wpId === null) {
                        $codeEsc = addslashes($kodeIkk);
                        $sqlResolve = "
                            SELECT id FROM hse_automation.ikk_work_permit
                            WHERE trim(toString(code)) = '{$codeEsc}'
                              AND toDate(start_date) = toDate('" . addslashes($filterDateModal) . "')
                            LIMIT 1
                        ";
                        $wpRow = $ch->query($sqlResolve);
                        if (!empty($wpRow[0])) {
                            $wpId = self::getClickHouseRowValue($wpRow[0], 'id');
                            $wpId = $wpId !== null ? (string) $wpId : null;
                        }
                    }
                    if ($wpId !== null && $wpId !== '') {
                        $wpIdEsc = addslashes($wpId);
                        $dateEsc = addslashes($filterDateModal);
                        $sqlIpkModal = "
                            SELECT id, code, work_permit_id, status, job_status, start_date, created_at, supervisor_id, cctv, m_job_duration_id
                            FROM hse_automation.ipk_assessment
                            WHERE work_permit_id = '{$wpIdEsc}'
                              AND toDate(start_date) = toDate('{$dateEsc}')
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                            ORDER BY created_at DESC
                        ";
                        $ipkRowsCh = $ch->query($sqlIpkModal);
                        $assessmentIds = [];
                        $durationIds = [];
                        foreach ($ipkRowsCh ?? [] as $r) {
                            $ts = self::getClickHouseRowValue($r, 'start_date') ?? self::getClickHouseRowValue($r, 'created_at');
                            $tsStr = self::formatClickHouseTsForAppTz($ts);
                            $aid = self::getClickHouseRowValue($r, 'id');
                            $durId = self::getClickHouseRowValue($r, 'm_job_duration_id');
                            if ($aid !== null && $aid !== '') {
                                $assessmentIds[] = $aid;
                            }
                            if ($durId !== null && $durId !== '') {
                                $durationIds[] = $durId;
                            }
                            $ipkIkk[] = [
                                'assessment_id' => $aid,
                                'm_job_duration_id' => $durId,
                                'ts' => $tsStr,
                                'nama_pengawas' => null,
                                'kode_sid' => null,
                                'kode_ikk' => $kodeIkk,
                                'nama_perusahaan' => null,
                                'site' => null,
                                'durasi_jam' => null,
                                'cctv_terekam' => self::getClickHouseRowValue($r, 'cctv'),
                                'kategori_ijk' => null,
                                'status_pekerjaan' => self::getClickHouseRowValue($r, 'job_status'),
                            ];
                        }
                        if (!empty($assessmentIds)) {
                            try {
                                $aidList = array_unique(array_filter($assessmentIds));
                                $aidEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $aidList));
                                $sqlAnswer = "
                                    SELECT assessment_id, argMax(employee_id, created_at) AS employee_id
                                    FROM hse_automation.ipk_assessment_answer
                                    WHERE assessment_id IN ({$aidEsc})
                                      AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                                    GROUP BY assessment_id
                                ";
                                $answerRows = $ch->query($sqlAnswer);
                                $assessmentToEmployee = [];
                                $employeeIds = [];
                                foreach ($answerRows ?? [] as $ar) {
                                    $aId = self::getClickHouseRowValue($ar, 'assessment_id');
                                    $eId = self::getClickHouseRowValue($ar, 'employee_id');
                                    if ($aId !== null && $eId !== null && $eId !== '') {
                                        $assessmentToEmployee[(string) $aId] = $eId;
                                        $employeeIds[] = $eId;
                                    }
                                }
                                if (!empty($employeeIds)) {
                                    $eidList = array_unique($employeeIds);
                                    $eidEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $eidList));
                                    $sqlEmp = "
                                        SELECT id, employee_name, employee_sid, company_name
                                        FROM hse_automation.ikk_work_permit_employee
                                        WHERE id IN ({$eidEsc})
                                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                                    ";
                                    $empRows = $ch->query($sqlEmp);
                                    $employeeInfo = [];
                                    foreach ($empRows ?? [] as $er) {
                                        $eId = self::getClickHouseRowValue($er, 'id');
                                        if ($eId !== null) {
                                            $employeeInfo[(string) $eId] = [
                                                'employee_name' => self::getClickHouseRowValue($er, 'employee_name'),
                                                'employee_sid' => self::getClickHouseRowValue($er, 'employee_sid'),
                                                'company_name' => self::getClickHouseRowValue($er, 'company_name'),
                                            ];
                                        }
                                    }
                                    foreach ($ipkIkk as $idx => $row) {
                                        $aid = $row['assessment_id'] ?? null;
                                        if ($aid !== null && isset($assessmentToEmployee[(string) $aid]) && isset($employeeInfo[(string) $assessmentToEmployee[(string) $aid]])) {
                                            $info = $employeeInfo[(string) $assessmentToEmployee[(string) $aid]];
                                            $ipkIkk[$idx]['nama_pengawas'] = $info['employee_name'] ?? null;
                                            $ipkIkk[$idx]['kode_sid'] = $info['employee_sid'] ?? null;
                                            $ipkIkk[$idx]['nama_perusahaan'] = $info['company_name'] ?? null;
                                        }
                                        unset($ipkIkk[$idx]['assessment_id']);
                                    }
                                } else {
                                    foreach ($ipkIkk as $idx => $row) {
                                        unset($ipkIkk[$idx]['assessment_id']);
                                    }
                                }
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::debug('Dashboard weekly modal IPK enrich: ' . $e->getMessage());
                                foreach ($ipkIkk as $idx => $row) {
                                    unset($ipkIkk[$idx]['assessment_id']);
                                }
                            }
                        } else {
                            foreach ($ipkIkk as $idx => $row) {
                                unset($ipkIkk[$idx]['assessment_id']);
                            }
                        }
                        if (!empty($ipkIkk)) {
                            try {
                                $wpSite = null;
                                $wpKategoriIjk = null;
                                $sqlWpIpk = "
                                    SELECT ra_site_name, m_job_id FROM hse_automation.ikk_work_permit
                                    WHERE id = '{$wpIdEsc}' LIMIT 1
                                ";
                                $wpInfoIpk = $ch->query($sqlWpIpk);
                                if (!empty($wpInfoIpk[0])) {
                                    $wpSite = self::getClickHouseRowValue($wpInfoIpk[0], 'ra_site_name');
                                    $jobId = self::getClickHouseRowValue($wpInfoIpk[0], 'm_job_id');
                                    if ($jobId !== null && $jobId !== '') {
                                        $jobIdEsc = addslashes((string) $jobId);
                                        $sqlJobIpk = "SELECT name FROM hse_automation.ikk_m_job WHERE id = '{$jobIdEsc}' LIMIT 1";
                                        $jobRowIpk = $ch->query($sqlJobIpk);
                                        if (!empty($jobRowIpk[0])) {
                                            $wpKategoriIjk = self::getClickHouseRowValue($jobRowIpk[0], 'name');
                                        }
                                    }
                                }
                                $durationById = [
                                    '6606dfe7-5df0-4d9e-9de3-7d49014d3b6b' => '9 jam',
                                    '3032f5de-2bfe-4fe6-a791-8ecfda4fc1fc' => '6 jam',
                                    '86390fff-42c2-4f31-aee5-953439312aa5' => '3 jam',
                                    '7de308e6-bde0-40d2-9411-d517fc5dc9c9' => 'Mengikuti durasi IKK',
                                ];
                                foreach ($ipkIkk as $idx => $row) {
                                    $ipkIkk[$idx]['site'] = $wpSite;
                                    $ipkIkk[$idx]['kategori_ijk'] = $wpKategoriIjk;
                                    $durId = $row['m_job_duration_id'] ?? null;
                                    if ($durId !== null && isset($durationById[(string) $durId])) {
                                        $ipkIkk[$idx]['durasi_jam'] = $durationById[(string) $durId];
                                    }
                                    unset($ipkIkk[$idx]['m_job_duration_id']);
                                }
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::debug('Dashboard weekly modal IPK site/kategori/durasi: ' . $e->getMessage());
                                foreach ($ipkIkk as $idx => $row) {
                                    unset($ipkIkk[$idx]['m_job_duration_id']);
                                }
                            }
                        }
                        $sqlOkkModal = "
                            SELECT id, code, work_permit_id, status, created_at, supervisor_id, indirect_supervisor_id
                            FROM hse_automation.okk_assessment
                            WHERE work_permit_id = '{$wpIdEsc}'
                              AND upper(trim(toString(status))) = 'SUBMITTED'
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                              AND toDate(created_at) = toDate('{$dateEsc}')
                            ORDER BY created_at ASC
                        ";
                        $okkRowsCh = $ch->query($sqlOkkModal);
                        $okkSupervisorIds = [];
                        $okkIndirectSupervisorIds = [];
                        foreach ($okkRowsCh ?? [] as $r) {
                            $ts = self::getClickHouseRowValue($r, 'created_at');
                            $tsStr = self::formatClickHouseTsForAppTz($ts);
                            $supId = self::getClickHouseRowValue($r, 'supervisor_id');
                            $indirectSupId = self::getClickHouseRowValue($r, 'indirect_supervisor_id');
                            if ($supId !== null && $supId !== '') {
                                $okkSupervisorIds[] = $supId;
                            }
                            if ($indirectSupId !== null && $indirectSupId !== '') {
                                $okkIndirectSupervisorIds[] = $indirectSupId;
                            }
                            $okk[] = [
                                'supervisor_id' => $supId,
                                'indirect_supervisor_id' => $indirectSupId,
                                'ts' => $tsStr,
                                'code' => self::getClickHouseRowValue($r, 'code'),
                                'nama_pengawas' => null,
                                'kode_sid' => null,
                                'kode_ikk' => $kodeIkk,
                                'nama_perusahaan' => null,
                                'site' => null,
                                'jenis_ijk' => null,
                                'layer_pengawas' => null,
                            ];
                        }
                        if (!empty($okk)) {
                            try {
                                $supIds = array_unique(array_filter($okkSupervisorIds));
                                $indirectIds = array_unique(array_filter($okkIndirectSupervisorIds));
                                $allEmpIds = array_values(array_unique(array_merge($supIds, $indirectIds)));
                                $wpSite = null;
                                $wpJenisIjk = null;
                                $sqlWp = "
                                    SELECT ra_site_name, m_job_id FROM hse_automation.ikk_work_permit
                                    WHERE id = '{$wpIdEsc}' LIMIT 1
                                ";
                                $wpInfo = $ch->query($sqlWp);
                                if (!empty($wpInfo[0])) {
                                    $wpSite = self::getClickHouseRowValue($wpInfo[0], 'ra_site_name');
                                    $jobId = self::getClickHouseRowValue($wpInfo[0], 'm_job_id');
                                    if ($jobId !== null && $jobId !== '') {
                                        $jobIdEsc = addslashes((string) $jobId);
                                        $sqlJob = "SELECT name FROM hse_automation.ikk_m_job WHERE id = '{$jobIdEsc}' LIMIT 1";
                                        $jobRow = $ch->query($sqlJob);
                                        if (!empty($jobRow[0])) {
                                            $wpJenisIjk = self::getClickHouseRowValue($jobRow[0], 'name');
                                        }
                                    }
                                }
                                $employeeInfoOkk = [];
                                if (!empty($allEmpIds)) {
                                    $supEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $allEmpIds));
                                    $sqlEmpOkk = "
                                        SELECT id, employee_name, employee_sid, company_name, layer
                                        FROM hse_automation.ikk_work_permit_employee
                                        WHERE id IN ({$supEsc})
                                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                                    ";
                                    $empOkkRows = $ch->query($sqlEmpOkk);
                                    foreach ($empOkkRows ?? [] as $er) {
                                        $eId = self::getClickHouseRowValue($er, 'id');
                                        if ($eId !== null) {
                                            $layerRaw = self::getClickHouseRowValue($er, 'layer');
                                            $employeeInfoOkk[(string) $eId] = [
                                                'employee_name' => self::getClickHouseRowValue($er, 'employee_name'),
                                                'employee_sid' => self::getClickHouseRowValue($er, 'employee_sid'),
                                                'company_name' => self::getClickHouseRowValue($er, 'company_name'),
                                                'layer' => $layerRaw !== null && $layerRaw !== '' ? (string) $layerRaw : null,
                                            ];
                                        }
                                    }
                                }
                                foreach ($okk as $idx => $row) {
                                    $sid = $row['supervisor_id'] ?? null;
                                    $indirectSid = $row['indirect_supervisor_id'] ?? null;
                                    // Layer & data employee: indirect_supervisor_id NULL = Layer 1 + data dari supervisor_id; NOT NULL = Layer 2+ + data dari indirect employee
                                    if ($indirectSid === null || $indirectSid === '') {
                                        $okk[$idx]['layer_pengawas'] = '1';
                                        if ($sid !== null && isset($employeeInfoOkk[(string) $sid])) {
                                            $info = $employeeInfoOkk[(string) $sid];
                                            $okk[$idx]['nama_pengawas'] = $info['employee_name'] ?? null;
                                            $okk[$idx]['kode_sid'] = $info['employee_sid'] ?? null;
                                            $okk[$idx]['nama_perusahaan'] = $info['company_name'] ?? null;
                                        }
                                    } else {
                                        $infoIndirect = $employeeInfoOkk[(string) $indirectSid] ?? null;
                                        $layerVal = $infoIndirect !== null && isset($infoIndirect['layer']) && $infoIndirect['layer'] !== null && $infoIndirect['layer'] !== '' ? (string) $infoIndirect['layer'] : '2';
                                        $okk[$idx]['layer_pengawas'] = $layerVal;
                                        if ($infoIndirect !== null) {
                                            $okk[$idx]['nama_pengawas'] = $infoIndirect['employee_name'] ?? null;
                                            $okk[$idx]['kode_sid'] = $infoIndirect['employee_sid'] ?? null;
                                            $okk[$idx]['nama_perusahaan'] = $infoIndirect['company_name'] ?? null;
                                        } elseif ($sid !== null && isset($employeeInfoOkk[(string) $sid])) {
                                            $info = $employeeInfoOkk[(string) $sid];
                                            $okk[$idx]['nama_pengawas'] = $info['employee_name'] ?? null;
                                            $okk[$idx]['kode_sid'] = $info['employee_sid'] ?? null;
                                            $okk[$idx]['nama_perusahaan'] = $info['company_name'] ?? null;
                                        }
                                    }
                                    $okk[$idx]['site'] = $wpSite;
                                    $okk[$idx]['jenis_ijk'] = $wpJenisIjk;
                                    unset($okk[$idx]['supervisor_id'], $okk[$idx]['indirect_supervisor_id']);
                                }
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::debug('Dashboard weekly modal OKK enrich: ' . $e->getMessage());
                                foreach ($okk as $idx => $row) {
                                    unset($okk[$idx]['supervisor_id'], $okk[$idx]['indirect_supervisor_id']);
                                }
                            }
                        }
                        $ipkSource = 'clickhouse';
                        $okkSource = 'clickhouse';
                    }
                }
            }
            if ($ipkSource === 'mysql') {
                $ipkIkk = IpkIkk::where('kode_ikk', $kodeIkk)
                    ->orderByDesc('ts')
                    ->get()
                    ->map(fn ($row) => $row->toArray())
                    ->values()
                    ->toArray();
                $okk = Okk::where('kode_ikk', $kodeIkk)
                    ->orderByDesc('ts')
                    ->get()
                    ->map(fn ($row) => $row->toArray())
                    ->values()
                    ->toArray();
            }
        }

        // Normalisasi lokasi (trim) dan pastikan tidak null
        $locationName = trim((string) $locationName);
        $locationDetailName = trim((string) $locationDetailName);

        \Illuminate\Support\Facades\Log::debug('OAK modal: request params', [
            'kode_ikk' => $kodeIkk,
            'location_name' => $locationName,
            'location_detail_name' => $locationDetailName,
            'tanggal_dop' => $request->input('tanggal_dop', ''),
        ]);

        // Jika lokasi kosong, coba ambil dari ClickHouse work permit by code + tanggal
        if (($locationName === '' || $locationDetailName === '') && $kodeIkk !== '') {
            $tanggalDop = $request->input('tanggal_dop', '');
            try {
                $filterDateForWp = $tanggalDop !== '' ? \Carbon\Carbon::parse($tanggalDop)->format('Y-m-d') : date('Y-m-d');
            } catch (\Exception $e) {
                $filterDateForWp = date('Y-m-d');
            }
            try {
                if (class_exists(\App\Services\ClickHouseService::class)) {
                    $clickHouse = app(\App\Services\ClickHouseService::class);
                    if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                        $codeEscaped = addslashes($kodeIkk);
                        $sqlWp = "
                            SELECT location_name, location_detail_name
                            FROM hse_automation.ikk_work_permit
                            WHERE trim(toString(code)) = '{$codeEscaped}'
                              AND toDate(start_date) = toDate('{$filterDateForWp}')
                            LIMIT 1
                        ";
                        $wpLoc = $clickHouse->query($sqlWp);
                        \Illuminate\Support\Facades\Log::debug('OAK modal: fallback WP query result', [
                            'wp_rows_count' => is_array($wpLoc) ? count($wpLoc) : 0,
                            'first_row_keys' => !empty($wpLoc[0]) ? array_keys($wpLoc[0]) : [],
                        ]);
                        if (!empty($wpLoc[0])) {
                            $row = $wpLoc[0];
                            $locationName = trim((string) self::getClickHouseRowValue($row, 'location_name'));
                            $locationDetailName = trim((string) self::getClickHouseRowValue($row, 'location_detail_name'));
                            \Illuminate\Support\Facades\Log::debug('OAK modal: after fallback', [
                                'location_name' => $locationName,
                                'location_detail_name' => $locationDetailName,
                            ]);
                        }
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::debug('Dashboard modal OAK fallback location from WP: ' . $e->getMessage());
            }
        }

        // Ambil OAK: di tanggal filter (submit_date), yang location & detail_location sama dengan work permit
        if ($locationName !== '' && $locationDetailName !== '') {
            try {
                if (class_exists(\App\Services\ClickHouseService::class)) {
                    $clickHouse = app(\App\Services\ClickHouseService::class);
                    if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                        $tanggalDop = $request->input('tanggal_dop', '');
                        if ($tanggalDop === '') {
                            $filterDate = date('Y-m-d');
                        } else {
                            try {
                                $filterDate = \Carbon\Carbon::parse($tanggalDop)->format('Y-m-d');
                            } catch (\Exception $e) {
                                $filterDate = date('Y-m-d');
                            }
                        }

                        $locationNameEscaped = addslashes($locationName);
                        $locationDetailEscaped = addslashes($locationDetailName);

                        // OAK di tanggal filter: lokasi + tipe observer, id unik (satu baris per id).
                        // WHERE di subquery agar tidak ada agregat di WHERE (ILLEGAL_AGGREGATION); GROUP BY id di luar.
                        $sqlOak = "
                            SELECT
                                toString(id) as id,
                                toString(argMax(activity, submit_date)) as activity,
                                toString(argMax(sub_activity, submit_date)) as sub_activity,
                                toString(argMax(submit_date, submit_date)) as submit_date_str,
                                toString(argMax(submit_by, submit_date)) as submit_by,
                                toString(argMax(kode_sid_pelapor, submit_date)) as kode_sid_pelapor,
                                toString(argMax(kode_sid_team, submit_date)) as kode_sid_team,
                                toString(argMax(conclusion, submit_date)) as conclusion,
                                toString(argMax(site, submit_date)) as site,
                                toString(argMax(location, submit_date)) as location,
                                toString(argMax(detail_location, submit_date)) as detail_location
                            FROM (
                                SELECT id, activity, sub_activity, submit_date, submit_by, kode_sid_pelapor, kode_sid_team, conclusion, site, location, detail_location
                                FROM hse_automation.aaj_vw_car_oak_register_ytd_only
                                WHERE toDate(submit_date) = '{$filterDate}'
                                  AND lower(trim(toString(tipe))) = 'observer'
                                  AND lower(trim(toString(location))) = lower('{$locationNameEscaped}')
                                  AND lower(trim(toString(detail_location))) = lower('{$locationDetailEscaped}')
                            ) AS filtered
                            GROUP BY id
                            ORDER BY max(submit_date) DESC
                            LIMIT 100
                        ";
                        \Illuminate\Support\Facades\Log::debug('OAK modal: running OAK query', [
                            'filter_date' => $filterDate,
                            'location_name_escaped' => $locationNameEscaped,
                            'location_detail_escaped' => $locationDetailEscaped,
                        ]);
                        $oakResult = $clickHouse->query($sqlOak);
                        $oak = is_array($oakResult) ? array_map([self::class, 'normalizeOakRow'], $oakResult) : [];
                        \Illuminate\Support\Facades\Log::debug('OAK modal: OAK result', [
                            'raw_count' => is_array($oakResult) ? count($oakResult) : 0,
                            'oak_count' => count($oak),
                            'first_raw_keys' => !empty($oakResult[0]) ? array_keys($oakResult[0]) : [],
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::debug('Dashboard modal OAK by location fetch: ' . $e->getMessage());
            }
        } else {
            \Illuminate\Support\Facades\Log::debug('OAK modal: skip OAK query (location empty)', [
                'location_name_empty' => $locationName === '',
                'location_detail_empty' => $locationDetailName === '',
            ]);
        }

        // Jika OAK belum diambil berdasarkan lokasi, coba ambil dari full-maps API (berdasarkan SID)
        if (empty($oak)) {
            try {
                $fullMapsUrl = route('full-maps.api.ikk-modal-data') . '?' . http_build_query([
                    'kode_ikk' => $kodeIkk,
                    'jenis_ijin_kerja_khusus' => $jenisIjin,
                    'nama_layer_2' => $namaLayer2,
                    'nama_layer_3' => $namaLayer3,
                    'nama_layer_4' => $namaLayer4,
                    'sid_layer_2' => $sidLayer2,
                    'sid_layer_3' => $sidLayer3,
                    'sid_layer_4' => $sidLayer4,
                ]);
                $response = Http::timeout(15)->get($fullMapsUrl);
                if ($response->successful()) {
                    $body = $response->json();
                    $oak = $body['oak'] ?? [];
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::debug('Dashboard modal OAK fetch: ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'ipk_ikk' => $ipkIkk,
            'okk' => $okk,
            'oak' => $oak,
            'ipk_source' => $ipkSource,
            'okk_source' => $okkSource,
            'location_name' => $locationName !== '' ? $locationName : null,
            'location_detail_name' => $locationDetailName !== '' ? $locationDetailName : null,
            'dopm_context' => [
                'nama_layer_2' => $namaLayer2,
                'nama_layer_3' => $namaLayer3,
                'nama_layer_4' => $namaLayer4,
            ],
        ]);
    }

    /**
     * API untuk modal Intervensi: ambil user Layer 1 dari vw_user dengan join by SID (sid_layer_1).
     * Kriteria sama seperti CctvDataController getUsersFromClickHouse: is_active=1, username/nama tidak kosong.
     * Return id, username, nama, email, selular, nik untuk tampilan dan kirim WA (IPK/OKK).
     */
    public function getLayer1Users(Request $request): JsonResponse
    {
        $sidLayer1 = trim((string) $request->input('sid_layer_1', ''));
        $namaLayer1 = trim((string) $request->input('nama_layer_1', ''));

        if ($sidLayer1 === '' && $namaLayer1 === '') {
            return response()->json(['success' => true, 'users' => [], 'nama_layer_1' => '']);
        }

        try {
            $query = \Illuminate\Support\Facades\DB::table('vw_user')
                ->where('is_active', 1)
                ->whereNotNull('username')
                ->where('username', '!=', '')
                ->whereNotNull('nama')
                ->where('nama', '!=', '');

            // Resolve by SID: username = sid_layer_1, atau banyak SID dipisah koma
            if ($sidLayer1 !== '') {
                $sids = array_map('trim', preg_split('/[\s,;]+/', $sidLayer1, -1, PREG_SPLIT_NO_EMPTY));
                $sids = array_unique(array_filter($sids));
                if (!empty($sids)) {
                    $query->whereIn('username', $sids);
                } else {
                    $query->where('username', '=', $sidLayer1);
                }
            } else {
                // Fallback: cari by nama (nama_layer_1) seperti sebelumnya
                $query->where(function ($q) use ($namaLayer1) {
                    $q->where('nama', 'LIKE', '%' . $namaLayer1 . '%')
                        ->orWhere('username', 'LIKE', '%' . $namaLayer1 . '%');
                });
            }

            $users = $query->select('id', 'username', 'nama', 'email', 'selular', 'nik')
                ->orderBy('username', 'ASC')
                ->limit(50)
                ->get()
                ->map(function ($row) {
                    return [
                        'id' => $row->id,
                        'username' => trim($row->username ?? ''),
                        'nama' => trim($row->nama ?? ''),
                        'email' => trim($row->email ?? ''),
                        'selular' => trim($row->selular ?? ''),
                        'nik' => trim($row->nik ?? ''),
                    ];
                })
                ->values()
                ->toArray();

            return response()->json([
                'success' => true,
                'users' => $users,
                'nama_layer_1' => $namaLayer1,
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('DOPMController getLayer1Users: ' . $e->getMessage());
            return response()->json(['success' => false, 'users' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API untuk modal Intervensi OAK: ambil user Layer 2, 3, 4 dari vw_user (by SID/nama).
     * Return layer_2, layer_3, layer_4 masing-masing berisi users + nama_layer untuk WA.
     */
    public function getLayers234Users(Request $request): JsonResponse
    {
        $layers = [
            'layer_2' => ['sid' => trim((string) $request->input('sid_layer_2', '')), 'nama' => trim((string) $request->input('nama_layer_2', ''))],
            'layer_3' => ['sid' => trim((string) $request->input('sid_layer_3', '')), 'nama' => trim((string) $request->input('nama_layer_3', ''))],
            'layer_4' => ['sid' => trim((string) $request->input('sid_layer_4', '')), 'nama' => trim((string) $request->input('nama_layer_4', ''))],
        ];

        $result = ['success' => true, 'layer_2' => ['users' => [], 'nama_layer' => $layers['layer_2']['nama']], 'layer_3' => ['users' => [], 'nama_layer' => $layers['layer_3']['nama']], 'layer_4' => ['users' => [], 'nama_layer' => $layers['layer_4']['nama']]];

        try {
            foreach (['layer_2', 'layer_3', 'layer_4'] as $key) {
                $sid = $layers[$key]['sid'];
                $nama = $layers[$key]['nama'];
                if ($sid === '' && $nama === '') {
                    continue;
                }

                $query = \Illuminate\Support\Facades\DB::table('vw_user')
                    ->where('is_active', 1)
                    ->whereNotNull('username')
                    ->where('username', '!=', '')
                    ->whereNotNull('nama')
                    ->where('nama', '!=', '');

                if ($sid !== '') {
                    $sids = array_map('trim', preg_split('/[\s,;]+/', $sid, -1, PREG_SPLIT_NO_EMPTY));
                    $sids = array_unique(array_filter($sids));
                    if (!empty($sids)) {
                        $query->whereIn('username', $sids);
                    } else {
                        $query->where('username', '=', $sid);
                    }
                } else {
                    $query->where(function ($q) use ($nama) {
                        $q->where('nama', 'LIKE', '%' . $nama . '%')
                            ->orWhere('username', 'LIKE', '%' . $nama . '%');
                    });
                }

                $users = $query->select('id', 'username', 'nama', 'email', 'selular', 'nik')
                    ->orderBy('username', 'ASC')
                    ->limit(50)
                    ->get()
                    ->map(function ($row) {
                        return [
                            'id' => $row->id,
                            'username' => trim($row->username ?? ''),
                            'nama' => trim($row->nama ?? ''),
                            'email' => trim($row->email ?? ''),
                            'selular' => trim($row->selular ?? ''),
                            'nik' => trim($row->nik ?? ''),
                        ];
                    })
                    ->values()
                    ->toArray();

                $result[$key] = ['users' => $users, 'nama_layer' => $nama];
            }

            return response()->json($result);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('DOPMController getLayers234Users: ' . $e->getMessage());
            return response()->json(array_merge($result, ['success' => false, 'message' => $e->getMessage()]), 500);
        }
    }

    /**
     * Display DOPM entries with search and pagination.
     */
    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $query = Dopm::query();

        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('id_dop', 'like', "%{$search}%")
                    ->orWhere('site_ijin_kerja_khusus', 'like', "%{$search}%")
                    ->orWhere('perusahaan_ijin_kerja_khusus', 'like', "%{$search}%")
                    ->orWhere('kode_ikk', 'like', "%{$search}%")
                    ->orWhere('nama_pekerjaan', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('nama_layer_2', 'like', "%{$search}%")
                    ->orWhere('nama_layer_3', 'like', "%{$search}%")
                    ->orWhere('nama_layer_4', 'like', "%{$search}%");
            });
        }

        $entries = $query->orderByDesc('id')->paginate($perPage)->withQueryString();

        return view('dopmikk.dopm.index', compact('entries', 'perPage'));
    }

    /**
     * Show the form for creating a new DOPM (optional; can use import only).
     */
    public function create(): View
    {
        return view('dopmikk.dopm.create');
    }

    /**
     * Store a new DOPM entry.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'id_dop' => ['nullable', 'string', 'max:100'],
            'timestamp' => ['nullable', 'date'],
            'site_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'perusahaan_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'jenis_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'kode_ikk' => ['nullable', 'string', 'max:255'],
            'tanggal_selesai_ijin' => ['nullable', 'date'],
            'nama_pekerjaan' => ['nullable', 'string', 'max:255'],
            'tanggal_dop' => ['nullable', 'date'],
            'status_pengiriman_notif' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'deskripsi_atau_alasan_cancel' => ['nullable', 'string'],
            'sid_layer_1' => ['nullable', 'string', 'max:255'],
            'nama_layer_1' => ['nullable', 'string', 'max:255'],
            'shift' => ['nullable', 'string', 'max:255'],
            'jam_mulai' => ['nullable', 'string', 'max:255'],
            'jam_akhir' => ['nullable', 'string', 'max:255'],
            'sid_layer_2' => ['nullable', 'string', 'max:255'],
            'nama_layer_2' => ['nullable', 'string', 'max:255'],
            'sid_layer_3' => ['nullable', 'string', 'max:255'],
            'nama_layer_3' => ['nullable', 'string', 'max:255'],
            'sid_layer_4' => ['nullable', 'string', 'max:255'],
            'nama_layer_4' => ['nullable', 'string', 'max:255'],
            'jenis_pengawasan_layer' => ['nullable', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string'],
        ]);

        if (!empty($validated['timestamp'])) {
            $validated['timestamp'] = Carbon::parse($validated['timestamp']);
        }

        Dopm::create($validated);

        return redirect()
            ->route('dopmikk.dopm.index')
            ->with('success', 'Data DOPM berhasil disimpan.');
    }

    /**
     * Show the form for editing the specified DOPM.
     */
    public function edit(Dopm $dopm): View
    {
        return view('dopmikk.dopm.edit', compact('dopm'));
    }

    /**
     * Update the specified DOPM.
     */
    public function update(Request $request, Dopm $dopm): RedirectResponse
    {
        $validated = $request->validate([
            'id_dop' => ['nullable', 'string', 'max:100'],
            'timestamp' => ['nullable', 'date'],
            'site_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'perusahaan_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'jenis_ijin_kerja_khusus' => ['nullable', 'string', 'max:255'],
            'kode_ikk' => ['nullable', 'string', 'max:255'],
            'tanggal_selesai_ijin' => ['nullable', 'date'],
            'nama_pekerjaan' => ['nullable', 'string', 'max:255'],
            'tanggal_dop' => ['nullable', 'date'],
            'status_pengiriman_notif' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:255'],
            'deskripsi_atau_alasan_cancel' => ['nullable', 'string'],
            'sid_layer_1' => ['nullable', 'string', 'max:255'],
            'nama_layer_1' => ['nullable', 'string', 'max:255'],
            'shift' => ['nullable', 'string', 'max:255'],
            'jam_mulai' => ['nullable', 'string', 'max:255'],
            'jam_akhir' => ['nullable', 'string', 'max:255'],
            'sid_layer_2' => ['nullable', 'string', 'max:255'],
            'nama_layer_2' => ['nullable', 'string', 'max:255'],
            'sid_layer_3' => ['nullable', 'string', 'max:255'],
            'nama_layer_3' => ['nullable', 'string', 'max:255'],
            'sid_layer_4' => ['nullable', 'string', 'max:255'],
            'nama_layer_4' => ['nullable', 'string', 'max:255'],
            'jenis_pengawasan_layer' => ['nullable', 'string', 'max:255'],
            'detail_lokasi' => ['nullable', 'string'],
        ]);

        if (!empty($validated['timestamp'])) {
            $validated['timestamp'] = Carbon::parse($validated['timestamp']);
        }

        $dopm->update($validated);

        return redirect()
            ->route('dopmikk.dopm.index')
            ->with('success', 'Data DOPM berhasil diperbarui.');
    }

    /**
     * Remove the specified DOPM.
     */
    public function destroy(Dopm $dopm): RedirectResponse
    {
        $dopm->delete();

        return redirect()
            ->route('dopmikk.dopm.index')
            ->with('success', 'Data DOPM berhasil dihapus.');
    }

    /**
     * Import DOPM data from Excel.
     */
    public function import(Request $request): RedirectResponse
    {
        try {
            $request->validate([
                'excel_file' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:10240'],
            ]);

            $file = $request->file('excel_file');

            if (!$file || !$file->isValid()) {
                return redirect()
                    ->route('dopmikk.dopm.index')
                    ->with('error', 'File tidak valid atau gagal diunggah.');
            }

            $uniqueName = uniqid('dopm_', true) . '.' . $file->getClientOriginalExtension();
            $storedPath = $file->storeAs('dopm-imports', $uniqueName);

            if (!$storedPath) {
                return redirect()
                    ->route('dopmikk.dopm.index')
                    ->with('error', 'Gagal menyimpan file.');
            }

            ImportDopmJob::dispatch($storedPath)->onQueue('default');

            return redirect()
                ->route('dopmikk.dopm.index')
                ->with('success', 'File berhasil diunggah dan sedang diproses. Silakan cek beberapa saat lagi.');
        } catch (\Exception $e) {
            \Log::error('DOPMController import error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()
                ->route('dopmikk.dopm.index')
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Download template Excel untuk import DOPM.
     */
    public function downloadTemplate()
    {
        // Urutan harus sama dengan ImportDopmJob
        $headers = [
            'ID',
            'Timestamp',
            'Site Ijin Kerja Khusus',
            'Perusahaan Ijin Kerja Khusus',
            'Jenis Ijin Kerja Khusus',
            'Kode IKK (Ijin Kerja Khusus)',
            'Tanggal Selesai Ijin',
            'Nama Pekerjaan',
            'Tanggal DOP',
            'SID Layer 1',
            'Nama Layer 1',
            'Shift',
            'Jam Mulai',
            'Jam Akhir',
            'Status Pengiriman Notif',
            'Status',
            'Deskripsi / Alasan cancel',
            'SID Layer 2',
            'Nama Layer 2',
            'SID Layer 3',
            'Nama Layer 3',
            'SID Layer 4',
            'Nama Layer 4',
            'Jenis Pengawasan Layer 2,3,4',
            'Detail Lokasi',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DOPM');
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $writer = new Xlsx($spreadsheet);
        $filename = 'template_import_dopm.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    /**
     * Singkatan label jenis ijin kerja khusus untuk chart (maks 12 karakter).
     */
    public static function singkatJenisIjin(string $jenis): string
    {
        $s = trim($jenis);
        if ($s === '' || $s === '-') {
            return $s ?: '-';
        }
        $s = preg_replace('/\s*ijin\s+kerja\s+khusus\s*/iu', 'IKK ', $s);
        $s = preg_replace('/\s*-\s*/u', ' ', trim($s));
        $s = trim($s);
        if (mb_strlen($s) > 12) {
            $s = mb_substr($s, 0, 11) . '…';
        }
        return $s ?: '-';
    }

    /**
     * Hitung status matriks berdasarkan Is IKK ada IPK dan Is IKK ada OKK.
     * Returns: 'Hijau' | 'Kuning' | 'Merah'
     *
     * @param  bool|null  $isIkkAdaIpk
     * @param  bool|null  $isIkkAdaOkk
     */
    public static function hitungStatusMatriks(?bool $isIkkAdaIpk, ?bool $isIkkAdaOkk): string
    {
        $ipk = $isIkkAdaIpk;
        $okk = $isIkkAdaOkk;

        // IF [Is IKK ada Full ada IPK OKK]=TRUE then 'Hijau'
        if ($ipk === true && $okk === true) {
            return 'Hijau';
        }
        // ELSEIF [Is IKK ada IPK]=TRUE and [Is IKK ada OKK]=TRUE then 'Hijau' (same)
        // ELSEIF [Is IKK ada IPK]=FALSE and [Is IKK ada OKK]=FALSE then 'Merah'
        if ($ipk === false && $okk === false) {
            return 'Merah';
        }
        // ELSEIF [Is IKK ada IPK]=NULL and [Is IKK ada OKK]=FALSE then 'Merah'
        if ($ipk === null && $okk === false) {
            return 'Merah';
        }
        // ELSEIF [Is IKK ada IPK]=FALSE and [Is IKK ada OKK]=NULL then 'Merah'
        if ($ipk === false && $okk === null) {
            return 'Merah';
        }
        // ELSEIF ISNULL([Is IKK ada IPK]) and ISNULL([Is IKK ada OKK]) then 'Merah'
        if ($ipk === null && $okk === null) {
            return 'Merah';
        }
        // ELSEIF [Is IKK ada IPK]=TRUE and [Is IKK ada OKK]=FALSE then 'Kuning'
        if ($ipk === true && $okk === false) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada IPK]=TRUE and ISNULL([Is IKK ada OKK]) then 'Kuning'
        if ($ipk === true && $okk === null) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada IPK]=FALSE and [Is IKK ada OKK]=TRUE then 'Kuning'
        if ($ipk === false && $okk === true) {
            return 'Kuning';
        }
        // ELSEIF ISNULL([Is IKK ada IPK]) and [Is IKK ada OKK]=TRUE then 'Kuning'
        if ($ipk === null && $okk === true) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada OKK]=FALSE then 'Kuning'
        if ($okk === false) {
            return 'Kuning';
        }
        // ELSEIF [Is IKK ada IPK]=FALSE then 'Kuning'
        if ($ipk === false) {
            return 'Kuning';
        }
        // ELSEIF ISNULL([Is IKK ada OKK]) then 'Kuning'
        if ($okk === null) {
            return 'Kuning';
        }
        // ELSEIF ISNULL([Is IKK ada IPK]) then 'Kuning'
        if ($ipk === null) {
            return 'Kuning';
        }
        // ELSE 'Merah'
        return 'Merah';
    }

    /**
     * Hitung status matriks untuk data IKK (work permit) dari ClickHouse
     * berdasarkan kolom status di tabel ikk_work_permit.
     * Returns: 'Hijau' | 'Kuning' | 'Merah'
     */
    public static function hitungStatusMatriksIkkClickhouse(?string $status): string
    {
        if ($status === null) {
            return 'Merah';
        }
        $s = strtoupper(trim($status));
        if ($s === '') {
            return 'Merah';
        }

        // Status baik / hijau
        if (in_array($s, ['ACTIVE', 'APPROVED', 'ONGOING', 'IN PROGRESS'], true)) {
            return 'Hijau';
        }

        // Status kuning (masih berjalan / menunggu)
        if (in_array($s, ['PENDING', 'SUBMITTED', 'EXTEND', 'EXTENDED', 'WAITING APPROVAL'], true)) {
            return 'Kuning';
        }

        // Status merah (selesai / kadaluarsa / batal)
        if (in_array($s, ['EXPIRED', 'REJECTED', 'CANCELLED', 'CANCELED', 'CLOSED'], true)) {
            return 'Merah';
        }

        // Default: kuning sebagai tengah
        return 'Kuning';
    }

    /**
     * Hitung status matriks lengkap untuk IKK ClickHouse berdasarkan matriks:
     * - IPK (ada/tidak, dengan durasi_jam)
     * - OKK (ada/tidak, sesuai target durasi, fraud detection, Layer 1 vs Layer 2 up)
     * - OAK (ada/tidak, lengkap berdasarkan lokasi, DIC mitra vs BC)
     * 
     * Returns: 'Hijau' | 'Kuning' | 'Merah'
     * 
     * @param string|null $kodeIkk Kode IKK (code dari work permit)
     * @param string|null $locationName Location name dari work permit
     * @param string|null $locationDetailName Location detail name dari work permit
     * @param string $filterDate Tanggal filter (format Y-m-d)
     * @param string|null $namaLayer1 Nama Layer 1 dari IKK
     * @param string|null $namaLayer2 Nama Layer 2 dari IKK
     * @param string|null $namaLayer3 Nama Layer 3 dari IKK
     * @param string|null $namaLayer4 Nama Layer 4 dari IKK
     * @return string
     */
    public static function hitungStatusMatriksLengkap(
        ?string $kodeIkk,
        ?string $locationName,
        ?string $locationDetailName,
        string $filterDate,
        ?string $namaLayer1 = null,
        ?string $namaLayer2 = null,
        ?string $namaLayer3 = null,
        ?string $namaLayer4 = null
    ): string {
        if ($kodeIkk === null || $kodeIkk === '') {
            return 'Merah';
        }

        // 1. Cek IPK dan ambil durasi_jam
        $ipk = IpkIkk::where('kode_ikk', $kodeIkk)
            ->whereDate('ts', $filterDate)
            ->first();

        $hasIpk = $ipk !== null;
        $durasiJam = $hasIpk ? ($ipk->durasi_jam ?? null) : null;

        // 2. Cek OKK dan ambil semua data untuk fraud detection dan Layer detection
        $okkList = Okk::where('kode_ikk', $kodeIkk)
            ->whereDate('ts', $filterDate)
            ->orderBy('ts')
            ->get();

        $hasOkk = $okkList->count() > 0;
        $okkCount = $okkList->count();

        // 2a. Pisahkan OKK berdasarkan Layer (Layer 1 vs Layer 2 up)
        $okkLayer1 = collect();
        $okkLayer2Up = collect();
        
        if ($hasOkk) {
            // Normalisasi nama Layer untuk matching
            $namaLayer1Normalized = $namaLayer1 ? trim(strtolower($namaLayer1)) : null;
            $namaLayer2Normalized = $namaLayer2 ? trim(strtolower($namaLayer2)) : null;
            $namaLayer3Normalized = $namaLayer3 ? trim(strtolower($namaLayer3)) : null;
            $namaLayer4Normalized = $namaLayer4 ? trim(strtolower($namaLayer4)) : null;
            
            foreach ($okkList as $okk) {
                $namaPengawas = trim(strtolower($okk->nama_pengawas ?? ''));
                $layerPengawas = trim(strtolower($okk->layer_pengawas ?? ''));
                
                // Cek apakah OKK dari Layer 1
                $isLayer1 = false;
                if ($namaLayer1Normalized && $namaPengawas) {
                    // Match berdasarkan nama pengawas atau layer_pengawas
                    if (strpos($namaPengawas, $namaLayer1Normalized) !== false || 
                        ($layerPengawas && strpos($layerPengawas, '1') !== false)) {
                        $isLayer1 = true;
                    }
                }
                
                if ($isLayer1) {
                    $okkLayer1->push($okk);
                } else {
                    // Cek apakah OKK dari Layer 2, 3, atau 4
                    // Matching dua arah: nama layer bisa lebih panjang atau lebih pendek dari nama pengawas
                    $isLayer2Up = false;
                    if ($namaPengawas) {
                        // Cek Layer 2: nama pengawas mengandung nama layer ATAU nama layer mengandung nama pengawas
                        if ($namaLayer2Normalized) {
                            if (strpos($namaPengawas, $namaLayer2Normalized) !== false || 
                                strpos($namaLayer2Normalized, $namaPengawas) !== false) {
                                $isLayer2Up = true;
                            }
                        }
                        // Cek Layer 3: nama pengawas mengandung nama layer ATAU nama layer mengandung nama pengawas
                        if (!$isLayer2Up && $namaLayer3Normalized) {
                            if (strpos($namaPengawas, $namaLayer3Normalized) !== false || 
                                strpos($namaLayer3Normalized, $namaPengawas) !== false) {
                                $isLayer2Up = true;
                            }
                        }
                        // Cek Layer 4: nama pengawas mengandung nama layer ATAU nama layer mengandung nama pengawas
                        if (!$isLayer2Up && $namaLayer4Normalized) {
                            if (strpos($namaPengawas, $namaLayer4Normalized) !== false || 
                                strpos($namaLayer4Normalized, $namaPengawas) !== false) {
                                $isLayer2Up = true;
                            }
                        }
                    }
                    if ($layerPengawas && !$isLayer2Up) {
                        // Cek berdasarkan layer_pengawas jika ada
                        if (strpos($layerPengawas, '2') !== false || 
                            strpos($layerPengawas, '3') !== false || 
                            strpos($layerPengawas, '4') !== false) {
                            $isLayer2Up = true;
                        }
                    }
                    
                    if ($isLayer2Up) {
                        $okkLayer2Up->push($okk);
                    }
                }
            }
        }
        
        $hasOkkLayer1 = $okkLayer1->count() > 0;
        $hasOkkLayer2Up = $okkLayer2Up->count() > 0;
        $okkLayer1Count = $okkLayer1->count();

        // 3. Fraud detection untuk OKK Layer 1 berdasarkan durasi
        $isOkkFraud = false;
        $isOkkSesuaiTarget = false;

        if ($hasOkkLayer1 && $durasiJam !== null) {
            // Parse durasi_jam (format: "3-6", "6-9", dll)
            $durasiParts = explode('-', trim($durasiJam));
            if (count($durasiParts) === 2) {
                $durasiMin = (float) trim($durasiParts[0]);
                $durasiMax = (float) trim($durasiParts[1]);
                $durasiRata = ($durasiMin + $durasiMax) / 2;

                // Tentukan target OKK dan jarak waktu
                $targetOkkCount = 0;
                $jarakMenit = 0;

                if ($durasiRata >= 3 && $durasiRata <= 6) {
                    $targetOkkCount = 2;
                    $jarakMenit = 30;
                } elseif ($durasiRata > 6 && $durasiRata <= 9) {
                    $targetOkkCount = 3;
                    $jarakMenit = 60;
                }

                // Cek apakah jumlah OKK Layer 1 sesuai target
                $isOkkSesuaiTarget = ($targetOkkCount > 0 && $okkLayer1Count >= $targetOkkCount);

                // Cek jarak waktu antar OKK Layer 1 untuk fraud detection
                if ($targetOkkCount > 0 && $okkLayer1Count >= $targetOkkCount) {
                    // Ambil timestamp dari OKK Layer 1, konversi ke Carbon, dan sort ascending
                    $tsList = $okkLayer1->pluck('ts')->map(function ($ts) {
                        if ($ts instanceof \Carbon\Carbon) {
                            return $ts;
                        }
                        try {
                            return \Carbon\Carbon::parse($ts);
                        } catch (\Exception $e) {
                            return null;
                        }
                    })->filter()->sort(function ($a, $b) {
                        // Sort ascending berdasarkan timestamp
                        return $a->timestamp <=> $b->timestamp;
                    })->values()->all();
                    
                    $isValidJarak = true;

                    for ($i = 1; $i < count($tsList); $i++) {
                        $prev = $tsList[$i - 1];
                        $curr = $tsList[$i];
                        // Gunakan absolute value untuk memastikan nilai positif
                        $diffMinutes = abs($curr->diffInMinutes($prev, false));
                        if ($diffMinutes < $jarakMenit) {
                            $isValidJarak = false;
                            break;
                        }
                    }

                    $isOkkFraud = !$isValidJarak;
                } else {
                    // Jika jumlah OKK Layer 1 kurang dari target, dianggap fraud
                    $isOkkFraud = true;
                }
            }
        }

        // 4. Cek OAK berdasarkan lokasi (dibedakan DIC mitra vs BC)
        $hasOakDicMitra = false;
        $hasOakBc = false;
        $hasOak = false;

        if ($locationName !== null && $locationName !== '' && 
            $locationDetailName !== null && $locationDetailName !== '') {
            try {
                if (class_exists(\App\Services\ClickHouseService::class)) {
                    $clickHouse = app(\App\Services\ClickHouseService::class);
                    if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                        $locationNameEscaped = addslashes(trim($locationName));
                        $locationDetailEscaped = addslashes(trim($locationDetailName));

                        // Cek OAK dari DIC mitra (tipe = 'observee')
                        $sqlOakDicMitra = "
                            SELECT count() as cnt
                            FROM hse_automation.aaj_vw_car_oak_register_ytd_only
                            WHERE toDate(submit_date) = '{$filterDate}'
                              AND trim(lower(toString(tipe))) = 'observee'
                              AND trim(toString(location)) = '{$locationNameEscaped}'
                              AND trim(toString(detail_location)) = '{$locationDetailEscaped}'
                        ";
                        $oakResultDicMitra = $clickHouse->query($sqlOakDicMitra);
                        $oakCountDicMitra = isset($oakResultDicMitra[0]['cnt']) ? (int) $oakResultDicMitra[0]['cnt'] : 0;
                        $hasOakDicMitra = $oakCountDicMitra > 0;

                        // Cek OAK dari BC (tipe = 'observe')
                        $sqlOakBc = "
                            SELECT count() as cnt
                            FROM hse_automation.aaj_vw_car_oak_register_ytd_only
                            WHERE toDate(submit_date) = '{$filterDate}'
                              AND trim(lower(toString(tipe))) = 'observe'
                              AND trim(toString(location)) = '{$locationNameEscaped}'
                              AND trim(toString(detail_location)) = '{$locationDetailEscaped}'
                        ";
                        $oakResultBc = $clickHouse->query($sqlOakBc);
                        $oakCountBc = isset($oakResultBc[0]['cnt']) ? (int) $oakResultBc[0]['cnt'] : 0;
                        $hasOakBc = $oakCountBc > 0;

                        $hasOak = $hasOakDicMitra || $hasOakBc;
                    }
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::debug('Dashboard OAK check skip: ' . $e->getMessage());
            }
        }

        // 5. Hitung status matriks berdasarkan kondisi sesuai kriteria baru
        
        // Cek apakah ada Layer 2 up di IKK
        $hasLayer2UpInIkk = ($namaLayer2 !== null && trim($namaLayer2) !== '') ||
                             ($namaLayer3 !== null && trim($namaLayer3) !== '') ||
                             ($namaLayer4 !== null && trim($namaLayer4) !== '');

        // MERAH: 
        // 1. Tidak ada IPK atau OKK sama sekali
        // 2. Hanya ada IPK atau ada OKK saja (tidak keduanya) - sudah tercakup di kondisi 1
        // 3. Ada IPK + OKK dari Layer 1 tapi tidak ada OKK dari Layer 2 up sesuai yang tercantum di IKK
        if (!$hasIpk || !$hasOkk) {
            return 'Merah';
        }
        // Jika ada IPK + OKK Layer 1 tapi tidak ada OKK Layer 2 up (dan ada Layer 2/3/4 di IKK)
        if ($hasIpk && $hasOkkLayer1 && $hasLayer2UpInIkk && !$hasOkkLayer2Up) {
            return 'Merah';
        }

        // HIJAU:
        // 1. Full ada IPK - OKK sesuai target dari Layer 1
        // 2. Ada OKK dari Layer 2 up sesuai IKK (jika ada Layer 2/3/4 di IKK)
        // 3. Harus ada OAK (baik dari DIC mitra maupun BC)
        if ($hasIpk && $hasOkkLayer1 && $isOkkSesuaiTarget && !$isOkkFraud && $hasOak) {
            // Jika ada Layer 2 up di IKK, harus ada OKK Layer 2 up juga
            if ($hasLayer2UpInIkk) {
                if ($hasOkkLayer2Up) {
                    return 'Hijau';
                }
            } else {
                // Jika tidak ada Layer 2 up di IKK, cukup OKK Layer 1 sesuai target dan ada OAK
                return 'Hijau';
            }
        }

        // KUNING:
        // 1. Ada IPK + OKK sesuai target tapi ada yang fraud (sehingga berkurang tidak sesuai target)
        // 2. Tidak ada OAK dari DIC mitra maupun dari BC
        if ($hasIpk && $hasOkkLayer1 && $isOkkSesuaiTarget && $isOkkFraud) {
            return 'Kuning';
        }
        if ($hasIpk && $hasOkk && !$hasOak) {
            return 'Kuning';
        }
        // Jika ada IPK + OKK Layer 1 sesuai target tapi tidak ada OKK Layer 2 up (padahal ada di IKK)
        if ($hasIpk && $hasOkkLayer1 && $isOkkSesuaiTarget && !$isOkkFraud && 
            $hasLayer2UpInIkk && !$hasOkkLayer2Up) {
            return 'Kuning';
        }

        // Default: Kuning untuk kondisi lainnya
        return 'Kuning';
    }

    /**
     * Parse end_date dari ClickHouse (string, timestamp, atau object) ke Carbon; null jika gagal.
     * Menggunakan app timezone agar konsisten dengan perbandingan "sekarang".
     */
    private static function parseEndDate(mixed $value): ?\Carbon\Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        $tz = config('app.timezone', 'UTC');
        try {
            if (is_numeric($value)) {
                return \Carbon\Carbon::createFromTimestamp((int) $value, $tz);
            }
            if ($value instanceof \DateTimeInterface) {
                return \Carbon\Carbon::instance($value)->setTimezone($tz);
            }
            return \Carbon\Carbon::parse($value, $tz);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Format approver_names dari ClickHouse (groupUniqArray) ke string untuk tampilan.
     * Bisa berupa array atau string seperti "['A','B']".
     */
    private static function formatApproverNames(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (is_array($value)) {
            $value = array_filter(array_map('trim', $value));
            return empty($value) ? null : implode(', ', $value);
        }
        $s = trim((string) $value);
        if ($s === '') {
            return null;
        }
        $decoded = json_decode($s, true);
        if (is_array($decoded)) {
            $decoded = array_filter(array_map('trim', $decoded));
            return empty($decoded) ? null : implode(', ', $decoded);
        }
        return $s;
    }

    /**
     * Format timestamp dari ClickHouse ke string Y-m-d H:i:s untuk tampilan.
     * Data ClickHouse di sini sudah dalam waktu lokal (WIB); tampilkan as-is tanpa konversi timezone.
     */
    private static function formatClickHouseTsForAppTz(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $tz = config('app.timezone', 'Asia/Jakarta');
        try {
            if ($value instanceof \DateTimeInterface) {
                // Anggap nilai sudah lokal (WIB); format tanpa menggeser jam
                return \Carbon\Carbon::parse($value->format('Y-m-d H:i:s'), $tz)->format('Y-m-d H:i:s');
            }
            // String: anggap sudah dalam waktu aplikasi (WIB)
            return \Carbon\Carbon::parse($value, $tz)->format('Y-m-d H:i:s');
        } catch (\Throwable $e) {
            return is_string($value) ? $value : '';
        }
    }

    /**
     * Ambil nilai dari satu row hasil query ClickHouse dengan fallback key (case / snake).
     * ClickHouse/HTTP bisa mengembalikan key dengan casing berbeda.
     */
    private static function getClickHouseRowValue(array $row, string $key): mixed
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
     * Format array of layer employees untuk tampilan: "Nama1 (SID1), Nama2 (SID2)".
     * @param array<int, array{name: string, sid: string}> $employees
     */
    private static function formatLayerEmployees(array $employees): ?string
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

    /**
     * Format array of layer employees SID saja: "SID1, SID2".
     * @param array<int, array{name: string, sid: string}> $employees
     */
    private static function formatLayerSids(array $employees): ?string
    {
        if (empty($employees)) {
            return null;
        }
        $sids = array_filter(array_map(fn ($e) => trim((string) ($e['sid'] ?? '')), $employees));
        return $sids !== [] ? implode(', ', $sids) : null;
    }

    /**
     * Normalisasi satu row OAK dari ClickHouse agar key konsisten (activity, sub_activity, dll) untuk frontend.
     */
    private static function normalizeOakRow(array $row): array
    {
        $keys = ['activity', 'sub_activity', 'submit_date', 'submit_by', 'kode_sid_pelapor', 'location', 'detail_location', 'conclusion', 'site'];
        $out = [];
        foreach ($keys as $key) {
            if ($key === 'submit_date') {
                $out[$key] = self::getClickHouseRowValue($row, 'submit_date_str') ?? self::getClickHouseRowValue($row, 'submit_date') ?? '';
            } else {
                $out[$key] = self::getClickHouseRowValue($row, $key) ?? '';
            }
        }
        return $out;
    }
}
