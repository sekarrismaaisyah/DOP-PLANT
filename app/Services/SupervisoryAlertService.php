<?php

namespace App\Services;

use App\Models\CctvCoverage;
use App\Models\SupervisoryAlertLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SupervisoryAlertService
{
    /** Keywords untuk area high-risk (sama dengan frontend checkIfHighRiskArea) */
    private const HIGH_RISK_KEYWORDS = ['pit', 'hauling', 'tambang', 'mining', 'high risk', 'highrisk'];

    /**
     * Query ClickHouse (logika sama dengan fullMapsController::queryClickHouseCustom).
     */
    public function queryClickHouse(string $sql, string $database = 'hse_automation'): array
    {
        try {
            $host = config('services.clickhouse_custom.host', '10.10.10.38');
            $port = config('services.clickhouse_custom.port', 8123);
            $username = config('services.clickhouse_custom.username', 'default');
            $password = config('services.clickhouse_custom.password', '');
            $timeout = config('services.clickhouse_custom.timeout', 60);
            $baseUrl = 'http' . '://' . $host . ':' . $port;
            $url = $baseUrl . '/?database=' . urlencode($database) . '&default_format=JSON';

            $response = Http::timeout($timeout)
                ->withBasicAuth($username, $password)
                ->withBody($sql, 'text/plain')
                ->post($url);

            if (! $response->successful()) {
                Log::warning('SupervisoryAlertService ClickHouse query failed', [
                    'status' => $response->status(),
                    'body_preview' => substr($response->body(), 0, 300),
                ]);
                return [];
            }

            $result = json_decode($response->body(), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return [];
            }
            if (is_array($result) && isset($result['data'])) {
                return $result['data'];
            }
            if (is_array($result) && ! empty($result) && isset($result[0])) {
                return $result;
            }
            return is_array($result) ? $result : [];
        } catch (\Throwable $e) {
            Log::error('SupervisoryAlertService queryClickHouse error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil data SAP hari ini (timezone Asia/Makassar).
     */
    public function getSapDataToday(int $limit = 500): array
    {
        $sql = "
            SELECT 
                toString(id) as task_number,
                ifNull(toString(jenis_laporan), 'INSPEKSI_HAZARD') as jenis_laporan,
                ifNull(toString(nama_lokasi), '') as lokasi,
                ifNull(toString(nama_detail_lokasi), '') as detail_lokasi,
                ifNull(toString(latitude), '') as latitude,
                ifNull(toString(longitude), '') as longitude,
                ifNull(toString(tanggal_pembuatan), toString(bedraft_date)) as tanggal_pelaporan
            FROM hse_automation.aaj_car_all_year_from_dav
            WHERE latitude IS NOT NULL AND longitude IS NOT NULL
                AND latitude != '' AND longitude != ''
                AND (
                    (tanggal_pembuatan IS NOT NULL 
                        AND toDate(tanggal_pembuatan, 'Asia/Makassar') = toDate(toTimeZone(now(), 'Asia/Makassar')))
                    OR (bedraft_date IS NOT NULL 
                        AND toDate(bedraft_date, 'Asia/Makassar') = toDate(toTimeZone(now(), 'Asia/Makassar')))
                )
            LIMIT " . (int) $limit;

        $rows = $this->queryClickHouse($sql, 'hse_automation');
        $out = [];
        foreach ($rows as $row) {
            if (is_object($row)) {
                $row = (array) $row;
            }
            $out[] = $row;
        }
        return $out;
    }

    /**
     * Daftar area kerja unik dari cctv_coverage (coverage_lokasi).
     */
    public function getAreaList(): array
    {
        $list = CctvCoverage::query()
            ->select('coverage_lokasi')
            ->distinct()
            ->whereNotNull('coverage_lokasi')
            ->where('coverage_lokasi', '!=', '')
            ->pluck('coverage_lokasi')
            ->filter()
            ->values()
            ->toArray();
        return $list;
    }

    /**
     * CCTV di area (query sama dengan fullMapsController::getCctvByCoverageLocation).
     */
    public function getCctvListForArea(string $lokasiName): array
    {
        $normalizedLokasi = strtolower(trim($lokasiName));
        $normalizedLokasiNoSpaces = str_replace(' ', '', $normalizedLokasi);

        $rows = CctvCoverage::select(
            'cctv_coverage.id as coverage_id',
            'cctv_coverage.id_cctv',
            'cctv_coverage.coverage_lokasi',
            'cctv_coverage.coverage_detail_lokasi',
            'cctv_data_bmo2.id',
            'cctv_data_bmo2.no_cctv',
            'cctv_data_bmo2.nama_cctv',
            'cctv_data_bmo2.kondisi',
            'cctv_data_bmo2.status',
            'cctv_data_bmo2.connected',
            'cctv_data_bmo2.lokasi_pemasangan'
        )
            ->leftJoin('cctv_data_bmo2', 'cctv_coverage.id_cctv', '=', 'cctv_data_bmo2.id')
            ->where(function ($q) use ($normalizedLokasi, $normalizedLokasiNoSpaces) {
                $q->whereRaw('LOWER(cctv_coverage.coverage_lokasi) LIKE ?', ['%' . $normalizedLokasi . '%'])
                    ->orWhereRaw("LOWER(REPLACE(REPLACE(cctv_coverage.coverage_lokasi, '(', ''), ')', '')) LIKE ?", ['%' . $normalizedLokasi . '%'])
                    ->orWhereRaw("LOWER(REPLACE(REPLACE(REPLACE(cctv_coverage.coverage_lokasi, '(', ''), ')', ''), ' ', '')) LIKE ?", ['%' . $normalizedLokasiNoSpaces . '%'])
                    ->orWhereRaw("? LIKE CONCAT('%', LOWER(REPLACE(REPLACE(cctv_coverage.coverage_lokasi, '(', ''), ')', '')), '%')", [$normalizedLokasi])
                    ->orWhereRaw("? LIKE CONCAT('%', LOWER(REPLACE(REPLACE(REPLACE(cctv_coverage.coverage_lokasi, '(', ''), ')', ''), ' ', '')), '%')", [$normalizedLokasiNoSpaces]);
            })
            ->whereNotNull('cctv_data_bmo2.id')
            ->get();

        return $rows->map(function ($item) {
            $kondisi = $item->kondisi ?? $item->status ?? null;
            return [
                'no_cctv' => $item->no_cctv ?? null,
                'nama_cctv' => $item->nama_cctv ?? null,
                'kondisi' => $kondisi,
                'status' => $item->status ?? null,
                'connected' => $item->connected ?? null,
                'lokasi' => $item->coverage_detail_lokasi ?? $item->coverage_lokasi ?? $item->lokasi_pemasangan ?? null,
            ];
        })->toArray();
    }

    /**
     * Apakah CCTV dianggap online (sama dengan frontend).
     */
    public function isCctvOnline(array $cctv): bool
    {
        $kondisi = strtolower((string) ($cctv['kondisi'] ?? ''));
        $status = strtolower((string) ($cctv['status'] ?? ''));
        $connected = strtolower((string) ($cctv['connected'] ?? ''));

        return $kondisi === 'baik' || $kondisi === 'online'
            || $status === 'live view' || $connected === 'yes'
            || (isset($cctv['status']) && (int) $cctv['status'] === 1)
            || ! empty($cctv['is_online']) || (isset($cctv['status_online']) && (int) $cctv['status_online'] === 1);
    }

    /**
     * Normalize nama lokasi untuk matching (sama konsep frontend).
     */
    public function normalizeLocationName(string $name): string
    {
        $s = strtolower(trim(preg_replace('/\s+/', ' ', $name)));
        return str_replace(['(', ')'], '', $s);
    }

    /**
     * Apakah ada laporan SAP hari ini yang match ke area (by nama lokasi).
     */
    public function hasSapReportForArea(array $sapRows, string $namaLokasi): bool
    {
        $normalizedArea = $this->normalizeLocationName($namaLokasi);
        if ($normalizedArea === '') {
            return false;
        }
        foreach ($sapRows as $sap) {
            if (is_object($sap)) {
                $sap = (array) $sap;
            }
            $sapLokasi = $this->normalizeLocationName((string) ($sap['lokasi'] ?? ''));
            $sapDetail = $this->normalizeLocationName((string) ($sap['detail_lokasi'] ?? ''));
            if ($sapLokasi !== '' && (str_contains($sapLokasi, $normalizedArea) || str_contains($normalizedArea, $sapLokasi))) {
                return true;
            }
            if ($sapDetail !== '' && (str_contains($sapDetail, $normalizedArea) || str_contains($normalizedArea, $sapDetail))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Daftar SAP hari ini yang match ke area (untuk disimpan di log).
     */
    public function getSapListForArea(array $sapRows, string $namaLokasi): array
    {
        $normalizedArea = $this->normalizeLocationName($namaLokasi);
        if ($normalizedArea === '') {
            return [];
        }
        $out = [];
        foreach ($sapRows as $sap) {
            if (is_object($sap)) {
                $sap = (array) $sap;
            }
            $sapLokasi = $this->normalizeLocationName((string) ($sap['lokasi'] ?? ''));
            $sapDetail = $this->normalizeLocationName((string) ($sap['detail_lokasi'] ?? ''));
            $match = false;
            if ($sapLokasi !== '' && (str_contains($sapLokasi, $normalizedArea) || str_contains($normalizedArea, $sapLokasi))) {
                $match = true;
            }
            if (! $match && $sapDetail !== '' && (str_contains($sapDetail, $normalizedArea) || str_contains($normalizedArea, $sapDetail))) {
                $match = true;
            }
            if ($match) {
                $out[] = [
                    'task_number' => $sap['task_number'] ?? null,
                    'jenis_laporan' => $sap['jenis_laporan'] ?? null,
                    'lokasi' => $sap['lokasi'] ?? null,
                    'detail_lokasi' => $sap['detail_lokasi'] ?? null,
                    'tanggal_pelaporan' => $sap['tanggal_pelaporan'] ?? null,
                ];
            }
        }
        return $out;
    }

    /**
     * Bangun rekomendasi TARP (sama logika dengan frontend getTARPRecommendations).
     */
    public function buildTARPRecommendations(string $riskLevel, string $namaLokasi, array $cctvList): array
    {
        $recommendations = [];
        $lokasiLabel = $namaLokasi !== '' ? $namaLokasi : 'ini';
        $cctvCount = count($cctvList);

        $offlineCctv = array_filter($cctvList, function ($c) {
            return ! $this->isCctvOnline($c);
        });
        $offlineCctvNos = array_values(array_filter(array_map(function ($c) {
            return $c['no_cctv'] ?? $c['nomor_cctv'] ?? $c['nama_cctv'] ?? null;
        }, $offlineCctv)));
        $offlineCctvStr = implode(', ', array_slice($offlineCctvNos, 0, 10));
        if (count($offlineCctvNos) > 10) {
            $offlineCctvStr .= ', ...';
        }

        if ($riskLevel === SupervisoryAlertLog::RISK_HIGH) {
            $recommendations[] = [
                'priority' => 'HIGH',
                'action' => "Safety dan Mining Superintendent BC memberikan teguran terhadap PJA dan IT Mitra jika tidak ada follow up utilisasi CCTV dan perbaikan status offline CCTV 3 hari berturut-turut di area {$lokasiLabel}.",
            ];
            $recommendations[] = [
                'priority' => 'HIGH',
                'action' => "WKTT menerima laporan dan melakukan koordinasi dengan Dept Head/Project Manager untuk menentukan langkah tindakan perbaikan terkait kondisi yang terjadi di lapangan di area {$lokasiLabel}.",
            ];
            if ($offlineCctvStr !== '') {
                $recommendations[] = [
                    'priority' => 'HIGH',
                    'action' => "Segera koordinasi dengan IT Mitra Kerja dan Berau Coal untuk memperbaiki CCTV yang offline ({$offlineCctvStr}) di area {$lokasiLabel}.",
                ];
            }
        } elseif ($riskLevel === SupervisoryAlertLog::RISK_MEDIUM) {
            $recommendations[] = [
                'priority' => 'MEDIUM',
                'action' => "Pengawas Control Room wajib melakukan pemeriksaan kondisi aktivitas highrisk 3x/shift di area {$lokasiLabel}.",
            ];
            if ($offlineCctvStr !== '') {
                $recommendations[] = [
                    'priority' => 'MEDIUM',
                    'action' => "Koordinasi dengan IT Mitra Kerja dan Berau Coal memfollow up kondisi status offline CCTV {$offlineCctvStr} dan memastikan kondisi jaringan internet lancar dan tersedia di area {$lokasiLabel}.",
                ];
            }
            if ($cctvCount > 0) {
                $recommendations[] = [
                    'priority' => 'MEDIUM',
                    'action' => "Monitoring CCTV yang tidak aktif digunakan pengawasan dan Tim PJA terkait wajib mengutilisasi CCTV tersebut dengan dibuktikan laporan SAP di area {$lokasiLabel}.",
                ];
            }
            $recommendations[] = [
                'priority' => 'MEDIUM',
                'action' => "L3 Pengawas Control Room memberikan teguran terhadap PJA dan IT terkait kondisi dan utilisasi CCTV yang masih rendah atau tidak ada follow up 3x berturut-turut di area kerja Control Room di area {$lokasiLabel}.",
            ];
            $recommendations[] = [
                'priority' => 'MEDIUM',
                'action' => "Inspektorat Safety BC melaporkan hasil kondisi & utilisasi CCTV pada WA Group K3L Site untuk area {$lokasiLabel}.",
            ];
        }

        return $recommendations;
    }

    /**
     * Apakah area termasuk high-risk (keyword match, sama dengan frontend).
     */
    public function isHighRiskArea(string $lokasiName): bool
    {
        $areaName = strtolower(trim($lokasiName));
        foreach (self::HIGH_RISK_KEYWORDS as $keyword) {
            if (str_contains($areaName, $keyword)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Hitung risk level (matriks sama dengan frontend calculateRiskForAreaKerja).
     */
    public function calculateRiskLevel(
        bool $hasSapReport,
        bool $hasOnlineCctv,
        bool $isHighRiskArea,
        bool $hasSapInHighRiskArea
    ): string {
        if (! $hasSapReport && ! $hasOnlineCctv && ! $hasSapInHighRiskArea) {
            return SupervisoryAlertLog::RISK_HIGH;
        }
        if ($hasSapReport && ! $hasOnlineCctv && ! $hasSapInHighRiskArea) {
            return SupervisoryAlertLog::RISK_HIGH;
        }
        if (! $hasSapReport && ! $hasOnlineCctv && $hasSapInHighRiskArea) {
            return SupervisoryAlertLog::RISK_HIGH;
        }
        if (! $hasSapReport && $hasSapInHighRiskArea && $hasOnlineCctv) {
            return SupervisoryAlertLog::RISK_MEDIUM;
        }
        if ($hasSapReport && ! $hasSapInHighRiskArea && $hasOnlineCctv) {
            return SupervisoryAlertLog::RISK_MEDIUM;
        }
        if ($hasSapReport && $hasOnlineCctv) {
            return SupervisoryAlertLog::RISK_NORMAL;
        }
        if (! $isHighRiskArea && $hasOnlineCctv) {
            return SupervisoryAlertLog::RISK_NORMAL;
        }
        if ($hasSapReport && ! $hasOnlineCctv && ! $isHighRiskArea) {
            return SupervisoryAlertLog::RISK_MEDIUM;
        }
        return SupervisoryAlertLog::RISK_MEDIUM;
    }

    /**
     * Update alert log untuk satu hari: hitung per area dan simpan hanya HIGH/MEDIUM.
     */
    public function updateAlertLogForDate(?Carbon $date = null): array
    {
        $date = $date ?? Carbon::now('Asia/Makassar');
        $tanggalStr = $date->format('Y-m-d');

        $areas = $this->getAreaList();
        $sapToday = $this->getSapDataToday(500);
        $saved = 0;
        $errors = [];

        foreach ($areas as $namaLokasi) {
            $namaLokasi = (string) $namaLokasi;
            if ($namaLokasi === '') {
                continue;
            }
            try {
                $cctvList = $this->getCctvListForArea($namaLokasi);
                $hasOnlineCctv = false;
                foreach ($cctvList as $cctv) {
                    if ($this->isCctvOnline($cctv)) {
                        $hasOnlineCctv = true;
                        break;
                    }
                }
                $hasSapReport = $this->hasSapReportForArea($sapToday, $namaLokasi);
                $isHighRiskArea = $this->isHighRiskArea($namaLokasi);
                $hasSapInHighRiskArea = $isHighRiskArea && $hasSapReport;

                $riskLevel = $this->calculateRiskLevel($hasSapReport, $hasOnlineCctv, $isHighRiskArea, $hasSapInHighRiskArea);

                $tarpRecommendations = $this->buildTARPRecommendations($riskLevel, $namaLokasi, $cctvList);
                $sapList = $this->getSapListForArea($sapToday, $namaLokasi);

                $data = [
                    'tanggal' => $tanggalStr,
                    'id_lokasi' => null,
                    'nama_lokasi' => $namaLokasi,
                    'risk_level' => $riskLevel,
                    'has_sap_report' => $hasSapReport,
                    'has_online_cctv' => $hasOnlineCctv,
                    'is_high_risk_area' => $isHighRiskArea,
                    'tarp_recommendations' => $tarpRecommendations,
                    'cctv_list' => $cctvList,
                    'sap_list' => $sapList,
                ];
                if (SupervisoryAlertLog::storeIfNotGreen($data)) {
                    $saved++;
                }
            } catch (\Throwable $e) {
                $errors[] = $namaLokasi . ': ' . $e->getMessage();
                Log::warning('SupervisoryAlertService updateAlertLogForDate area error', [
                    'area' => $namaLokasi,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return [
            'tanggal' => $tanggalStr,
            'areas_processed' => count($areas),
            'saved' => $saved,
            'errors' => $errors,
        ];
    }
}
