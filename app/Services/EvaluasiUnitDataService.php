<?php

namespace App\Services;

/**
 * Service untuk data Evaluasi Unit (NO UNIT | JARAK | WAKTU AKTIF | TANGGAL).
 * Menggunakan satu query agregat di ClickHouse untuk data jutaan baris.
 */
class EvaluasiUnitDataService
{
    /**
     * Ambil data agregat evaluasi unit dalam rentang tanggal.
     * Satu query agregat (jarak + waktu per unit per hari) + satu query daftar unit.
     *
     * @return array<int, array{no_unit: string, jarak: string, waktu_jam: float, tanggal_aktif: string}>
     */
    public function getAggregatedData(?string $dateFrom, ?string $dateTo): array
    {
        if (!$dateFrom || !$dateTo) {
            return [];
        }

        $ch = new ClickHouseService('clickhouse_nitip');
        if (!$ch->isConnected()) {
            return [];
        }

        $safeFrom = "'" . addslashes($dateFrom) . "'";
        $safeTo = "'" . addslashes($dateTo) . "'";

        // Satu query: agregat per (unit_id, log_date) -> day_km, day_seconds
        // greatCircleDistance(lon1, lat1, lon2, lat2) returns meters -> /1000 = km
        $sqlAgg = "
            WITH logs AS (
                SELECT
                    toString(unit_id) AS unit_id,
                    toFloat64(latitude) AS lat,
                    toFloat64(longitude) AS lon,
                    parseDateTimeBestEffort(toString(updated_at)) AS ts,
                    toDate(parseDateTimeBestEffort(toString(updated_at))) AS log_date
                FROM nitip.unit_gps_logs
                WHERE toFloat64(latitude) != 0 AND toFloat64(longitude) != 0
                  AND toDate(parseDateTimeBestEffort(toString(updated_at))) >= $safeFrom
                  AND toDate(parseDateTimeBestEffort(toString(updated_at))) <= $safeTo
            ),
            with_next AS (
                SELECT
                    unit_id,
                    log_date,
                    lat,
                    lon,
                    ts,
                    lead(lat, 1) OVER (PARTITION BY unit_id, log_date ORDER BY ts) AS next_lat,
                    lead(lon, 1) OVER (PARTITION BY unit_id, log_date ORDER BY ts) AS next_lon
                FROM logs
            )
            SELECT
                unit_id,
                log_date,
                sum(greatCircleDistance(lon, lat, next_lon, next_lat)) / 1000.0 AS day_km,
                dateDiff('second', min(ts), max(ts)) AS day_seconds
            FROM with_next
            WHERE next_lat IS NOT NULL AND next_lon IS NOT NULL
            GROUP BY unit_id, log_date
            ORDER BY unit_id, log_date
        ";

        $dailyRows = $ch->query($sqlAgg);
        if (!is_array($dailyRows)) {
            $dailyRows = [];
        }

        // Satu query: daftar unit untuk label
        $sqlUnits = "
            SELECT toString(id) AS id, toString(vehicle_name) AS vehicle_name, toString(vehicle_number) AS vehicle_number
            FROM nitip.units
            ORDER BY vehicle_name ASC
        ";
        $units = $ch->query($sqlUnits);
        if (!is_array($units)) {
            $units = [];
        }

        $unitLabels = [];
        foreach ($units as $u) {
            $id = $u['id'] ?? null;
            if ($id === null || $id === '') {
                continue;
            }
            $noUnit = trim((string) ($u['vehicle_number'] ?? $u['vehicle_name'] ?? $id));
            $unitLabels[$id] = $noUnit !== '' ? $noUnit : (string) $id;
        }

        // Agregasi per unit di PHP (sum km, sum seconds, kumpulkan tanggal)
        $byUnit = [];
        foreach ($dailyRows as $row) {
            $unitId = $row['unit_id'] ?? null;
            if ($unitId === null || $unitId === '') {
                continue;
            }
            if (!isset($byUnit[$unitId])) {
                $byUnit[$unitId] = ['km' => 0.0, 'seconds' => 0, 'dates' => []];
            }
            $byUnit[$unitId]['km'] += (float) ($row['day_km'] ?? 0);
            $byUnit[$unitId]['seconds'] += (int) ($row['day_seconds'] ?? 0);
            $logDate = $row['log_date'] ?? null;
            if ($logDate !== null && $logDate !== '') {
                $byUnit[$unitId]['dates'][] = $logDate;
            }
        }

        // Urutan sesuai daftar unit, output format yang sama seperti sebelumnya
        $result = [];
        foreach ($units as $u) {
            $unitId = $u['id'] ?? null;
            if ($unitId === null || $unitId === '') {
                continue;
            }
            $agg = $byUnit[$unitId] ?? ['km' => 0.0, 'seconds' => 0, 'dates' => []];
            $totalKm = $agg['km'];
            $totalSeconds = $agg['seconds'];
            $activeDates = $agg['dates'];

            $totalHours = round($totalSeconds / 3600, 2);
            $jarakText = $totalKm >= 1
                ? number_format($totalKm, 2, ',', '.') . ' km'
                : number_format($totalKm * 1000, 0, ',', '.') . ' m';
            $tanggalAktif = count($activeDates) > 0 ? implode(', ', $activeDates) : '-';

            $result[] = [
                'no_unit' => $unitLabels[$unitId] ?? (string) $unitId,
                'jarak' => $jarakText,
                'waktu_jam' => $totalHours,
                'tanggal_aktif' => $tanggalAktif,
            ];
        }

        return $result;
    }
}
