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
                ifNull(toString(nama_lokasi), '') as lokasi,
                ifNull(toString(nama_detail_lokasi), '') as detail_lokasi,
                ifNull(toString(latitude), '') as latitude,
                ifNull(toString(longitude), '') as longitude
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
            'cctv_data_bmo2.id',
            'cctv_data_bmo2.no_cctv',
            'cctv_data_bmo2.nama_cctv',
            'cctv_data_bmo2.kondisi',
            'cctv_data_bmo2.status',
            'cctv_data_bmo2.connected'
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
            return [
                'kondisi' => $item->kondisi ?? null,
                'status' => $item->status ?? null,
                'connected' => $item->connected ?? null,
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

                $data = [
                    'tanggal' => $tanggalStr,
                    'id_lokasi' => null,
                    'nama_lokasi' => $namaLokasi,
                    'risk_level' => $riskLevel,
                    'has_sap_report' => $hasSapReport,
                    'has_online_cctv' => $hasOnlineCctv,
                    'is_high_risk_area' => $isHighRiskArea,
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
