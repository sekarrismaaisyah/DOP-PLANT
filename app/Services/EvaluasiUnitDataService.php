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

        $dailyRows = $this->getRawDailyRows($dateFrom, $dateTo);
        if (empty($dailyRows)) {
            return [];
        }

        // Daftar unit untuk label
        $sqlUnits = "
            SELECT toString(id) AS id, toString(vehicle_name) AS vehicle_name, toString(vehicle_number) AS vehicle_number
            FROM nitip.units
            ORDER BY vehicle_name ASC
        ";
        $ch = new ClickHouseService('clickhouse_nitip');
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
            $tanggalAktif = count($activeDates) > 0
                ? min($activeDates) . ' - ' . max($activeDates)
                : '-';

            $result[] = [
                'no_unit' => $unitLabels[$unitId] ?? (string) $unitId,
                'jarak' => $jarakText,
                'waktu_jam' => $totalHours,
                'tanggal_aktif' => $tanggalAktif,
            ];
        }

        return $result;
    }

    /**
     * Raw rows dari ClickHouse: per (unit_id, log_date) -> day_km, day_seconds.
     *
     * @return list<array{unit_id: string, log_date: string, day_km: float, day_seconds: int}>
     */
    private function getRawDailyRows(string $dateFrom, string $dateTo): array
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (!$ch->isConnected()) {
            return [];
        }

        $safeFrom = "'" . addslashes($dateFrom) . "'";
        $safeTo = "'" . addslashes($dateTo) . "'";

        $sqlAgg = "
            WITH logs AS (
                SELECT
                    toString(unit_id) AS unit_id,
                    assumeNotNull(toFloat64(latitude)) AS lat,
                    assumeNotNull(toFloat64(longitude)) AS lon,
                    parseDateTimeBestEffort(toString(updated_at)) AS ts,
                    toDate(parseDateTimeBestEffort(toString(updated_at))) AS log_date
                FROM nitip.unit_gps_logs
                WHERE toFloat64(latitude) != 0 AND toFloat64(longitude) != 0
                  AND toDate(parseDateTimeBestEffort(toString(updated_at))) >= $safeFrom
                  AND toDate(parseDateTimeBestEffort(toString(updated_at))) <= $safeTo
            ),
            grouped AS (
                SELECT
                    unit_id,
                    log_date,
                    groupArray((ts, lat, lon)) AS arr,
                    min(ts) AS first_ts,
                    max(ts) AS last_ts
                FROM logs
                GROUP BY unit_id, log_date
            ),
            with_arrays AS (
                SELECT
                    unit_id,
                    log_date,
                    arrayMap(t -> t.2, arraySort(arr)) AS lat_arr,
                    arrayMap(t -> t.3, arraySort(arr)) AS lon_arr,
                    first_ts,
                    last_ts
                FROM grouped
            )
            SELECT
                unit_id,
                log_date,
                if(length(lat_arr) >= 2,
                    arraySum(arrayMap((lon1, lat1, lon2, lat2) -> greatCircleDistance(lon1, lat1, lon2, lat2),
                        arraySlice(lon_arr, 1, length(lon_arr) - 1),
                        arraySlice(lat_arr, 1, length(lat_arr) - 1),
                        arraySlice(lon_arr, 2, length(lon_arr) - 1),
                        arraySlice(lat_arr, 2, length(lat_arr) - 1)
                    )) / 1000.0,
                    0
                ) AS day_km,
                dateDiff('second', first_ts, last_ts) AS day_seconds
            FROM with_arrays
            ORDER BY unit_id, log_date
        ";

        $dailyRows = $ch->query($sqlAgg);
        return is_array($dailyRows) ? $dailyRows : [];
    }

    /**
     * Ringkasan per hari: total jarak dan total durasi (jam) per tanggal (semua unit digabung).
     * Konsep sama dengan evaluasi unit: jarak = sum segment GPS, durasi = sum (max-min ts per unit per hari).
     *
     * @return array<int, array{tanggal: string, total_jarak: string, total_jam: float}>
     */
    public function getDailyTotals(?string $dateFrom, ?string $dateTo): array
    {
        if (!$dateFrom || !$dateTo) {
            return [];
        }

        $dailyRows = $this->getRawDailyRows($dateFrom, $dateTo);
        if (empty($dailyRows)) {
            return [];
        }

        $byDate = [];
        foreach ($dailyRows as $row) {
            $logDate = $row['log_date'] ?? null;
            if ($logDate === null || $logDate === '') {
                continue;
            }
            if (!isset($byDate[$logDate])) {
                $byDate[$logDate] = ['km' => 0.0, 'seconds' => 0];
            }
            $byDate[$logDate]['km'] += (float) ($row['day_km'] ?? 0);
            $byDate[$logDate]['seconds'] += (int) ($row['day_seconds'] ?? 0);
        }

        ksort($byDate);
        $result = [];
        foreach ($byDate as $tanggal => $agg) {
            $totalKm = $agg['km'];
            $totalSeconds = $agg['seconds'];
            $totalHours = round($totalSeconds / 3600, 2);
            $jarakText = $totalKm >= 1
                ? number_format($totalKm, 2, ',', '.') . ' km'
                : number_format($totalKm * 1000, 0, ',', '.') . ' m';
            $result[] = [
                'tanggal' => $tanggal,
                'total_jarak' => $jarakText,
                'total_jam' => $totalHours,
            ];
        }
        return $result;
    }
}
