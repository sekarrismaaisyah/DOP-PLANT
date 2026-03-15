<?php

namespace App\Http\Controllers;

use App\Services\ClickHouseService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EvaluasiUnitTabelController extends Controller
{
    /**
     * Haversine distance between two points in km.
     */
    private static function haversineKm(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $r = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $r * $c;
    }

    /**
     * Ambil data evaluasi unit dari ClickHouse Nitip (NO UNIT | JARAK | WAKTU AKTIF | TANGGAL).
     * Default rentang 30 hari terakhir jika tidak ada parameter.
     *
     * @return array{no_unit: string, jarak: string, waktu_jam: float, tanggal_aktif: string}[]
     */
    private function getEvaluasiUnitData(?string $dateFrom, ?string $dateTo): array
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (!$ch->isConnected()) {
            return [];
        }

        $sqlUnits = "
            SELECT toString(id) AS id, toString(vehicle_name) AS vehicle_name, toString(vehicle_number) AS vehicle_number
            FROM nitip.units
            ORDER BY vehicle_name ASC
        ";
        $units = $ch->query($sqlUnits);
        if (!is_array($units)) {
            $units = [];
        }

        $dateFilter = '';
        if ($dateFrom) {
            $dateFilter .= " AND toDate(parseDateTimeBestEffort(toString(updated_at))) >= '" . addslashes($dateFrom) . "'";
        }
        if ($dateTo) {
            $dateFilter .= " AND toDate(parseDateTimeBestEffort(toString(updated_at))) <= '" . addslashes($dateTo) . "'";
        }

        $rows = [];
        foreach ($units as $u) {
            $unitId = $u['id'] ?? null;
            if ($unitId === null || $unitId === '') {
                continue;
            }
            $noUnit = trim((string) ($u['vehicle_number'] ?? $u['vehicle_name'] ?? $unitId));
            if ($noUnit === '') {
                $noUnit = (string) $unitId;
            }

            $safeId = "'" . addslashes((string) $unitId) . "'";
            $sqlDates = "
                SELECT toDate(parseDateTimeBestEffort(toString(updated_at))) AS log_date
                FROM nitip.unit_gps_logs
                WHERE toString(unit_id) = $safeId
                  AND toFloat64(latitude) != 0 AND toFloat64(longitude) != 0
                $dateFilter
                GROUP BY toDate(parseDateTimeBestEffort(toString(updated_at)))
                ORDER BY log_date ASC
            ";
            $dateRows = $ch->query($sqlDates);
            if (!is_array($dateRows)) {
                $dateRows = [];
            }

            $totalKm = 0.0;
            $totalSeconds = 0;
            $activeDates = [];

            foreach ($dateRows as $dr) {
                $logDate = $dr['log_date'] ?? null;
                if ($logDate === null || $logDate === '') {
                    continue;
                }
                $activeDates[] = $logDate;

                $safeDate = "'" . addslashes((string) $logDate) . "'";
                $sqlLogs = "
                    SELECT toFloat64(latitude) AS latitude, toFloat64(longitude) AS longitude, toString(updated_at) AS updated_at
                    FROM nitip.unit_gps_logs
                    WHERE toString(unit_id) = $safeId
                      AND toDate(parseDateTimeBestEffort(toString(updated_at))) = $safeDate
                      AND toFloat64(latitude) != 0 AND toFloat64(longitude) != 0
                    ORDER BY parseDateTimeBestEffort(toString(updated_at)) ASC
                    LIMIT 2000
                ";
                $logs = $ch->query($sqlLogs);
                if (!is_array($logs)) {
                    $logs = [];
                }

                $dayKm = 0.0;
                for ($i = 0; $i < count($logs) - 1; $i++) {
                    $a = $logs[$i];
                    $b = $logs[$i + 1];
                    $lat1 = isset($a['latitude']) ? (float) $a['latitude'] : null;
                    $lon1 = isset($a['longitude']) ? (float) $a['longitude'] : null;
                    $lat2 = isset($b['latitude']) ? (float) $b['latitude'] : null;
                    $lon2 = isset($b['longitude']) ? (float) $b['longitude'] : null;
                    if ($lat1 !== null && $lon1 !== null && $lat2 !== null && $lon2 !== null) {
                        $dayKm += self::haversineKm($lat1, $lon1, $lat2, $lon2);
                    }
                }
                $totalKm += $dayKm;

                if (count($logs) >= 1) {
                    $firstTs = null;
                    $lastTs = null;
                    foreach ($logs as $log) {
                        $t = $log['updated_at'] ?? null;
                        if ($t === null || $t === '') {
                            continue;
                        }
                        $ts = is_numeric($t) ? (int) $t : strtotime($t);
                        if ($ts === false) {
                            continue;
                        }
                        if ($firstTs === null || $ts < $firstTs) {
                            $firstTs = $ts;
                        }
                        if ($lastTs === null || $ts > $lastTs) {
                            $lastTs = $ts;
                        }
                    }
                    if ($firstTs !== null && $lastTs !== null && $lastTs >= $firstTs) {
                        $totalSeconds += ($lastTs - $firstTs);
                    }
                }
            }

            $totalHours = round($totalSeconds / 3600, 2);
            $jarakText = $totalKm >= 1
                ? number_format($totalKm, 2, ',', '.') . ' km'
                : number_format($totalKm * 1000, 0, ',', '.') . ' m';
            $tanggalAktif = count($activeDates) > 0 ? implode(', ', $activeDates) : '-';

            $rows[] = [
                'no_unit' => $noUnit,
                'jarak' => $jarakText,
                'waktu_jam' => $totalHours,
                'tanggal_aktif' => $tanggalAktif,
            ];
        }

        return $rows;
    }

    /**
     * Tampilkan halaman tabel Evaluasi Unit (NO UNIT | JARAK | WAKTU AKTIF | TANGGAL).
     * Parameter: date_from, date_to (YYYY-MM-DD). Default: 30 hari terakhir.
     */
    public function index(Request $request): View
    {
        $dateFrom = $request->query('date_from');
        $dateTo = $request->query('date_to');
        if ($dateFrom && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateFrom)) {
            $dateFrom = null;
        }
        if ($dateTo && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateTo)) {
            $dateTo = null;
        }

        if (!$dateFrom || !$dateTo) {
            $dateTo = $dateTo ?: Carbon::now()->format('Y-m-d');
            $dateFrom = $dateFrom ?: Carbon::now()->subDays(30)->format('Y-m-d');
        }

        $evaluasiUnits = [];
        $error = null;
        try {
            $evaluasiUnits = $this->getEvaluasiUnitData($dateFrom, $dateTo);
        } catch (Exception $e) {
            Log::error('EvaluasiUnitTabelController::index: ' . $e->getMessage());
            $error = $e->getMessage();
        }

        return view('fuelingEvaluasi.index', [
            'evaluasiUnits' => $evaluasiUnits,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'error' => $error,
        ]);
    }
}
