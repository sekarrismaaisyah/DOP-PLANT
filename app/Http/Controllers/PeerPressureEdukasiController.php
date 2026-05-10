<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\PeerPressure\GeneratePeerPressureDashboardHighlightIssueRecommendationAction;
use App\Actions\PeerPressure\GetPeerPressureDeviationModalBreakdownAction;
use App\Actions\PeerPressure\GetPeerPressureDashboardGapMatrixAction;
use App\Actions\PeerPressure\GetPeerPressurePerusahaanPelaksanaanHeatmapAction;
use App\Actions\PeerPressure\GetPeerPressureDashboardEvaluationSummaryAction;
use App\Actions\PeerPressure\GetPeerPressureDashboardInsightCardsAction;
use App\Actions\PeerPressure\GetPeerPressureDashboardKpiStatsAction;
use App\Actions\PeerPressure\GetPeerPressureDashboardComplianceBreakdownAction;
use App\Actions\PeerPressure\GetPeerPressureDashboardWeeklyTrendAction;
use App\Actions\PeerPressure\GetPeerPressureKejadianDetailForDashboardAction;
use App\Actions\PeerPressure\GetPeerPressurePelanggarProfilingDetailAction;
use App\Actions\PeerPressure\GetPeerPressureTbcHighRiskCardsAction;
use App\Actions\PeerPressure\ListPeerPressureDashboardKejadianAction;
use App\Actions\PeerPressure\ListPeerPressureDeviationModalDetailAction;
use App\Actions\PeerPressure\ListPeerPressurePelaksanaanBelumKejadianAction;
use App\Actions\PeerPressure\ListPeerPressurePelaksanaanSelesaiKejadianAction;
use App\Models\PeerPressureKejadianEdukasi;
use App\Services\PeerPressure\PeerPressureKaryawanNitipService;
use App\Services\PeerPressure\PeerPressureResourcesDataAiSummaryService;
use App\Services\PeerPressure\SitePerformanceBriefAnalysisService;
use App\Models\PeerPressurePesertaEdukasi;
use App\Services\ClickHouseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class PeerPressureEdukasiController extends Controller
{
    /** @var array<int, string> Kolom data (0-based) sesuai template */
    private const COL = [
        'tanggal_temuan' => 0,
        'jam_temuan' => 1,
        'kelompok_lokasi_temuan' => 2,
        'lokasi_temuan' => 3,
        'kelompok_lokasi_edukasi' => 4,
        'lokasi_edukasi' => 5,
        'tanggal_edukasi' => 6,
        'jam_edukasi' => 7,
        'perusahaan' => 8,
        'tasklist_temuan' => 9,
        'kronologi_temuan' => 10,
        'kategori_deviasi' => 11,
        'pemimpin_edukasi' => 12,
        'id_berecord' => 13,
        'sid_pelanggar' => 14,
        'nama_pelanggar' => 15,
        'sid_peer' => 16,
        'nama_peer' => 17,
        'jenis_kelompok_kerja' => 18,
        'kelompok_aktivitas_pekerjaan' => 19,
        'aktivitas_pekerjaan' => 20,
        'departemen' => 21,
        'evidence' => 22,
        'durasi_menit' => 23,
        'status' => 24,
        /** Kolom terakhir: file Excel lama (tanpa Site) tetap valid — indeks 0–24 tidak berubah. */
        'site' => 25,
    ];

    public function index(Request $request): View
    {
        $query = PeerPressureKejadianEdukasi::query()->with('peserta')->orderByDesc('tanggal_temuan')->orderByDesc('id');

        $q = trim((string) $request->get('q', ''));
        if ($q !== '') {
            $query->where(function ($sub) use ($q) {
                $sub->where('perusahaan', 'like', '%' . $q . '%')
                    ->orWhere('site', 'like', '%' . $q . '%')
                    ->orWhere('lokasi_temuan', 'like', '%' . $q . '%')
                    ->orWhere('kronologi_temuan', 'like', '%' . $q . '%')
                    ->orWhere('pemimpin_edukasi', 'like', '%' . $q . '%')
                    ->orWhereHas('peserta', function ($p) use ($q) {
                        $p->where('sid', 'like', '%' . $q . '%')
                            ->orWhere('nama', 'like', '%' . $q . '%');
                    });
            });
        }

        $kejadian = $query->paginate(15)->withQueryString();

        return view('peer-pressure-edukasi.index', [
            'kejadian' => $kejadian,
            'q' => $q,
        ]);
    }

    /**
     * Dashboard evaluasi Peer Pressure (data kejadian + peserta dari database).
     */
    public function dashboard(
        Request $request,
        ListPeerPressureDashboardKejadianAction $listDashboardKejadian,
        PeerPressureKaryawanNitipService $karyawanNitip,
        GetPeerPressureDashboardKpiStatsAction $dashboardKpiStats,
        GetPeerPressureDashboardWeeklyTrendAction $weeklyTrend,
        GetPeerPressureDashboardEvaluationSummaryAction $evaluationSummary,
        GetPeerPressureDashboardInsightCardsAction $insightCards,
        GetPeerPressureDeviationModalBreakdownAction $deviationModalBreakdown,
        PeerPressureResourcesDataAiSummaryService $resourcesDataAiSummary,
        SitePerformanceBriefAnalysisService $sitePerformanceBriefAnalysis
    ): View {
        $q = trim((string) $request->get('q', ''));
        $chartPeriodMonth = $request->filled('year') && $request->filled('month');

        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));
            $kpiData = $dashboardKpiStats($chartYear, $chartMonth);
            $weeklyTrendData = $weeklyTrend($chartYear, $chartMonth);
        } else {
            $chartYear = null;
            $chartMonth = null;
            $kpiData = $dashboardKpiStats();
            $weeklyTrendData = $weeklyTrend();
        }

        $kejadian = $listDashboardKejadian($q !== '' ? $q : null);

        $peerSids = [];
        foreach ($kejadian as $k) {
            foreach ($k->peserta->where('peran', 'peer') as $p) {
                $peerSids[] = $p->sid;
            }
        }
        $peerFotoUrls = $karyawanNitip->fotoUrlsByKodeSids($peerSids);

        $peerHazardReportingBySite = $this->loadPeerHazardReportingBySite();
        $peerTbcHighBySite = $this->loadPeerTbcHighBySite();
        $peerTbcBlindspotBySite = $this->loadPeerTbcBlindspotBySite();
        $hazardSiteAllowed = ['__all'];
        if (is_array($peerHazardReportingBySite) && ! empty($peerHazardReportingBySite['sites'])) {
            foreach ($peerHazardReportingBySite['sites'] as $siteLabel) {
                $hazardSiteAllowed[] = (string) $siteLabel;
            }
        }
        if (is_array($peerTbcHighBySite) && ! empty($peerTbcHighBySite['sites'])) {
            foreach ($peerTbcHighBySite['sites'] as $siteLabel) {
                $s = (string) $siteLabel;
                if (! in_array($s, $hazardSiteAllowed, true)) {
                    $hazardSiteAllowed[] = $s;
                }
            }
        }
        if (is_array($peerTbcBlindspotBySite) && ! empty($peerTbcBlindspotBySite['sites'])) {
            foreach ($peerTbcBlindspotBySite['sites'] as $siteLabel) {
                $s = (string) $siteLabel;
                if (! in_array($s, $hazardSiteAllowed, true)) {
                    $hazardSiteAllowed[] = $s;
                }
            }
        }
        $peerGoldenRulesBySite = $this->loadPeerGoldenRulesBySite();
        if (is_array($peerGoldenRulesBySite) && ! empty($peerGoldenRulesBySite['sites'])) {
            foreach ($peerGoldenRulesBySite['sites'] as $siteLabel) {
                $s = (string) $siteLabel;
                if (! in_array($s, $hazardSiteAllowed, true)) {
                    $hazardSiteAllowed[] = $s;
                }
            }
        }
        $peerAreaNonKritisBySite = $this->loadPeerAreaNonKritisBySite();
        if (is_array($peerAreaNonKritisBySite) && ! empty($peerAreaNonKritisBySite['sites'])) {
            foreach ($peerAreaNonKritisBySite['sites'] as $siteLabel) {
                $s = (string) $siteLabel;
                if (! in_array($s, $hazardSiteAllowed, true)) {
                    $hazardSiteAllowed[] = $s;
                }
            }
        }
        $peerAreaKritisBySite = $this->loadPeerAreaKritisBySite();
        if (is_array($peerAreaKritisBySite) && ! empty($peerAreaKritisBySite['sites'])) {
            foreach ($peerAreaKritisBySite['sites'] as $siteLabel) {
                $s = (string) $siteLabel;
                if (! in_array($s, $hazardSiteAllowed, true)) {
                    $hazardSiteAllowed[] = $s;
                }
            }
        }
        $hazardSiteReq = (string) $request->query('hazard_site', '__all');
        $hazardSite = in_array($hazardSiteReq, $hazardSiteAllowed, true) ? $hazardSiteReq : '__all';
        $peerHrEvalFromJson = is_array($peerHazardReportingBySite)
            ? $this->peerMetricEvalFromJson($peerHazardReportingBySite, $hazardSite, '#d97706', 0)
            : null;

        $peerTbcEvalFromJson = is_array($peerTbcHighBySite)
            ? $this->peerMetricEvalFromJson($peerTbcHighBySite, $hazardSite, '#3952bc', 0)
            : null;

        $peerTbcBlindEvalFromJson = is_array($peerTbcBlindspotBySite)
            ? $this->peerMetricEvalFromJson($peerTbcBlindspotBySite, $hazardSite, '#16a34a', 0)
            : null;

        $peerGoldenRulesEvalFromJson = is_array($peerGoldenRulesBySite)
            ? $this->peerMetricEvalFromJson($peerGoldenRulesBySite, $hazardSite, '#c8102e', 0)
            : null;

        $peerAreaNonKritisEvalFromJson = is_array($peerAreaNonKritisBySite)
            ? $this->peerMetricEvalFromJson($peerAreaNonKritisBySite, $hazardSite, '#ea580c', 0)
            : null;

        $peerAreaKritisEvalFromJson = is_array($peerAreaKritisBySite)
            ? $this->peerMetricEvalFromJson($peerAreaKritisBySite, $hazardSite, '#dc2626', 0)
            : null;

        $dashboardView = match (true) {
            $request->routeIs('peer-pressure-edukasi.dashboard') => 'peer-pressure-edukasi.dashboard',
            $request->routeIs('peer-pressure-edukasi.dashboard-performance') => 'peer-pressure-edukasi.DashPerformance',
            $request->routeIs('peer-pressure-edukasi.tematic') => 'peer-pressure-edukasi.tematic',
            default => 'peer-pressure-edukasi.dashboard-peer',
        };

        $deviationModalBreakdownData = $deviationModalBreakdown(
            $chartPeriodMonth ? $chartYear : null,
            $chartPeriodMonth ? $chartMonth : null
        );

        return view($dashboardView, [
            'navActive' => 'overview',
            'kejadian' => $kejadian,
            'peerFotoUrls' => $peerFotoUrls,
            'q' => $q,
            'kpi' => $kpiData,
            'peerResourcesAiSummary' => $resourcesDataAiSummary->generate($kpiData),
            'sitePerformanceBrief' => $sitePerformanceBriefAnalysis->analyze(),
            'weeklyTrend' => $weeklyTrendData,
            'chartYear' => $chartYear,
            'chartMonth' => $chartMonth,
            'chartPeriodMonth' => $chartPeriodMonth,
            'evaluationSummary' =>             $evaluationSummary(
                $chartPeriodMonth ? $chartYear : null,
                $chartPeriodMonth ? $chartMonth : null
            ),
            'insightCards' => $insightCards(
                $chartPeriodMonth ? $chartYear : null,
                $chartPeriodMonth ? $chartMonth : null
            ),
            'deviationModalBreakdown' => $deviationModalBreakdownData,
            'peerHazardReportingBySite' => $peerHazardReportingBySite,
            'hazardSite' => $hazardSite,
            'peerHrEvalFromJson' => $peerHrEvalFromJson,
            'peerTbcHighBySite' => $peerTbcHighBySite,
            'peerTbcEvalFromJson' => $peerTbcEvalFromJson,
            'peerTbcBlindspotBySite' => $peerTbcBlindspotBySite,
            'peerTbcBlindEvalFromJson' => $peerTbcBlindEvalFromJson,
            'peerGoldenRulesBySite' => $peerGoldenRulesBySite,
            'peerGoldenRulesEvalFromJson' => $peerGoldenRulesEvalFromJson,
            'peerAreaNonKritisBySite' => $peerAreaNonKritisBySite,
            'peerAreaNonKritisEvalFromJson' => $peerAreaNonKritisEvalFromJson,
            'peerAreaKritisBySite' => $peerAreaKritisBySite,
            'peerAreaKritisEvalFromJson' => $peerAreaKritisEvalFromJson,
        ]);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadPeerAreaKritisBySite(): ?array
    {
        $path = resource_path('data/peer_pressure_area_kritis_by_site.json');
        if (! is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadPeerAreaNonKritisBySite(): ?array
    {
        $path = resource_path('data/peer_pressure_area_non_kritis_by_site.json');
        if (! is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadPeerGoldenRulesBySite(): ?array
    {
        $path = resource_path('data/peer_pressure_golden_rules_by_site.json');
        if (! is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadPeerTbcBlindspotBySite(): ?array
    {
        $path = resource_path('data/peer_pressure_tbc_blindspot_by_site.json');
        if (! is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadPeerTbcHighBySite(): ?array
    {
        $path = resource_path('data/peer_pressure_tbc_high_by_site.json');
        if (! is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @return array<string, mixed>|null
     */
    private function loadPeerHazardReportingBySite(): ?array
    {
        $path = resource_path('data/peer_pressure_hazard_reporting_by_site.json');
        if (! is_file($path)) {
            return null;
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            return null;
        }
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * Ambil nilai minggu dari baris bySite; key minggu di JSON bisa beda huruf besar/kecil (W14 vs w14).
     *
     * @param  array<string, mixed>  $row
     */
    private function peerMetricWeekValueFromRow(array $row, string $wk): float
    {
        if (array_key_exists($wk, $row)) {
            return (float) $row[$wk];
        }
        foreach ($row as $k => $v) {
            if (is_string($k) && strcasecmp($k, $wk) === 0) {
                return (float) $v;
            }
        }

        return 0.0;
    }

    /**
     * Agregasi mini-chart metrik per site dari JSON (Hazard, TBC, dll.).
     *
     * @param  array<string, mixed>  $json
     * @return array{weeks: list<string>, label: string, bar: string, values: list<float|int>, decimals: int}
     */
    private function peerMetricEvalFromJson(array $json, string $site, string $bar, int $decimals = 0): array
    {
        $weeks = [];
        if (! empty($json['weeks']) && is_array($json['weeks'])) {
            foreach ($json['weeks'] as $w) {
                $weeks[] = (string) $w;
            }
        }
        if ($weeks === []) {
            $weeks = ['W12', 'W13', 'W14', 'W15'];
        }
        $bySite = $json['bySite'] ?? [];
        if (! is_array($bySite)) {
            $bySite = [];
        }
        $values = [];
        if ($site === '__all') {
            foreach ($weeks as $wk) {
                $sum = 0;
                foreach ($bySite as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $sum += (int) round($this->peerMetricWeekValueFromRow($row, $wk));
                }
                $values[] = (float) $sum;
            }
        } else {
            $row = is_array($bySite[$site] ?? null) ? $bySite[$site] : [];
            foreach ($weeks as $wk) {
                $values[] = $this->peerMetricWeekValueFromRow($row, $wk);
            }
        }

        return [
            'weeks' => $weeks,
            'label' => (string) ($json['parameter'] ?? ''),
            'bar' => $bar,
            'values' => $values,
            'decimals' => $decimals,
        ];
    }

    /**
     * Data chart trend mingguan (JSON) untuk filter periode via popup di dashboard.
     */
    public function weeklyTrendData(
        Request $request,
        GetPeerPressureDashboardWeeklyTrendAction $weeklyTrend,
        GetPeerPressureDashboardKpiStatsAction $dashboardKpiStats,
        GetPeerPressureDashboardEvaluationSummaryAction $evaluationSummary,
        GetPeerPressureDashboardInsightCardsAction $insightCards,
        GetPeerPressureDeviationModalBreakdownAction $deviationModalBreakdown
    ): JsonResponse {
        $chartPeriodMonth = $request->filled('year') && $request->filled('month');

        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));

            return response()->json(array_merge(
                $weeklyTrend($chartYear, $chartMonth),
                [
                    'kpi' => $dashboardKpiStats($chartYear, $chartMonth),
                    'evaluation_summary' => $evaluationSummary($chartYear, $chartMonth),
                    'insight_cards' => $insightCards($chartYear, $chartMonth),
                    'deviation_modal_breakdown' => $deviationModalBreakdown($chartYear, $chartMonth),
                ]
            ));
        }

        return response()->json(array_merge(
            $weeklyTrend(),
            [
                'kpi' => $dashboardKpiStats(),
                'evaluation_summary' => $evaluationSummary(),
                'insight_cards' => $insightCards(),
                'deviation_modal_breakdown' => $deviationModalBreakdown(),
            ]
        ));
    }

    /**
     * Matriks gap pelaksanaan vs kepatuhan per kelompok kerja (JSON) — periode sama dengan filter chart.
     */
    public function gapMatrixData(Request $request, GetPeerPressureDashboardGapMatrixAction $gapMatrix): JsonResponse
    {
        $chartPeriodMonth = $request->filled('year') && $request->filled('month');

        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));

            return response()->json($gapMatrix($chartYear, $chartMonth));
        }

        return response()->json($gapMatrix());
    }

    /**
     * Heatmap % pelaksanaan selesai per perusahaan × tanggal temuan (JSON).
     */
    public function perusahaanPelaksanaanHeatmapData(
        Request $request,
        GetPeerPressurePerusahaanPelaksanaanHeatmapAction $heatmap
    ): JsonResponse {
        $chartPeriodMonth = $request->filled('year') && $request->filled('month');

        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));

            return response()->json($heatmap($chartYear, $chartMonth));
        }

        return response()->json($heatmap());
    }

    /**
     * Ringkasan AI: Highlight Issue & Rekomendasi dari agregat dashboard (periode sama dengan filter chart).
     */
    public function dashboardHighlightIssueRecommendation(
        Request $request,
        GeneratePeerPressureDashboardHighlightIssueRecommendationAction $action
    ): JsonResponse {
        $chartPeriodMonth = $request->filled('year') && $request->filled('month');

        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));

            return response()->json($action($chartYear, $chartMonth));
        }

        return response()->json($action(null, null));
    }

    public function complianceBreakdownData(
        Request $request,
        GetPeerPressureDashboardComplianceBreakdownAction $breakdown
    ): JsonResponse {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(50, max(5, (int) $request->query('per_page', 15)));

        $chartPeriodMonth = $request->filled('year') && $request->filled('month');

        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));

            return response()->json($breakdown($chartYear, $chartMonth, $page, $perPage));
        }

        return response()->json($breakdown(null, null, $page, $perPage));
    }

    /**
     * Tabel kejadian pelaksanaan selesai (CLOSED/SELESAI) untuk modal dashboard Peer Pressure.
     */
    public function pelaksanaanSelesaiData(
        Request $request,
        ListPeerPressurePelaksanaanSelesaiKejadianAction $action
    ): JsonResponse {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(50, max(5, (int) $request->query('per_page', 10)));

        $chartPeriodMonth = $request->filled('year') && $request->filled('month');

        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));

            return response()->json($action($chartYear, $chartMonth, $page, $perPage));
        }

        return response()->json($action(null, null, $page, $perPage));
    }

    /**
     * Detail terpaginasi per tab pada modal statistik deviasi (BeRecord / Validasi blindspot / Speak Up Fatigue).
     */
    public function deviationModalDetailData(
        Request $request,
        ListPeerPressureDeviationModalDetailAction $action
    ): JsonResponse {
        $type = (string) $request->query('type', ListPeerPressureDeviationModalDetailAction::TYPE_BERECORD);
        if (! in_array($type, ListPeerPressureDeviationModalDetailAction::allowedTypes(), true)) {
            return response()->json(['message' => 'Parameter type tidak valid.'], 422);
        }

        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(50, max(5, (int) $request->query('per_page', 10)));

        $chartPeriodMonth = $request->filled('year') && $request->filled('month');

        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));

            return response()->json($action($type, $chartYear, $chartMonth, $page, $perPage));
        }

        return response()->json($action($type, null, null, $page, $perPage));
    }

    /**
     * Tabel kejadian pelaksanaan belum selesai (bukan CLOSED/SELESAI) untuk modal dashboard Peer Pressure.
     */
    public function pelaksanaanBelumData(
        Request $request,
        ListPeerPressurePelaksanaanBelumKejadianAction $action
    ): JsonResponse {
        $page = max(1, (int) $request->query('page', 1));
        $perPage = min(50, max(5, (int) $request->query('per_page', 10)));

        $chartPeriodMonth = $request->filled('year') && $request->filled('month');

        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));

            return response()->json($action($chartYear, $chartMonth, $page, $perPage));
        }

        return response()->json($action(null, null, $page, $perPage));
    }

    /**
     * Detail kejadian untuk modal dashboard (JSON).
     */
    public function kejadianDetail(int $id, GetPeerPressureKejadianDetailForDashboardAction $detail): JsonResponse
    {
        return response()->json($detail($id));
    }

    /**
     * Kartu horizontal TBC GENERAL untuk modal KPI (JSON).
     */
    public function tbcHighRiskCards(Request $request, GetPeerPressureTbcHighRiskCardsAction $action): JsonResponse
    {
        $chartPeriodMonth = $request->filled('year') && $request->filled('month');
        if ($chartPeriodMonth) {
            $chartYear = (int) $request->get('year');
            $chartMonth = (int) $request->get('month');
            $chartYear = max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear));
            $chartMonth = max(1, min(12, $chartMonth));

            return response()->json(['cards' => $action($chartYear, $chartMonth)]);
        }

        return response()->json(['cards' => $action(null, null)]);
    }

    /**
     * Detail profiling pelanggar (JSON) untuk modal dashboard — query ?sid=
     */
    public function pelanggarProfilingDetail(
        Request $request,
        GetPeerPressurePelanggarProfilingDetailAction $action
    ): JsonResponse {
        $sid = trim((string) $request->query('sid', ''));
        if ($sid === '') {
            return response()->json(['message' => 'Parameter sid wajib diisi.'], 422);
        }

        return response()->json($action($sid));
    }

    /**
     * Pencarian ringkas CAR/AAJ (nitip.aaj_car_all_year_from_dav) untuk modal Highlight TBC — query ?q=
     */
    public function tbcAajCarSearch(Request $request): JsonResponse
    {
        $q = trim((string) $request->query('q', ''));
        if ($q === '' || mb_strlen($q) > 200) {
            return response()->json(['ok' => true, 'items' => []]);
        }

        try {
            $ch = new ClickHouseService('clickhouse_nitip');
            if (! $ch->isConnected()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'ClickHouse (nitip) tidak terhubung. Periksa CLICKHOUSE_NITIP_* di .env.',
                    'items' => [],
                ], 503);
            }

            $esc = $this->tbcAajEscapeChString($q);
            $digits = preg_replace('/\D/', '', $q) ?? '';
            // Kolom teks bisa String/Nullable — pakai toString agar coalesce aman di semua tipe.
            $parts = [
                "positionCaseInsensitive(toString(coalesce(issue, '')), '{$esc}') > 0",
                "positionCaseInsensitive(toString(coalesce(deskripsi, '')), '{$esc}') > 0",
                "positionCaseInsensitive(toString(coalesce(nama_lokasi, '')), '{$esc}') > 0",
                "positionCaseInsensitive(toString(coalesce(nama_kategori, '')), '{$esc}') > 0",
                "positionCaseInsensitive(toString(coalesce(nama_pelapor, '')), '{$esc}') > 0",
            ];
            if ($digits !== '') {
                $dEsc = $this->tbcAajEscapeChString($digits);
                $parts[] = "replaceAll(toString(id), ',', '') LIKE '%{$dEsc}%'";
            }
            $where = implode(' OR ', $parts);

            $sql = "
                SELECT
                    id,
                    issue,
                    nama_lokasi,
                    toString(tanggal_pembuatan) AS tanggal_pembuatan,
                    status
                FROM aaj_car_all_year_from_dav
                WHERE ({$where})
                ORDER BY coalesce(
                    parseDateTimeBestEffortOrNull(toString(tanggal_pembuatan)),
                    toDateTime(0)
                ) DESC
                LIMIT 40
            ";

            $rows = $ch->query($sql) ?? [];
            $items = [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $idVal = $this->tbcAajRowVal($row, 'id');
                $idNorm = $this->tbcAajNormalizeId((string) $idVal);
                $issue = (string) ($this->tbcAajRowVal($row, 'issue') ?? '');
                $loc = (string) ($this->tbcAajRowVal($row, 'nama_lokasi') ?? '');
                $st = (string) ($this->tbcAajRowVal($row, 'status') ?? '');
                $tp = (string) ($this->tbcAajRowVal($row, 'tanggal_pembuatan') ?? '');
                $label = $idNorm . ' — ' . ($issue !== '' ? mb_substr($issue, 0, 72) : ($loc !== '' ? $loc : $st));
                $items[] = [
                    'id' => $idNorm,
                    'label' => $label,
                    'issue' => $issue,
                    'nama_lokasi' => $loc,
                    'status' => $st,
                    'tanggal_pembuatan' => $tp,
                ];
            }

            return response()->json(['ok' => true, 'items' => $items]);
        } catch (\Throwable $e) {
            Log::error('tbcAajCarSearch: ' . $e->getMessage());

            return response()->json([
                'ok' => false,
                'message' => 'Gagal mengambil data pencarian.',
                'items' => [],
            ], 500);
        }
    }

    /**
     * Satu baris atau batch by ids — query ?id= atau ?ids= (koma dipisah), dari nitip.aaj_car_all_year_from_dav
     */
    public function tbcAajCarShow(Request $request): JsonResponse
    {
        if ($request->filled('ids')) {
            return $this->tbcAajCarBatch($request);
        }

        $idRaw = trim((string) $request->query('id', ''));
        if ($idRaw === '') {
            return response()->json(['ok' => false, 'message' => 'Parameter id atau ids wajib diisi.'], 422);
        }

        $idNorm = $this->tbcAajNormalizeId($idRaw);
        if ($idNorm === '') {
            return response()->json(['ok' => false, 'message' => 'ID tidak valid.'], 422);
        }

        try {
            $ch = new ClickHouseService('clickhouse_nitip');
            if (! $ch->isConnected()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'ClickHouse (nitip) tidak terhubung.',
                ], 503);
            }

            $sql = "
                SELECT *
                FROM aaj_car_all_year_from_dav
                WHERE replaceAll(toString(id), ',', '') = '{$this->tbcAajEscapeChString($idNorm)}'
                LIMIT 1
            ";
            $rows = $ch->query($sql) ?? [];
            $row = isset($rows[0]) && is_array($rows[0]) ? $rows[0] : null;
            if ($row === null) {
                return response()->json(['ok' => false, 'message' => 'Data tidak ditemukan.'], 404);
            }

            return response()->json([
                'ok' => true,
                'card' => $this->tbcAajMapRowToCard($row),
                'record' => $row,
            ]);
        } catch (\Throwable $e) {
            Log::error('tbcAajCarShow: ' . $e->getMessage());

            return response()->json(['ok' => false, 'message' => 'Gagal mengambil detail.'], 500);
        }
    }

    private function tbcAajCarBatch(Request $request): JsonResponse
    {
        $raw = (string) $request->query('ids', '');
        $parts = array_filter(array_map('trim', explode(',', $raw)));
        $normalized = [];
        foreach ($parts as $p) {
            $n = $this->tbcAajNormalizeId($p);
            if ($n !== '' && ! in_array($n, $normalized, true)) {
                $normalized[] = $n;
            }
        }
        if ($normalized === []) {
            return response()->json(['ok' => true, 'cards' => []]);
        }
        if (count($normalized) > 80) {
            return response()->json(['ok' => false, 'message' => 'Maksimal 80 ID per permintaan.'], 422);
        }

        try {
            $ch = new ClickHouseService('clickhouse_nitip');
            if (! $ch->isConnected()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'ClickHouse (nitip) tidak terhubung.',
                    'cards' => [],
                ], 503);
            }

            $inList = implode(',', array_map(function (string $n) {
                return "'" . $this->tbcAajEscapeChString($n) . "'";
            }, $normalized));

            $sql = "
                SELECT *
                FROM aaj_car_all_year_from_dav
                WHERE replaceAll(toString(id), ',', '') IN ({$inList})
            ";
            $rows = $ch->query($sql) ?? [];
            $byId = [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $card = $this->tbcAajMapRowToCard($row);
                $byId[$card['raw_id']] = $card;
            }

            $cards = [];
            foreach ($normalized as $nid) {
                if (isset($byId[$nid])) {
                    $cards[] = $byId[$nid];
                }
            }

            return response()->json(['ok' => true, 'cards' => $cards]);
        } catch (\Throwable $e) {
            Log::error('tbcAajCarBatch: ' . $e->getMessage());

            return response()->json(['ok' => false, 'message' => 'Gagal memuat daftar.', 'cards' => []], 500);
        }
    }

    private function tbcAajEscapeChString(string $s): string
    {
        return str_replace("'", "''", $s);
    }

    private function tbcAajNormalizeId(string $raw): string
    {
        $digits = preg_replace('/\D/', '', $raw) ?? '';

        return $digits;
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function tbcAajRowVal(array $row, string $key): mixed
    {
        $kLower = strtolower($key);
        foreach ($row as $k => $v) {
            if (strtolower((string) $k) === $kLower) {
                return $v;
            }
        }

        return $row[$key] ?? null;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function tbcAajMapRowToCard(array $row): array
    {
        $idVal = $this->tbcAajRowVal($row, 'id');
        $idStr = $this->tbcAajNormalizeId((string) $idVal);

        $jenis = trim((string) ($this->tbcAajRowVal($row, 'jenis_laporan') ?? ''));
        $kat = trim((string) ($this->tbcAajRowVal($row, 'nama_kategori') ?? ''));
        $section = $jenis !== '' ? $jenis : ($kat !== '' ? $kat : 'CAR / Observasi');

        $issue = trim((string) ($this->tbcAajRowVal($row, 'issue') ?? ''));
        $title = $issue !== '' ? $issue : ($kat !== '' ? $kat : 'Temuan');

        $tanggal = $this->tbcAajRowVal($row, 'tanggal');
        $tp = $this->tbcAajRowVal($row, 'tanggal_pembuatan');
        $dateStr = '';
        if ($tp !== null && $tp !== '') {
            $dateStr = substr((string) $tp, 0, 10);
        } elseif ($tanggal !== null && $tanggal !== '') {
            $dateStr = substr((string) $tanggal, 0, 10);
        }

        $deskripsi = trim((string) ($this->tbcAajRowVal($row, 'deskripsi') ?? ''));

        $namaPic = trim((string) ($this->tbcAajRowVal($row, 'nama_pic') ?? ''));
        $jabPic = trim((string) ($this->tbcAajRowVal($row, 'jabatan_fungsional_pic') ?? ''));
        $perPic = trim((string) ($this->tbcAajRowVal($row, 'perusahaan_pic') ?? ''));
        $people = $namaPic;
        if ($jabPic !== '') {
            $people .= ($people !== '' ? ' — ' : '') . $jabPic;
        }
        if ($perPic !== '') {
            $people .= ($people !== '' ? ' · ' : '') . $perPic;
        }

        $nl = trim((string) ($this->tbcAajRowVal($row, 'nama_lokasi') ?? ''));
        $ndl = trim((string) ($this->tbcAajRowVal($row, 'nama_detail_lokasi') ?? ''));
        $ld = trim((string) ($this->tbcAajRowVal($row, 'lokasi_detail') ?? ''));
        $lokasi = $nl;
        if ($ndl !== '') {
            $lokasi .= ($lokasi !== '' ? ' — ' : '') . $ndl;
        }
        if ($ld !== '' && $ld !== $ndl) {
            $lokasi .= ($lokasi !== '' ? ' · ' : '') . $ld;
        }

        $np = trim((string) ($this->tbcAajRowVal($row, 'nama_pelapor') ?? ''));
        $method = trim((string) ($this->tbcAajRowVal($row, 'method') ?? ''));
        $pelapor = 'Pelapor: ' . $np;
        if ($method !== '') {
            $pelapor .= ' · ' . $method;
        }

        $st = strtoupper(trim((string) ($this->tbcAajRowVal($row, 'status') ?? '')));
        $status = 'closed';
        if ($st === 'OPEN' || ($st !== '' && str_contains($st, 'OPEN') && ! str_contains($st, 'CLOSE'))) {
            $status = 'open';
        } elseif (str_contains($st, 'SUBMIT')) {
            $status = 'submitted';
        }

        $url = trim((string) ($this->tbcAajRowVal($row, 'url_photo') ?? ''));

        return [
            'source' => 'clickhouse',
            'raw_id' => $idStr,
            'section' => $section,
            'tone' => 'slate',
            'title' => $title,
            'date' => $dateStr,
            'body' => $deskripsi,
            'people' => $people,
            'lokasi' => $lokasi,
            'pelapor' => $pelapor,
            'status' => $status,
            'url_photo' => $url !== '' ? $url : null,
        ];
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls|max:15360',
        ], [
            'excel_file.required' => 'File Excel wajib diupload.',
        ]);

        $prevExcelCalendar = ExcelDate::getExcelCalendar();

        try {
            $file = $request->file('excel_file');
            $spreadsheet = IOFactory::load($file->getRealPath());
            ExcelDate::setExcelCalendar($spreadsheet->getExcelCalendar());

            $worksheet = $spreadsheet->getActiveSheet();
            // Nilai mentah (serial Excel untuk tanggal/waktu), bukan string terformat — hindari salah parse & locale Carbon.
            $rows = $worksheet->toArray(null, true, false, false);

            if (count($rows) < 2) {
                return redirect()->route('peer-pressure-edukasi.index')
                    ->with('error', 'File tidak berisi data (minimal 1 baris header + 1 baris data).');
            }

            array_shift($rows);

            $importedKejadian = 0;
            $importedPeserta = 0;
            $errors = [];
            $rowNum = 2;

            /** @var PeerPressureKejadianEdukasi|null $current */
            $current = null;
            $urutan = 0;

            DB::beginTransaction();

            foreach ($rows as $row) {
                $row = $this->normalizeRowToZeroBased($row);
                if ($this->rowIsEmpty($row)) {
                    $rowNum++;
                    continue;
                }

                $tanggalTemuanRaw = $this->cell($row, self::COL['tanggal_temuan']);

                if ($tanggalTemuanRaw !== '') {
                    $parsed = $this->parseKejadianRow($row, $rowNum, $errors);
                    if ($parsed === null) {
                        $rowNum++;
                        continue;
                    }

                    $current = PeerPressureKejadianEdukasi::create($parsed['attributes']);
                    $importedKejadian++;
                    $urutan = 0;

                    $n = $this->appendPesertaForRow($current->id, $row, $urutan);
                    $importedPeserta += $n;

                    $rowNum++;
                    continue;
                }

                if ($current === null) {
                    $errors[] = "Baris {$rowNum}: baris lanjutan tanpa kejadian sebelumnya (kolom Tanggal Temuan kosong).";
                    $rowNum++;
                    continue;
                }

                $this->mergeContinuationFieldsIfEmpty($current, $row);

                $n = $this->appendPesertaForRow($current->id, $row, $urutan);
                if ($n === 0) {
                    $errors[] = "Baris {$rowNum}: baris lanjutan tidak berisi SID/Nama pelanggar atau peer.";
                }
                $importedPeserta += $n;
                $rowNum++;
            }

            DB::commit();

            $msg = "Import selesai: {$importedKejadian} kejadian, {$importedPeserta} baris peserta.";
            if (! empty($errors)) {
                $msg .= ' Peringatan: ' . implode(' ', array_slice($errors, 0, 10));
                if (count($errors) > 10) {
                    $msg .= ' …';
                }
            }

            return redirect()->route('peer-pressure-edukasi.index')->with('success', $msg);
        } catch (Exception $e) {
            DB::rollBack();

            return redirect()->route('peer-pressure-edukasi.index')
                ->with('error', 'Import gagal: ' . $e->getMessage());
        } finally {
            ExcelDate::setExcelCalendar($prevExcelCalendar);
        }
    }

    /**
     * Pastikan indeks kolom 0..n sesuai urutan kolom Excel.
     *
     * @param  array<int|string, mixed>  $row
     * @return array<int, mixed>
     */
    private function normalizeRowToZeroBased(array $row): array
    {
        if ($row === []) {
            return [];
        }
        $keys = array_keys($row);
        $firstKey = $keys[0] ?? 0;
        if (is_string($firstKey) && preg_match('/^[A-Z]{1,3}$/', $firstKey)) {
            ksort($row, SORT_STRING);
        }

        return array_values($row);
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $v) {
            if ($v !== null && trim((string) $v) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function cell(array $row, int $index): string
    {
        if (! isset($row[$index]) || $row[$index] === null) {
            return '';
        }
        if (is_float($row[$index]) || is_int($row[$index])) {
            return trim((string) $row[$index]);
        }

        return trim((string) $row[$index]);
    }

    /**
     * @param  array<int, mixed>  $row
     * @param  array<int, string>  $errors
     * @return array{attributes: array<string, mixed>}|null
     */
    private function parseKejadianRow(array $row, int $rowNum, array &$errors): ?array
    {
        $tanggalTemuan = $this->parseDateCell($row[self::COL['tanggal_temuan']] ?? null);
        $jamTemuan = $this->parseTimeCell($row[self::COL['jam_temuan']] ?? null);
        $tanggalEdukasi = $this->parseDateCell($row[self::COL['tanggal_edukasi']] ?? null);
        $jamEdukasi = $this->parseTimeCell($row[self::COL['jam_edukasi']] ?? null);

        if ($tanggalTemuan === null || $jamTemuan === null || $tanggalEdukasi === null || $jamEdukasi === null) {
            $errors[] = "Baris {$rowNum}: Tanggal/Jam temuan atau edukasi tidak valid.";

            return null;
        }

        $durasiRaw = $this->cell($row, self::COL['durasi_menit']);
        $durasi = $durasiRaw !== '' ? (int) preg_replace('/[^\d]/', '', $durasiRaw) : 0;
        if ($durasi < 1) {
            $errors[] = "Baris {$rowNum}: Durasi edukasi (menit) wajib diisi angka > 0.";

            return null;
        }

        $kronologi = $this->cell($row, self::COL['kronologi_temuan']);
        if ($kronologi === '') {
            $errors[] = "Baris {$rowNum}: Kronologi Temuan wajib diisi.";

            return null;
        }

        $attributes = [
            'tanggal_temuan' => $tanggalTemuan,
            'jam_temuan' => $jamTemuan,
            'kelompok_lokasi_temuan' => $this->cell($row, self::COL['kelompok_lokasi_temuan']) ?: '-',
            'lokasi_temuan' => $this->cell($row, self::COL['lokasi_temuan']) ?: '-',
            'kelompok_lokasi_edukasi' => $this->cell($row, self::COL['kelompok_lokasi_edukasi']) ?: '-',
            'lokasi_edukasi' => $this->cell($row, self::COL['lokasi_edukasi']) ?: '-',
            'tanggal_edukasi' => $tanggalEdukasi,
            'jam_edukasi' => $jamEdukasi,
            'perusahaan' => $this->cell($row, self::COL['perusahaan']) ?: '-',
            'site' => $this->nullableString($row, self::COL['site']),
            'tasklist_temuan' => $this->nullableString($row, self::COL['tasklist_temuan']),
            'kronologi_temuan' => $kronologi,
            'kategori_deviasi' => $this->cell($row, self::COL['kategori_deviasi']) ?: '-',
            'pemimpin_edukasi' => $this->cell($row, self::COL['pemimpin_edukasi']) ?: '-',
            'id_berecord' => $this->nullableString($row, self::COL['id_berecord']),
            'jenis_kelompok_kerja' => $this->nullableString($row, self::COL['jenis_kelompok_kerja']),
            'kelompok_aktivitas_pekerjaan' => $this->nullableString($row, self::COL['kelompok_aktivitas_pekerjaan']),
            'aktivitas_pekerjaan' => $this->nullableString($row, self::COL['aktivitas_pekerjaan']),
            'departemen' => $this->nullableString($row, self::COL['departemen']),
            'evidence_url' => $this->nullableString($row, self::COL['evidence']),
            'durasi_edukasi_menit' => $durasi,
            'status_pelaksanaan_edukasi' => $this->cell($row, self::COL['status']) ?: 'OPEN',
        ];

        return ['attributes' => $attributes];
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function nullableString(array $row, int $index): ?string
    {
        $s = $this->cell($row, $index);

        return $s === '' ? null : $s;
    }

    private function parseDateCell(mixed $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $v)->format('Y-m-d');
            } catch (Exception $e) {
                return null;
            }
        }
        $s = trim((string) $v);
        if ($s === '') {
            return null;
        }
        // Tanggal teks: selalu DD/MM/YYYY (Indonesia). Jangan pakai Carbon::parse dulu — bisa dianggap MM/DD (mis. 01/02/2026 jadi 2 Jan, bukan 1 Feb).
        if (preg_match('/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{4})$/', $s, $m)) {
            try {
                $day = (int) $m[1];
                $month = (int) $m[2];
                $year = (int) $m[3];

                return Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            } catch (Exception $e) {
                // lanjut ke format lain
            }
        }
        foreach (['Y-m-d', 'd/m/Y', 'j/n/Y', 'd-m-Y', 'j-n-Y'] as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $s)->format('Y-m-d');
            } catch (Exception $e) {
            }
        }
        try {
            return Carbon::parse($s)->format('Y-m-d');
        } catch (Exception $e) {
            return null;
        }
    }

    private function parseTimeCell(mixed $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $v)->format('H:i:s');
            } catch (Exception $e) {
                return null;
            }
        }
        $s = trim((string) $v);
        if ($s === '') {
            return null;
        }
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $s)) {
            $parts = explode(':', $s);
            $h = str_pad((string) ((int) $parts[0]), 2, '0', STR_PAD_LEFT);
            $m = str_pad((string) ((int) ($parts[1] ?? 0)), 2, '0', STR_PAD_LEFT);
            $sec = isset($parts[2]) ? str_pad((string) ((int) $parts[2]), 2, '0', STR_PAD_LEFT) : '00';

            return "{$h}:{$m}:{$sec}";
        }
        try {
            return Carbon::parse($s)->format('H:i:s');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function appendPesertaForRow(int $kejadianId, array $row, int &$urutan): int
    {
        $added = 0;
        $sidP = $this->cell($row, self::COL['sid_pelanggar']);
        $namaP = $this->cell($row, self::COL['nama_pelanggar']);
        $sidPeer = $this->cell($row, self::COL['sid_peer']);
        $namaPeer = $this->cell($row, self::COL['nama_peer']);

        if ($sidP !== '' || $namaP !== '') {
            PeerPressurePesertaEdukasi::create([
                'kejadian_edukasi_id' => $kejadianId,
                'sid' => $sidP !== '' ? $sidP : '-',
                'nama' => $namaP !== '' ? $namaP : '-',
                'peran' => 'pelanggar',
                'urutan' => ++$urutan,
            ]);
            $added++;
        }

        if ($sidPeer !== '' || $namaPeer !== '') {
            PeerPressurePesertaEdukasi::create([
                'kejadian_edukasi_id' => $kejadianId,
                'sid' => $sidPeer !== '' ? $sidPeer : '-',
                'nama' => $namaPeer !== '' ? $namaPeer : '-',
                'peran' => 'peer',
                'urutan' => ++$urutan,
            ]);
            $added++;
        }

        return $added;
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function mergeContinuationFieldsIfEmpty(PeerPressureKejadianEdukasi $k, array $row): void
    {
        $updates = [];
        $map = [
            self::COL['site'] => 'site',
            self::COL['jenis_kelompok_kerja'] => 'jenis_kelompok_kerja',
            self::COL['kelompok_aktivitas_pekerjaan'] => 'kelompok_aktivitas_pekerjaan',
            self::COL['aktivitas_pekerjaan'] => 'aktivitas_pekerjaan',
            self::COL['departemen'] => 'departemen',
        ];
        foreach ($map as $idx => $field) {
            $v = $this->cell($row, $idx);
            if ($v !== '' && ($k->{$field} === null || $k->{$field} === '')) {
                $updates[$field] = $v;
            }
        }
        if (! empty($updates)) {
            $k->update($updates);
            $k->refresh();
        }
    }

    public function downloadTemplate()
    {
        $headers = [
            'Tanggal Temuan',
            'Jam Temuan',
            'Kelompok Lokasi Temuan',
            'Lokasi Temuan',
            'Kelompok Lokasi Edukasi',
            'Lokasi Edukasi',
            'Tanggal Edukasi',
            'Jam Edukasi',
            'Perusahaan',
            'Tasklist Temuan (Jika Ada)',
            'Kronologi Temuan',
            'Kategori Deviasi',
            'Pemimpin Edukasi',
            'Id Berecord',
            'SID Pelanggar',
            'Nama Pelanggar',
            'SID Peer',
            'Nama Peer',
            'Jenis Kelompok Kerja',
            'Kelompok Aktivitas Pekerjaan',
            'Aktivitas Pekerjaan',
            'Departemen',
            'Evidence',
            'Durasi Edukasi (Menit)',
            'Status Pelaksanaan Edukasi',
            'Site',
        ];

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Peer Pressure');

        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        for ($c = 1; $c <= count($headers); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        $filename = 'peer_pressure_edukasi_template_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
