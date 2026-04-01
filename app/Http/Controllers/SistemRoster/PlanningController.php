<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Jobs\GeneratePlanningJob;
use App\Jobs\SendPlanningSummaryEmailJob;
use App\Models\CctvCoverage;
use App\Models\CctvData;
use App\Models\InsidenTabel;
use App\Models\RosterPlanning;
use App\Models\RosterPlanningJob;
use App\Models\RosterPlanningKaryawan;
use App\Models\RosterReferenceExclusion;
use App\Services\ClickHouseService;
use App\Services\SistemRoster\PlanningSiteService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PlanningController extends Controller
{
    public function __construct(
        private readonly PlanningSiteService $planningSiteService,
    ) {}

    /** @deprecated Gunakan PlanningSiteService::FILTER_SITES */
    public const PLANNING_FILTER_SITES = PlanningSiteService::FILTER_SITES;

    /** Tabel roster sebagai acuan (non area kritis). Hanya dibaca; saat Save masuk ke roster_plannings. */
    private const ROSTER_REFERENCE_TABLES = [
        'roster_bmo1' => 'BMO 1',
        'roster_bmo3' => 'BMO 3',
        'roster_gmo' => 'GMO',
        'roster_hote' => 'HOTE',
        'roster_lmo' => 'LMO',
    ];

    public function index(Request $request): View
    {
        $filterStartDate = $request->get('start_date', now()->toDateString());
        $filterEndDate = $request->get('end_date', now()->toDateString());
        
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterStartDate)) {
            $filterStartDate = now()->toDateString();
        }
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filterEndDate)) {
            $filterEndDate = now()->toDateString();
        }
        
        if ($filterStartDate > $filterEndDate) {
            $temp = $filterStartDate;
            $filterStartDate = $filterEndDate;
            $filterEndDate = $temp;
        }

        $perPage = (int) $request->get('per_page', 25);
        $perPage = in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 25;

        $search = $request->get('search', '');
        $filterSite = $request->get('filter_site', '');
        $filterPerusahaan = $request->get('filter_perusahaan', '');

        $filterSite = $this->planningSiteService->normalizeFilterSite($filterSite);

        $query = RosterPlanning::with(['karyawans' => function ($q) {
                $q->select('id', 'roster_planning_id', 'user_id', 'nama_karyawan', 'sid_karyawan');
            }])
            ->select([
                'id', 'tanggal', 'source_type', 'source_id', 'site', 'no_ikk',
                'aktivitas', 'lokasi', 'detail_lokasi', 'shift',
                'perusahaan_pic', 'status', 'created_at',
            ])
            ->whereBetween('tanggal', [$filterStartDate, $filterEndDate]);

        $this->planningSiteService->applyIkkDopSourceFilter($query, $filterStartDate, $filterEndDate, $filterSite);

        if ($search !== '') {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('no_ikk', 'like', $term)
                    ->orWhere('aktivitas', 'like', $term)
                    ->orWhere('lokasi', 'like', $term)
                    ->orWhere('detail_lokasi', 'like', $term)
                    ->orWhere('site', 'like', $term)
                    ->orWhere('perusahaan_pic', 'like', $term)
                    ->orWhere('pengawas_langsung', 'like', $term);
            });
        }
        if ($filterPerusahaan !== '') {
            $query->where('perusahaan_pic', 'like', '%' . trim($filterPerusahaan) . '%');
        }

        $plannings = $query->orderByDesc('tanggal')
            ->orderBy('source_type')
            ->orderByDesc('created_at')
            ->paginate($perPage)
            ->withQueryString();

        $grouped = $plannings->getCollection()->groupBy(function ($p) {
            $t = $p->tanggal ? $p->tanggal->format('Y-m-d') : '';
            $s = $p->site ?? '';
            $j = $p->source_type ?? '';

            return $t . '|' . $s . '|' . $j;
        });

        $perusahaanList = $this->planningSiteService->getPerusahaanListForPlanningFilter($filterStartDate, $filterEndDate, $filterSite);

        $latestJob = RosterPlanningJob::whereIn('status', ['pending', 'processing'])
            ->select(['id', 'job_id', 'status', 'start_date', 'end_date', 'created_at'])
            ->orderByDesc('created_at')
            ->first();

        $queueConnection = config('queue.default');

        $groupedRoster = $this->getRosterReferenceGrouped($filterStartDate, $filterEndDate, $filterSite ?? '');
        $exclusionKeys = $this->getRosterExclusionKeys($filterStartDate, $filterEndDate);
        $groupedRoster = $this->applyRosterExclusions($groupedRoster, $exclusionKeys);
        // Untuk evaluasi: "terakhir ada data" dihitung dari data 1 tahun ke belakang (bukan hanya periode filter)
        $evalStartDate = Carbon::parse($filterEndDate)->subYear()->format('Y-m-d');
        $inspeksiHazardLastMap = $this->getInspeksiHazardLastPerLokasi($evalStartDate, $filterEndDate);
        $groupedRoster = $this->enrichRosterWithInspeksiHazardLast($groupedRoster, $inspeksiHazardLastMap);
        $existingRosterKeys = $this->getExistingRosterPlanningKeys($filterStartDate, $filterEndDate);

        $summaryByPersonMerged = $this->buildSummaryByPersonMerged(
            $filterStartDate,
            $filterEndDate,
            $filterSite ?? '',
            $search ?? '',
            $filterPerusahaan ?? '',
            $groupedRoster
        );

        return view('SistemRoster.planning.index', [
            'plannings' => $plannings,
            'grouped' => $grouped,
            'groupedRoster' => $groupedRoster,
            'existingRosterKeys' => $existingRosterKeys,
            'filterStartDate' => $filterStartDate,
            'filterEndDate' => $filterEndDate,
            'perPage' => $perPage,
            'search' => $search,
            'filterSite' => $filterSite,
            'filterPerusahaan' => $filterPerusahaan,
            'planningFilterSites' => PlanningSiteService::FILTER_SITES,
            'planningSiteTabs' => $this->planningSiteService->getPlanningSiteTabs(),
            'perusahaanList' => $perusahaanList,
            'users' => [],
            'latestJob' => $latestJob,
            'queueConnection' => $queueConnection,
            'summaryByPersonMerged' => $summaryByPersonMerged,
        ]);
    }

    /**
     * Summary per orang: dari planning (assign) — siapa harus mengunjungi mana saja.
     * Key = nama_karyawan, value = array of { tanggal, site, source_type, lokasi, detail_lokasi, aktivitas, no_ikk }.
     */
    private function buildSummaryByPersonPlanning(string $startDate, string $endDate, string $filterSite, ?string $search, string $filterPerusahaan): \Illuminate\Support\Collection
    {
        $search = $search ?? '';
        $filterPerusahaan = $filterPerusahaan ?? '';
        $query = RosterPlanning::with(['karyawans' => function ($q) {
            $q->select('id', 'roster_planning_id', 'user_id', 'nama_karyawan', 'sid_karyawan');
        }])
            ->select([
                'id', 'tanggal', 'source_type', 'source_id', 'site', 'no_ikk',
                'aktivitas', 'lokasi', 'detail_lokasi', 'shift',
                'perusahaan_pic', 'status', 'created_at',
            ])
            ->whereBetween('tanggal', [$startDate, $endDate]);

        $this->planningSiteService->applyIkkDopSourceFilter($query, $startDate, $endDate, $filterSite);

        if ($search !== '') {
            $term = '%' . trim($search) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('no_ikk', 'like', $term)
                    ->orWhere('aktivitas', 'like', $term)
                    ->orWhere('lokasi', 'like', $term)
                    ->orWhere('detail_lokasi', 'like', $term)
                    ->orWhere('site', 'like', $term)
                    ->orWhere('perusahaan_pic', 'like', $term);
            });
        }
        if ($filterPerusahaan !== '') {
            $query->where('perusahaan_pic', 'like', '%' . trim($filterPerusahaan) . '%');
        }

        $items = $query->orderBy('tanggal')->orderBy('site')->limit(3000)->get();

        $byPerson = collect();
        foreach ($items as $p) {
            foreach ($p->karyawans ?? [] as $k) {
                $nama = trim((string) ($k->nama_karyawan ?? ''));
                if ($nama === '') {
                    $nama = 'Tanpa Nama';
                }
                if (!$byPerson->has($nama)) {
                    $byPerson->put($nama, collect());
                }
                $byPerson->get($nama)->push((object)[
                    'tanggal' => $p->tanggal,
                    'site' => $p->site ?? '-',
                    'source_type' => $p->source_type ?? '-',
                    'lokasi' => $p->lokasi ?? '-',
                    'detail_lokasi' => $p->detail_lokasi ?? '-',
                    'aktivitas' => $p->aktivitas ?? '-',
                    'no_ikk' => $p->no_ikk ?? '-',
                ]);
            }
        }
        return $byPerson->map(fn ($list) => $list->values())->sortKeys();
    }

    /**
     * Summary per orang: dari roster acuan (jika tidak ada perubahan) — lokasi + detail lokasi per nama.
     * Key = nama, value = array of { date_ins, site, lokasi, detail_lokasi }.
     */
    private function buildSummaryByPersonRoster(\Illuminate\Support\Collection $groupedRoster): \Illuminate\Support\Collection
    {
        $byPerson = collect();
        foreach ($groupedRoster as $items) {
            foreach ($items as $r) {
                $nama = trim((string) ($r->nama ?? ''));
                if ($nama === '') {
                    $nama = 'Tanpa Nama';
                }
                if (!$byPerson->has($nama)) {
                    $byPerson->put($nama, collect());
                }
                $byPerson->get($nama)->push((object)[
                    'date_ins' => $r->date_ins ?? null,
                    'site' => $r->site ?? '-',
                    'lokasi' => $r->lokasi ?? '-',
                    'detail_lokasi' => $r->detail_lokasi ?? '-',
                ]);
            }
        }
        return $byPerson->map(fn ($list) => $list->values())->sortKeys();
    }

    /**
     * Summary per orang: gabungan dari Planning (IKK/DOP/Roster assign) + Roster acuan — satu list per orang.
     * Key = nama, value = array of { tanggal, site, source_type (IKK|DOP|Roster), lokasi, detail_lokasi, aktivitas }.
     */
    private function buildSummaryByPersonMerged(string $startDate, string $endDate, string $filterSite, ?string $search, string $filterPerusahaan, \Illuminate\Support\Collection $groupedRoster): \Illuminate\Support\Collection
    {
        $fromPlanning = $this->buildSummaryByPersonPlanning($startDate, $endDate, $filterSite, $search ?? '', $filterPerusahaan ?? '');
        $fromRoster = $this->buildSummaryByPersonRoster($groupedRoster);

        $allNames = $fromPlanning->keys()->merge($fromRoster->keys())->unique()->sort()->values();

        $merged = collect();
        foreach ($allNames as $nama) {
            $items = collect();

            foreach ($fromPlanning->get($nama, []) as $it) {
                $items->push((object)[
                    'tanggal' => $it->tanggal,
                    'site' => $it->site ?? '-',
                    'source_type' => $it->source_type ?? '-',
                    'lokasi' => $it->lokasi ?? '-',
                    'detail_lokasi' => $it->detail_lokasi ?? '-',
                    'aktivitas' => $it->aktivitas ?? '-',
                ]);
            }

            foreach ($fromRoster->get($nama, []) as $it) {
                $tanggal = $it->date_ins ? Carbon::parse($it->date_ins) : null;
                $items->push((object)[
                    'tanggal' => $tanggal,
                    'site' => $it->site ?? '-',
                    'source_type' => 'Roster',
                    'lokasi' => $it->lokasi ?? '-',
                    'detail_lokasi' => $it->detail_lokasi ?? '-',
                    'aktivitas' => '-',
                ]);
            }

            $merged->put($nama, $items->sortBy(function ($it) {
                if (!$it->tanggal) {
                    return '9999-99-99';
                }
                return $it->tanggal instanceof \DateTimeInterface ? $it->tanggal->format('Y-m-d') : $it->tanggal;
            })->values());
        }

        return $merged->sortKeys();
    }

    /**
     * Data dari tabel roster (acuan awal, non area kritis). Dikelompokkan per tanggal|site|Roster.
     */
    private function getRosterReferenceGrouped(string $startDate, string $endDate, ?string $filterSite = ''): \Illuminate\Support\Collection
    {
        $filterSite = $filterSite ?? '';
        $groups = collect();
        foreach (self::ROSTER_REFERENCE_TABLES as $tableName => $siteLabel) {
            if (! $this->planningSiteService->rosterSiteLabelMatchesFilter($siteLabel, $filterSite ?? '')) {
                continue;
            }
            try {
                $rows = DB::table($tableName)
                    ->whereBetween('date_ins', [$startDate, $endDate])
                    ->orderBy('date_ins')
                    ->orderBy('nama')
                    ->get();
            } catch (\Throwable $e) {
                Log::warning("PlanningController: could not read roster table {$tableName}: " . $e->getMessage());
                continue;
            }
            foreach ($rows as $row) {
                $tanggalStr = $row->date_ins ? \Carbon\Carbon::parse($row->date_ins)->format('Y-m-d') : '';
                if ($tanggalStr === '') {
                    continue;
                }
                $key = $tanggalStr . '|' . $siteLabel . '|Roster';
                if (!$groups->has($key)) {
                    $groups->put($key, collect());
                }
                $lokasi = isset($row->lokasi) ? $row->lokasi : (isset($row->lokasi_kerja) ? $row->lokasi_kerja : null);
                $sublokasi = isset($row->sublokasi) ? $row->sublokasi : (isset($row->detail_lokasi) ? $row->detail_lokasi : null);
                $groups->get($key)->push((object)[
                    'date_ins' => $row->date_ins,
                    'nama' => $row->nama ?? 'Tanpa Nama',
                    'lokasi' => $lokasi,
                    'detail_lokasi' => $sublokasi,
                    'site' => $siteLabel,
                    'roster_table' => $tableName,
                ]);
            }
        }
        return $groups->map(function ($items) {
            return $items->sortBy('nama')->values();
        });
    }

    /**
     * Key-key exclusion untuk (tanggal|site|roster_table|nama|lokasi|detail_lokasi) — normalisasi spasi.
     */
    private function getRosterExclusionKeys(string $startDate, string $endDate): \Illuminate\Support\Collection
    {
        return RosterReferenceExclusion::whereBetween('tanggal', [$startDate, $endDate])
            ->get()
            ->map(function ($e) {
                $t = $e->tanggal ? $e->tanggal->format('Y-m-d') : '';
                $lok = preg_replace('/\s+/', ' ', trim((string) ($e->lokasi ?? '')));
                $det = preg_replace('/\s+/', ' ', trim((string) ($e->detail_lokasi ?? '')));
                return $t . '|' . ($e->site ?? '') . '|' . ($e->roster_table ?? '') . '|' . ($e->nama ?? '') . '|' . $lok . '|' . $det;
            })
            ->flip();
    }

    /**
     * Hapus item roster yang ada di daftar exclusion (take out lokasi + detail lokasi acuan).
     */
    private function applyRosterExclusions(\Illuminate\Support\Collection $groupedRoster, \Illuminate\Support\Collection $exclusionKeys): \Illuminate\Support\Collection
    {
        if ($exclusionKeys->isEmpty()) {
            return $groupedRoster;
        }
        return $groupedRoster->map(function ($items) use ($exclusionKeys) {
            return $items->filter(function ($item) use ($exclusionKeys) {
                $tanggalStr = $item->date_ins ? Carbon::parse($item->date_ins)->format('Y-m-d') : '';
                $lok = preg_replace('/\s+/', ' ', trim((string) ($item->lokasi ?? '')));
                $det = preg_replace('/\s+/', ' ', trim((string) ($item->detail_lokasi ?? '')));
                $key = $tanggalStr . '|' . ($item->site ?? '') . '|' . ($item->roster_table ?? '') . '|' . ($item->nama ?? '') . '|' . $lok . '|' . $det;
                return !$exclusionKeys->has($key);
            })->values();
        });
    }

    /**
     * Untuk setiap (lokasi, detail_lokasi): tanggal terakhir inspeksi hazard & subketidaksesuaian dari record terakhir.
     * Sumber: ClickHouse hse_automation.aaj_car_all_year_from_dav.
     * Key = "normalized_lokasi|normalized_detail_lokasi", value = ['last_date' => 'Y-m-d', 'subketidaksesuaian' => '...'].
     */
    private function getInspeksiHazardLastPerLokasi(string $startDate, string $endDate): array
    {
        $map = [];
        $sql = "
            SELECT
                replaceRegexpAll(trim(ifNull(toString(nama_lokasi), '')), '\\\\s+', ' ') AS lokasi,
                replaceRegexpAll(trim(ifNull(toString(nama_detail_lokasi), '')), '\\\\s+', ' ') AS detail_lokasi,
                argMax(toDate(tanggal_pembuatan, 'Asia/Makassar'), tanggal_pembuatan) AS last_date,
                argMax(ifNull(toString(subketidaksesuaian), ''), tanggal_pembuatan) AS subketidaksesuaian
            FROM hse_automation.aaj_car_all_year_from_dav
            WHERE jenis_laporan IN ('HAZARD', 'INSPEKSI')
                AND tanggal_pembuatan IS NOT NULL
                AND toDate(tanggal_pembuatan, 'Asia/Makassar') >= toDate('" . addslashes($startDate) . "')
                AND toDate(tanggal_pembuatan, 'Asia/Makassar') <= toDate('" . addslashes($endDate) . "')
            GROUP BY lokasi, detail_lokasi
        ";
        try {
            if (!class_exists(ClickHouseService::class)) {
                return $map;
            }
            $ch = app(ClickHouseService::class);
            if (!method_exists($ch, 'query') || !$ch->isConnected()) {
                return $map;
            }
            $results = $ch->query($sql);
            if (empty($results)) {
                return $map;
            }
            foreach ($results as $row) {
                $lokasi = isset($row['lokasi']) ? preg_replace('/\s+/', ' ', trim((string) $row['lokasi'])) : '';
                $detailLokasi = isset($row['detail_lokasi']) ? preg_replace('/\s+/', ' ', trim((string) $row['detail_lokasi'])) : '';
                $lastDate = $row['last_date'] ?? null;
                $lastDateStr = null;
                if ($lastDate !== null && $lastDate !== '') {
                    $lastDateStr = is_string($lastDate) ? substr($lastDate, 0, 10) : Carbon::parse($lastDate)->format('Y-m-d');
                }
                $subketidaksesuaian = isset($row['subketidaksesuaian']) ? trim((string) $row['subketidaksesuaian']) : '';
                $key = $lokasi . '|' . $detailLokasi;
                $map[$key] = [
                    'last_date' => $lastDateStr,
                    'subketidaksesuaian' => $subketidaksesuaian,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('PlanningController getInspeksiHazardLastPerLokasi: ' . $e->getMessage());
        }
        return $map;
    }

    /**
     * Enrich setiap item roster dengan last_inspeksi_date dan last_inspeksi_subketidaksesuaian (match lokasi + detail_lokasi).
     */
    private function enrichRosterWithInspeksiHazardLast(\Illuminate\Support\Collection $groupedRoster, array $lastMap): \Illuminate\Support\Collection
    {
        return $groupedRoster->map(function ($items) use ($lastMap) {
            return $items->map(function ($item) use ($lastMap) {
                $lokasi = preg_replace('/\s+/', ' ', trim((string) ($item->lokasi ?? '')));
                $detailLokasi = preg_replace('/\s+/', ' ', trim((string) ($item->detail_lokasi ?? '')));
                $key = $lokasi . '|' . $detailLokasi;
                $data = $lastMap[$key] ?? null;
                $item->last_inspeksi_date = $data['last_date'] ?? null;
                $item->last_inspeksi_subketidaksesuaian = $data['subketidaksesuaian'] ?? null;
                return $item;
            })->values();
        });
    }

    /**
     * Key-key (tanggal|site|roster_table) yang sudah ada di roster_plannings source_type Roster.
     * source_id format: roster_table_md5 (satu per lokasi+detail), key untuk UI tetap tanggal|site|roster_table.
     */
    private function getExistingRosterPlanningKeys(string $startDate, string $endDate): array
    {
        $existing = RosterPlanning::where('source_type', 'Roster')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->get(['tanggal', 'site', 'source_id']);
        $keys = [];
        foreach ($existing as $p) {
            $t = $p->tanggal ? $p->tanggal->format('Y-m-d') : '';
            $s = $p->site ?? '';
            $tid = $p->source_id ?? '';
            $rosterTable = $tid;
            if (preg_match('/^(.+)_[a-f0-9]{32}$/', $tid, $m)) {
                $rosterTable = $m[1];
            }
            if ($t !== '' && $s !== '') {
                $keys[] = $t . '|' . $s . '|' . $rosterTable;
            }
        }
        return array_values(array_unique($keys));
    }

    /**
     * API: Daftar IKK dari hse_automation.ikk_work_permit untuk pilihan input manual ke roster_plannings.
     */
    public function getIkkListForPlanning(Request $request): JsonResponse
    {
        $search = trim((string) $request->get('q', $request->get('search', '')));
        $limit = min(50, max(10, (int) $request->get('limit', 30)));

        $items = [];
        try {
            if (!class_exists(ClickHouseService::class)) {
                return response()->json(['results' => []]);
            }
            $ch = app(ClickHouseService::class);
            if (!method_exists($ch, 'query') || !$ch->isConnected()) {
                return response()->json(['results' => []]);
            }

            $where = "
                (wp.deleted_at IS NULL OR wp.deleted_at = toDateTime(0))
                AND wp.status IN ('APPROVED', 'EXPIRED')
            ";
            if ($search !== '') {
                $where .= " AND (
                    positionCaseInsensitive(wp.code, '" . addslashes($search) . "') > 0
                    OR positionCaseInsensitive(COALESCE(wp.ra_site_name, ''), '" . addslashes($search) . "') > 0
                    OR positionCaseInsensitive(COALESCE(wp.company_name, ''), '" . addslashes($search) . "') > 0
                    OR positionCaseInsensitive(COALESCE(wp.location_name, ''), '" . addslashes($search) . "') > 0
                    OR positionCaseInsensitive(COALESCE(wp.location_detail_name, ''), '" . addslashes($search) . "') > 0
                )";
            }

            $sql = "
                SELECT
                    wp.id,
                    wp.code,
                    wp.status,
                    wp.ra_site_name,
                    wp.company_name,
                    wp.m_job_id,
                    wp.start_date,
                    wp.end_date,
                    wp.location_name,
                    wp.location_detail_name,
                    wp.location_detail_id
                FROM hse_automation.ikk_work_permit AS wp
                WHERE {$where}
                ORDER BY wp.start_date DESC, wp.id DESC
                LIMIT {$limit}
            ";
            $rows = $ch->query($sql);

            $jobIds = [];
            $wpIds = [];
            foreach ($rows ?? [] as $row) {
                $jobId = $this->getClickHouseRowValue($row, 'm_job_id');
                $wpId = $this->getClickHouseRowValue($row, 'id');
                if (is_array($wpId)) {
                    $wpId = $wpId[0] ?? null;
                }
                $wpId = $wpId !== null && $wpId !== '' ? (string) $wpId : null;
                if ($jobId) {
                    $jobIds[$jobId] = true;
                }
                if ($wpId) {
                    $wpIds[$wpId] = true;
                }
            }

            $jobMap = [];
            if (!empty($jobIds)) {
                $inJobs = implode(',', array_map(fn($id) => "'" . addslashes((string) $id) . "'", array_keys($jobIds)));
                $sqlJobs = "SELECT id, name FROM hse_automation.ikk_m_job WHERE id IN ({$inJobs})";
                $jobRows = $ch->query($sqlJobs);
                foreach ($jobRows ?? [] as $jr) {
                    $id = $this->getClickHouseRowValue($jr, 'id');
                    $name = $this->getClickHouseRowValue($jr, 'name');
                    if ($id !== null) {
                        $jobMap[$id] = $name;
                    }
                }
            }

            $employeeMap = [];
            if (!empty($wpIds)) {
                $inWpIds = implode(',', array_map(fn($id) => "'" . addslashes($id) . "'", array_keys($wpIds)));
                $sqlEmp = "
                    SELECT work_permit_id, employee_name
                    FROM hse_automation.ikk_work_permit_employee
                    WHERE work_permit_id IN ({$inWpIds}) AND layer = '1'
                      AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                ";
                $empRows = $ch->query($sqlEmp);
                foreach ($empRows ?? [] as $er) {
                    $empWpId = $this->getClickHouseRowValue($er, 'work_permit_id');
                    $name = $this->getClickHouseRowValue($er, 'employee_name');
                    if ($empWpId && $name) {
                        if (!isset($employeeMap[$empWpId])) {
                            $employeeMap[$empWpId] = $name;
                        } else {
                            $employeeMap[$empWpId] .= ', ' . $name;
                        }
                    }
                }
            }

            foreach ($rows ?? [] as $row) {
                $wpId = $this->getClickHouseRowValue($row, 'id');
                if (is_array($wpId)) {
                    $wpId = $wpId[0] ?? null;
                }
                $wpId = $wpId !== null && $wpId !== '' ? (string) $wpId : null;
                if (!$wpId) {
                    continue;
                }
                $code = $this->getClickHouseRowValue($row, 'code');
                $site = trim($this->getClickHouseRowValue($row, 'ra_site_name') ?? '');
                $companyName = trim($this->getClickHouseRowValue($row, 'company_name') ?? '');
                $jobId = $this->getClickHouseRowValue($row, 'm_job_id');
                $locationName = $this->getClickHouseRowValue($row, 'location_name');
                $locationDetailName = $this->getClickHouseRowValue($row, 'location_detail_name');
                $locationDetailId = $this->getClickHouseRowValue($row, 'location_detail_id');
                $items[] = [
                    'id' => $wpId,
                    'text' => $code . ' — ' . ($site ?: 'Lainnya') . ' · ' . ($locationName ?? '-') . ($locationDetailName ? ' / ' . $locationDetailName : ''),
                    'code' => $code,
                    'site' => $site ?: null,
                    'company_name' => $companyName ?: null,
                    'job_name' => $jobMap[$jobId] ?? null,
                    'location_name' => $locationName,
                    'location_detail_name' => $locationDetailName,
                    'location_detail_id' => $locationDetailId,
                    'pengawas_langsung' => $employeeMap[$wpId] ?? null,
                ];
            }
        } catch (\Throwable $e) {
            Log::warning('PlanningController getIkkListForPlanning: ' . $e->getMessage());
        }

        return response()->json(['results' => $items]);
    }

    /**
     * Simpan IKK pilihan (dari ikk_work_permit) ke roster_plannings untuk satu tanggal.
     */
    public function storeIkkManual(Request $request): JsonResponse|RedirectResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'ikk_id' => 'required|string|max:100',
        ]);

        $tanggal = $request->get('tanggal');
        $wpId = trim($request->get('ikk_id'));

        try {
            if (!class_exists(ClickHouseService::class)) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Layanan IKK tidak tersedia.'], 502);
                }
                return redirect()->back()->with('error', 'Layanan IKK tidak tersedia.');
            }
            $ch = app(ClickHouseService::class);
            if (!method_exists($ch, 'query') || !$ch->isConnected()) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Koneksi IKK tidak tersedia.'], 502);
                }
                return redirect()->back()->with('error', 'Koneksi IKK tidak tersedia.');
            }

            $idEsc = addslashes($wpId);
            $sql = "
                SELECT id, code, status, company_name, m_job_id, start_date, end_date,
                       ra_site_name, location_name, location_detail_name, location_detail_id
                FROM hse_automation.ikk_work_permit
                WHERE id = '{$idEsc}' AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
                LIMIT 1
            ";
            $rows = $ch->query($sql);
            if (empty($rows)) {
                if ($request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'IKK tidak ditemukan.'], 404);
                }
                return redirect()->back()->with('error', 'IKK tidak ditemukan.');
            }

            $row = $rows[0];
            $code = $this->getClickHouseRowValue($row, 'code');
            $companyName = trim($this->getClickHouseRowValue($row, 'company_name') ?? '');
            $jobId = $this->getClickHouseRowValue($row, 'm_job_id');
            $raSiteName = $this->getClickHouseRowValue($row, 'ra_site_name');
            $locationName = $this->getClickHouseRowValue($row, 'location_name');
            $locationDetailName = $this->getClickHouseRowValue($row, 'location_detail_name');
            $locationDetailId = $this->getClickHouseRowValue($row, 'location_detail_id');

            $jobName = null;
            if ($jobId) {
                $inJobs = "'" . addslashes((string) $jobId) . "'";
                $jobRows = $ch->query("SELECT id, name FROM hse_automation.ikk_m_job WHERE id IN ({$inJobs})");
                if (!empty($jobRows)) {
                    $jobName = $this->getClickHouseRowValue($jobRows[0], 'name');
                }
            }

            $sqlEmp = "
                SELECT employee_name FROM hse_automation.ikk_work_permit_employee
                WHERE work_permit_id = '{$idEsc}' AND layer = '1'
                  AND (deleted_at IS NULL OR deleted_at = toDateTime(0))
            ";
            $empRows = $ch->query($sqlEmp);
            $pengawasLangsung = null;
            foreach ($empRows ?? [] as $er) {
                $n = $this->getClickHouseRowValue($er, 'employee_name');
                if ($n) {
                    $pengawasLangsung = $pengawasLangsung === null ? $n : $pengawasLangsung . ', ' . $n;
                }
            }

            $planning = RosterPlanning::updateOrCreate(
                [
                    'source_type' => 'IKK',
                    'source_id' => $wpId,
                    'tanggal' => $tanggal,
                ],
                [
                    'no_ikk' => $code,
                    'site' => $raSiteName,
                    'aktivitas' => $jobName,
                    'lokasi' => $locationName,
                    'detail_lokasi' => $locationDetailName,
                    'id_detail_lokasi' => $locationDetailId,
                    'pengawas_langsung' => $pengawasLangsung,
                    'perusahaan_pic' => $companyName ?: null,
                    'shift' => null,
                    'status' => 'draft',
                ]
            );
            if ($planning->wasRecentlyCreated) {
                $planning->update(['status' => 'draft']);
            }
        } catch (\Throwable $e) {
            Log::error('PlanningController storeIkkManual: ' . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal menyimpan: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'IKK berhasil ditambahkan ke planning.',
                'planning_id' => $planning->id,
            ]);
        }
        return redirect()->back()->with('success', 'IKK berhasil ditambahkan ke planning.');
    }

    /**
     * Simpan data roster (acuan) ke roster_plannings. Dipanggil saat user klik "Save ke Planning".
     */
    public function saveRosterToPlanning(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'roster_table' => 'required|string|in:' . implode(',', array_keys(self::ROSTER_REFERENCE_TABLES)),
            'emails' => 'nullable|array',
            'emails.*' => 'email',
        ]);

        $tanggal = $request->get('tanggal');
        $rosterTable = $request->get('roster_table');
        $siteLabel = self::ROSTER_REFERENCE_TABLES[$rosterTable] ?? $rosterTable;

        try {
            $rows = DB::table($rosterTable)
                ->whereDate('date_ins', $tanggal)
                ->orderBy('nama')
                ->get();
        } catch (\Throwable $e) {
            Log::warning("PlanningController saveRosterToPlanning: " . $e->getMessage());
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Gagal membaca data roster.'], 422);
            }
            return redirect()->back()->with('error', 'Gagal membaca data roster.');
        }

        $exclusionKeys = RosterReferenceExclusion::where('tanggal', $tanggal)
            ->where('roster_table', $rosterTable)
            ->where('site', $siteLabel)
            ->get()
            ->map(function ($e) {
                $lok = preg_replace('/\s+/', ' ', trim((string) ($e->lokasi ?? '')));
                $det = preg_replace('/\s+/', ' ', trim((string) ($e->detail_lokasi ?? '')));
                return ($e->nama ?? '') . '|' . $lok . '|' . $det;
            })
            ->flip();

        $rows = $rows->filter(function ($row) use ($exclusionKeys, $rosterTable) {
            $lok = isset($row->lokasi) ? $row->lokasi : (isset($row->lokasi_kerja) ? $row->lokasi_kerja : '');
            $det = isset($row->sublokasi) ? $row->sublokasi : (isset($row->detail_lokasi) ? $row->detail_lokasi : '');
            $lok = preg_replace('/\s+/', ' ', trim((string) $lok));
            $det = preg_replace('/\s+/', ' ', trim((string) $det));
            $key = ($row->nama ?? '') . '|' . $lok . '|' . $det;
            return !$exclusionKeys->has($key);
        })->values();

        if ($rows->isEmpty()) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Tidak ada data roster untuk tanggal ini.'], 422);
            }
            return redirect()->back()->with('error', 'Tidak ada data roster untuk tanggal ini.');
        }

        // Group by (lokasi, detail_lokasi) — satu roster_plannings per lokasi + detail lokasi, karyawan di roster_planning_karyawans
        $grouped = collect($rows)->groupBy(function ($row) {
            $lok = isset($row->lokasi) ? $row->lokasi : (isset($row->lokasi_kerja) ? $row->lokasi_kerja : '');
            $det = isset($row->sublokasi) ? $row->sublokasi : (isset($row->detail_lokasi) ? $row->detail_lokasi : '');
            return preg_replace('/\s+/', ' ', trim((string) $lok)) . '|' . preg_replace('/\s+/', ' ', trim((string) $det));
        });

        $createdSourceIds = [];
        foreach ($grouped as $lokasiKey => $groupRows) {
            $firstRow = $groupRows->first();
            $lokasi = isset($firstRow->lokasi) ? $firstRow->lokasi : (isset($firstRow->lokasi_kerja) ? $firstRow->lokasi_kerja : null);
            $detailLokasi = isset($firstRow->sublokasi) ? $firstRow->sublokasi : (isset($firstRow->detail_lokasi) ? $firstRow->detail_lokasi : null);
            $sourceId = $rosterTable . '_' . md5($lokasiKey);
            if (strlen($sourceId) > 100) {
                $sourceId = $rosterTable . '_' . substr(md5($lokasiKey), 0, 32);
            }

            $planning = RosterPlanning::updateOrCreate(
                [
                    'source_type' => 'Roster',
                    'source_id' => $sourceId,
                    'tanggal' => $tanggal,
                ],
                [
                    'site' => $siteLabel,
                    'aktivitas' => 'Non Area Kritis',
                    'no_ikk' => null,
                    'lokasi' => $lokasi,
                    'detail_lokasi' => $detailLokasi,
                    'shift' => null,
                    'perusahaan_pic' => null,
                    'status' => 'assigned',
                ]
            );

            $planning->karyawans()->delete();
            foreach ($groupRows as $row) {
                $sid = isset($row->sid) ? $row->sid : (isset($row->nik) ? $row->nik : null);
                $planning->karyawans()->create([
                    'user_id' => null,
                    'nama_karyawan' => $row->nama ?? 'Tanpa Nama',
                    'sid_karyawan' => $sid,
                ]);
            }
            $createdSourceIds[] = $sourceId;
        }

        // Hapus roster_plannings untuk tanggal+roster_table ini yang tidak lagi ada di acuan (lokasi dihapus / berubah)
        RosterPlanning::where('source_type', 'Roster')
            ->where('tanggal', $tanggal)
            ->where('source_id', 'like', $rosterTable . '_%')
            ->whereNotIn('source_id', $createdSourceIds)
            ->delete();

        $count = $grouped->count();
        $emails = $request->input('emails', []);
        $emails = is_array($emails) ? array_values(array_filter(array_map('trim', $emails))) : [];
        if (! empty($emails)) {
            SendPlanningSummaryEmailJob::dispatch($emails, $tanggal, $rosterTable, $siteLabel);
        }
        if ($request->wantsJson()) {
            $msg = "Data roster berhasil disimpan ke planning ({$count} lokasi).";
            if (! empty($emails)) {
                $msg .= ' Summary akan dikirim ke ' . count($emails) . ' email.';
            }
            return response()->json(['success' => true, 'message' => $msg, 'planning_id' => null]);
        }
        return redirect()->back()->with('success', "Data roster berhasil disimpan ke planning ({$count} lokasi).");
    }

    /**
     * Take out (exclude) satu lokasi + detail lokasi dari acuan roster — untuk bahan evaluasi / setting ulang.
     */
    public function excludeRosterLocation(Request $request): JsonResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'roster_table' => 'required|string|in:' . implode(',', array_keys(self::ROSTER_REFERENCE_TABLES)),
            'site' => 'required|string|max:100',
            'nama' => 'required|string|max:255',
            'lokasi' => 'nullable|string|max:255',
            'detail_lokasi' => 'nullable|string|max:255',
        ]);

        RosterReferenceExclusion::firstOrCreate([
            'tanggal' => $request->get('tanggal'),
            'site' => $request->get('site'),
            'roster_table' => $request->get('roster_table'),
            'nama' => $request->get('nama'),
            'lokasi' => $request->get('lokasi'),
            'detail_lokasi' => $request->get('detail_lokasi'),
        ]);

        return response()->json(['success' => true, 'message' => 'Lokasi acuan telah dihapus dari tampilan. Gunakan "Setting ulang" untuk mengembalikan.']);
    }

    /**
     * Setting ulang: hapus semua exclusion untuk satu grup (tanggal + site + roster_table).
     */
    public function resetRosterExclusions(Request $request): JsonResponse
    {
        $request->validate([
            'tanggal' => 'required|date',
            'roster_table' => 'required|string|in:' . implode(',', array_keys(self::ROSTER_REFERENCE_TABLES)),
            'site' => 'required|string|max:100',
        ]);

        $deleted = RosterReferenceExclusion::where('tanggal', $request->get('tanggal'))
            ->where('roster_table', $request->get('roster_table'))
            ->where('site', $request->get('site'))
            ->delete();

        return response()->json(['success' => true, 'message' => 'Setting ulang berhasil. ' . $deleted . ' lokasi acuan dikembalikan.']);
    }

    public function generate(Request $request): RedirectResponse
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        if ($startDate > $endDate) {
            $temp = $startDate;
            $startDate = $endDate;
            $endDate = $temp;
        }

        $existingJob = RosterPlanningJob::whereIn('status', ['pending', 'processing'])
            ->where('start_date', $startDate)
            ->where('end_date', $endDate)
            ->first();

        if ($existingJob) {
            return redirect()
                ->route('sistem-roster.planning.index', [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ])
                ->with('warning', 'Proses generate untuk periode ini sedang berjalan. Silakan tunggu hingga selesai.');
        }

        $jobId = Str::uuid()->toString();

        $planningJob = RosterPlanningJob::create([
            'job_id' => $jobId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => 'pending',
            'user_id' => Auth::id(),
        ]);

        GeneratePlanningJob::dispatch($jobId, $startDate, $endDate);

        $queueConnection = config('queue.default');
        $queueNote = $queueConnection !== 'sync'
            ? ' Pastikan queue worker berjalan: php artisan queue:work'
            : '';

        return redirect()
            ->route('sistem-roster.planning.index', [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ])
            ->with('info', 'Proses generate planning sedang berjalan di background. Setelah selesai, data DOP & IKK akan muncul di tabel.' . $queueNote);
    }

    public function jobStatus(Request $request): JsonResponse
    {
        $jobId = $request->get('job_id');
        
        if ($jobId) {
            $job = RosterPlanningJob::where('job_id', $jobId)->first();
        } else {
            $job = RosterPlanningJob::whereIn('status', ['pending', 'processing'])
                ->orderByDesc('created_at')
                ->first();
        }

        if (!$job) {
            return response()->json([
                'status' => 'not_found',
                'message' => 'Tidak ada job yang sedang berjalan',
            ]);
        }

        return response()->json([
            'status' => $job->status,
            'job_id' => $job->job_id,
            'start_date' => $job->start_date->format('Y-m-d'),
            'end_date' => $job->end_date->format('Y-m-d'),
            'dop_created' => $job->dop_created,
            'dop_updated' => $job->dop_updated,
            'ikk_created' => $job->ikk_created,
            'ikk_updated' => $job->ikk_updated,
            'error_message' => $job->error_message,
            'started_at' => $job->started_at?->format('H:i:s'),
            'completed_at' => $job->completed_at?->format('H:i:s'),
        ]);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $search = $request->get('search', '');
        
        $query = DB::table('vw_user')
            ->select('id', 'nik', 'nama')
            ->where('is_active', '1');
        
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }
        
        $users = $query->orderBy('nama')->limit(50)->get();
        
        return response()->json($users);
    }

    public function getKaryawans($id): JsonResponse
    {
        $planning = RosterPlanning::with(['karyawans' => function ($q) {
            $q->select('id', 'roster_planning_id', 'user_id', 'nama_karyawan', 'sid_karyawan', 'task', 'reason', 'detail');
        }])->select('id', 'aktivitas', 'tanggal', 'source_type', 'no_ikk', 'lokasi', 'detail_lokasi')
          ->findOrFail($id);

        return response()->json([
            'planning' => $planning,
            'karyawans' => $planning->karyawans,
        ]);
    }

    public function assignKaryawan(Request $request, $id): JsonResponse|RedirectResponse
    {
        $planning = RosterPlanning::findOrFail($id);

        $karyawans = $request->input('karyawans', []);

        $planning->karyawans()->delete();

        if (!empty($karyawans)) {
            foreach ($karyawans as $karyawanData) {
                $nama = $karyawanData['nama_karyawan'] ?? null;
                if (!empty($nama)) {
                    RosterPlanningKaryawan::create([
                        'roster_planning_id' => $planning->id,
                        'user_id' => $karyawanData['user_id'] ?? null,
                        'nama_karyawan' => $nama,
                        'sid_karyawan' => $karyawanData['sid_karyawan'] ?? null,
                        'task' => $karyawanData['task'] ?? null,
                        'reason' => $karyawanData['reason'] ?? null,
                        'detail' => $karyawanData['detail'] ?? null,
                    ]);
                }
            }
        }

        $newStatus = $planning->karyawans()->count() > 0 ? 'assigned' : 'draft';
        $planning->update(['status' => $newStatus]);

        if ($request->expectsJson()) {
            $payload = [
                'success' => true,
                'status' => $newStatus,
                'count' => $planning->karyawans()->count(),
            ];
            if ($newStatus === 'assigned') {
                $payload['planning'] = [
                    'id' => $planning->id,
                    'tanggal' => $planning->tanggal?->format('Y-m-d'),
                    'tanggal_formatted' => $planning->tanggal?->format('d M Y'),
                    'source_type' => $planning->source_type ?? '',
                    'site' => $planning->site ?? '',
                    'no_ikk' => $planning->no_ikk ?? '',
                    'aktivitas' => $planning->aktivitas ?? '',
                    'lokasi' => $planning->lokasi ?? '',
                    'detail_lokasi' => $planning->detail_lokasi ?? '',
                    'perusahaan_pic' => $planning->perusahaan_pic ?? '',
                ];
            }
            return response()->json($payload);
        }

        return redirect()
            ->back()
            ->with('success', 'Karyawan berhasil di-assign.');
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $planning = RosterPlanning::findOrFail($id);

        $validated = $request->validate([
            'shift' => 'nullable|string|max:50',
            'kategori_area' => 'nullable|string|max:255',
            'jenis_sap' => 'nullable|string|max:100',
            'status' => 'nullable|in:draft,assigned,completed',
        ]);

        $planning->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Planning berhasil diupdate.');
    }

    /**
     * Konten pesan WA untuk planning yang di-assign: "Kamu harus mengunjungi" + summary lokasi (insiden, aktivitas kritis, statistik).
     */
    public function waMessageContent($id): JsonResponse
    {
        $planning = RosterPlanning::findOrFail($id);
        $tanggal = $planning->tanggal ? Carbon::parse($planning->tanggal) : today();
        $filterLokasi = $planning->lokasi !== null && $planning->lokasi !== '' ? preg_replace('/\s+/', ' ', trim($planning->lokasi)) : null;
        $filterDetailLokasi = $planning->detail_lokasi !== null && $planning->detail_lokasi !== '' ? preg_replace('/\s+/', ' ', trim($planning->detail_lokasi)) : null;

        // Aktivitas kritis di lokasi ini (termasuk baris planning ini)
        $aktivitasQuery = RosterPlanning::whereDate('tanggal', $tanggal);
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $aktivitasQuery->whereRaw("REGEXP_REPLACE(TRIM(lokasi), '[[:space:]]+', ' ') = ?", [$filterLokasi]);
        }
        if ($filterDetailLokasi !== null && $filterDetailLokasi !== '') {
            $aktivitasQuery->whereRaw("REGEXP_REPLACE(TRIM(detail_lokasi), '[[:space:]]+', ' ') = ?", [$filterDetailLokasi]);
        }
        $aktivitasKritis = $aktivitasQuery->orderBy('lokasi')->orderBy('detail_lokasi')->orderBy('no_ikk')->get();

        // Insiden
        $insidenQuery = InsidenTabel::whereNotNull('lokasi')->where('lokasi', '!=', '');
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $insidenQuery->whereRaw("REGEXP_REPLACE(TRIM(lokasi), '[[:space:]]+', ' ') = ?", [$filterLokasi]);
        }
        $totalInsiden = (int) $insidenQuery->selectRaw('COUNT(DISTINCT no_kecelakaan) as total')->value('total');

        $recentInsidenQuery = InsidenTabel::whereNotNull('lokasi')->where('lokasi', '!=', '');
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $recentInsidenQuery->whereRaw("REGEXP_REPLACE(TRIM(lokasi), '[[:space:]]+', ' ') = ?", [$filterLokasi]);
        }
        $recentInsiden = $recentInsidenQuery->orderByRaw('tahun DESC, bulan DESC, tanggal DESC')->get()->unique('no_kecelakaan')->take(10)->values();

        // CCTV
        $normLokasi = $filterLokasi !== null && $filterLokasi !== '' ? preg_replace('/\s+/', ' ', strtolower(trim($filterLokasi))) : null;
        $normDetail = $filterDetailLokasi !== null && $filterDetailLokasi !== '' ? preg_replace('/\s+/', ' ', strtolower(trim($filterDetailLokasi))) : null;
        $coverageQuery = CctvCoverage::query()->select('id_cctv');
        if ($normLokasi !== null) {
            $coverageQuery->whereRaw('LOWER(REGEXP_REPLACE(TRIM(coverage_lokasi), \'[[:space:]]+\', \' \')) = ?', [$normLokasi]);
        }
        if ($normDetail !== null) {
            $coverageQuery->whereRaw('LOWER(REGEXP_REPLACE(TRIM(coverage_detail_lokasi), \'[[:space:]]+\', \' \')) = ?', [$normDetail]);
        }
        $cctvIds = $coverageQuery->pluck('id_cctv')->unique()->values()->map(fn ($id) => (int) str_replace(',', '', (string) $id))->filter(fn ($id) => $id > 0)->unique()->values()->all();
        $cctvActiveCount = 0;
        if (! empty($cctvIds)) {
            $cctvList = CctvData::query()->select('id', 'kondisi')->whereIn('id', $cctvIds)->get();
            $cctvActiveCount = $cctvList->filter(fn ($c) => strtolower(trim((string) ($c->kondisi ?? ''))) === 'baik')->count();
        }

        // Hazard & Inspeksi Open minggu ini
        $totalHazardWeekly = 0;
        $weekStartStr = $tanggal->copy()->startOfWeek()->format('Y-m-d');
        $weekEndStr = $tanggal->copy()->startOfWeek()->addWeek()->format('Y-m-d');
        $conditions = [
            "jenis_laporan IN ('HAZARD', 'INSPEKSI')",
            "trim(ifNull(status, '')) = 'SUBMITTED'",
            "((tanggal_pembuatan IS NOT NULL AND toDate(tanggal_pembuatan, 'Asia/Makassar') >= toDate('" . addslashes($weekStartStr) . "') AND toDate(tanggal_pembuatan, 'Asia/Makassar') < toDate('" . addslashes($weekEndStr) . "')) "
            . "or (bedraft_date IS NOT NULL AND toDate(bedraft_date, 'Asia/Makassar') >= toDate('" . addslashes($weekStartStr) . "') AND toDate(bedraft_date, 'Asia/Makassar') < toDate('" . addslashes($weekEndStr) . "')))",
        ];
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $conditions[] = "replaceRegexpAll(trim(nama_lokasi), '\\\\s+', ' ') = '" . addslashes($filterLokasi) . "'";
        }
        if ($filterDetailLokasi !== null && $filterDetailLokasi !== '') {
            $conditions[] = "replaceRegexpAll(trim(nama_detail_lokasi), '\\\\s+', ' ') = '" . addslashes($filterDetailLokasi) . "'";
        }
        $whereClause = implode(' AND ', $conditions);
        $sqlCount = "SELECT count() AS total FROM hse_automation.aaj_car_all_year_from_dav WHERE {$whereClause}";
        try {
            if (class_exists(ClickHouseService::class)) {
                $ch = app(ClickHouseService::class);
                if (method_exists($ch, 'query') && $ch->isConnected()) {
                    $results = $ch->query($sqlCount);
                    if (! empty($results)) {
                        $totalHazardWeekly = (int) ($this->getClickHouseRowValue($results[0], 'total') ?? 0);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('PlanningController waMessageContent: hazard weekly count failed: ' . $e->getMessage());
        }

        $lokasiLabel = $filterLokasi ?: '—';
        $detailLabel = $filterDetailLokasi ?: '—';

        // Link Tasklist: tanggal = tanggal planning, lokasi & detail_lokasi dari planning yang di-assign
        $tasklistUrl = url(route('sistem-roster.tasklist.index', [
            'tanggal' => $planning->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'lokasi' => $planning->lokasi ?? '',
            'detail_lokasi' => $planning->detail_lokasi ?? '',
        ]));

        // Build message text — format per baris: Label : nilai (bukan tabel)
        $lines = [];
        $lines[] = 'Kamu harus mengunjungi:';
        $lines[] = '';
        $lines[] = 'Tanggal : ' . ($planning->tanggal?->format('d M Y') ?? '—');
        $lines[] = 'Sumber : ' . ($planning->source_type ?? '—');
        $lines[] = 'Site : ' . ($planning->site ?? '—');
        $lines[] = 'No IKK : ' . ($planning->no_ikk ?? '—');
        $lines[] = 'Aktivitas : ' . ($planning->aktivitas ?? '—');
        $lines[] = 'Lokasi : ' . ($planning->lokasi ?? '—');
        $lines[] = 'Detail Lokasi : ' . ($planning->detail_lokasi ?? '—');
        $lines[] = 'Perusahaan : ' . ($planning->perusahaan_pic ?? '—');
        $lines[] = '';
        $lines[] = 'Link Tasklist (Summary Detail Lokasi):';
        $lines[] = $tasklistUrl;
        $lines[] = '';
        $lines[] = '--- Summary Lokasi: ' . $lokasiLabel . ' / ' . $detailLabel . ' ---';
        $lines[] = '';
        $lines[] = 'Statistik:';
        $lines[] = '• Total Insiden YTD: ' . $totalInsiden;
        $lines[] = '• Total Area/Aktivitas Kritis: ' . $aktivitasKritis->count();
        $lines[] = '• Total CCTV: ' . $cctvActiveCount;
        $lines[] = '• Total Hazard Weekly: ' . $totalHazardWeekly;
        $lines[] = '• Hazard & Inspeksi Open (SUBMITTED) Minggu Ini: ' . $totalHazardWeekly;
        $lines[] = '';

        if ($recentInsiden->isNotEmpty()) {
            $lines[] = 'Detail Insiden:';
            foreach ($recentInsiden as $ins) {
                $lines[] = '  - ' . ($ins->no_kecelakaan ?? '—') . ' | ' . ($ins->lokasi ?? '—') . ($ins->sublokasi ? ' • ' . $ins->sublokasi : '') . ' | ' . ($ins->kategori ?? $ins->status_lpi ?? '—');
            }
            $lines[] = '';
        }

        if ($aktivitasKritis->isNotEmpty()) {
            $lines[] = 'Aktivitas Kritis:';
            foreach ($aktivitasKritis as $row) {
                $lines[] = '  - ' . ($row->aktivitas ?? '—') . ' | IKK: ' . ($row->no_ikk ?? '—') . ' | ' . ($row->lokasi ?? '—') . ' / ' . ($row->detail_lokasi ?? '—') . ' | ' . ($row->perusahaan_pic ?? '—');
            }
        }

        $message = implode("\n", $lines);

        // Karyawan yang di-assign + selular dari vw_user (untuk WA otomatis ke nomor tujuan)
        $karyawansWithSelular = [];
        $assignedKaryawans = $planning->karyawans()->get();
        $userIds = $assignedKaryawans->pluck('user_id')->filter()->unique()->values()->all();
        if (! empty($userIds)) {
            $users = DB::table('vw_user')
                ->whereIn('id', $userIds)
                ->select('id', 'nama', 'selular')
                ->get()
                ->keyBy(function ($u) {
                    return (int) $u->id;
                });
            foreach ($assignedKaryawans as $k) {
                $nama = $k->nama_karyawan ?? '';
                $selular = null;
                $uid = $k->user_id !== null ? (int) $k->user_id : null;
                if ($uid !== null && $users->has($uid)) {
                    $selular = trim($users->get($uid)->selular ?? '');
                }
                $karyawansWithSelular[] = [
                    'nama_karyawan' => $nama,
                    'selular' => $selular !== '' ? $selular : null,
                ];
            }
        }

        return response()->json([
            'message' => $message,
            'planning' => [
                'id' => $planning->id,
                'tanggal_formatted' => $planning->tanggal?->format('d M Y'),
                'lokasi' => $planning->lokasi,
                'detail_lokasi' => $planning->detail_lokasi,
            ],
            'karyawans' => $karyawansWithSelular,
        ]);
    }

    private function getClickHouseRowValue(array $row, string $key): mixed
    {
        $keyLower = strtolower($key);
        foreach ($row as $k => $v) {
            if (strtolower((string) $k) === $keyLower) {
                return $v;
            }
        }
        return $row[$key] ?? null;
    }

    public function destroy($id): RedirectResponse
    {
        $planning = RosterPlanning::findOrFail($id);
        $planning->karyawans()->delete();
        $planning->delete();

        return redirect()
            ->back()
            ->with('success', 'Planning berhasil dihapus.');
    }
}
