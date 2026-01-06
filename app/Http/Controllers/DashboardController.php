<?php

namespace App\Http\Controllers;

use App\Models\CctvData;
use App\Models\CctvCoverage;
use App\Models\PjaCctvDedicated;
use App\Models\CctvControlRoomPengawas;
use App\Models\WmsLink;
use App\Models\GeojsonArea;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index()
    {
        // CCTV Database Statistics (cctv_data_bmo2)
        $totalCctv = CctvData::count();
        $cctvBaik = CctvData::where('kondisi', 'Baik')->count();
        $cctvRusak = CctvData::where('kondisi', 'Breakdown')->orWhere('kondisi', 'Rusak')->count();
        $cctvLive = CctvData::where('status', 'Live View')->count();
        $cctvWithLink = CctvData::whereNotNull('link_akses')->where('link_akses', '!=', '')->count();
        $cctvWithCoordinates = CctvData::whereNotNull('longitude')->whereNotNull('latitude')->count();
        
        // Distribution by site
        $distributionBySite = CctvData::select('site', DB::raw('COUNT(*) as count'))
            ->whereNotNull('site')
            ->where('site', '!=', '')
            ->groupBy('site')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        // Distribution by perusahaan
        $distributionByPerusahaan = CctvData::select('perusahaan', DB::raw('COUNT(*) as count'))
            ->whereNotNull('perusahaan')
            ->where('perusahaan', '!=', '')
            ->groupBy('perusahaan')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        // Distribution by kondisi
        $distributionByKondisi = CctvData::select('kondisi', DB::raw('COUNT(*) as count'))
            ->whereNotNull('kondisi')
            ->where('kondisi', '!=', '')
            ->groupBy('kondisi')
            ->orderByDesc('count')
            ->get();
        
        // Distribution by status
        $distributionByStatus = CctvData::select('status', DB::raw('COUNT(*) as count'))
            ->whereNotNull('status')
            ->where('status', '!=', '')
            ->groupBy('status')
            ->orderByDesc('count')
            ->get();
        
        // Control rooms count
        $totalControlRooms = CctvData::whereNotNull('control_room')
            ->where('control_room', '!=', '')
            ->distinct('control_room')
            ->count('control_room');
        
        // CCTV PJA Dedicated Statistics (pja_cctv_dedicated)
        $totalPjaDedicated = PjaCctvDedicated::count();
        $pjaDedicatedByPja = PjaCctvDedicated::select('pja', DB::raw('COUNT(*) as count'))
            ->whereNotNull('pja')
            ->where('pja', '!=', '')
            ->groupBy('pja')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        // CCTV Coverage Statistics (cctv_coverage)
        $totalCoverage = CctvCoverage::count();
        $coverageByLocation = CctvCoverage::select('coverage_lokasi', DB::raw('COUNT(*) as count'))
            ->whereNotNull('coverage_lokasi')
            ->where('coverage_lokasi', '!=', '')
            ->groupBy('coverage_lokasi')
            ->orderByDesc('count')
            ->limit(10)
            ->get();
        
        $coverageByKategoriArea = CctvCoverage::select('kategori_area', DB::raw('COUNT(*) as count'))
            ->whereNotNull('kategori_area')
            ->where('kategori_area', '!=', '')
            ->groupBy('kategori_area')
            ->orderByDesc('count')
            ->get();
        
        $coverageByKategoriAktivitas = CctvCoverage::select('kategori_aktivitas', DB::raw('COUNT(*) as count'))
            ->whereNotNull('kategori_aktivitas')
            ->where('kategori_aktivitas', '!=', '')
            ->groupBy('kategori_aktivitas')
            ->orderByDesc('count')
            ->get();
        
        // Control Room Pengawas Statistics (cctv_control_room_pengawas)
        $totalPengawas = CctvControlRoomPengawas::count();
        $pengawasByControlRoom = CctvControlRoomPengawas::select('control_room', DB::raw('COUNT(*) as count'))
            ->whereNotNull('control_room')
            ->where('control_room', '!=', '')
            ->groupBy('control_room')
            ->orderByDesc('count')
            ->get();
        
        // WMS Links Statistics (wms_links)
        $totalWmsLinks = WmsLink::count();
        $wmsLinksByYear = WmsLink::select('year', DB::raw('COUNT(*) as count'))
            ->whereNotNull('year')
            ->groupBy('year')
            ->orderByDesc('year')
            ->get();
        
        $wmsLinksByWeek = WmsLink::select('week', DB::raw('COUNT(*) as count'))
            ->whereNotNull('week')
            ->where('year', date('Y'))
            ->groupBy('week')
            ->orderBy('week')
            ->get();
        
        // GeoJSON Areas Statistics (geojson_areas)
        $totalGeojsonAreas = GeojsonArea::count();
        $geojsonAreasByType = GeojsonArea::select('type', DB::raw('COUNT(*) as count'))
            ->whereNotNull('type')
            ->where('type', '!=', '')
            ->groupBy('type')
            ->orderByDesc('count')
            ->get();
        
        $geojsonAreasByYear = GeojsonArea::select('year', DB::raw('COUNT(*) as count'))
            ->whereNotNull('year')
            ->groupBy('year')
            ->orderByDesc('year')
            ->get();
        
        // Calculate percentages
        $cctvBaikPercentage = $totalCctv > 0 ? round(($cctvBaik / $totalCctv) * 100, 1) : 0;
        $cctvRusakPercentage = $totalCctv > 0 ? round(($cctvRusak / $totalCctv) * 100, 1) : 0;
        $cctvLivePercentage = $totalCctv > 0 ? round(($cctvLive / $totalCctv) * 100, 1) : 0;
        $cctvWithLinkPercentage = $totalCctv > 0 ? round(($cctvWithLink / $totalCctv) * 100, 1) : 0;
        
        // Recent activity (last 6 months)
        $recentMonths = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $monthName = date('M', strtotime("-$i months"));
            $count = CctvData::where('tahun_update', date('Y', strtotime("-$i months")))
                ->where('bulan_update', date('n', strtotime("-$i months")))
                ->count();
            $recentMonths[] = [
                'month' => $monthName,
                'count' => $count
            ];
        }
        
        // Prepare data for charts
        $stats = [
            // CCTV Database Stats
            'cctv' => [
                'total' => $totalCctv,
                'baik' => $cctvBaik,
                'rusak' => $cctvRusak,
                'live' => $cctvLive,
                'with_link' => $cctvWithLink,
                'with_coordinates' => $cctvWithCoordinates,
                'baik_percentage' => $cctvBaikPercentage,
                'rusak_percentage' => $cctvRusakPercentage,
                'live_percentage' => $cctvLivePercentage,
                'with_link_percentage' => $cctvWithLinkPercentage,
                'total_control_rooms' => $totalControlRooms,
                'distribution_by_site' => $distributionBySite,
                'distribution_by_perusahaan' => $distributionByPerusahaan,
                'distribution_by_kondisi' => $distributionByKondisi,
                'distribution_by_status' => $distributionByStatus,
                'recent_months' => $recentMonths,
            ],
            // PJA Dedicated Stats
            'pja_dedicated' => [
                'total' => $totalPjaDedicated,
                'by_pja' => $pjaDedicatedByPja,
            ],
            // Coverage Stats
            'coverage' => [
                'total' => $totalCoverage,
                'by_location' => $coverageByLocation,
                'by_kategori_area' => $coverageByKategoriArea,
                'by_kategori_aktivitas' => $coverageByKategoriAktivitas,
            ],
            // Control Room Pengawas Stats
            'pengawas' => [
                'total' => $totalPengawas,
                'by_control_room' => $pengawasByControlRoom,
            ],
            // WMS Links Stats
            'wms' => [
                'total' => $totalWmsLinks,
                'by_year' => $wmsLinksByYear,
                'by_week' => $wmsLinksByWeek,
            ],
            // GeoJSON Areas Stats
            'geojson' => [
                'total' => $totalGeojsonAreas,
                'by_type' => $geojsonAreasByType,
                'by_year' => $geojsonAreasByYear,
            ],
        ];
        
        return view('dashboard.index', compact('stats'));
    }
    
    /**
     * Get dashboard data via API (for AJAX requests)
     */
    public function getData(Request $request)
    {
        // Similar logic as index but return JSON
        $totalCctv = CctvData::count();
        $cctvBaik = CctvData::where('kondisi', 'Baik')->count();
        $cctvRusak = CctvData::where('kondisi', 'Breakdown')->orWhere('kondisi', 'Rusak')->count();
        $cctvLive = CctvData::where('status', 'Live View')->count();
        
        $totalPjaDedicated = PjaCctvDedicated::count();
        $totalCoverage = CctvCoverage::count();
        $totalPengawas = CctvControlRoomPengawas::count();
        $totalWmsLinks = WmsLink::count();
        $totalGeojsonAreas = GeojsonArea::count();
        
        return response()->json([
            'success' => true,
            'data' => [
                'cctv_total' => $totalCctv,
                'cctv_baik' => $cctvBaik,
                'cctv_rusak' => $cctvRusak,
                'cctv_live' => $cctvLive,
                'pja_dedicated_total' => $totalPjaDedicated,
                'coverage_total' => $totalCoverage,
                'pengawas_total' => $totalPengawas,
                'wms_links_total' => $totalWmsLinks,
                'geojson_areas_total' => $totalGeojsonAreas,
            ]
        ]);
    }
}

