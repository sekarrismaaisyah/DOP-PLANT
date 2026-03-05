<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Models\CctvCoverage;
use App\Models\CctvData;
use App\Models\InsidenTabel;
use App\Models\RosterPlanning;
use App\Services\ClickHouseService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class TasklistController extends Controller
{
    /**
     * Menampilkan halaman Tasklist (Detailed Pre-Inspection Intelligence Hub).
     * Summary area kritis dari roster_plannings (lokasi + detail_lokasi) untuk hari ini.
     * Filter opsional: ?lokasi=...&detail_lokasi=... untuk melihat aktivitas di lokasi & detail lokasi tertentu.
     */
    public function index(Request $request): View
    {
        $tanggal = $request->get('tanggal') ? Carbon::parse($request->get('tanggal')) : today();

        $baseQuery = RosterPlanning::whereDate('tanggal', $tanggal);

        // Summary: daftar unik lokasi + detail_lokasi (area kritis) hari ini
        $summaryAreas = (clone $baseQuery)
            ->selectRaw('lokasi, detail_lokasi, COUNT(*) as total_aktivitas')
            ->groupBy('lokasi', 'detail_lokasi')
            ->orderBy('lokasi')
            ->orderBy('detail_lokasi')
            ->get();

        // Normalisasi parameter route: spasi ganda jadi satu spasi agar match dengan data (Pit  PQRT -> Pit PQRT)
        $filterLokasi = $request->get('lokasi');
        $filterLokasi = $filterLokasi !== null && $filterLokasi !== '' ? preg_replace('/\s+/', ' ', trim($filterLokasi)) : null;
        $filterDetailLokasi = $request->get('detail_lokasi');
        $filterDetailLokasi = $filterDetailLokasi !== null && $filterDetailLokasi !== '' ? preg_replace('/\s+/', ' ', trim($filterDetailLokasi)) : null;

        // Aktivitas kritis: bisa difilter by lokasi & detail_lokasi (match normalisasi spasi di DB)
        $aktivitasQuery = (clone $baseQuery);
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $aktivitasQuery->whereRaw("REGEXP_REPLACE(TRIM(lokasi), '[[:space:]]+', ' ') = ?", [$filterLokasi]);
        }
        if ($filterDetailLokasi !== null && $filterDetailLokasi !== '') {
            $aktivitasQuery->whereRaw("REGEXP_REPLACE(TRIM(detail_lokasi), '[[:space:]]+', ' ') = ?", [$filterDetailLokasi]);
        }
        $aktivitasKritis = $aktivitasQuery->orderBy('lokasi')->orderBy('detail_lokasi')->orderBy('no_ikk')->get();

        // Total insiden dari insiden_tabel: COUNT(DISTINCT no_kecelakaan), hanya dari lokasi (no_kecelakaan sama = 1)
        $insidenQuery = InsidenTabel::whereNotNull('lokasi')->where('lokasi', '!=', '');
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $insidenQuery->whereRaw("REGEXP_REPLACE(TRIM(lokasi), '[[:space:]]+', ' ') = ?", [$filterLokasi]);
        }
        $totalInsiden = (int) $insidenQuery->selectRaw('COUNT(DISTINCT no_kecelakaan) as total')->value('total');

        // Recent insiden untuk list "Detail Insiden": no_kecelakaan, lokasi, sublokasi, kategori (filter by lokasi sama)
        $recentInsidenQuery = InsidenTabel::whereNotNull('lokasi')->where('lokasi', '!=', '');
        if ($filterLokasi !== null && $filterLokasi !== '') {
            $recentInsidenQuery->whereRaw("REGEXP_REPLACE(TRIM(lokasi), '[[:space:]]+', ' ') = ?", [$filterLokasi]);
        }
        $recentInsiden = $recentInsidenQuery
            ->orderByRaw('tahun DESC, bulan DESC, tanggal DESC')
            ->get()
            ->unique('no_kecelakaan')
            ->take(10)
            ->values();

        // Semua baris per no_kecelakaan untuk tabel layer (layer, jenis_item_ipls, detail_layer, klasifikasi_layer, keterangan_layer)
        $insidenLayersByNo = collect();
        if ($recentInsiden->isNotEmpty()) {
            $insidenLayersByNo = InsidenTabel::whereIn('no_kecelakaan', $recentInsiden->pluck('no_kecelakaan'))
                ->orderBy('id')
                ->get()
                ->groupBy('no_kecelakaan');
        }

        // CCTV: filter lokasi & detail_lokasi = coverage_lokasi & coverage_detail_lokasi di cctv_coverage.
        // Normalisasi spasi (Pit  PQRT -> Pit PQRT) agar match dengan data di DB.
        $normLokasi = $filterLokasi !== null && $filterLokasi !== '' ? preg_replace('/\s+/', ' ', strtolower(trim($filterLokasi))) : null;
        $normDetail = $filterDetailLokasi !== null && $filterDetailLokasi !== '' ? preg_replace('/\s+/', ' ', strtolower(trim($filterDetailLokasi))) : null;
        $coverageQuery = CctvCoverage::query()->select('id_cctv');
        if ($normLokasi !== null) {
            $coverageQuery->whereRaw('LOWER(REGEXP_REPLACE(TRIM(coverage_lokasi), \'[[:space:]]+\', \' \')) = ?', [$normLokasi]);
        }
        if ($normDetail !== null) {
            $coverageQuery->whereRaw('LOWER(REGEXP_REPLACE(TRIM(coverage_detail_lokasi), \'[[:space:]]+\', \' \')) = ?', [$normDetail]);
        }
        $idCctvValues = $coverageQuery->pluck('id_cctv')->unique()->values();
        // Normalisasi id: bisa integer atau string "10,939" dari Excel
        $cctvIds = $idCctvValues->map(function ($id) {
            return (int) str_replace(',', '', (string) $id);
        })->filter(fn ($id) => $id > 0)->unique()->values()->all();
        $cctvList = collect();
        if (! empty($cctvIds)) {
            $cctvList = CctvData::query()
                ->select('id', 'no_cctv', 'nama_cctv', 'kondisi', 'lokasi_pemasangan', 'link_akses')
                ->whereIn('id', $cctvIds)
                ->orderBy('nama_cctv')
                ->get();
        }
        // Active/Offline berdasarkan kondisi: Baik = Active
        $cctvActiveCount = $cctvList->filter(fn ($c) => strtolower(trim((string) ($c->kondisi ?? ''))) === 'baik')->count();
        $cctvOfflineCount = $cctvList->count() - $cctvActiveCount;

        // Total Hazard & Inspeksi dengan status SUBMITTED (open) minggu ini — untuk card "Hazard & Inspeksi Open (SUBMITTED) Minggu Ini"
        // Sumber: ClickHouse hse_automation.aaj_car_all_year_from_dav
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

        // WHERE untuk list: sama tapi tanpa filter status agar dapat Open + Close untuk statistik modal
        $conditionsList = array_filter($conditions, fn ($c) => strpos($c, "status = 'SUBMITTED'") === false);
        $whereClauseList = implode(' AND ', $conditionsList);

        try {
            if (class_exists(ClickHouseService::class)) {
                /** @var \App\Services\ClickHouseService $clickHouse */
                $clickHouse = app(ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    $results = $clickHouse->query($sqlCount);
                    if (! empty($results)) {
                        $totalHazardWeekly = (int) (static::getClickHouseRowValue($results[0], 'total') ?? 0);
                    }
                }
            }
            // Fallback: koneksi langsung ke server hse_automation (seperti MapBaseController untuk aaj_car)
            if ($totalHazardWeekly === 0 && config('services.clickhouse_custom.host')) {
                $baseUrl = 'http://' . config('services.clickhouse_custom.host') . ':' . (config('services.clickhouse_custom.port') ?: 8123);
                $database = 'hse_automation';
                $url = $baseUrl . '/?database=' . urlencode($database) . '&default_format=JSON';
                $response = \Illuminate\Support\Facades\Http::timeout((int) (config('services.clickhouse_custom.timeout') ?: 60))
                    ->withBasicAuth(
                        config('services.clickhouse_custom.username', 'default'),
                        config('services.clickhouse_custom.password', '')
                    )
                    ->withBody($sqlCount, 'text/plain')
                    ->post($url);
                if ($response->successful()) {
                    $result = $response->json();
                    $data = $result['data'] ?? (isset($result[0]) ? $result : []);
                    if (! empty($data)) {
                        $totalHazardWeekly = (int) (static::getClickHouseRowValue($data[0], 'total') ?? 0);
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TasklistController: total hazard weekly from ClickHouse failed: ' . $e->getMessage());
        }

        // Daftar Hazard & Inspeksi weekly di lokasi (dan detail lokasi) untuk ditampilkan di tabel — hanya saat ada filter lokasi
        $hazardInspeksiWeekly = [];
        if ($filterLokasi !== null && $filterLokasi !== '') {
        $sqlList = "
            SELECT 
                id,
                jenis_laporan,
                nama_lokasi,
                nama_detail_lokasi,
                deskripsi,
                status,
                subketidaksesuaian,
                tanggal_pembuatan,
                bedraft_date,
                nama_pelapor,
                perusahaan_pelapor,
                nilai_resiko,
                nama_kategori
            FROM hse_automation.aaj_car_all_year_from_dav
            WHERE {$whereClauseList}
            ORDER BY toDateTime(ifNull(tanggal_pembuatan, bedraft_date)) DESC
            LIMIT 200
        ";
        try {
            if (class_exists(ClickHouseService::class)) {
                $clickHouse = app(ClickHouseService::class);
                if (method_exists($clickHouse, 'query') && $clickHouse->isConnected()) {
                    $hazardInspeksiWeekly = $clickHouse->query($sqlList) ?? [];
                }
            }
            if (empty($hazardInspeksiWeekly) && config('services.clickhouse_custom.host')) {
                $baseUrl = 'http://' . config('services.clickhouse_custom.host') . ':' . (config('services.clickhouse_custom.port') ?: 8123);
                $url = $baseUrl . '/?database=hse_automation&default_format=JSON';
                $response = \Illuminate\Support\Facades\Http::timeout((int) (config('services.clickhouse_custom.timeout') ?: 60))
                    ->withBasicAuth(config('services.clickhouse_custom.username', 'default'), config('services.clickhouse_custom.password', ''))
                    ->withBody($sqlList, 'text/plain')
                    ->post($url);
                if ($response->successful()) {
                    $result = $response->json();
                    $hazardInspeksiWeekly = $result['data'] ?? (isset($result[0]) ? $result : []);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('TasklistController: hazard inspeksi weekly list from ClickHouse failed: ' . $e->getMessage());
        }
        }

        return view('SistemRoster.tasklist.index', [
            'tanggal' => $tanggal,
            'summaryAreas' => $summaryAreas,
            'aktivitasKritis' => $aktivitasKritis,
            'filterLokasi' => $filterLokasi,
            'filterDetailLokasi' => $filterDetailLokasi,
            'totalInsiden' => $totalInsiden,
            'recentInsiden' => $recentInsiden,
            'insidenLayersByNo' => $insidenLayersByNo,
            'cctvList' => $cctvList,
            'cctvActiveCount' => $cctvActiveCount,
            'cctvOfflineCount' => $cctvOfflineCount,
            'totalHazardWeekly' => $totalHazardWeekly,
            'hazardInspeksiWeekly' => $hazardInspeksiWeekly,
        ]);
    }

    /**
     * Ambil nilai dari satu row hasil query ClickHouse (case-insensitive key), seperti DOPMController.
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
}
