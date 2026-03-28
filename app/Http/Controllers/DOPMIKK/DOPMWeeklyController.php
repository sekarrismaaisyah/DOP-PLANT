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
use Illuminate\Support\Facades\Cache;
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
        $maxExec = (int) config('dopm.weekly_dashboard_max_execution_seconds', 0);
        if ($maxExec > 0) {
            @set_time_limit($maxExec);
            @ini_set('max_execution_time', (string) $maxExec);
        }

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

        // Server-side cache untuk mencegah query berat berulang saat user bolak-balik week/filter yang sama.
        $cacheVersion = 'v2';
        $cacheTtlSeconds = (int) config('dopm.weekly_dashboard_cache_ttl', 300);
        $cacheUserKey = auth()->check() ? (string) auth()->id() : 'guest';
        $dashboardCacheKey = 'dopm_weekly_dashboard:' . sha1(json_encode([
            'v' => $cacheVersion,
            'date' => $filterDate,
            'site' => $filterSite,
            'week' => $filterWeek,
            'user' => $cacheUserKey,
        ]));
        if ($cacheTtlSeconds > 0) {
            $cachedViewData = Cache::get($dashboardCacheKey);
            if (is_array($cachedViewData)) {
                return view('dopmikk.dopm.dashboard-weekly', $cachedViewData);
            }
        }
        
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
                          AND subtractDays(toDate(end_date), 1) >= toDate('" . addslashes($mingguStartStr) . "')
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
                            // Hari terakhir aktif = end_date - 1 (h-1)
                            $lastActiveDate = $endDate->copy()->subDay()->startOfDay();
                            for ($i = 0; $i < 7; $i++) {
                                $dayStart = $mingguStart->copy()->addDays($i)->startOfDay();
                                $dayEnd = $mingguStart->copy()->addDays($i)->endOfDay();
                                if ($startDate->lte($dayEnd) && $lastActiveDate->gte($dayStart)) {
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
        $totalIpkSeharusnya = 0;
        $totalIpkAda = 0;
        $totalOkkSeharusnya = 0;
        $totalOkkAda = 0;
        /** @var array<int, array<string, mixed>>|null Daftar WP dari ClickHouse (satu sumber untuk statistik + tabel) */
        $clickHouseWpRowsForWeekly = null;

        // Tentukan cut off date untuk IPK/OKK
        $cutoffDate = Carbon::parse(config('dopm.ipk_okk_clickhouse_cutoff_date', '2025-02-20'))->startOfDay();
        $useClickHouseForWeekly = $weekStartDate->gte($cutoffDate);
        
        try {
            if (class_exists(\App\Services\ClickHouseService::class)) {
                $clickHouse = app(\App\Services\ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    $weekStartStr = $weekStartDate->format('Y-m-d');
                    $weekEndStr = $weekEndDate->format('Y-m-d');
                    
                    // Filter site (sama persis dengan query tabel agar satu sumber kebenaran)
                    $siteFilterClauseForWp = '';
                    if ($filterSite !== '' && $filterSite !== null) {
                        if ($filterSite === 'Lainnya') {
                            $siteFilterClauseForWp = " AND trim(COALESCE(wp.ra_site_name, '')) = ''";
                        } else {
                            $siteFilterClauseForWp = " AND trim(COALESCE(wp.ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                        }
                    }
                    
                    // Satu query WP untuk statistik mingguan DAN tabel (konsisten: IKK yang tampil = IKK yang masuk persen)
                    $sqlWorkPermitsForWeekly = "
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
                            AND toDate(wp.start_date) <= toDate('{$weekEndStr}')
                            AND subtractDays(toDate(wp.end_date), 1) >= toDate('{$weekStartStr}')
                            {$siteFilterClauseForWp}
                        GROUP BY
                            wp.id, wp.code, wp.name, wp.ra_site_name, wp.company_name,
                            wp.status, wp.m_job_id,
                            wp.start_date, wp.end_date, wp.location_name, wp.location_detail_name
                        HAVING
                            sum(if(upper(trim(toString(wp_pic.status))) = 'APPROVED'
                                AND trim(toString(m.m_privilege_id)) = '7d872114-0924-4c6a-880e-49b3c06b5429', 1, 0)) > 0
                        ORDER BY wp.start_date ASC
                    ";
                    $clickHouseWpRowsForWeekly = $clickHouse->query($sqlWorkPermitsForWeekly);
                    $weeklyIkkCodes = [];
                    $weeklyIkkIds = [];
                    $weeklyIkkDurations = []; // Simpan durasi per code untuk MySQL fallback
                    $totalIpkSeharusnya = 0;
                    if (!empty($clickHouseWpRowsForWeekly)) {
                        foreach ($clickHouseWpRowsForWeekly as $row) {
                            $code = trim((string) self::getClickHouseRowValue($row, 'code'));
                            $id = trim((string) self::getClickHouseRowValue($row, 'id'));
                            if ($code !== '') {
                                $weeklyIkkCodes[$code] = true;
                            }
                            if ($id !== '') {
                                $weeklyIkkIds[$id] = $code;
                                // Hitung durasi IKK (dalam hari) yang overlap dengan range minggu; parsing robust agar slot-hari tidak hilang
                                $durasiHari = self::computeIkkActiveDaysInWeek($row, $weekStartDate, $weekEndDate);
                                if ($durasiHari > 0) {
                                    $totalIpkSeharusnya += $durasiHari;
                                    $weeklyIkkDurations[$code] = ($weeklyIkkDurations[$code] ?? 0) + $durasiHari;
                                }
                            }
                        }
                    }
                    $totalIkkWeekly = count($weeklyIkkCodes);
                    
                    // Query IPK yang ada dalam rentang week
                    // Cek cut off: jika weekStartDate >= cutoff maka dari ClickHouse, jika tidak dari MySQL
                    $totalIpkAda = 0;
                    $ipkPerWpPerDay = [];
                    
                    if ($useClickHouseForWeekly) {
                        // === CLICKHOUSE: weekStartDate >= cutoff ===
                        if (!empty($weeklyIkkIds)) {
                            $wpIdsWeeklyEsc = implode(',', array_map(fn($id) => "'" . addslashes($id) . "'", array_keys($weeklyIkkIds)));
                            $sqlWeeklyIpk = "
                                SELECT work_permit_id, toDate(start_date) as ipk_date
                                FROM hse_automation.ipk_assessment
                                WHERE work_permit_id IN ({$wpIdsWeeklyEsc})
                                  AND toDate(start_date) >= toDate('{$weekStartStr}')
                                  AND toDate(start_date) <= toDate('{$weekEndStr}')
                                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                                GROUP BY work_permit_id, toDate(start_date)
                            ";
                            $weeklyIpkRows = $clickHouse->query($sqlWeeklyIpk);
                            if (!empty($weeklyIpkRows)) {
                                foreach ($weeklyIpkRows as $r) {
                                    $wpId = isset($r['work_permit_id']) ? trim((string) $r['work_permit_id']) : '';
                                    $ipkDate = isset($r['ipk_date']) ? trim((string) $r['ipk_date']) : '';
                                    if ($wpId !== '' && $ipkDate !== '' && isset($weeklyIkkIds[$wpId])) {
                                        $key = $wpId . '_' . $ipkDate;
                                        $ipkPerWpPerDay[$key] = true;
                                    }
                                }
                            }
                        }
                    } else {
                        // === MYSQL: weekStartDate < cutoff ===
                        if (!empty($weeklyIkkCodes)) {
                            $ikkCodesArray = array_keys($weeklyIkkCodes);
                            $ipkRows = IpkIkk::whereIn('kode_ikk', $ikkCodesArray)
                                ->whereBetween('ts', [$weekStartDate->format('Y-m-d'), $weekEndDate->format('Y-m-d 23:59:59')])
                                ->selectRaw('kode_ikk, DATE(ts) as ipk_date')
                                ->groupBy('kode_ikk', \DB::raw('DATE(ts)'))
                                ->get();
                            foreach ($ipkRows as $row) {
                                $kodeIkk = $row->kode_ikk ?? '';
                                $ipkDate = $row->ipk_date ?? '';
                                if ($kodeIkk !== '' && $ipkDate !== '') {
                                    $key = $kodeIkk . '_' . $ipkDate;
                                    $ipkPerWpPerDay[$key] = true;
                                }
                            }
                        }
                    }
                    
                    $totalIpkAda = count($ipkPerWpPerDay);
                    $ikkAdaIpkCountWeekly = $totalIpkAda;
                    
                    // Query OKK yang ada dalam rentang week (by work_permit_id) - tetap dari ClickHouse
                    // OKK dianggap comply jika ada Layer 1 DAN Layer 2 per hari per IKK
                    // Layer 1: indirect_supervisor_id IS NULL atau kosong
                    // Layer 2+: indirect_supervisor_id NOT NULL dan tidak kosong
                    if (!empty($weeklyIkkIds)) {
                        $wpIdsWeeklyEsc = implode(',', array_map(fn($id) => "'" . addslashes($id) . "'", array_keys($weeklyIkkIds)));
                        
                        // Hitung total OKK seharusnya (sama dengan IPK - berdasarkan durasi IKK dalam minggu)
                        $totalOkkSeharusnya = $totalIpkSeharusnya;
                        
                        // Query OKK dengan info layer per hari per work_permit
                        $sqlWeeklyOkk = "
                            SELECT 
                                work_permit_id, 
                                toDate(created_at) as okk_date,
                                CASE 
                                    WHEN indirect_supervisor_id IS NULL OR trim(toString(indirect_supervisor_id)) = '' THEN 'L1'
                                    ELSE 'L2'
                                END as layer_type
                            FROM hse_automation.okk_assessment
                            WHERE work_permit_id IN ({$wpIdsWeeklyEsc})
                              AND toDate(created_at) >= toDate('{$weekStartStr}')
                              AND toDate(created_at) <= toDate('{$weekEndStr}')
                              AND upper(trim(toString(status))) = 'SUBMITTED'
                              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                            GROUP BY work_permit_id, toDate(created_at), layer_type
                        ";
                        $weeklyOkkRows = $clickHouse->query($sqlWeeklyOkk);
                        
                        // Struktur: okkPerWpPerDay[wp_id][date] = ['L1' => true/false, 'L2' => true/false]
                        $okkPerWpPerDay = [];
                        $okkWpIds = [];
                        if (!empty($weeklyOkkRows)) {
                            foreach ($weeklyOkkRows as $r) {
                                $wpId = isset($r['work_permit_id']) ? trim((string) $r['work_permit_id']) : '';
                                $okkDate = isset($r['okk_date']) ? trim((string) $r['okk_date']) : '';
                                $layerType = isset($r['layer_type']) ? trim((string) $r['layer_type']) : '';
                                if ($wpId !== '' && $okkDate !== '' && $layerType !== '' && isset($weeklyIkkIds[$wpId])) {
                                    $code = $weeklyIkkIds[$wpId];
                                    $key = $wpId . '_' . $okkDate;
                                    if (!isset($okkPerWpPerDay[$key])) {
                                        $okkPerWpPerDay[$key] = ['L1' => false, 'L2' => false, 'code' => $code];
                                    }
                                    $okkPerWpPerDay[$key][$layerType] = true;
                                    $okkWpIds[$code] = true;
                                }
                            }
                        }
                        
                        // Hitung hari yang comply (ada Layer 1 DAN Layer 2)
                        $totalOkkAda = 0;
                        foreach ($okkPerWpPerDay as $key => $layers) {
                            if ($layers['L1'] === true && $layers['L2'] === true) {
                                $totalOkkAda++;
                            }
                        }
                        
                        $ikkAdaOkkCountWeekly = $totalOkkAda;
                        
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
                    
                    // Hitung persentase (cap 100%: IPK/OKK di hari non-aktif bisa bikin totalAda > totalSeharusnya)
                    // IPK: persentase berdasarkan jumlah hari IPK yang ada vs yang seharusnya
                    $pctIkkAdaIpkWeekly = $totalIpkSeharusnya > 0 ? min(100, round(($totalIpkAda / $totalIpkSeharusnya) * 100, 1)) : 0;
                    // OKK: persentase berdasarkan jumlah hari OKK comply (L1+L2) vs yang seharusnya
                    $pctIkkAdaOkkWeekly = $totalOkkSeharusnya > 0 ? min(100, round(($totalOkkAda / $totalOkkSeharusnya) * 100, 1)) : 0;
                    $pctComplianceWeekly = min(100, round(($pctIkkAdaIpkWeekly + $pctIkkAdaOkkWeekly) / 2, 1));
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
            // OKK Layer 1: kode_ikk yang ada OKK dari Layer 1
            $okkLayer1Kodes = Okk::whereIn('kode_ikk', $kodeIkks)
                ->whereDate('ts', $filterDate)
                ->where('layer_pengawas', '1')
                ->select('kode_ikk')
                ->distinct()
                ->pluck('kode_ikk')
                ->flip()
                ->all();
            // OKK Layer 2+: kode_ikk yang ada OKK dari Layer 2, 3, atau 4
            $okkLayer2UpKodes = Okk::whereIn('kode_ikk', $kodeIkks)
                ->whereDate('ts', $filterDate)
                ->whereIn('layer_pengawas', ['2', '3', '4'])
                ->select('kode_ikk')
                ->distinct()
                ->pluck('kode_ikk')
                ->flip()
                ->all();
            foreach ($kodeIkks as $k) {
                $hasIpkByKode[$k] = isset($ipkKodes[$k]);
                // IKK dianggap ada OKK hanya jika ada OKK dari Layer 1 DAN Layer 2+
                $hasOkkByKode[$k] = isset($okkLayer1Kodes[$k]) && isset($okkLayer2UpKodes[$k]);
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
        // Filter berdasarkan rentang minggu (weekStartDate - weekEndDate), distinct per kode IKK
        $ikkClickhouseListHarian = [];
        $ikkDailyDetailsByWpId = [];
        $weekStartStr = $weekStartDate->format('Y-m-d');
        $weekEndStr = $weekEndDate->format('Y-m-d');
        try {
            if (class_exists(\App\Services\ClickHouseService::class)) {
                /** @var \App\Services\ClickHouseService $clickHouse */
                $clickHouse = app(\App\Services\ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    // Weekly: WP yang rentang (start_date–end_date) overlap dengan minggu filter
                    // IKK dengan rentang 3 hari dalam minggu ini dianggap 1 IKK (distinct by code)
                    $siteFilterClause = '';
                    if ($filterSite !== '' && $filterSite !== null) {
                        if ($filterSite === 'Lainnya') {
                            $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = ''";
                        } else {
                            $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                        }
                    }

                    // Gunakan daftar WP yang sama dengan statistik mingguan (satu sumber kebenaran)
                    if (isset($clickHouseWpRowsForWeekly) && is_array($clickHouseWpRowsForWeekly) && !empty($clickHouseWpRowsForWeekly)) {
                        $wpRows = $clickHouseWpRowsForWeekly;
                    } else {
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
                                AND toDate(wp.start_date) <= toDate('{$weekEndStr}')
                                AND subtractDays(toDate(wp.end_date), 1) >= toDate('{$weekStartStr}')
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
                    }

                    if (config('app.debug')) {
                        $rowCount = is_array($wpRows) ? count($wpRows) : 0;
                        $firstRow = $wpRows[0] ?? null;
                        $sampleRows = [];
                        if (is_array($wpRows) && !empty($wpRows)) {
                            foreach (array_slice($wpRows, 0, 3) as $i => $r) {
                                $sampleRows[] = is_array($r) ? $r : (array) $r;
                            }
                        }
                        \Illuminate\Support\Facades\Log::debug('ClickHouse work permit query result (weekly)', [
                            'query' => 'sqlWorkPermits (wp + wp_pic + m_pic, HAVING all PIC APPROVED)',
                            'params' => [
                                'weekStart' => $weekStartStr,
                                'weekEnd' => $weekEndStr,
                                'site' => $filterSite,
                            ],
                            'row_count' => $rowCount,
                            'column_keys' => $firstRow !== null ? array_keys(is_array($firstRow) ? $firstRow : (array) $firstRow) : [],
                            'sample_rows' => $sampleRows,
                        ]);
                    }

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

        if (!empty($ikkClickhouseListHarian) && class_exists(\App\Services\ClickHouseService::class)) {
            $chIkkDaily = app(\App\Services\ClickHouseService::class);
            if (method_exists($chIkkDaily, 'query') && $chIkkDaily->isConnected()) {
                try {
                    $ikkDailyDetailsByWpId = self::computeDailyDetailsBatchForWeeklyIkks(
                        $ikkClickhouseListHarian,
                        $weekStartDate,
                        $weekEndDate,
                        $chIkkDaily
                    );
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::debug('Dashboard IKK daily batch: ' . $e->getMessage());
                }
            }
        }

        if (config('app.debug')) {
            \Illuminate\Support\Facades\Log::debug('IKK list size', [
                'count' => count($ikkClickhouseListHarian),
                'approved_count' => $totalWorkPermitApprovedHarian,
            ]);
        }

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

        // IKK batal karena reschedule: code_before di ikk_reschedule (ClickHouse), hanya untuk minggu yang dipilih (date_before dalam week)
        $rescheduleBatalCodes = [];
        if (class_exists(\App\Services\ClickHouseService::class)) {
            $chReschedule = app(\App\Services\ClickHouseService::class);
            if (method_exists($chReschedule, 'query') && $chReschedule->isConnected()) {
                $weekStartStrReschedule = $weekStartDate->format('Y-m-d');
                $weekEndStrReschedule = $weekEndDate->format('Y-m-d');
                $sqlReschedule = "
                    SELECT DISTINCT code_before
                    FROM hse_automation.ikk_reschedule
                    WHERE upper(trim(toString(reschedule_type))) = 'RESCHEDULE'
                      AND upper(trim(toString(status))) = 'APPROVE'
                      AND code_before IS NOT NULL
                      AND trim(toString(code_before)) != ''
                      AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                      AND toDate(date_before) >= toDate('" . addslashes($weekStartStrReschedule) . "')
                      AND toDate(date_before) <= toDate('" . addslashes($weekEndStrReschedule) . "')
                ";
                $rescheduleRows = $chReschedule->query($sqlReschedule);
                foreach ($rescheduleRows ?? [] as $r) {
                    $cb = trim((string) self::getClickHouseRowValue($r, 'code_before'));
                    if ($cb !== '') {
                        $rescheduleBatalCodes[] = $cb;
                    }
                }
                $rescheduleBatalCodes = array_values(array_unique($rescheduleBatalCodes));
            }
        }
        $cancelKodeIkk = array_values(array_unique(array_merge($cancelKodeIkk ?? [], $rescheduleBatalCodes)));

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
                        // Merge IPK/OKK dari tanggal end_date ke hari terakhir: IKK yang lastActiveDate = filterDate dan end_date > filterDate, agar IPK/OKK yang diisi di tanggal end_date ikut diakui untuk status matriks
                        $filterDateCarbon = Carbon::parse($filterDate)->startOfDay();
                        $mergeEndDateList = [];
                        foreach ($ikkClickhouseListHarian as $ikk) {
                            try {
                                $endDate = Carbon::parse($ikk->end_date)->startOfDay();
                                $lastActive = $endDate->copy()->subDay();
                                $code = $ikk->code ?? null;
                                $id = $ikk->id ?? null;
                                if ($id !== null && $code !== null && $code !== '' && in_array($code, $workPermitCodes, true)
                                    && $lastActive->eq($filterDateCarbon) && $endDate->gt($filterDateCarbon)) {
                                    $mergeEndDateList[] = ['wp_id' => $id, 'code' => $code, 'end_date_str' => $endDate->format('Y-m-d')];
                                }
                            } catch (\Throwable $e) {
                                continue;
                            }
                        }
                        if (!empty($mergeEndDateList)) {
                            $inClauses = [];
                            foreach ($mergeEndDateList as $item) {
                                $wpEsc = addslashes((string) $item['wp_id']);
                                $dEsc = addslashes($item['end_date_str']);
                                $inClauses[] = "(work_permit_id = '{$wpEsc}' AND toDate(start_date) = toDate('{$dEsc}'))";
                            }
                            $sqlIpkEndDate = "
                                SELECT id, work_permit_id, code, status, job_status, start_date, created_at, supervisor_id, cctv
                                FROM hse_automation.ipk_assessment
                                WHERE (" . implode(' OR ', $inClauses) . ")
                                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                                ORDER BY created_at DESC
                            ";
                            $ipkRowsEndDate = $ch->query($sqlIpkEndDate);
                            foreach ($ipkRowsEndDate ?? [] as $r) {
                                $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                                $code = $wpIdToCode[$wpId] ?? null;
                                if ($code !== null && !isset($ipkByKode[$code])) {
                                    $ipkByKode[$code] = (object) [
                                        'kode_ikk' => $code,
                                        'durasi_jam' => null,
                                        'status_pekerjaan' => self::getClickHouseRowValue($r, 'job_status'),
                                    ];
                                }
                            }
                            $okkEndDateInClauses = [];
                            foreach ($mergeEndDateList as $item) {
                                $wpEsc = addslashes((string) $item['wp_id']);
                                $dEsc = addslashes($item['end_date_str']);
                                $okkEndDateInClauses[] = "(work_permit_id = '{$wpEsc}' AND toDate(created_at) = toDate('{$dEsc}'))";
                            }
                            $sqlOkkEndDate = "
                                SELECT id, work_permit_id, code, status, created_at, supervisor_id, indirect_supervisor_id
                                FROM hse_automation.okk_assessment
                                WHERE (" . implode(' OR ', $okkEndDateInClauses) . ")
                                  AND upper(trim(toString(status))) = 'SUBMITTED'
                                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                                ORDER BY created_at ASC
                            ";
                            $okkRowsEndDate = $ch->query($sqlOkkEndDate);
                            foreach ($okkRowsEndDate ?? [] as $r) {
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
            // Batch load OAK berdasarkan lokasi + detail lokasi IKK saja (tanpa bedakan tipe DIC/BC)
            if (!empty($locationPairsForOak)) {
                try {
                    if (class_exists(\App\Services\ClickHouseService::class)) {
                        $ch = app(\App\Services\ClickHouseService::class);
                        if (method_exists($ch, 'query') && $ch->isConnected()) {
                            $dateEsc = addslashes($filterDate);
                            $conditions = [];
                            foreach ($locationPairsForOak as $p) {
                                $locEsc = addslashes($p[0]);
                                $detEsc = addslashes($p[1]);
                                $conditions[] = "(trim(toString(location)) = '{$locEsc}' AND trim(toString(detail_location)) = '{$detEsc}')";
                            }
                            $where = implode(' OR ', $conditions);
                            $sqlOakAny = "
                                SELECT trim(toString(location)) as location,
                                       trim(toString(detail_location)) as detail_location,
                                       count() as cnt
                                FROM hse_automation.aaj_vw_car_oak_register_ytd_only
                                WHERE toDate(submit_date) = '{$dateEsc}'
                                  AND ({$where})
                                GROUP BY location, detail_location
                            ";
                            $oakResultAny = $ch->query($sqlOakAny);
                            foreach ($oakResultAny ?? [] as $row) {
                                $loc = trim((string) ($row['location'] ?? ''));
                                $det = trim((string) ($row['detail_location'] ?? ''));
                                $key = $loc . '|' . $det;
                                $oakDataByLocation[$key] = [
                                    'has_oak' => ((int) ($row['cnt'] ?? 0)) > 0,
                                ];
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

        // Generate daily_details untuk setiap IKK (untuk tampilan expand/collapse di view)
        // Optimasi: hindari query per IKK per hari (N+1), gunakan batch query ClickHouse.
        if (!empty($ikkClickhouseListHarian) && class_exists(\App\Services\ClickHouseService::class)) {
            $ch = app(\App\Services\ClickHouseService::class);
            if (method_exists($ch, 'query') && $ch->isConnected()) {
                $metaByWpId = [];
                $wpIdsForDaily = [];
                $globalMinDate = null;
                $globalMaxDate = null;

                foreach ($ikkClickhouseListHarian as $ikk) {
                    $wpId = $ikk->id ?? null;
                    if ($wpId === null) {
                        $ikk->daily_details = [];
                        $ikk->total_hari = 0;
                        $ikk->ipk_count = 0;
                        $ikk->okk_count = 0;
                        continue;
                    }

                    try {
                        $ikkStartDate = Carbon::parse($ikk->start_date)->startOfDay();
                        $ikkEndDate = Carbon::parse($ikk->end_date)->startOfDay();
                        // Hari terakhir yang aktif = date(end_date) - 1 (pekerjaan selesai di pagi end_date, jadi tanggal end_date tidak dihitung)
                        $ikkLastActiveDate = $ikkEndDate->copy()->subDay();
                    } catch (\Throwable $e) {
                        $ikk->daily_details = [];
                        $ikk->total_hari = 0;
                        $ikk->ipk_count = 0;
                        $ikk->okk_count = 0;
                        continue;
                    }

                    $effectiveStart = $ikkStartDate->lt($weekStartDate) ? $weekStartDate->copy()->startOfDay() : $ikkStartDate->copy();
                    $effectiveEnd = $ikkLastActiveDate->gt($weekEndDate) ? $weekEndDate->copy()->startOfDay() : $ikkLastActiveDate->copy();
                    $includeEndDateMerge = $ikkEndDate->gt($effectiveEnd);

                    $requiredDates = [];
                    if ($effectiveStart->lte($effectiveEnd)) {
                        $cursor = $effectiveStart->copy();
                        while ($cursor->lte($effectiveEnd)) {
                            $requiredDates[] = $cursor->format('Y-m-d');
                            $cursor->addDay();
                        }
                    }
                    if ($includeEndDateMerge) {
                        $requiredDates[] = $ikkEndDate->format('Y-m-d');
                    }
                    $requiredDates = array_values(array_unique($requiredDates));

                    if (!empty($requiredDates)) {
                        $minDate = min($requiredDates);
                        $maxDate = max($requiredDates);
                        $globalMinDate = $globalMinDate === null ? $minDate : min($globalMinDate, $minDate);
                        $globalMaxDate = $globalMaxDate === null ? $maxDate : max($globalMaxDate, $maxDate);
                    }

                    $metaByWpId[(string) $wpId] = [
                        'effective_start' => $effectiveStart->copy(),
                        'effective_end' => $effectiveEnd->copy(),
                        'end_date_str' => $ikkEndDate->format('Y-m-d'),
                        'include_end_date_merge' => $includeEndDateMerge,
                        'required_date_set' => array_fill_keys($requiredDates, true),
                    ];
                    $wpIdsForDaily[] = (string) $wpId;
                }

                $wpIdsForDaily = array_values(array_unique($wpIdsForDaily));
                $ipkByWpDate = [];
                $okkByWpDate = [];

                if (!empty($wpIdsForDaily) && $globalMinDate !== null && $globalMaxDate !== null) {
                    $wpIdsEsc = implode(',', array_map(fn ($id) => "'" . addslashes($id) . "'", $wpIdsForDaily));
                    $minDateEsc = addslashes($globalMinDate);
                    $maxDateEsc = addslashes($globalMaxDate);

                    $sqlIpkBatch = "
                        SELECT work_permit_id, toDate(start_date) AS d,
                               argMax(code, created_at) AS code,
                               argMax(job_status, created_at) AS job_status
                        FROM hse_automation.ipk_assessment
                        WHERE work_permit_id IN ({$wpIdsEsc})
                          AND toDate(start_date) >= toDate('{$minDateEsc}')
                          AND toDate(start_date) <= toDate('{$maxDateEsc}')
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                        GROUP BY work_permit_id, d
                    ";
                    $ipkBatchRows = $ch->query($sqlIpkBatch);
                    foreach ($ipkBatchRows ?? [] as $r) {
                        $wpId = (string) self::getClickHouseRowValue($r, 'work_permit_id');
                        $dateStr = (string) self::getClickHouseRowValue($r, 'd');
                        if ($wpId === '' || $dateStr === '') {
                            continue;
                        }
                        if (!isset($metaByWpId[$wpId]['required_date_set'][$dateStr])) {
                            continue;
                        }
                        $ipkByWpDate[$wpId . '|' . $dateStr] = [
                            'code' => self::getClickHouseRowValue($r, 'code'),
                            'job_status' => self::getClickHouseRowValue($r, 'job_status'),
                        ];
                    }

                    $sqlOkkBatch = "
                        SELECT work_permit_id, toDate(created_at) AS d,
                               argMax(code, created_at) AS code,
                               argMax(status, created_at) AS status
                        FROM hse_automation.okk_assessment
                        WHERE work_permit_id IN ({$wpIdsEsc})
                          AND toDate(created_at) >= toDate('{$minDateEsc}')
                          AND toDate(created_at) <= toDate('{$maxDateEsc}')
                          AND upper(trim(toString(status))) = 'SUBMITTED'
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                        GROUP BY work_permit_id, d
                    ";
                    $okkBatchRows = $ch->query($sqlOkkBatch);
                    foreach ($okkBatchRows ?? [] as $r) {
                        $wpId = (string) self::getClickHouseRowValue($r, 'work_permit_id');
                        $dateStr = (string) self::getClickHouseRowValue($r, 'd');
                        if ($wpId === '' || $dateStr === '') {
                            continue;
                        }
                        if (!isset($metaByWpId[$wpId]['required_date_set'][$dateStr])) {
                            continue;
                        }
                        $okkByWpDate[$wpId . '|' . $dateStr] = [
                            'code' => self::getClickHouseRowValue($r, 'code'),
                            'status' => self::getClickHouseRowValue($r, 'status'),
                        ];
                    }
                }

                foreach ($ikkClickhouseListHarian as $ikk) {
                    $wpId = (string) ($ikk->id ?? '');
                    if ($wpId === '' || !isset($metaByWpId[$wpId])) {
                        if (!isset($ikk->daily_details)) {
                            $ikk->daily_details = [];
                            $ikk->total_hari = 0;
                            $ikk->ipk_count = 0;
                            $ikk->okk_count = 0;
                        }
                        continue;
                    }

                    $meta = $metaByWpId[$wpId];
                    /** @var Carbon $effectiveStart */
                    $effectiveStart = $meta['effective_start'];
                    /** @var Carbon $effectiveEnd */
                    $effectiveEnd = $meta['effective_end'];
                    $endDateStr = $meta['end_date_str'];

                    $dailyDetails = [];
                    $ipkCount = 0;
                    $okkCount = 0;

                    if ($effectiveStart->lte($effectiveEnd)) {
                        $cursor = $effectiveStart->copy();
                        while ($cursor->lte($effectiveEnd)) {
                            $dateStr = $cursor->format('Y-m-d');
                            $key = $wpId . '|' . $dateStr;

                            $ipkRow = $ipkByWpDate[$key] ?? null;
                            $okkRow = $okkByWpDate[$key] ?? null;

                            $hasIpk = $ipkRow !== null;
                            $hasOkk = $okkRow !== null;

                            if ($hasIpk) {
                                $ipkCount++;
                            }
                            if ($hasOkk) {
                                $okkCount++;
                            }

                            $dailyDetails[] = [
                                'tanggal' => $cursor->format('d/m/Y'),
                                'hari' => $cursor->locale('id')->translatedFormat('l'),
                                'has_ipk' => $hasIpk,
                                'ipk_kode' => $hasIpk ? ($ipkRow['code'] ?? null) : null,
                                'ipk_status' => $hasIpk ? ($ipkRow['job_status'] ?? null) : null,
                                'has_okk' => $hasOkk,
                                'okk_kode' => $hasOkk ? ($okkRow['code'] ?? null) : null,
                                'okk_status' => $hasOkk ? ($okkRow['status'] ?? null) : null,
                            ];

                            $cursor->addDay();
                        }
                    }

                    // Jika end_date (tanggal) > effectiveEnd, IPK/OKK di end_date diakui sebagai hari terakhir aktif.
                    if (($meta['include_end_date_merge'] ?? false) && !empty($dailyDetails)) {
                        $lastIdx = count($dailyDetails) - 1;
                        $endKey = $wpId . '|' . $endDateStr;

                        $ipkEnd = $ipkByWpDate[$endKey] ?? null;
                        if ($ipkEnd !== null && !($dailyDetails[$lastIdx]['has_ipk'] ?? false)) {
                            $dailyDetails[$lastIdx]['has_ipk'] = true;
                            $dailyDetails[$lastIdx]['ipk_kode'] = $ipkEnd['code'] ?? null;
                            $dailyDetails[$lastIdx]['ipk_status'] = $ipkEnd['job_status'] ?? null;
                            $ipkCount++;
                        }

                        $okkEnd = $okkByWpDate[$endKey] ?? null;
                        if ($okkEnd !== null && !($dailyDetails[$lastIdx]['has_okk'] ?? false)) {
                            $dailyDetails[$lastIdx]['has_okk'] = true;
                            $dailyDetails[$lastIdx]['okk_kode'] = $okkEnd['code'] ?? null;
                            $dailyDetails[$lastIdx]['okk_status'] = $okkEnd['status'] ?? null;
                            $okkCount++;
                        }
                    }

                    $ikk->daily_details = $dailyDetails;
                    $ikk->total_hari = count($dailyDetails);
                    $ikk->ipk_count = $ipkCount;
                    $ikk->okk_count = $okkCount;
                }
            }
        }

        // Jangan kirim daily_details ke Blade (HTML sangat besar); detail harian diload via API saat baris di-expand.
        foreach ($ikkClickhouseListHarian as $ikkStrip) {
            unset($ikkStrip->daily_details);
        }

        // Persentase IKK ada IPK / IKK ada OKK (workPermitCodes sudah dihitung di atas)
        if (!empty($workPermitCodes)) {
            if ($useChIpkOkk) {
                $ipkKodesWp = array_flip(array_keys($ipkByKode));
                // OKK dianggap ada hanya jika ada Layer 1 DAN Layer 2+ per kode_ikk
                $okkKodesWp = [];
                foreach ($okkByKode as $kode => $okkCollection) {
                    $hasLayer1 = false;
                    $hasLayer2Up = false;
                    foreach ($okkCollection as $okk) {
                        $layer = (string) ($okk->layer_pengawas ?? '');
                        if ($layer === '1') {
                            $hasLayer1 = true;
                        } elseif (in_array($layer, ['2', '3', '4'], true)) {
                            $hasLayer2Up = true;
                        }
                        if ($hasLayer1 && $hasLayer2Up) {
                            break;
                        }
                    }
                    if ($hasLayer1 && $hasLayer2Up) {
                        $okkKodesWp[$kode] = true;
                    }
                }
            } else {
                $ipkKodesWp = IpkIkk::whereIn('kode_ikk', $workPermitCodes)
                    ->whereDate('ts', $filterDate)
                    ->select('kode_ikk')
                    ->distinct()
                    ->pluck('kode_ikk')
                    ->flip()
                    ->all();
                // OKK Layer 1: kode_ikk yang ada OKK dari Layer 1
                $okkLayer1KodesWp = Okk::whereIn('kode_ikk', $workPermitCodes)
                    ->whereDate('ts', $filterDate)
                    ->where('layer_pengawas', '1')
                    ->select('kode_ikk')
                    ->distinct()
                    ->pluck('kode_ikk')
                    ->flip()
                    ->all();
                // OKK Layer 2+: kode_ikk yang ada OKK dari Layer 2, 3, atau 4
                $okkLayer2UpKodesWp = Okk::whereIn('kode_ikk', $workPermitCodes)
                    ->whereDate('ts', $filterDate)
                    ->whereIn('layer_pengawas', ['2', '3', '4'])
                    ->select('kode_ikk')
                    ->distinct()
                    ->pluck('kode_ikk')
                    ->flip()
                    ->all();
                // OKK dianggap ada hanya jika ada Layer 1 DAN Layer 2+
                $okkKodesWp = array_intersect_key($okkLayer1KodesWp, $okkLayer2UpKodesWp);
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
        // Menggunakan cutoff date: tanggal < cutoff dari MySQL, tanggal >= cutoff dari ClickHouse.
        $complianceByDay = [];
        $monthStart = Carbon::parse($filterDate)->startOfMonth()->format('Y-m-d');
        $monthEnd = Carbon::parse($filterDate)->endOfMonth()->format('Y-m-d');
        $monthStartCarbon = Carbon::parse($monthStart)->startOfDay();
        $monthEndCarbon = Carbon::parse($monthEnd)->endOfDay();
        $daysInMonthCalendar = (int) $monthEndCarbon->day;

        $cutoffDtCalendar = Carbon::parse(config('dopm.ipk_okk_clickhouse_cutoff_date', '2025-02-20'))->startOfDay();
        $cutoffStrCalendar = $cutoffDtCalendar->format('Y-m-d');

        $codesPerDayCalendar = [];
        $codesWithIpkByDayCalendar = [];
        $codesWithOkkByDayCalendar = [];
        $cancelPerDayCalendar = [];

        try {
            $useClickHouseCalendar = Carbon::parse($monthEnd)->gte($cutoffDtCalendar);
            $useMySQLCalendar = Carbon::parse($monthStart)->lt($cutoffDtCalendar);

            $clickHouseCalendar = null;
            $clickHouseConnectedCalendar = false;
            if ($useClickHouseCalendar && class_exists(\App\Services\ClickHouseService::class)) {
                $clickHouseCalendar = app(\App\Services\ClickHouseService::class);
                $clickHouseConnectedCalendar = method_exists($clickHouseCalendar, 'query') && $clickHouseCalendar->isConnected();
            }

            $monthIdToCodeCalendar = [];

            // === AMBIL DAFTAR IKK DARI CLICKHOUSE (untuk tanggal >= cutoff) ===
            if ($clickHouseConnectedCalendar) {
                $siteFilterClauseCalendar = '';
                if ($filterSite !== '' && $filterSite !== null) {
                    if ($filterSite === 'Lainnya') {
                        $siteFilterClauseCalendar = " AND trim(COALESCE(wp.ra_site_name, '')) = ''";
                    } else {
                        $siteFilterClauseCalendar = " AND trim(COALESCE(wp.ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                    }
                }

                $sqlMonthCalendar = "
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
                        {$siteFilterClauseCalendar}
                    GROUP BY wp.id, wp.code, wp.start_date, wp.end_date
                    HAVING sum(if(upper(trim(toString(wp_pic.status))) = 'APPROVED'
                        AND trim(toString(m.m_privilege_id)) = '7d872114-0924-4c6a-880e-49b3c06b5429', 1, 0)) > 0
                ";
                $wpRowsMonthCalendar = $clickHouseCalendar->query($sqlMonthCalendar);
                if (!empty($wpRowsMonthCalendar)) {
                    foreach ($wpRowsMonthCalendar as $row) {
                        $id = self::getClickHouseRowValue($row, 'id');
                        $code = isset($row['code']) ? trim((string) $row['code']) : '';
                        if ($id !== null && $code !== '') {
                            $monthIdToCodeCalendar[$id] = $code;
                        }
                        if ($code === '') {
                            continue;
                        }
                        $startDate = self::parseEndDate(self::getClickHouseRowValue($row, 'start_date'));
                        $endDate = self::parseEndDate(self::getClickHouseRowValue($row, 'end_date'));
                        if ($startDate === null || $endDate === null) {
                            continue;
                        }
                        $startDay = $startDate->copy()->startOfDay();
                        // Hari terakhir aktif = date(end_date) - 1 (jika sama hari, pakai startDay)
                        $endDay = $endDate->copy()->subDay()->startOfDay();
                        if ($endDay->lt($startDay)) {
                            $endDay = $startDay->copy();
                        }
                        for ($day = 1; $day <= $daysInMonthCalendar; $day++) {
                            $dayDate = $monthStartCarbon->copy()->addDays($day - 1)->startOfDay();
                            if ($dayDate->lt($cutoffDtCalendar)) {
                                continue;
                            }
                            if ($startDay->lte($dayDate) && $endDay->gte($dayDate)) {
                                $d = $dayDate->format('Y-m-d');
                                if (!isset($codesPerDayCalendar[$d])) {
                                    $codesPerDayCalendar[$d] = [];
                                }
                                $codesPerDayCalendar[$d][$code] = true;
                            }
                        }
                    }
                }

                // IPK dari ClickHouse (untuk tanggal >= cutoff)
                if (!empty($monthIdToCodeCalendar)) {
                    $wpIdsMonthCalendar = array_keys($monthIdToCodeCalendar);
                    $wpIdsMonthEscCalendar = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $wpIdsMonthCalendar));
                    $chCutoffStrCalendar = addslashes($cutoffStrCalendar);
                    $monthEndEscCalendar = addslashes($monthEnd);
                    $sqlIpkMonthCalendar = "
                        SELECT toDate(start_date) AS d, work_permit_id
                        FROM hse_automation.ipk_assessment
                        WHERE work_permit_id IN ({$wpIdsMonthEscCalendar})
                          AND toDate(start_date) >= toDate('{$chCutoffStrCalendar}')
                          AND toDate(start_date) <= toDate('{$monthEndEscCalendar}')
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                    ";
                    $ipkMonthRowsCalendar = $clickHouseCalendar->query($sqlIpkMonthCalendar);
                    foreach ($ipkMonthRowsCalendar ?? [] as $r) {
                        $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                        $dateVal = self::getClickHouseRowValue($r, 'd');
                        if ($wpId === null || $dateVal === null) {
                            continue;
                        }
                        $d = $dateVal instanceof \Carbon\Carbon ? $dateVal->format('Y-m-d') : (is_string($dateVal) ? $dateVal : null);
                        if ($d === null) {
                            continue;
                        }
                        $code = $monthIdToCodeCalendar[$wpId] ?? null;
                        if ($code !== null) {
                            if (!isset($codesWithIpkByDayCalendar[$d])) {
                                $codesWithIpkByDayCalendar[$d] = [];
                            }
                            $codesWithIpkByDayCalendar[$d][$code] = true;
                        }
                    }

                    // OKK dari ClickHouse (untuk tanggal >= cutoff)
                    $sqlOkkMonthCalendar = "
                        SELECT toDate(created_at) AS d, work_permit_id
                        FROM hse_automation.okk_assessment
                        WHERE work_permit_id IN ({$wpIdsMonthEscCalendar})
                          AND upper(trim(toString(status))) = 'SUBMITTED'
                          AND toDate(created_at) >= toDate('{$chCutoffStrCalendar}')
                          AND toDate(created_at) <= toDate('{$monthEndEscCalendar}')
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                    ";
                    $okkMonthRowsCalendar = $clickHouseCalendar->query($sqlOkkMonthCalendar);
                    foreach ($okkMonthRowsCalendar ?? [] as $r) {
                        $wpId = self::getClickHouseRowValue($r, 'work_permit_id');
                        $dateVal = self::getClickHouseRowValue($r, 'd');
                        if ($wpId === null || $dateVal === null) {
                            continue;
                        }
                        $d = $dateVal instanceof \Carbon\Carbon ? $dateVal->format('Y-m-d') : (is_string($dateVal) ? $dateVal : null);
                        if ($d === null) {
                            continue;
                        }
                        $code = $monthIdToCodeCalendar[$wpId] ?? null;
                        if ($code !== null) {
                            if (!isset($codesWithOkkByDayCalendar[$d])) {
                                $codesWithOkkByDayCalendar[$d] = [];
                            }
                            $codesWithOkkByDayCalendar[$d][$code] = true;
                        }
                    }
                }
            }

            // === AMBIL DAFTAR IKK DARI MYSQL (untuk tanggal < cutoff) ===
            if ($useMySQLCalendar) {
                $mysqlEndDateCalendar = Carbon::parse($monthEnd)->lt($cutoffDtCalendar) ? $monthEnd : Carbon::parse($cutoffStrCalendar)->subDay()->format('Y-m-d');
                
                // IPK dari MySQL (untuk tanggal < cutoff)
                $ipkIkkRowsCalendar = IpkIkk::whereBetween('ts', [$monthStart, $mysqlEndDateCalendar])
                    ->whereNotIn('status_pekerjaan', ['Batal', 'BATAL', 'Cancel', 'CANCEL'])
                    ->select('ts', 'kode_ikk')
                    ->get();
                foreach ($ipkIkkRowsCalendar as $row) {
                    $d = Carbon::parse($row->ts)->format('Y-m-d');
                    $k = trim((string) ($row->kode_ikk ?? ''));
                    if ($k !== '' && Carbon::parse($d)->lt($cutoffDtCalendar)) {
                        // Tambahkan ke daftar IKK per hari
                        if (!isset($codesPerDayCalendar[$d])) {
                            $codesPerDayCalendar[$d] = [];
                        }
                        $codesPerDayCalendar[$d][$k] = true;
                        // Tandai bahwa IPK sudah terisi untuk kode ini
                        if (!isset($codesWithIpkByDayCalendar[$d])) {
                            $codesWithIpkByDayCalendar[$d] = [];
                        }
                        $codesWithIpkByDayCalendar[$d][$k] = true;
                    }
                }

                // OKK dari MySQL (untuk tanggal < cutoff)
                $okkRowsCalendar = Okk::whereBetween('ts', [$monthStart, $mysqlEndDateCalendar])
                    ->select('ts', 'kode_ikk')
                    ->get();
                foreach ($okkRowsCalendar as $row) {
                    $d = Carbon::parse($row->ts)->format('Y-m-d');
                    $k = trim((string) ($row->kode_ikk ?? ''));
                    if ($k !== '' && Carbon::parse($d)->lt($cutoffDtCalendar)) {
                        // Tambahkan juga ke daftar IKK per hari (untuk kasus OKK ada tapi IPK belum)
                        if (!isset($codesPerDayCalendar[$d])) {
                            $codesPerDayCalendar[$d] = [];
                        }
                        $codesPerDayCalendar[$d][$k] = true;
                        // Tandai bahwa OKK sudah terisi untuk kode ini
                        if (!isset($codesWithOkkByDayCalendar[$d])) {
                            $codesWithOkkByDayCalendar[$d] = [];
                        }
                        $codesWithOkkByDayCalendar[$d][$k] = true;
                    }
                }
            }

            // Konversi codesPerDay dari associative ke indexed array
            foreach ($codesPerDayCalendar as $d => $codes) {
                $codesPerDayCalendar[$d] = array_keys($codes);
            }

            // Cancel dari MySQL
            $cancelRowsCalendar = IpkIkk::whereIn('status_pekerjaan', ['Batal', 'BATAL', 'Cancel', 'CANCEL'])
                ->whereBetween('ts', [$monthStart, $monthEnd])
                ->get(['ts', 'kode_ikk']);
            foreach ($cancelRowsCalendar as $r) {
                $d = Carbon::parse($r->ts)->format('Y-m-d');
                $k = trim((string) ($r->kode_ikk ?? ''));
                if ($k !== '') {
                    if (!isset($cancelPerDayCalendar[$d])) {
                        $cancelPerDayCalendar[$d] = [];
                    }
                    $cancelPerDayCalendar[$d][$k] = true;
                }
            }

            // Hitung compliance per hari
            for ($day = 1; $day <= $daysInMonthCalendar; $day++) {
                $d = $monthStartCarbon->copy()->addDays($day - 1)->format('Y-m-d');
                $codes = $codesPerDayCalendar[$d] ?? [];
                $cancelCodes = isset($cancelPerDayCalendar[$d]) ? array_keys($cancelPerDayCalendar[$d]) : [];
                $codes = array_values(array_diff($codes, $cancelCodes));
                $total = count($codes);
                if ($total === 0) {
                    $complianceByDay[$d] = null;
                    continue;
                }
                $ipkCount = 0;
                $okkCount = 0;
                $ipkByDay = $codesWithIpkByDayCalendar[$d] ?? [];
                $okkByDay = $codesWithOkkByDayCalendar[$d] ?? [];
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
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Dashboard complianceByDay: ' . $e->getMessage());
        }

        $viewData = [
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
            'totalIpkSeharusnya' => $totalIpkSeharusnya,
            'totalIpkAda' => $totalIpkAda,
            'totalOkkSeharusnya' => $totalOkkSeharusnya,
            'totalOkkAda' => $totalOkkAda,
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
            'ikkDailyDetailsByWpId' => $ikkDailyDetailsByWpId,
            'complianceByDay' => $complianceByDay,
            'rescheduleBatalCodes' => $rescheduleBatalCodes ?? [],
        ];

        if ($cacheTtlSeconds > 0) {
            Cache::put($dashboardCacheKey, $viewData, now()->addSeconds($cacheTtlSeconds));
        }

        return view('dopmikk.dopm.dashboard-weekly', $viewData);
    }

    /**
     * API endpoint untuk mengambil data compliance IKK per bulan (untuk navigasi kalender AJAX).
     * Menggunakan cutoff date: tanggal < cutoff dari MySQL, tanggal >= cutoff dari ClickHouse.
     */
    public function getComplianceByMonth(Request $request): JsonResponse
    {
        $year = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);
        $filterSite = $request->get('site');
        if ($filterSite === null || trim($filterSite) === '') {
            $filterSite = '';
        } else {
            $filterSite = trim($filterSite);
        }

        $monthStart = Carbon::createFromDate($year, $month, 1)->startOfMonth()->format('Y-m-d');
        $monthEnd = Carbon::createFromDate($year, $month, 1)->endOfMonth()->format('Y-m-d');
        $monthStartCarbon = Carbon::parse($monthStart)->startOfDay();
        $monthEndCarbon = Carbon::parse($monthEnd)->endOfDay();
        $daysInMonth = (int) $monthEndCarbon->day;

        $cutoffDt = Carbon::parse(config('dopm.ipk_okk_clickhouse_cutoff_date', '2025-02-20'))->startOfDay();
        $cutoffStr = $cutoffDt->format('Y-m-d');

        $complianceByDay = [];
        $codesPerDay = [];
        $codesWithIpkByDay = [];
        $codesWithOkkByDay = [];
        $cancelPerDay = [];

        try {
            $useClickHouse = Carbon::parse($monthEnd)->gte($cutoffDt);
            $useMySQL = Carbon::parse($monthStart)->lt($cutoffDt);

            $clickHouse = null;
            $clickHouseConnected = false;
            if ($useClickHouse && class_exists(\App\Services\ClickHouseService::class)) {
                $clickHouse = app(\App\Services\ClickHouseService::class);
                $clickHouseConnected = method_exists($clickHouse, 'query') && $clickHouse->isConnected();
            }

            $monthIdToCode = [];

            // === AMBIL DAFTAR IKK DARI CLICKHOUSE (untuk tanggal >= cutoff) ===
            if ($clickHouseConnected) {
                $siteFilterClause = '';
                if ($filterSite !== '' && $filterSite !== null) {
                    if ($filterSite === 'Lainnya') {
                        $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = ''";
                    } else {
                        $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = '" . addslashes($filterSite) . "'";
                    }
                }

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
                if (!empty($wpRowsMonth)) {
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
                        $startDay = $startDate->copy()->startOfDay();
                        // Hari terakhir aktif = date(end_date) - 1 (jika sama hari, pakai startDay)
                        $endDay = $endDate->copy()->subDay()->startOfDay();
                        if ($endDay->lt($startDay)) {
                            $endDay = $startDay->copy();
                        }
                        for ($day = 1; $day <= $daysInMonth; $day++) {
                            $dayDate = $monthStartCarbon->copy()->addDays($day - 1)->startOfDay();
                            if ($dayDate->lt($cutoffDt)) {
                                continue;
                            }
                            if ($startDay->lte($dayDate) && $endDay->gte($dayDate)) {
                                $d = $dayDate->format('Y-m-d');
                                if (!isset($codesPerDay[$d])) {
                                    $codesPerDay[$d] = [];
                                }
                                $codesPerDay[$d][$code] = true;
                            }
                        }
                    }
                }

                // IPK dari ClickHouse (untuk tanggal >= cutoff)
                if (!empty($monthIdToCode)) {
                    $wpIdsMonth = array_keys($monthIdToCode);
                    $wpIdsMonthEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $wpIdsMonth));
                    $chCutoffStr = addslashes($cutoffStr);
                    $monthEndEsc = addslashes($monthEnd);
                    $sqlIpkMonth = "
                        SELECT toDate(start_date) AS d, work_permit_id
                        FROM hse_automation.ipk_assessment
                        WHERE work_permit_id IN ({$wpIdsMonthEsc})
                          AND toDate(start_date) >= toDate('{$chCutoffStr}')
                          AND toDate(start_date) <= toDate('{$monthEndEsc}')
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                    ";
                    $ipkMonthRows = $clickHouse->query($sqlIpkMonth);
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

                    // OKK dari ClickHouse (untuk tanggal >= cutoff)
                    $sqlOkkMonth = "
                        SELECT toDate(created_at) AS d, work_permit_id
                        FROM hse_automation.okk_assessment
                        WHERE work_permit_id IN ({$wpIdsMonthEsc})
                          AND upper(trim(toString(status))) = 'SUBMITTED'
                          AND toDate(created_at) >= toDate('{$chCutoffStr}')
                          AND toDate(created_at) <= toDate('{$monthEndEsc}')
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                    ";
                    $okkMonthRows = $clickHouse->query($sqlOkkMonth);
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

            // === AMBIL DAFTAR IKK DARI MYSQL (untuk tanggal < cutoff) ===
            if ($useMySQL) {
                $mysqlEndDate = Carbon::parse($monthEnd)->lt($cutoffDt) ? $monthEnd : Carbon::parse($cutoffStr)->subDay()->format('Y-m-d');
                
                // IPK dari MySQL (untuk tanggal < cutoff)
                $ipkIkkRows = IpkIkk::whereBetween('ts', [$monthStart, $mysqlEndDate])
                    ->whereNotIn('status_pekerjaan', ['Batal', 'BATAL', 'Cancel', 'CANCEL'])
                    ->select('ts', 'kode_ikk')
                    ->get();
                foreach ($ipkIkkRows as $row) {
                    $d = Carbon::parse($row->ts)->format('Y-m-d');
                    $k = trim((string) ($row->kode_ikk ?? ''));
                    if ($k !== '' && Carbon::parse($d)->lt($cutoffDt)) {
                        // Tambahkan ke daftar IKK per hari
                        if (!isset($codesPerDay[$d])) {
                            $codesPerDay[$d] = [];
                        }
                        $codesPerDay[$d][$k] = true;
                        // Tandai bahwa IPK sudah terisi untuk kode ini
                        if (!isset($codesWithIpkByDay[$d])) {
                            $codesWithIpkByDay[$d] = [];
                        }
                        $codesWithIpkByDay[$d][$k] = true;
                    }
                }

                // OKK dari MySQL (untuk tanggal < cutoff)
                $okkRows = Okk::whereBetween('ts', [$monthStart, $mysqlEndDate])
                    ->select('ts', 'kode_ikk')
                    ->get();
                foreach ($okkRows as $row) {
                    $d = Carbon::parse($row->ts)->format('Y-m-d');
                    $k = trim((string) ($row->kode_ikk ?? ''));
                    if ($k !== '' && Carbon::parse($d)->lt($cutoffDt)) {
                        // Tambahkan juga ke daftar IKK per hari (untuk kasus OKK ada tapi IPK belum)
                        if (!isset($codesPerDay[$d])) {
                            $codesPerDay[$d] = [];
                        }
                        $codesPerDay[$d][$k] = true;
                        // Tandai bahwa OKK sudah terisi untuk kode ini
                        if (!isset($codesWithOkkByDay[$d])) {
                            $codesWithOkkByDay[$d] = [];
                        }
                        $codesWithOkkByDay[$d][$k] = true;
                    }
                }
            }

            // Konversi codesPerDay dari associative ke indexed array
            foreach ($codesPerDay as $d => $codes) {
                $codesPerDay[$d] = array_keys($codes);
            }

            // Cancel dari MySQL
            $cancelRows = IpkIkk::whereIn('status_pekerjaan', ['Batal', 'BATAL', 'Cancel', 'CANCEL'])
                ->whereBetween('ts', [$monthStart, $monthEnd])
                ->get(['ts', 'kode_ikk']);
            foreach ($cancelRows as $r) {
                $d = Carbon::parse($r->ts)->format('Y-m-d');
                $k = trim((string) ($r->kode_ikk ?? ''));
                if ($k !== '') {
                    if (!isset($cancelPerDay[$d])) {
                        $cancelPerDay[$d] = [];
                    }
                    $cancelPerDay[$d][$k] = true;
                }
            }

            // Hitung compliance per hari
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $d = $monthStartCarbon->copy()->addDays($day - 1)->format('Y-m-d');
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
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('API complianceByMonth: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'year' => $year,
            'month' => $month,
            'complianceByDay' => $complianceByDay,
        ]);
    }

    /**
     * API: detail IPK/OKK per tanggal untuk satu work permit (lazy load di dashboard weekly).
     */
    public function getIkkDailyDetails(Request $request): JsonResponse
    {
        $wpId = trim((string) $request->query('work_permit_id', ''));
        if ($wpId === '') {
            return response()->json(['success' => false, 'message' => 'work_permit_id wajib'], 422);
        }

        $filterWeek = $request->query('week', '');
        if ($filterWeek === '' || !preg_match('/^\d{4}-W\d{2}$/', $filterWeek)) {
            return response()->json(['success' => false, 'message' => 'week tidak valid (format YYYY-Www)'], 422);
        }

        $filterSite = $request->query('site');
        if ($filterSite === null || trim((string) $filterSite) === '') {
            $filterSite = '';
        } else {
            $filterSite = trim((string) $filterSite);
        }

        preg_match('/^(\d{4})-W(\d{2})$/', $filterWeek, $weekMatches);
        $weekYear = (int) ($weekMatches[1] ?? now()->year);
        $weekNumber = (int) ($weekMatches[2] ?? now()->weekOfYear);
        $weekStartDate = Carbon::now()->setISODate($weekYear, $weekNumber, 1)->startOfDay();
        $weekEndDate = $weekStartDate->copy()->addDays(6)->endOfDay();
        $weekStartStr = $weekStartDate->format('Y-m-d');
        $weekEndStr = $weekEndDate->format('Y-m-d');

        $siteFilterClause = '';
        if ($filterSite !== '') {
            if ($filterSite === 'Lainnya') {
                $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = ''";
            } else {
                $siteFilterClause = " AND trim(COALESCE(wp.ra_site_name, '')) = '" . addslashes($filterSite) . "'";
            }
        }

        $wpIdEsc = addslashes($wpId);
        if (!class_exists(\App\Services\ClickHouseService::class)) {
            return response()->json(['success' => false, 'message' => 'ClickHouse tidak tersedia'], 503);
        }
        $ch = app(\App\Services\ClickHouseService::class);
        if (!method_exists($ch, 'query') || !$ch->isConnected()) {
            return response()->json(['success' => false, 'message' => 'ClickHouse tidak terhubung'], 503);
        }

        $sqlVerify = "
            SELECT wp.start_date AS start_date, wp.end_date AS end_date
            FROM hse_automation.ikk_work_permit AS wp
            INNER JOIN hse_automation.ikk_work_permit_pic AS wp_pic
                ON toString(wp_pic.work_permit_id) = toString(wp.id)
                AND (wp_pic.deleted_at IS NULL OR wp_pic.deleted_at = toDateTime(0))
            LEFT JOIN hse_automation.ikk_m_pic AS m
                ON toString(m.id) = toString(wp_pic.m_pic_id)
            WHERE (wp.deleted_at IS NULL OR wp.deleted_at = toDateTime(0))
                AND toString(wp.id) = '{$wpIdEsc}'
                AND toDate(wp.start_date) <= toDate('{$weekEndStr}')
                AND subtractDays(toDate(wp.end_date), 1) >= toDate('{$weekStartStr}')
                {$siteFilterClause}
            GROUP BY wp.id, wp.start_date, wp.end_date
            HAVING
                sum(if(upper(trim(toString(wp_pic.status))) = 'APPROVED'
                    AND trim(toString(m.m_privilege_id)) = '7d872114-0924-4c6a-880e-49b3c06b5429', 1, 0)) > 0
            LIMIT 1
        ";

        $rows = $ch->query($sqlVerify);
        if (empty($rows)) {
            return response()->json(['success' => false, 'message' => 'Work permit tidak ditemukan atau tidak overlap minggu ini'], 404);
        }

        $startRaw = self::getClickHouseRowValue($rows[0], 'start_date');
        $endRaw = self::getClickHouseRowValue($rows[0], 'end_date');

        $cacheKey = 'dopm_ikk_daily:' . sha1($wpId . '|' . $filterWeek . '|' . $filterSite);
        $ttl = (int) env('DOPM_IKK_DAILY_CACHE_TTL', 120);
        if ($ttl > 0) {
            $cached = Cache::get($cacheKey);
            if (is_array($cached)) {
                return response()->json(['success' => true] + $cached);
            }
        }

        $payload = self::computeDailyDetailsForSingleWorkPermitFromClickHouse(
            $wpId,
            $startRaw,
            $endRaw,
            $weekStartDate,
            $weekEndDate,
            $ch
        );

        if ($ttl > 0) {
            Cache::put($cacheKey, $payload, now()->addSeconds($ttl));
        }

        return response()->json(['success' => true] + $payload);
    }

    /**
     * Export data IKK (work permit) ke file Excel.
     * Format: setiap IKK dipecah per hari aktif dengan detail IPK dan OKK per tanggal.
     */
    public function exportIkkExcel(Request $request): void
    {
        $filterSite = $request->get('site');
        if ($filterSite === null || trim($filterSite) === '') {
            $filterSite = '';
        } else {
            $filterSite = trim($filterSite);
        }

        $filterWeek = $request->get('week', '');
        if ($filterWeek === '' || !preg_match('/^\d{4}-W\d{2}$/', $filterWeek)) {
            $filterWeek = Carbon::now()->format('o-\WW');
        }
        preg_match('/^(\d{4})-W(\d{2})$/', $filterWeek, $weekMatches);
        $weekYear = (int) ($weekMatches[1] ?? now()->year);
        $weekNumber = (int) ($weekMatches[2] ?? now()->weekOfYear);

        $weekStartDate = Carbon::now()->setISODate($weekYear, $weekNumber, 1)->startOfDay();
        $weekEndDate = $weekStartDate->copy()->addDays(6)->endOfDay();
        $weekStartStr = $weekStartDate->format('Y-m-d');
        $weekEndStr = $weekEndDate->format('Y-m-d');

        $ikkList = [];
        $clickHouse = null;
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
                            AND toDate(wp.start_date) <= toDate('{$weekEndStr}')
                            AND subtractDays(toDate(wp.end_date), 1) >= toDate('{$weekStartStr}')
                            {$siteFilterClause}
                        GROUP BY
                            wp.id, wp.code, wp.name, wp.ra_site_name, wp.company_name,
                            wp.status, wp.m_job_id,
                            wp.start_date, wp.end_date, wp.location_name, wp.location_detail_name
                        HAVING
                            sum(if(upper(trim(toString(wp_pic.status))) = 'APPROVED'
                                AND trim(toString(m.m_privilege_id)) = '7d872114-0924-4c6a-880e-49b3c06b5429', 1, 0)) > 0
                        ORDER BY wp.code ASC, wp.start_date ASC
                    ";

                    $wpRows = $clickHouse->query($sqlWorkPermits);

                    if (!empty($wpRows)) {
                        $jobIds = array_values(array_unique(array_filter(array_column($wpRows, 'm_job_id'))));
                        $jobNamesById = [];
                        if (!empty($jobIds)) {
                            $inJobs = implode(',', array_map(fn($id) => "'" . addslashes($id) . "'", $jobIds));
                            $sqlJobs = "SELECT id, name FROM hse_automation.ikk_m_job WHERE id IN ({$inJobs})";
                            $jobRows = $clickHouse->query($sqlJobs);
                            foreach ($jobRows as $jr) {
                                if (!isset($jr['id'])) continue;
                                $jobNamesById[$jr['id']] = $jr['name'] ?? null;
                            }
                        }

                        $wpIds = array_values(array_unique(array_column($wpRows, 'id')));
                        $layersByWp = [];
                        if (!empty($wpIds)) {
                            $inWpIds = implode(',', array_map(fn($id) => "'" . addslashes($id) . "'", $wpIds));
                            $sqlEmp = "SELECT work_permit_id, layer, employee_name, employee_sid FROM hse_automation.ikk_work_permit_employee WHERE work_permit_id IN ({$inWpIds})";
                            $empRows = $clickHouse->query($sqlEmp);
                            foreach ($empRows as $er) {
                                $wpId = $er['work_permit_id'] ?? null;
                                if ($wpId === null || $wpId === '') continue;
                                $layerRaw = $er['layer'] ?? null;
                                if ($layerRaw === null || $layerRaw === '') continue;
                                $layerNum = (int) $layerRaw;
                                if (!in_array($layerNum, [1, 2, 3, 4], true)) continue;
                                if (!isset($layersByWp[$wpId])) $layersByWp[$wpId] = [];
                                if (!isset($layersByWp[$wpId][$layerNum])) $layersByWp[$wpId][$layerNum] = [];
                                $layersByWp[$wpId][$layerNum][] = [
                                    'name' => trim((string) ($er['employee_name'] ?? '')),
                                    'sid' => trim((string) ($er['employee_sid'] ?? '')),
                                ];
                            }
                        }

                        foreach ($wpRows as $row) {
                            $wpId = $row['id'] ?? null;
                            if ($wpId === null || $wpId === '') continue;
                            $layers = $layersByWp[$wpId] ?? [];
                            $rawStatus = $row['status'] ?? null;
                            $statusUpper = $rawStatus !== null ? strtoupper(trim((string) $rawStatus)) : null;
                            if ($statusUpper === 'REJECTED') continue;

                            $statusLabel = $rawStatus;
                            if ($statusUpper === 'APPROVED') $statusLabel = 'Berlaku';
                            elseif ($statusUpper === 'EXPIRED') $statusLabel = 'Kadaluarsa';

                            $ikkList[] = [
                                'id' => $wpId,
                                'code' => $row['code'] ?? null,
                                'site' => $row['ra_site_name'] ?? null,
                                'jenis_ijin_kerja_khusus' => isset($row['m_job_id']) && $row['m_job_id'] ? ($jobNamesById[$row['m_job_id']] ?? null) : null,
                                'nama_pekerjaan' => $row['name'] ?? null,
                                'perusahaan' => $row['company_name'] ?? null,
                                'status' => $statusLabel,
                                'nama_layer_1' => self::formatLayerEmployees($layers[1] ?? []),
                                'sid_layer_1' => self::formatLayerSids($layers[1] ?? []),
                                'nama_layer_2' => self::formatLayerEmployees($layers[2] ?? []),
                                'sid_layer_2' => self::formatLayerSids($layers[2] ?? []),
                                'nama_layer_3' => self::formatLayerEmployees($layers[3] ?? []),
                                'sid_layer_3' => self::formatLayerSids($layers[3] ?? []),
                                'nama_layer_4' => self::formatLayerEmployees($layers[4] ?? []),
                                'sid_layer_4' => self::formatLayerSids($layers[4] ?? []),
                                'start_date' => $row['start_date'] ?? null,
                                'end_date' => $row['end_date'] ?? null,
                                'location_name' => self::getClickHouseRowValue($row, 'location_name'),
                                'location_detail_name' => self::getClickHouseRowValue($row, 'location_detail_name'),
                                'pic_approver_name' => self::formatApproverNames(self::getClickHouseRowValue($row, 'approver_names')),
                            ];
                        }
                    }
                }
            }
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::debug('Export IKK ClickHouse skip: ' . $e->getMessage());
        }

        $byCode = [];
        foreach ($ikkList as $ikk) {
            $c = $ikk['code'] ?? '';
            if ($c !== '' && $c !== null && !isset($byCode[$c])) {
                $byCode[$c] = $ikk;
            }
        }
        $ikkList = array_values($byCode);

        // IKK batal karena reschedule: code_before di ikk_reschedule (ClickHouse) dengan RESCHEDULE + APPROVE
        $rescheduleBatalCodesExport = [];
        if ($clickHouse && method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
            $sqlRescheduleExport = "
                SELECT code_before
                FROM hse_automation.ikk_reschedule
                WHERE upper(trim(toString(reschedule_type))) = 'RESCHEDULE'
                  AND upper(trim(toString(status))) = 'APPROVE'
                  AND code_before IS NOT NULL
                  AND trim(toString(code_before)) != ''
                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
            ";
            $rescheduleRowsExport = $clickHouse->query($sqlRescheduleExport);
            foreach ($rescheduleRowsExport ?? [] as $r) {
                $cb = trim((string) self::getClickHouseRowValue($r, 'code_before'));
                if ($cb !== '') {
                    $rescheduleBatalCodesExport[] = $cb;
                }
            }
            $rescheduleBatalCodesExport = array_values(array_unique($rescheduleBatalCodesExport));
        }
        foreach ($ikkList as &$ikkRef) {
            if (in_array($ikkRef['code'] ?? '', $rescheduleBatalCodesExport, true)) {
                $ikkRef['status'] = 'RESCHEDULE/Batal';
            }
        }
        unset($ikkRef);

        $excelRows = [];
        foreach ($ikkList as $ikk) {
            $wpId = $ikk['id'];
            $wpIdEsc = addslashes($wpId);
            
            try {
                $ikkStartDate = Carbon::parse($ikk['start_date'])->startOfDay();
                // Hari terakhir yang aktif = date(end_date) - 1 (pekerjaan selesai di pagi end_date)
                $ikkLastActiveDate = Carbon::parse($ikk['end_date'])->subDay()->startOfDay();
            } catch (\Throwable $e) {
                continue;
            }

            $effectiveStart = $ikkStartDate->lt($weekStartDate) ? $weekStartDate->copy() : $ikkStartDate->copy();
            $effectiveEnd = $ikkLastActiveDate->gt($weekEndDate) ? $weekEndDate->copy()->startOfDay() : $ikkLastActiveDate->copy();

            $currentDate = $effectiveStart->copy();
            while ($currentDate->lte($effectiveEnd)) {
                $dateStr = $currentDate->format('Y-m-d');
                $dateEsc = addslashes($dateStr);

                $ipkData = ['ada' => 'Tidak', 'kode' => '-', 'detail' => '-'];
                $okkData = ['ada' => 'Tidak', 'kode' => '-', 'detail' => '-'];

                if ($clickHouse && $clickHouse->isConnected()) {
                    $sqlIpk = "
                        SELECT id, code, job_status, start_date, supervisor_id
                        FROM hse_automation.ipk_assessment
                        WHERE work_permit_id = '{$wpIdEsc}'
                          AND toDate(start_date) = toDate('{$dateEsc}')
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                        ORDER BY created_at DESC
                        LIMIT 5
                    ";
                    $ipkRows = $clickHouse->query($sqlIpk);
                    if (!empty($ipkRows)) {
                        $ipkData['ada'] = 'Ya';
                        $ipkCodes = [];
                        $ipkDetails = [];
                        foreach ($ipkRows as $ipkRow) {
                            $ipkCode = self::getClickHouseRowValue($ipkRow, 'code');
                            $ipkJobStatus = self::getClickHouseRowValue($ipkRow, 'job_status');
                            if ($ipkCode) $ipkCodes[] = $ipkCode;
                            if ($ipkJobStatus) $ipkDetails[] = "Status: {$ipkJobStatus}";
                        }
                        $ipkData['kode'] = !empty($ipkCodes) ? implode(', ', array_unique($ipkCodes)) : '-';
                        $ipkData['detail'] = !empty($ipkDetails) ? implode('; ', $ipkDetails) : '-';
                    }

                    $sqlOkk = "
                        SELECT id, code, status, created_at, supervisor_id, indirect_supervisor_id
                        FROM hse_automation.okk_assessment
                        WHERE work_permit_id = '{$wpIdEsc}'
                          AND toDate(created_at) = toDate('{$dateEsc}')
                          AND upper(trim(toString(status))) = 'SUBMITTED'
                          AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                        ORDER BY created_at DESC
                        LIMIT 5
                    ";
                    $okkRows = $clickHouse->query($sqlOkk);
                    if (!empty($okkRows)) {
                        $okkData['ada'] = 'Ya';
                        $okkCodes = [];
                        $okkDetails = [];
                        foreach ($okkRows as $okkRow) {
                            $okkCode = self::getClickHouseRowValue($okkRow, 'code');
                            $okkStatus = self::getClickHouseRowValue($okkRow, 'status');
                            if ($okkCode) $okkCodes[] = $okkCode;
                            if ($okkStatus) $okkDetails[] = "Status: {$okkStatus}";
                        }
                        $okkData['kode'] = !empty($okkCodes) ? implode(', ', array_unique($okkCodes)) : '-';
                        $okkData['detail'] = !empty($okkDetails) ? implode('; ', $okkDetails) : '-';
                    }
                }

                $excelRows[] = [
                    'code' => $ikk['code'],
                    'tanggal' => $currentDate->format('d/m/Y'),
                    'site' => $ikk['site'],
                    'jenis_ijin_kerja_khusus' => $ikk['jenis_ijin_kerja_khusus'],
                    'nama_pekerjaan' => $ikk['nama_pekerjaan'],
                    'perusahaan' => $ikk['perusahaan'],
                    'status' => $ikk['status'],
                    'pic_approver_name' => $ikk['pic_approver_name'],
                    'nama_layer_1' => $ikk['nama_layer_1'],
                    'sid_layer_1' => $ikk['sid_layer_1'],
                    'nama_layer_2' => $ikk['nama_layer_2'],
                    'sid_layer_2' => $ikk['sid_layer_2'],
                    'nama_layer_3' => $ikk['nama_layer_3'],
                    'sid_layer_3' => $ikk['sid_layer_3'],
                    'nama_layer_4' => $ikk['nama_layer_4'],
                    'sid_layer_4' => $ikk['sid_layer_4'],
                    'start_date' => Carbon::parse($ikk['start_date'])->format('d/m/Y'),
                    'end_date' => Carbon::parse($ikk['end_date'])->format('d/m/Y'),
                    'location_name' => $ikk['location_name'],
                    'location_detail_name' => $ikk['location_detail_name'],
                    'ada_ipk' => $ipkData['ada'],
                    'kode_ipk' => $ipkData['kode'],
                    'detail_ipk' => $ipkData['detail'],
                    'ada_okk' => $okkData['ada'],
                    'kode_okk' => $okkData['kode'],
                    'detail_okk' => $okkData['detail'],
                ];

                $currentDate->addDay();
            }
        }

        $headers = [
            'No',
            'Kode IKK',
            'Tanggal',
            'Site',
            'Jenis Ijin Kerja Khusus',
            'Nama Pekerjaan',
            'Perusahaan',
            'Status WP',
            'PIC Approver',
            'Nama Layer 1',
            'SID Layer 1',
            'Nama Layer 2',
            'SID Layer 2',
            'Nama Layer 3',
            'SID Layer 3',
            'Nama Layer 4',
            'SID Layer 4',
            'Start Date',
            'End Date',
            'Location',
            'Location Detail',
            'Ada IPK',
            'Kode IPK',
            'Detail IPK',
            'Ada OKK',
            'Kode OKK',
            'Detail OKK',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data IKK Weekly');

        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);
        $sheet->getStyle('A1:' . $lastCol . '1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FFD9EAD3');

        $rowNum = 2;
        foreach ($excelRows as $idx => $row) {
            $sheet->setCellValue('A' . $rowNum, $idx + 1);
            $sheet->setCellValue('B' . $rowNum, $row['code'] ?? '-');
            $sheet->setCellValue('C' . $rowNum, $row['tanggal'] ?? '-');
            $sheet->setCellValue('D' . $rowNum, $row['site'] ?? '-');
            $sheet->setCellValue('E' . $rowNum, $row['jenis_ijin_kerja_khusus'] ?? '-');
            $sheet->setCellValue('F' . $rowNum, $row['nama_pekerjaan'] ?? '-');
            $sheet->setCellValue('G' . $rowNum, $row['perusahaan'] ?? '-');
            $sheet->setCellValue('H' . $rowNum, $row['status'] ?? '-');
            $sheet->setCellValue('I' . $rowNum, $row['pic_approver_name'] ?? '-');
            $sheet->setCellValue('J' . $rowNum, $row['nama_layer_1'] ?? '-');
            $sheet->setCellValue('K' . $rowNum, $row['sid_layer_1'] ?? '-');
            $sheet->setCellValue('L' . $rowNum, $row['nama_layer_2'] ?? '-');
            $sheet->setCellValue('M' . $rowNum, $row['sid_layer_2'] ?? '-');
            $sheet->setCellValue('N' . $rowNum, $row['nama_layer_3'] ?? '-');
            $sheet->setCellValue('O' . $rowNum, $row['sid_layer_3'] ?? '-');
            $sheet->setCellValue('P' . $rowNum, $row['nama_layer_4'] ?? '-');
            $sheet->setCellValue('Q' . $rowNum, $row['sid_layer_4'] ?? '-');
            $sheet->setCellValue('R' . $rowNum, $row['start_date'] ?? '-');
            $sheet->setCellValue('S' . $rowNum, $row['end_date'] ?? '-');
            $sheet->setCellValue('T' . $rowNum, $row['location_name'] ?? '-');
            $sheet->setCellValue('U' . $rowNum, $row['location_detail_name'] ?? '-');
            $sheet->setCellValue('V' . $rowNum, $row['ada_ipk'] ?? '-');
            $sheet->setCellValue('W' . $rowNum, $row['kode_ipk'] ?? '-');
            $sheet->setCellValue('X' . $rowNum, $row['detail_ipk'] ?? '-');
            $sheet->setCellValue('Y' . $rowNum, $row['ada_okk'] ?? '-');
            $sheet->setCellValue('Z' . $rowNum, $row['kode_okk'] ?? '-');
            $sheet->setCellValue('AA' . $rowNum, $row['detail_okk'] ?? '-');
            $rowNum++;
        }

        foreach (range('A', 'Z') as $colLetter) {
            $sheet->getColumnDimension($colLetter)->setAutoSize(true);
        }
        $sheet->getColumnDimension('AA')->setAutoSize(true);

        $filename = "Data_IKK_Weekly_{$filterWeek}_{$weekStartDate->format('d_M')}-{$weekEndDate->format('d_M_Y')}.xlsx";

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
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
                                if (!empty($durationIds)) {
                                    $durIds = array_unique(array_filter($durationIds));
                                    $durEsc = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", $durIds));
                                    try {
                                        $sqlDur = "
                                            SELECT id, name FROM hse_automation.ikk_m_job_duration
                                            WHERE id IN ({$durEsc})
                                        ";
                                        $durRows = $ch->query($sqlDur);
                                        foreach ($durRows ?? [] as $dr) {
                                            $dId = self::getClickHouseRowValue($dr, 'id');
                                            if ($dId !== null) {
                                                $durationById[(string) $dId] = self::getClickHouseRowValue($dr, 'name');
                                            }
                                        }
                                    } catch (\Throwable $e) {
                                        \Illuminate\Support\Facades\Log::debug('Dashboard weekly modal IPK durasi lookup: ' . $e->getMessage());
                                    }
                                }
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
     * Parse tanggal dari ClickHouse dengan fallback: coba parseEndDate, lalu coba ekstrak Y-m-d dari string.
     * Agar slot-hari IKK tidak hilang saat format datetime aneh (mis. DateTime64).
     */
    private static function parseDateSafe(mixed $value): ?\Carbon\Carbon
    {
        $parsed = self::parseEndDate($value);
        if ($parsed !== null) {
            return $parsed;
        }
        if ($value === null || $value === '') {
            return null;
        }
        $tz = config('app.timezone', 'UTC');
        try {
            $str = is_string($value) ? trim($value) : (string) $value;
            if ($str === '') {
                return null;
            }
            // Coba ambil 10 karakter pertama (Y-m-d) atau 19 (Y-m-d H:i:s)
            if (preg_match('/^(\d{4}-\d{2}-\d{2})/', $str, $m)) {
                return \Carbon\Carbon::parse($m[1], $tz);
            }
            if (preg_match('/^(\d{2}[\/\-]\d{2}[\/\-]\d{4})/', $str, $m)) {
                return \Carbon\Carbon::parse($m[1], $tz);
            }
        } catch (\Throwable $e) {
            // ignore
        }
        return null;
    }

    /**
     * Hitung jumlah hari aktif IKK dalam minggu (overlap start_date..end_date-1 dengan week).
     * Menggunakan parsing robust agar setiap WP yang ada di daftar ikut menyumbang slot-hari.
     */
    private static function computeIkkActiveDaysInWeek(array $row, Carbon $weekStartDate, Carbon $weekEndDate): int
    {
        $ikkStart = self::parseDateSafe(self::getClickHouseRowValue($row, 'start_date'));
        $ikkEnd = self::parseDateSafe(self::getClickHouseRowValue($row, 'end_date'));
        if ($ikkStart === null || $ikkEnd === null) {
            return 0;
        }
        // Hari terakhir aktif = end_date - 1 (pekerjaan selesai di pagi end_date)
        $ikkLastActiveDate = $ikkEnd->copy()->subDay()->startOfDay();
        $effectiveStart = $ikkStart->lt($weekStartDate) ? $weekStartDate->copy() : $ikkStart->copy()->startOfDay();
        $effectiveEnd = $ikkLastActiveDate->gt($weekEndDate) ? $weekEndDate->copy()->startOfDay() : $ikkLastActiveDate->copy();
        if (!$effectiveStart->lte($effectiveEnd)) {
            return 0;
        }
        return $effectiveStart->diffInDays($effectiveEnd) + 1;
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
     * Meta rentang hari aktif per WP dalam satu minggu (sama logika dengan detail harian API).
     *
     * @return array{effective_start: Carbon, effective_end: Carbon, include_end_date_merge: bool, required_dates: string[], required_date_set: array<string, true>, end_date_str: string}|null
     */
    private static function resolveWeeklyDailyMetaForWorkPermit(
        mixed $startDateRaw,
        mixed $endDateRaw,
        Carbon $weekStartDate,
        Carbon $weekEndDate
    ): ?array {
        try {
            $ikkStartDate = Carbon::parse($startDateRaw)->startOfDay();
            $ikkEndDate = Carbon::parse($endDateRaw)->startOfDay();
            $ikkLastActiveDate = $ikkEndDate->copy()->subDay();
        } catch (\Throwable $e) {
            return null;
        }

        $effectiveStart = $ikkStartDate->lt($weekStartDate) ? $weekStartDate->copy()->startOfDay() : $ikkStartDate->copy();
        $effectiveEnd = $ikkLastActiveDate->gt($weekEndDate) ? $weekEndDate->copy()->startOfDay() : $ikkLastActiveDate->copy();
        $includeEndDateMerge = $ikkEndDate->gt($effectiveEnd);

        $requiredDates = [];
        if ($effectiveStart->lte($effectiveEnd)) {
            $cursor = $effectiveStart->copy();
            while ($cursor->lte($effectiveEnd)) {
                $requiredDates[] = $cursor->format('Y-m-d');
                $cursor->addDay();
            }
        }
        if ($includeEndDateMerge) {
            $requiredDates[] = $ikkEndDate->format('Y-m-d');
        }
        $requiredDates = array_values(array_unique($requiredDates));

        if (empty($requiredDates)) {
            return null;
        }

        return [
            'effective_start' => $effectiveStart,
            'effective_end' => $effectiveEnd,
            'include_end_date_merge' => $includeEndDateMerge,
            'required_dates' => $requiredDates,
            'required_date_set' => array_fill_keys($requiredDates, true),
            'end_date_str' => $ikkEndDate->format('Y-m-d'),
        ];
    }

    /**
     * Susun daily_details dari map IPK/OKK (key: work_permit_id|Y-m-d).
     *
     * @param  array{effective_start: Carbon, effective_end: Carbon, include_end_date_merge: bool, end_date_str: string}  $meta
     * @param  array<string, array{code: mixed, job_status: mixed}>  $ipkByWpDate
     * @param  array<string, array{code: mixed, status: mixed}>  $okkByWpDate
     */
    private static function assembleDailyDetailsFromIpkOkkMaps(
        string $wpId,
        array $meta,
        array $ipkByWpDate,
        array $okkByWpDate
    ): array {
        $effectiveStart = $meta['effective_start'];
        $effectiveEnd = $meta['effective_end'];
        $includeEndDateMerge = $meta['include_end_date_merge'];
        $endDateStr = $meta['end_date_str'];

        $dailyDetails = [];
        $ipkCount = 0;
        $okkCount = 0;

        if ($effectiveStart->lte($effectiveEnd)) {
            $cursor = $effectiveStart->copy();
            while ($cursor->lte($effectiveEnd)) {
                $dateStr = $cursor->format('Y-m-d');
                $key = $wpId . '|' . $dateStr;

                $ipkRow = $ipkByWpDate[$key] ?? null;
                $okkRow = $okkByWpDate[$key] ?? null;

                $hasIpk = $ipkRow !== null;
                $hasOkk = $okkRow !== null;

                if ($hasIpk) {
                    $ipkCount++;
                }
                if ($hasOkk) {
                    $okkCount++;
                }

                $dailyDetails[] = [
                    'tanggal' => $cursor->format('d/m/Y'),
                    'hari' => $cursor->locale('id')->translatedFormat('l'),
                    'has_ipk' => $hasIpk,
                    'ipk_kode' => $hasIpk ? ($ipkRow['code'] ?? null) : null,
                    'ipk_status' => $hasIpk ? ($ipkRow['job_status'] ?? null) : null,
                    'has_okk' => $hasOkk,
                    'okk_kode' => $hasOkk ? ($okkRow['code'] ?? null) : null,
                    'okk_status' => $hasOkk ? ($okkRow['status'] ?? null) : null,
                ];

                $cursor->addDay();
            }
        }

        if ($includeEndDateMerge && !empty($dailyDetails)) {
            $lastIdx = count($dailyDetails) - 1;
            $endKey = $wpId . '|' . $endDateStr;

            $ipkEnd = $ipkByWpDate[$endKey] ?? null;
            if ($ipkEnd !== null && !($dailyDetails[$lastIdx]['has_ipk'] ?? false)) {
                $dailyDetails[$lastIdx]['has_ipk'] = true;
                $dailyDetails[$lastIdx]['ipk_kode'] = $ipkEnd['code'] ?? null;
                $dailyDetails[$lastIdx]['ipk_status'] = $ipkEnd['job_status'] ?? null;
                $ipkCount++;
            }

            $okkEnd = $okkByWpDate[$endKey] ?? null;
            if ($okkEnd !== null && !($dailyDetails[$lastIdx]['has_okk'] ?? false)) {
                $dailyDetails[$lastIdx]['has_okk'] = true;
                $dailyDetails[$lastIdx]['okk_kode'] = $okkEnd['code'] ?? null;
                $dailyDetails[$lastIdx]['okk_status'] = $okkEnd['status'] ?? null;
                $okkCount++;
            }
        }

        return [
            'daily_details' => $dailyDetails,
            'ipk_count' => $ipkCount,
            'okk_count' => $okkCount,
            'total_hari' => count($dailyDetails),
        ];
    }

    /**
     * Prefetch detail harian untuk semua baris IKK weekly (2 query IN), untuk render server-side.
     *
     * @param  array<int, object>  $ikkList
     * @return array<string, array{daily_details: array, ipk_count: int, okk_count: int, total_hari: int}>
     */
    private static function computeDailyDetailsBatchForWeeklyIkks(
        array $ikkList,
        Carbon $weekStartDate,
        Carbon $weekEndDate,
        object $ch
    ): array {
        $out = [];
        if (!method_exists($ch, 'query') || !$ch->isConnected() || empty($ikkList)) {
            return $out;
        }

        $metas = [];
        foreach ($ikkList as $ikk) {
            $wpId = trim((string) ($ikk->id ?? ''));
            if ($wpId === '') {
                continue;
            }
            $meta = self::resolveWeeklyDailyMetaForWorkPermit(
                $ikk->start_date ?? null,
                $ikk->end_date ?? null,
                $weekStartDate,
                $weekEndDate
            );
            if ($meta === null) {
                $out[$wpId] = ['daily_details' => [], 'ipk_count' => 0, 'okk_count' => 0, 'total_hari' => 0];
                continue;
            }
            $metas[$wpId] = $meta;
            $wpIds[$wpId] = true;
        }

        if (empty($metas)) {
            return $out;
        }

        $allDates = [];
        foreach ($metas as $m) {
            foreach ($m['required_dates'] as $d) {
                $allDates[] = $d;
            }
        }
        $globalMin = min($allDates);
        $globalMax = max($allDates);
        $minDateEsc = addslashes($globalMin);
        $maxDateEsc = addslashes($globalMax);

        $wpInList = implode(',', array_map(fn ($id) => "'" . addslashes((string) $id) . "'", array_keys($metas)));

        $sqlIpkBatch = "
            SELECT work_permit_id, toDate(start_date) AS d,
                   argMax(code, created_at) AS code,
                   argMax(job_status, created_at) AS job_status
            FROM hse_automation.ipk_assessment
            WHERE work_permit_id IN ({$wpInList})
              AND toDate(start_date) >= toDate('{$minDateEsc}')
              AND toDate(start_date) <= toDate('{$maxDateEsc}')
              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
            GROUP BY work_permit_id, d
        ";
        $ipkFull = [];
        foreach ($ch->query($sqlIpkBatch) ?? [] as $r) {
            $w = (string) self::getClickHouseRowValue($r, 'work_permit_id');
            $dateStr = (string) self::getClickHouseRowValue($r, 'd');
            if ($w === '' || $dateStr === '' || !isset($metas[$w])) {
                continue;
            }
            if (!isset($metas[$w]['required_date_set'][$dateStr])) {
                continue;
            }
            $ipkFull[$w . '|' . $dateStr] = [
                'code' => self::getClickHouseRowValue($r, 'code'),
                'job_status' => self::getClickHouseRowValue($r, 'job_status'),
            ];
        }

        $sqlOkkBatch = "
            SELECT work_permit_id, toDate(created_at) AS d,
                   argMax(code, created_at) AS code,
                   argMax(status, created_at) AS status
            FROM hse_automation.okk_assessment
            WHERE work_permit_id IN ({$wpInList})
              AND toDate(created_at) >= toDate('{$minDateEsc}')
              AND toDate(created_at) <= toDate('{$maxDateEsc}')
              AND upper(trim(toString(status))) = 'SUBMITTED'
              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
            GROUP BY work_permit_id, d
        ";
        $okkFull = [];
        foreach ($ch->query($sqlOkkBatch) ?? [] as $r) {
            $w = (string) self::getClickHouseRowValue($r, 'work_permit_id');
            $dateStr = (string) self::getClickHouseRowValue($r, 'd');
            if ($w === '' || $dateStr === '' || !isset($metas[$w])) {
                continue;
            }
            if (!isset($metas[$w]['required_date_set'][$dateStr])) {
                continue;
            }
            $okkFull[$w . '|' . $dateStr] = [
                'code' => self::getClickHouseRowValue($r, 'code'),
                'status' => self::getClickHouseRowValue($r, 'status'),
            ];
        }

        foreach ($metas as $wpId => $meta) {
            $subIpk = [];
            $subOkk = [];
            foreach ($meta['required_dates'] as $d) {
                $k = $wpId . '|' . $d;
                if (isset($ipkFull[$k])) {
                    $subIpk[$k] = $ipkFull[$k];
                }
                if (isset($okkFull[$k])) {
                    $subOkk[$k] = $okkFull[$k];
                }
            }
            $mergeMeta = [
                'effective_start' => $meta['effective_start'],
                'effective_end' => $meta['effective_end'],
                'include_end_date_merge' => $meta['include_end_date_merge'],
                'end_date_str' => $meta['end_date_str'],
            ];
            $out[$wpId] = self::assembleDailyDetailsFromIpkOkkMaps($wpId, $mergeMeta, $subIpk, $subOkk);
        }

        return $out;
    }

    /**
     * Hitung daily_details + ipk/okk count untuk satu work permit (2 query ClickHouse batch).
     */
    private static function computeDailyDetailsForSingleWorkPermitFromClickHouse(
        string $wpId,
        mixed $startDateRaw,
        mixed $endDateRaw,
        Carbon $weekStartDate,
        Carbon $weekEndDate,
        object $ch
    ): array {
        if (!method_exists($ch, 'query') || !$ch->isConnected()) {
            return ['daily_details' => [], 'ipk_count' => 0, 'okk_count' => 0, 'total_hari' => 0];
        }

        $meta = self::resolveWeeklyDailyMetaForWorkPermit($startDateRaw, $endDateRaw, $weekStartDate, $weekEndDate);
        if ($meta === null) {
            return ['daily_details' => [], 'ipk_count' => 0, 'okk_count' => 0, 'total_hari' => 0];
        }

        $minDate = min($meta['required_dates']);
        $maxDate = max($meta['required_dates']);
        $wpIdEsc = addslashes($wpId);
        $minDateEsc = addslashes($minDate);
        $maxDateEsc = addslashes($maxDate);
        $requiredDateSet = $meta['required_date_set'];

        $sqlIpkBatch = "
            SELECT work_permit_id, toDate(start_date) AS d,
                   argMax(code, created_at) AS code,
                   argMax(job_status, created_at) AS job_status
            FROM hse_automation.ipk_assessment
            WHERE work_permit_id = '{$wpIdEsc}'
              AND toDate(start_date) >= toDate('{$minDateEsc}')
              AND toDate(start_date) <= toDate('{$maxDateEsc}')
              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
            GROUP BY work_permit_id, d
        ";
        $ipkByWpDate = [];
        foreach ($ch->query($sqlIpkBatch) ?? [] as $r) {
            $w = (string) self::getClickHouseRowValue($r, 'work_permit_id');
            $dateStr = (string) self::getClickHouseRowValue($r, 'd');
            if ($w === '' || $dateStr === '' || !isset($requiredDateSet[$dateStr])) {
                continue;
            }
            $ipkByWpDate[$w . '|' . $dateStr] = [
                'code' => self::getClickHouseRowValue($r, 'code'),
                'job_status' => self::getClickHouseRowValue($r, 'job_status'),
            ];
        }

        $sqlOkkBatch = "
            SELECT work_permit_id, toDate(created_at) AS d,
                   argMax(code, created_at) AS code,
                   argMax(status, created_at) AS status
            FROM hse_automation.okk_assessment
            WHERE work_permit_id = '{$wpIdEsc}'
              AND toDate(created_at) >= toDate('{$minDateEsc}')
              AND toDate(created_at) <= toDate('{$maxDateEsc}')
              AND upper(trim(toString(status))) = 'SUBMITTED'
              AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
            GROUP BY work_permit_id, d
        ";
        $okkByWpDate = [];
        foreach ($ch->query($sqlOkkBatch) ?? [] as $r) {
            $w = (string) self::getClickHouseRowValue($r, 'work_permit_id');
            $dateStr = (string) self::getClickHouseRowValue($r, 'd');
            if ($w === '' || $dateStr === '' || !isset($requiredDateSet[$dateStr])) {
                continue;
            }
            $okkByWpDate[$w . '|' . $dateStr] = [
                'code' => self::getClickHouseRowValue($r, 'code'),
                'status' => self::getClickHouseRowValue($r, 'status'),
            ];
        }

        $mergeMeta = [
            'effective_start' => $meta['effective_start'],
            'effective_end' => $meta['effective_end'],
            'include_end_date_merge' => $meta['include_end_date_merge'],
            'end_date_str' => $meta['end_date_str'],
        ];

        return self::assembleDailyDetailsFromIpkOkkMaps($wpId, $mergeMeta, $ipkByWpDate, $okkByWpDate);
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
