<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Models\RosterPlanning;
use App\Services\ClickHouseService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /** Koneksi ClickHouse untuk data nitip (172.21.1.29, database nitip). */
    private function getClickHouseNitip(): ClickHouseService
    {
        return app(ClickHouseService::class, ['connectionName' => 'clickhouse_nitip']);
    }

    /**
     * Menampilkan halaman Dashboard Coverage Area (Coverage All).
     * KPI: total lokasi+detail aktif dari nitip.bep_vw_site_lokasi_detil_lokasi (status = 1),
     * covered = yang punya minimal satu SAP (Inspeksi, OAK, Observasi, Coaching) di nitip.
     */
    public function coverageAll(): View
    {
        $totalLokasi = 0;
        $coveredLokasi = 0;
        $pctCoverage = 0;

        try {
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return view('SistemRoster.dashboard.coverage-all', compact('totalLokasi', 'coveredLokasi', 'pctCoverage'));
            }

            // 1. Master: (lokasi, Detil_Lokasi) aktif — status_site, status_lokasi, status_detil_lokasi = '1'
            $sqlMaster = "SELECT trim(ifNull(toString(lokasi), '')) AS loc, trim(ifNull(toString(Detil_Lokasi), '')) AS det
                FROM nitip.bep_vw_site_lokasi_detil_lokasi
                WHERE trim(ifNull(toString(status_site), '')) = '1'
                  AND trim(ifNull(toString(status_lokasi), '')) = '1'
                  AND trim(ifNull(toString(status_detil_lokasi), '')) = '1'";
            $rowsMaster = $ch->query($sqlMaster);
            if (empty($rowsMaster) || ! is_array($rowsMaster)) {
                return view('SistemRoster.dashboard.coverage-all', compact('totalLokasi', 'coveredLokasi', 'pctCoverage'));
            }

            // Key = lowercase(normalize(lokasi)|normalize(detil)) agar match case-insensitive dengan data SAP
            $masterKeys = [];
            foreach ($rowsMaster as $row) {
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $key = mb_strtolower($loc . '|' . $det);
                $masterKeys[$key] = true;
            }
            $totalLokasi = count($masterKeys);

            if ($totalLokasi === 0) {
                return view('SistemRoster.dashboard.coverage-all', compact('totalLokasi', 'coveredLokasi', 'pctCoverage'));
            }

            // 2. Lokasi yang punya minimal satu SAP (Inspeksi, OAK, Observasi, Coaching) di nitip — pakai DISTINCT agar ringan
            $coveredKeys = [];

            $addCovered = function ($result, string $locCol, string $detCol) use (&$coveredKeys) {
                $rows = is_array($result) ? $result : [];
                foreach ($rows as $r) {
                    $loc = $this->normalizeLocation($this->getClickHouseRowValue($r, $locCol));
                    $det = $this->normalizeLocation($this->getClickHouseRowValue($r, $detCol));
                    $key = mb_strtolower($loc . '|' . $det);
                    $coveredKeys[$key] = true;
                }
            };

            $sqlCar = "SELECT DISTINCT trim(ifNull(nama_lokasi, '')) AS loc, trim(ifNull(nama_detail_lokasi, '')) AS det FROM nitip.aaj_car_all_year_from_dav WHERE trim(ifNull(nama_lokasi, '')) != '' OR trim(ifNull(nama_detail_lokasi, '')) != ''";
            try {
                $addCovered($ch->query($sqlCar), 'loc', 'det');
            } catch (\Throwable $e) {
                Log::warning('DashboardController coverageAll CAR: ' . $e->getMessage());
            }

            $sqlOak = "SELECT DISTINCT trim(ifNull(toString(location), '')) AS loc, trim(ifNull(toString(detail_location), '')) AS det FROM nitip.aaj_vw_car_oak_register_ytd_only WHERE trim(ifNull(toString(location), '')) != '' OR trim(ifNull(toString(detail_location), '')) != ''";
            try {
                $addCovered($ch->query($sqlOak), 'loc', 'det');
            } catch (\Throwable $e) {
                Log::warning('DashboardController coverageAll OAK: ' . $e->getMessage());
            }

            $sqlObs = "SELECT DISTINCT trim(ifNull(toString(Lokasi), '')) AS loc, trim(ifNull(toString(Detil_Lokasi), '')) AS det FROM nitip.aaj_database_observasi_from_bep_ytd_only WHERE trim(ifNull(toString(Lokasi), '')) != '' OR trim(ifNull(toString(Detil_Lokasi), '')) != ''";
            try {
                $addCovered($ch->query($sqlObs), 'loc', 'det');
            } catch (\Throwable $e) {
                Log::warning('DashboardController coverageAll Observasi: ' . $e->getMessage());
            }

            $sqlCoaching = "SELECT DISTINCT trim(ifNull(toString(lokasi), '')) AS loc, trim(ifNull(toString(detil_lokasi), '')) AS det FROM nitip.bep_vw_database_coaching WHERE trim(ifNull(toString(lokasi), '')) != '' OR trim(ifNull(toString(detil_lokasi), '')) != ''";
            try {
                $addCovered($ch->query($sqlCoaching), 'loc', 'det');
            } catch (\Throwable $e) {
                Log::warning('DashboardController coverageAll Coaching: ' . $e->getMessage());
            }

            foreach (array_keys($masterKeys) as $key) {
                if (isset($coveredKeys[$key])) {
                    $coveredLokasi++;
                }
            }

            $pctCoverage = $totalLokasi > 0 ? (int) round($coveredLokasi / $totalLokasi * 100) : 0;
        } catch (\Throwable $e) {
            Log::warning('DashboardController coverageAll: ' . $e->getMessage());
        }

        return view('SistemRoster.dashboard.coverage-all', compact('totalLokasi', 'coveredLokasi', 'pctCoverage'));
    }

    /**
     * Menampilkan halaman Performance Dashboard Sistem Roster.
     * Coverage by Location: ada SAP di lokasi (siapapun) — tidak match nama.
     * Detail Plan Pengecekan: OK hanya jika ada SAP dari karyawan yang di-assign (match nama + lokasi + tanggal).
     * Filter tanggal via query string ?date=YYYY-MM-DD (default: hari ini).
     */
    public function index(): View
    {
        $filterDate = request('date')
            ? Carbon::parse(request('date'))->startOfDay()
            : now()->startOfDay();

        $assignedPlannings = RosterPlanning::with('karyawans')
            ->where('status', 'assigned')
            ->whereDate('tanggal', $filterDate)
            ->orderByDesc('tanggal')
            ->orderByDesc('updated_at')
            ->get();

        $filterDateStr = $filterDate->format('Y-m-d');
        $carHazardInspeksiForDate = $this->getCarHazardInspeksiFromClickHouseForDate($filterDateStr);
        // Coverage by Location: tidak match nama — lokasi covered jika ada SAP ATAU OAK ATAU Observasi ATAU Coaching (siapapun) di lokasi itu
        $oakLocationKeysForDate = $this->getOakLocationKeysForDate($filterDateStr);
        $observasiLocationKeysForDate = $this->getObservasiLocationKeysForDate($filterDateStr);
        $coachingLocationKeysForDate = $this->getCoachingLocationKeysForDate($filterDateStr);
        // Detail Plan: match sampai nama — butuh OAK, Observasi, Coaching per (date, locationKey, nama) untuk filter date saja
        $oakForDate = $this->getOakDataByDateRange($filterDateStr, $filterDateStr);
        $observasiForDate = $this->getObservasiDataByDateRange($filterDateStr, $filterDateStr);
        $coachingForDate = $this->getCoachingDataByDateRange($filterDateStr, $filterDateStr);
        $oakByLocForDate = $oakForDate[$filterDateStr] ?? [];
        $observasiByLocForDate = $observasiForDate[$filterDateStr] ?? [];
        $coachingByLocForDate = $coachingForDate[$filterDateStr] ?? [];

        foreach ($assignedPlannings as $planning) {
            $planLokasiNorm = $this->normalizeLocation($planning->lokasi ?? '');
            $planDetailNorm = $this->normalizeLocation($planning->detail_lokasi ?? '');
            $locationKey = $planLokasiNorm . '|' . $planDetailNorm;

            // Coverage by Location: ada SAP atau OAK atau Observasi atau Coaching di lokasi (siapapun) — tidak match nama
            $matchCoverage = $this->planningMatchHazardInspeksiByLokasi($planning, $carHazardInspeksiForDate);
            $coveredByOak = in_array($locationKey, $oakLocationKeysForDate, true);
            $coveredByObservasi = in_array($locationKey, $observasiLocationKeysForDate, true);
            $coveredByCoaching = in_array($locationKey, $coachingLocationKeysForDate, true);
            $planning->setAttribute('car_status_coverage', ($matchCoverage['ok'] || $coveredByOak || $coveredByObservasi || $coveredByCoaching) ? 'ok' : 'notok');
            $planning->setAttribute('car_task_id_coverage', $matchCoverage['task_id']);

            // Detail Plan Pengecekan: OK jika ada SAP atau OAK atau Observasi dari karyawan yang di-assign (match nama + lokasi + tanggal)
            $matchDetail = $this->planningMatchHazardInspeksiByLokasiAndNama($planning, $carHazardInspeksiForDate);
            $carStatus = $matchDetail['ok'] ? 'ok' : 'notok';
            $carTaskId = $matchDetail['task_id'];
            $carJenisSap = $matchDetail['jenis_sap'] ?? null;

            if ($carStatus !== 'ok') {
                $karyawanNamesLower = [];
                foreach ($planning->karyawans ?? [] as $k) {
                    $nama = trim((string) ($k->nama_karyawan ?? ''));
                    if ($nama !== '') {
                        $karyawanNamesLower[mb_strtolower($nama)] = true;
                    }
                }
                if (isset($oakByLocForDate[$locationKey])) {
                    foreach (array_keys($karyawanNamesLower) as $namaLower) {
                        if (isset($oakByLocForDate[$locationKey][$namaLower])) {
                            $carStatus = 'ok';
                            $carJenisSap = $carJenisSap ?? 'OAK';
                            break;
                        }
                    }
                }
                if ($carStatus !== 'ok' && isset($observasiByLocForDate[$locationKey])) {
                    foreach (array_keys($karyawanNamesLower) as $namaLower) {
                        if (isset($observasiByLocForDate[$locationKey][$namaLower])) {
                            $carStatus = 'ok';
                            $carJenisSap = $carJenisSap ?? 'Observasi';
                            break;
                        }
                    }
                }
                if ($carStatus !== 'ok' && isset($coachingByLocForDate[$locationKey])) {
                    foreach (array_keys($karyawanNamesLower) as $namaLower) {
                        if (isset($coachingByLocForDate[$locationKey][$namaLower])) {
                            $carStatus = 'ok';
                            $carJenisSap = $carJenisSap ?? 'Coaching';
                            break;
                        }
                    }
                }
            }

            $planning->setAttribute('car_status', $carStatus);
            $planning->setAttribute('car_task_id', $carTaskId);
            $planning->setAttribute('car_jenis_sap', $carJenisSap);
        }

        $buildCoverage = function ($plannings) {
            return $plannings
                ->groupBy(fn ($p) => trim($p->lokasi ?? '') . '|' . trim($p->detail_lokasi ?? ''))
                ->map(function ($items) {
                    $first = $items->first();
                    $okCount = $items->where('car_status_coverage', 'ok')->count();
                    $total = $items->count();
                    $pct = $total > 0 ? (int) round($okCount / $total * 100) : 0;
                    $taskIds = [];
                    foreach ($items as $p) {
                        $ids = $p->getAttribute('car_task_id_coverage');
                        if ($ids !== null && $ids !== '') {
                            foreach (array_map('trim', explode(',', (string) $ids)) as $id) {
                                if ($id !== '') {
                                    $taskIds[$id] = true;
                                }
                            }
                        }
                    }
                    $taskIds = array_keys($taskIds);
                    return (object) [
                        'lokasi' => $first->lokasi ?? '',
                        'detail_lokasi' => $first->detail_lokasi ?? '',
                        'site' => trim($first->site ?? ''),
                        'total' => $total,
                        'ok_count' => $okCount,
                        'pct' => $pct,
                        'task_ids' => $taskIds,
                    ];
                })
                ->values();
        };

        $coverageLocations = $buildCoverage($assignedPlannings);
        $coverageByIkk = $buildCoverage($assignedPlannings->where('source_type', 'IKK'));
        $coverageByDop = $buildCoverage($assignedPlannings->where('source_type', 'DOP'));
        $coverageNonKritis = $buildCoverage($assignedPlannings->where('source_type', 'Roster'));

        // Detail Plan Pengecekan: satu baris per karyawan (tiap karyawan beda pembagian lokasi/detail lokasi dari planning-nya)
        $detailRows = collect();
        foreach ($assignedPlannings as $planning) {
            $shiftVal = $planning->shift ? trim((string) $planning->shift) : '';
            $karyawans = $planning->karyawans ?? collect();
            if ($karyawans->isEmpty()) {
                $detailRows->push((object) [
                    'tanggal' => $planning->tanggal,
                    'shift' => $planning->shift,
                    'shift_val' => $shiftVal,
                    'kategori_area' => $planning->kategori_area ?? 'Area Highrisk',
                    'lokasi' => $planning->lokasi ?? '—',
                    'detail_lokasi' => $planning->detail_lokasi ?? '—',
                    'aktivitas' => $planning->aktivitas ?? '—',
                    'karyawan_nama' => '—',
                    'car_task_id' => $planning->getAttribute('car_task_id') ?? '—',
                    'detail_reason' => '—',
                    'jenis_sap' => $planning->getAttribute('car_jenis_sap') ?? $planning->jenis_sap ?? null,
                    'car_status' => $planning->getAttribute('car_status') ?? 'notok',
                ]);
            } else {
                foreach ($karyawans as $k) {
                    $firstK = $planning->karyawans->first();
                    $detailReason = $firstK && ($firstK->reason || $firstK->detail) ? ($firstK->reason ?? $firstK->detail ?? '') : '';
                    $detailRows->push((object) [
                        'tanggal' => $planning->tanggal,
                        'shift' => $planning->shift,
                        'shift_val' => $shiftVal,
                        'kategori_area' => $planning->kategori_area ?? 'Area Highrisk',
                        'lokasi' => $planning->lokasi ?? '—',
                        'detail_lokasi' => $planning->detail_lokasi ?? '—',
                        'aktivitas' => $planning->aktivitas ?? '—',
                        'karyawan_nama' => $k->nama_karyawan ?? '—',
                        'car_task_id' => $planning->getAttribute('car_task_id') ?? '—',
                        'detail_reason' => $detailReason ?: '—',
                        'jenis_sap' => $planning->getAttribute('car_jenis_sap') ?? $planning->jenis_sap ?? null,
                        'car_status' => $planning->getAttribute('car_status') ?? 'notok',
                    ]);
                }
            }
        }
        $detailByLokasi = $detailRows;

        $heatmapData = $this->buildHeatmapData();

        return view('SistemRoster.dashboard.index', [
            'assignedPlannings' => $assignedPlannings,
            'coverageLocations' => $coverageLocations,
            'coverageByIkk' => $coverageByIkk,
            'coverageByDop' => $coverageByDop,
            'coverageNonKritis' => $coverageNonKritis,
            'detailByLokasi' => $detailByLokasi,
            'heatmapData' => $heatmapData,
            'filterDate' => $filterDate->format('Y-m-d'),
        ]);
    }

    /**
     * Data heatmap: per (date, site) -> planned count & actual count.
     * Actual = CAR (nama + lokasi + detail_lokasi + tanggal) OR OAK OR Observasi OR Coaching — semuanya match lokasi planning.
     *
     * @return array<int, array{date: string, site: string, planned: int, actual: int}>
     */
    private function buildHeatmapData(): array
    {
        $start = now()->subMonths(2)->startOfMonth()->format('Y-m-d');
        $end = now()->addMonths(2)->endOfMonth()->format('Y-m-d');

        // Planned = planning yang sudah punya minimal 1 karyawan (roster_planning_karyawans), bukan berdasarkan status
        $plannings = RosterPlanning::with('karyawans')
            ->whereBetween('tanggal', [$start, $end])
            ->whereHas('karyawans')
            ->orderBy('tanggal')
            ->get();

        $carByDate = $this->getCarDataByDateRange($start, $end);
        $oakByDate = $this->getOakDataByDateRange($start, $end);
        $observasiByDate = $this->getObservasiDataByDateRange($start, $end);
        $coachingByDate = $this->getCoachingDataByDateRange($start, $end);

        $byKey = [];
        foreach ($plannings as $p) {
            $date = $p->tanggal ? Carbon::parse($p->tanggal)->format('Y-m-d') : null;
            if (! $date) {
                continue;
            }
            $site = trim((string) ($p->site ?? ''));
            $key = $date . '|' . $site;
            if (! isset($byKey[$key])) {
                $byKey[$key] = ['date' => $date, 'site' => $site, 'planned' => 0, 'actual' => 0];
            }
            $byKey[$key]['planned']++;

            $planLokasiNorm = $this->normalizeLocation($p->lokasi ?? '');
            $planDetailNorm = $this->normalizeLocation($p->detail_lokasi ?? '');
            $locationKey = $planLokasiNorm . '|' . $planDetailNorm;

            $karyawanNamesLower = [];
            foreach ($p->karyawans ?? [] as $k) {
                $nama = trim((string) ($k->nama_karyawan ?? ''));
                if ($nama !== '') {
                    $karyawanNamesLower[mb_strtolower($nama)] = true;
                }
            }

            $isActual = false;
            // CAR (Inspeksi Hazard): nama_pelapor + nama_lokasi + nama_detail_lokasi + tanggal match
            if (isset($carByDate[$date][$locationKey])) {
                foreach (array_keys($karyawanNamesLower) as $namaLower) {
                    if (isset($carByDate[$date][$locationKey][$namaLower])) {
                        $isActual = true;
                        break;
                    }
                }
            }
            // OAK: location + detail_location + submit_by match
            if (! $isActual && isset($oakByDate[$date][$locationKey])) {
                foreach (array_keys($karyawanNamesLower) as $namaLower) {
                    if (isset($oakByDate[$date][$locationKey][$namaLower])) {
                        $isActual = true;
                        break;
                    }
                }
            }
            // Observasi: lokasi + detil_lokasi + nama_pelapor match
            if (! $isActual && isset($observasiByDate[$date][$locationKey])) {
                foreach (array_keys($karyawanNamesLower) as $namaLower) {
                    if (isset($observasiByDate[$date][$locationKey][$namaLower])) {
                        $isActual = true;
                        break;
                    }
                }
            }
            // Coaching: lokasi + detil_lokasi + nama_coach match
            if (! $isActual && isset($coachingByDate[$date][$locationKey])) {
                foreach (array_keys($karyawanNamesLower) as $namaLower) {
                    if (isset($coachingByDate[$date][$locationKey][$namaLower])) {
                        $isActual = true;
                        break;
                    }
                }
            }

            if ($isActual) {
                $byKey[$key]['actual']++;
            }
        }

        return array_values($byKey);
    }

    /**
     * OAK per (date, location_key): set of submit_by (lowercase) from aaj_vw_car_oak_register_ytd_only.
     * Untuk heatmap: actual jika ada OAK dengan location+detail_location match dan submit_by = salah satu karyawan assign.
     *
     * @return array<string, array<string, array<string, true>>> [date => [ locationKey => [ nama_lower => true ] ] ]
     */
    private function getOakDataByDateRange(string $start, string $end): array
    {
        $startEsc = addslashes($start);
        $endEsc = addslashes($end);
        $sql = "SELECT toDate(submit_date, 'Asia/Makassar') AS dt,
                trim(ifNull(toString(location), '')) AS loc,
                trim(ifNull(toString(detail_location), '')) AS det,
                trim(ifNull(toString(submit_by), '')) AS submit_by
                FROM nitip.aaj_vw_car_oak_register_ytd_only
                WHERE submit_date IS NOT NULL
                  AND toDate(submit_date, 'Asia/Makassar') >= toDate('{$startEsc}')
                  AND toDate(submit_date, 'Asia/Makassar') <= toDate('{$endEsc}')";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $byDate = [];
            foreach ($results as $row) {
                $dt = $this->getClickHouseRowValue($row, 'dt');
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $nama = trim((string) $this->getClickHouseRowValue($row, 'submit_by'));
                $dateStr = $dt instanceof \DateTimeInterface
                    ? $dt->format('Y-m-d')
                    : (is_string($dt) ? substr($dt, 0, 10) : '');
                if ($dateStr === '') {
                    continue;
                }
                $locationKey = $loc . '|' . $det;
                if (! isset($byDate[$dateStr])) {
                    $byDate[$dateStr] = [];
                }
                if (! isset($byDate[$dateStr][$locationKey])) {
                    $byDate[$dateStr][$locationKey] = [];
                }
                if ($nama !== '') {
                    $byDate[$dateStr][$locationKey][mb_strtolower($nama)] = true;
                }
            }
            return $byDate;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getOakDataByDateRange: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Observasi per (date, location_key): set of nama_pelapor (lowercase) from nitip.aaj_database_observasi_from_bep_ytd_only.
     * Untuk heatmap: actual jika ada Observasi dengan lokasi+detil_lokasi match dan nama_pelapor = salah satu karyawan assign.
     * Kolom tanggal di nitip: Date (DateTime64).
     *
     * @return array<string, array<string, array<string, true>>> [date => [ locationKey => [ nama_lower => true ] ] ]
     */
    private function getObservasiDataByDateRange(string $start, string $end): array
    {
        $startEsc = addslashes($start);
        $endEsc = addslashes($end);
        $sql = "SELECT toDate(Date, 'Asia/Makassar') AS dt,
                trim(ifNull(toString(Lokasi), '')) AS loc,
                trim(ifNull(toString(Detil_Lokasi), '')) AS det,
                trim(ifNull(toString(nama_pelapor), '')) AS nama_pelapor
                FROM nitip.aaj_database_observasi_from_bep_ytd_only
                WHERE Date IS NOT NULL
                  AND toDate(Date, 'Asia/Makassar') >= toDate('{$startEsc}')
                  AND toDate(Date, 'Asia/Makassar') <= toDate('{$endEsc}')";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $byDate = [];
            foreach ($results as $row) {
                $dt = $this->getClickHouseRowValue($row, 'dt');
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $nama = trim((string) $this->getClickHouseRowValue($row, 'nama_pelapor'));
                $dateStr = $dt instanceof \DateTimeInterface
                    ? $dt->format('Y-m-d')
                    : (is_string($dt) ? substr($dt, 0, 10) : '');
                if ($dateStr === '') {
                    continue;
                }
                $locationKey = $loc . '|' . $det;
                if (! isset($byDate[$dateStr])) {
                    $byDate[$dateStr] = [];
                }
                if (! isset($byDate[$dateStr][$locationKey])) {
                    $byDate[$dateStr][$locationKey] = [];
                }
                if ($nama !== '') {
                    $byDate[$dateStr][$locationKey][mb_strtolower($nama)] = true;
                }
            }
            return $byDate;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getObservasiDataByDateRange: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * OAK: daftar location key (lokasi_norm|detail_norm) yang punya minimal 1 record pada tanggal tersebut.
     * Untuk Coverage by Location — tidak match nama, hanya cek ada data di lokasi atau tidak.
     *
     * @return array<int, string>
     */
    private function getOakLocationKeysForDate(string $dateStr): array
    {
        $dateEsc = addslashes($dateStr);
        $sql = "SELECT trim(ifNull(toString(location), '')) AS loc, trim(ifNull(toString(detail_location), '')) AS det
                FROM nitip.aaj_vw_car_oak_register_ytd_only
                WHERE submit_date IS NOT NULL AND toDate(submit_date, 'Asia/Makassar') = toDate('{$dateEsc}')";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $keys = [];
            foreach ($results as $row) {
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $key = $loc . '|' . $det;
                $keys[$key] = true;
            }
            return array_keys($keys);
        } catch (\Throwable $e) {
            Log::warning('DashboardController getOakLocationKeysForDate: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Observasi: daftar location key (lokasi_norm|detail_norm) yang punya minimal 1 record pada tanggal tersebut.
     * Untuk Coverage by Location — tidak match nama.
     *
     * @return array<int, string>
     */
    private function getObservasiLocationKeysForDate(string $dateStr): array
    {
        $dateEsc = addslashes($dateStr);
        $sql = "SELECT trim(ifNull(toString(Lokasi), '')) AS loc, trim(ifNull(toString(Detil_Lokasi), '')) AS det
                FROM nitip.aaj_database_observasi_from_bep_ytd_only
                WHERE Date IS NOT NULL AND toDate(Date, 'Asia/Makassar') = toDate('{$dateEsc}')";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $keys = [];
            foreach ($results as $row) {
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $key = $loc . '|' . $det;
                $keys[$key] = true;
            }
            return array_keys($keys);
        } catch (\Throwable $e) {
            Log::warning('DashboardController getObservasiLocationKeysForDate: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Coaching per (date, location_key): set of nama_coach (lowercase) dari nitip.bep_vw_database_coaching.
     * Match: nama_coach = salah satu karyawan assign, lokasi + detil_lokasi + Tanggal_Pembuatan.
     *
     * @return array<string, array<string, array<string, true>>> [date => [ locationKey => [ nama_lower => true ] ] ]
     */
    private function getCoachingDataByDateRange(string $start, string $end): array
    {
        $startEsc = addslashes($start);
        $endEsc = addslashes($end);
        $sql = "SELECT toDate(Tanggal_Pembuatan, 'Asia/Makassar') AS dt,
                trim(ifNull(toString(lokasi), '')) AS loc,
                trim(ifNull(toString(detil_lokasi), '')) AS det,
                trim(ifNull(toString(nama_coach), '')) AS nama_coach
                FROM nitip.bep_vw_database_coaching
                WHERE Tanggal_Pembuatan IS NOT NULL
                  AND toDate(Tanggal_Pembuatan, 'Asia/Makassar') >= toDate('{$startEsc}')
                  AND toDate(Tanggal_Pembuatan, 'Asia/Makassar') <= toDate('{$endEsc}')";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $byDate = [];
            foreach ($results as $row) {
                $dt = $this->getClickHouseRowValue($row, 'dt');
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $nama = trim((string) $this->getClickHouseRowValue($row, 'nama_coach'));
                $dateStr = $dt instanceof \DateTimeInterface
                    ? $dt->format('Y-m-d')
                    : (is_string($dt) ? substr($dt, 0, 10) : '');
                if ($dateStr === '') {
                    continue;
                }
                $locationKey = $loc . '|' . $det;
                if (! isset($byDate[$dateStr])) {
                    $byDate[$dateStr] = [];
                }
                if (! isset($byDate[$dateStr][$locationKey])) {
                    $byDate[$dateStr][$locationKey] = [];
                }
                if ($nama !== '') {
                    $byDate[$dateStr][$locationKey][mb_strtolower($nama)] = true;
                }
            }
            return $byDate;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getCoachingDataByDateRange: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Coaching: daftar location key yang punya minimal 1 record pada tanggal tersebut (untuk coverage by location).
     *
     * @return array<int, string>
     */
    private function getCoachingLocationKeysForDate(string $dateStr): array
    {
        $dateEsc = addslashes($dateStr);
        $sql = "SELECT trim(ifNull(toString(lokasi), '')) AS loc, trim(ifNull(toString(detil_lokasi), '')) AS det
                FROM nitip.bep_vw_database_coaching
                WHERE Tanggal_Pembuatan IS NOT NULL AND toDate(Tanggal_Pembuatan, 'Asia/Makassar') = toDate('{$dateEsc}')";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $keys = [];
            foreach ($results as $row) {
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $key = $loc . '|' . $det;
                $keys[$key] = true;
            }
            return array_keys($keys);
        } catch (\Throwable $e) {
            Log::warning('DashboardController getCoachingLocationKeysForDate: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * CAR (Inspeksi Hazard) per (date, location_key): set of nama_pelapor (lowercase) dari nitip.aaj_car_all_year_from_dav.
     * Untuk heatmap: actual jika ada CAR dengan nama_lokasi + nama_detail_lokasi + nama_pelapor + tanggal match planning.
     *
     * @return array<string, array<string, array<string, true>>> [date => [ locationKey => [ nama_lower => true ] ] ]
     */
    private function getCarDataByDateRange(string $start, string $end): array
    {
        $startEsc = addslashes($start);
        $endEsc = addslashes($end);
        $conditions = [
            "tanggal_pembuatan IS NOT NULL",
            "toDate(tanggal_pembuatan, 'Asia/Makassar') >= toDate('{$startEsc}')",
            "toDate(tanggal_pembuatan, 'Asia/Makassar') <= toDate('{$endEsc}')",
            "trim(ifNull(jenis_laporan, '')) IN ('HAZARD', 'INSPEKSI', 'INSPEKSI_HAZARD')",
        ];
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT toDate(tanggal_pembuatan, 'Asia/Makassar') AS dt,
                trim(ifNull(nama_lokasi, '')) AS loc,
                trim(ifNull(nama_detail_lokasi, '')) AS det,
                trim(ifNull(nama_pelapor, '')) AS nama_pelapor
                FROM nitip.aaj_car_all_year_from_dav
                WHERE {$whereClause}";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $byDate = [];
            foreach ($results as $row) {
                $dt = $this->getClickHouseRowValue($row, 'dt');
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $nama = trim((string) $this->getClickHouseRowValue($row, 'nama_pelapor'));
                $dateStr = $dt instanceof \DateTimeInterface
                    ? $dt->format('Y-m-d')
                    : (is_string($dt) ? substr($dt, 0, 10) : '');
                if ($dateStr === '') {
                    continue;
                }
                $locationKey = $loc . '|' . $det;
                if (! isset($byDate[$dateStr])) {
                    $byDate[$dateStr] = [];
                }
                if (! isset($byDate[$dateStr][$locationKey])) {
                    $byDate[$dateStr][$locationKey] = [];
                }
                if ($nama !== '') {
                    $byDate[$dateStr][$locationKey][mb_strtolower($nama)] = true;
                }
            }
            return $byDate;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getCarDataByDateRange: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ClickHouse: untuk setiap tanggal di range, daftar nama_pelapor (lowercase) yang ada.
     *
     * @return array<string, array<string, true>> key = date Y-m-d, value = map nama_lower => true
     */
    private function getCarNamaPelaporByDateRange(string $start, string $end): array
    {
        $conditions = [
            "tanggal_pembuatan IS NOT NULL",
            "toDate(tanggal_pembuatan, 'Asia/Makassar') >= toDate('" . addslashes($start) . "')",
            "toDate(tanggal_pembuatan, 'Asia/Makassar') <= toDate('" . addslashes($end) . "')",
        ];
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT toDate(tanggal_pembuatan, 'Asia/Makassar') AS dt, trim(ifNull(nama_pelapor, '')) AS nama_pelapor "
            . "FROM nitip.aaj_car_all_year_from_dav WHERE {$whereClause}";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $byDate = [];
            foreach ($results as $row) {
                $dt = $this->getClickHouseRowValue($row, 'dt');
                $nama = trim((string) $this->getClickHouseRowValue($row, 'nama_pelapor'));
                if ($nama === '') {
                    continue;
                }
                $dateStr = $dt instanceof \DateTimeInterface
                    ? $dt->format('Y-m-d')
                    : (is_string($dt) ? substr($dt, 0, 10) : '');
                if ($dateStr === '') {
                    continue;
                }
                if (! isset($byDate[$dateStr])) {
                    $byDate[$dateStr] = [];
                }
                $byDate[$dateStr][mb_strtolower($nama)] = true;
            }
            return $byDate;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getCarNamaPelaporByDateRange: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Normalisasi string lokasi/detail lokasi untuk perbandingan (trim + collapse spasi).
     */
    private function normalizeLocation(?string $s): string
    {
        if ($s === null || $s === '') {
            return '';
        }
        return preg_replace('/\s+/', ' ', trim($s));
    }

    /**
     * Ambil data hazard inspeksi (CAR jenis HAZARD/INSPEKSI, status SUBMITTED) dari ClickHouse untuk tanggal tertentu.
     * Digunakan untuk match: nama pelapor + lokasi + detail lokasi + tanggal.
     *
     * @param  string  $dateStr  Format Y-m-d
     * @return array<int, array{id: mixed, nama_lower: string, lokasi_norm: string, detail_lokasi_norm: string, jenis_laporan: string|null}>
     */
    private function getCarHazardInspeksiFromClickHouseForDate(string $dateStr): array
    {
        $conditions = [
            "tanggal_pembuatan IS NOT NULL AND toDate(tanggal_pembuatan, 'Asia/Makassar') = toDate('" . addslashes($dateStr) . "')",
            "trim(ifNull(jenis_laporan, '')) IN ('HAZARD', 'INSPEKSI', 'INSPEKSI_HAZARD')",
        ];
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT id, trim(ifNull(nama_pelapor, '')) AS nama_pelapor, trim(ifNull(nama_lokasi, '')) AS nama_lokasi, trim(ifNull(nama_detail_lokasi, '')) AS nama_detail_lokasi, trim(ifNull(jenis_laporan, '')) AS jenis_laporan FROM nitip.aaj_car_all_year_from_dav WHERE {$whereClause}";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $list = [];
            foreach ($results as $row) {
                $id = $this->getClickHouseRowValue($row, 'id');
                $nama = trim((string) $this->getClickHouseRowValue($row, 'nama_pelapor'));
                $lokasi = $this->normalizeLocation($this->getClickHouseRowValue($row, 'nama_lokasi'));
                $detailLokasi = $this->normalizeLocation($this->getClickHouseRowValue($row, 'nama_detail_lokasi'));
                $jenisLaporan = trim((string) $this->getClickHouseRowValue($row, 'jenis_laporan'));
                $list[] = [
                    'id' => $id,
                    'nama_lower' => $nama !== '' ? mb_strtolower($nama) : '',
                    'lokasi_norm' => $lokasi,
                    'detail_lokasi_norm' => $detailLokasi,
                    'jenis_laporan' => $jenisLaporan !== '' ? $jenisLaporan : null,
                ];
            }
            return $list;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getCarHazardInspeksiFromClickHouseForDate: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * OAK count per (locationKey, nama_lower) untuk satu tanggal. Untuk heatmap day detail.
     *
     * @return array<string, array<string, int>>
     */
    private function getOakCountsForDate(string $dateStr): array
    {
        $dateEsc = addslashes($dateStr);
        $sql = "SELECT trim(ifNull(toString(location), '')) AS loc, trim(ifNull(toString(detail_location), '')) AS det, trim(ifNull(toString(submit_by), '')) AS submit_by, count() AS cnt
                FROM nitip.aaj_vw_car_oak_register_ytd_only
                WHERE submit_date IS NOT NULL AND toDate(submit_date, 'Asia/Makassar') = toDate('{$dateEsc}')
                GROUP BY location, detail_location, submit_by";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $out = [];
            foreach ($results as $row) {
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $nama = trim((string) $this->getClickHouseRowValue($row, 'submit_by'));
                $cnt = (int) ($this->getClickHouseRowValue($row, 'cnt') ?? 0);
                $key = $loc . '|' . $det;
                if (! isset($out[$key])) {
                    $out[$key] = [];
                }
                if ($nama !== '') {
                    $out[$key][mb_strtolower($nama)] = $cnt;
                }
            }
            return $out;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getOakCountsForDate: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Observasi count per (locationKey, nama_lower) untuk satu tanggal.
     *
     * @return array<string, array<string, int>>
     */
    private function getObservasiCountsForDate(string $dateStr): array
    {
        $dateEsc = addslashes($dateStr);
        $sql = "SELECT trim(ifNull(toString(Lokasi), '')) AS loc, trim(ifNull(toString(Detil_Lokasi), '')) AS det, trim(ifNull(toString(nama_pelapor), '')) AS nama_pelapor, count() AS cnt
                FROM nitip.aaj_database_observasi_from_bep_ytd_only
                WHERE Date IS NOT NULL AND toDate(Date, 'Asia/Makassar') = toDate('{$dateEsc}')
                GROUP BY Lokasi, Detil_Lokasi, nama_pelapor";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $out = [];
            foreach ($results as $row) {
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $nama = trim((string) $this->getClickHouseRowValue($row, 'nama_pelapor'));
                $cnt = (int) ($this->getClickHouseRowValue($row, 'cnt') ?? 0);
                $key = $loc . '|' . $det;
                if (! isset($out[$key])) {
                    $out[$key] = [];
                }
                if ($nama !== '') {
                    $out[$key][mb_strtolower($nama)] = $cnt;
                }
            }
            return $out;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getObservasiCountsForDate: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Coaching count per (locationKey, nama_lower) untuk satu tanggal. Match nama_coach = karyawan assign.
     *
     * @return array<string, array<string, int>>
     */
    private function getCoachingCountsForDate(string $dateStr): array
    {
        $dateEsc = addslashes($dateStr);
        $sql = "SELECT trim(ifNull(toString(lokasi), '')) AS loc, trim(ifNull(toString(detil_lokasi), '')) AS det, trim(ifNull(toString(nama_coach), '')) AS nama_coach, count() AS cnt
                FROM nitip.bep_vw_database_coaching
                WHERE Tanggal_Pembuatan IS NOT NULL AND toDate(Tanggal_Pembuatan, 'Asia/Makassar') = toDate('{$dateEsc}')
                GROUP BY lokasi, detil_lokasi, nama_coach";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return [];
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }
            $out = [];
            foreach ($results as $row) {
                $loc = $this->normalizeLocation($this->getClickHouseRowValue($row, 'loc'));
                $det = $this->normalizeLocation($this->getClickHouseRowValue($row, 'det'));
                $nama = trim((string) $this->getClickHouseRowValue($row, 'nama_coach'));
                $cnt = (int) ($this->getClickHouseRowValue($row, 'cnt') ?? 0);
                $key = $loc . '|' . $det;
                if (! isset($out[$key])) {
                    $out[$key] = [];
                }
                if ($nama !== '') {
                    $out[$key][mb_strtolower($nama)] = $cnt;
                }
            }
            return $out;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getCoachingCountsForDate: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * API: Detail per hari heatmap — per planning (karyawan + lokasi) dengan count Inspeksi, OAK, Observasi, Coaching.
     * GET ?date=Y-m-d&site=...
     */
    public function heatmapDayDetail(Request $request): JsonResponse
    {
        $dateStr = $request->input('date');
        if (! $dateStr || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return response()->json(['data' => [], 'message' => 'date (Y-m-d) required']);
        }
        $site = trim((string) $request->input('site', ''));
        $siteFilter = $site === '' ? 'all' : $site;

        $plannings = RosterPlanning::with('karyawans')
            ->whereDate('tanggal', $dateStr)
            ->whereHas('karyawans')
            ->orderBy('lokasi')
            ->get();

        if ($siteFilter !== 'all') {
            $plannings = $plannings->where('site', $siteFilter);
        }

        $carList = $this->getCarHazardInspeksiFromClickHouseForDate($dateStr);
        $oakCounts = $this->getOakCountsForDate($dateStr);
        $observasiCounts = $this->getObservasiCountsForDate($dateStr);
        $coachingCounts = $this->getCoachingCountsForDate($dateStr);

        $rows = [];
        foreach ($plannings as $planning) {
            $planLokasiNorm = $this->normalizeLocation($planning->lokasi ?? '');
            $planDetailNorm = $this->normalizeLocation($planning->detail_lokasi ?? '');
            $locationKey = $planLokasiNorm . '|' . $planDetailNorm;

            $karyawans = $planning->karyawans ?? collect();
            if ($karyawans->isEmpty()) {
                $rows[] = [
                    'karyawan_nama' => '—',
                    'lokasi' => $planning->lokasi ?? '—',
                    'detail_lokasi' => $planning->detail_lokasi ?? '—',
                    'count_inspeksi' => 0,
                    'count_oak' => 0,
                    'count_observasi' => 0,
                    'count_coaching' => 0,
                ];
                continue;
            }

            foreach ($karyawans as $k) {
                $nama = trim((string) ($k->nama_karyawan ?? ''));
                $namaLower = $nama !== '' ? mb_strtolower($nama) : '';

                $countInspeksi = 0;
                foreach ($carList as $item) {
                    if ($item['lokasi_norm'] !== $planLokasiNorm || $item['detail_lokasi_norm'] !== $planDetailNorm) {
                        continue;
                    }
                    if ($item['nama_lower'] !== '' && $item['nama_lower'] === $namaLower) {
                        $countInspeksi++;
                    }
                }
                $countOak = isset($oakCounts[$locationKey][$namaLower]) ? $oakCounts[$locationKey][$namaLower] : 0;
                $countObservasi = isset($observasiCounts[$locationKey][$namaLower]) ? $observasiCounts[$locationKey][$namaLower] : 0;
                $countCoaching = isset($coachingCounts[$locationKey][$namaLower]) ? $coachingCounts[$locationKey][$namaLower] : 0;

                $rows[] = [
                    'karyawan_nama' => $nama ?: '—',
                    'lokasi' => $planning->lokasi ?? '—',
                    'detail_lokasi' => $planning->detail_lokasi ?? '—',
                    'count_inspeksi' => $countInspeksi,
                    'count_oak' => $countOak,
                    'count_observasi' => $countObservasi,
                    'count_coaching' => $countCoaching,
                ];
            }
        }

        // Group by (karyawan_nama, lokasi, detail_lokasi), sum counts — satu baris per kombinasi unik
        $grouped = [];
        foreach ($rows as $r) {
            $key = ($r['karyawan_nama'] ?? '') . '|' . ($r['lokasi'] ?? '') . '|' . ($r['detail_lokasi'] ?? '');
            if (! isset($grouped[$key])) {
                $grouped[$key] = [
                    'karyawan_nama' => $r['karyawan_nama'] ?? '—',
                    'lokasi' => $r['lokasi'] ?? '—',
                    'detail_lokasi' => $r['detail_lokasi'] ?? '—',
                    'count_inspeksi' => 0,
                    'count_oak' => 0,
                    'count_observasi' => 0,
                    'count_coaching' => 0,
                ];
            }
            $grouped[$key]['count_inspeksi'] += (int) ($r['count_inspeksi'] ?? 0);
            $grouped[$key]['count_oak'] += (int) ($r['count_oak'] ?? 0);
            $grouped[$key]['count_observasi'] += (int) ($r['count_observasi'] ?? 0);
            $grouped[$key]['count_coaching'] += (int) ($r['count_coaching'] ?? 0);
        }
        $rows = array_values($grouped);

        return response()->json(['data' => $rows, 'date' => $dateStr, 'site' => $siteFilter]);
    }

    /**
     * Cek apakah ada hazard inspeksi (SAP) yang match dengan plan: lokasi + detail lokasi + tanggal.
     * Nama pelapor tidak di-match — yang penting ada SAP dari siapapun di lokasi & tanggal tersebut.
     *
     * @param  array<int, array{id: mixed, nama_lower: string, lokasi_norm: string, detail_lokasi_norm: string, jenis_laporan: string|null}>  $carHazardInspeksi
     * @return array{ok: bool, task_id: string, jenis_sap: string|null}
     */
    private function planningMatchHazardInspeksiByLokasi(RosterPlanning $planning, array $carHazardInspeksi): array
    {
        $planLokasiNorm = $this->normalizeLocation($planning->lokasi ?? '');
        $planDetailNorm = $this->normalizeLocation($planning->detail_lokasi ?? '');

        $taskIds = [];
        $jenisSap = null;
        foreach ($carHazardInspeksi as $item) {
            if ($item['lokasi_norm'] !== $planLokasiNorm || $item['detail_lokasi_norm'] !== $planDetailNorm) {
                continue;
            }
            $taskIds[] = $item['id'];
            if ($jenisSap === null && ($item['jenis_laporan'] ?? null) !== null) {
                $jenisSap = $item['jenis_laporan'];
            }
        }
        $taskIds = array_values(array_unique($taskIds));
        $taskIdDisplay = $taskIds !== [] ? implode(', ', $taskIds) : '';

        return [
            'ok' => $taskIds !== [],
            'task_id' => $taskIdDisplay,
            'jenis_sap' => $jenisSap,
        ];
    }

    /**
     * Cek apakah ada hazard inspeksi yang match dengan plan: nama pelapor = salah satu karyawan yang di-assign + lokasi + detail lokasi + tanggal.
     * Dipakai untuk Detail Plan Pengecekan (OK = karyawan yang di-assign sudah isi SAP).
     *
     * @param  array<int, array{id: mixed, nama_lower: string, lokasi_norm: string, detail_lokasi_norm: string, jenis_laporan: string|null}>  $carHazardInspeksi
     * @return array{ok: bool, task_id: string, jenis_sap: string|null}
     */
    private function planningMatchHazardInspeksiByLokasiAndNama(RosterPlanning $planning, array $carHazardInspeksi): array
    {
        $planLokasiNorm = $this->normalizeLocation($planning->lokasi ?? '');
        $planDetailNorm = $this->normalizeLocation($planning->detail_lokasi ?? '');
        $karyawanNamesLower = [];
        foreach ($planning->karyawans ?? [] as $k) {
            $nama = trim((string) ($k->nama_karyawan ?? ''));
            if ($nama !== '') {
                $karyawanNamesLower[mb_strtolower($nama)] = true;
            }
        }

        $taskIds = [];
        $jenisSap = null;
        foreach ($carHazardInspeksi as $item) {
            if ($item['nama_lower'] === '') {
                continue;
            }
            if (! isset($karyawanNamesLower[$item['nama_lower']])) {
                continue;
            }
            if ($item['lokasi_norm'] !== $planLokasiNorm || $item['detail_lokasi_norm'] !== $planDetailNorm) {
                continue;
            }
            $taskIds[] = $item['id'];
            if ($jenisSap === null && ($item['jenis_laporan'] ?? null) !== null) {
                $jenisSap = $item['jenis_laporan'];
            }
        }
        $taskIds = array_values(array_unique($taskIds));
        $taskIdDisplay = $taskIds !== [] ? implode(', ', $taskIds) : '';

        return [
            'ok' => $taskIds !== [],
            'task_id' => $taskIdDisplay,
            'jenis_sap' => $jenisSap,
        ];
    }

    /**
     * API: Detail SAP (data inspeksi hazard) by task_ids dari aaj_car_all_year_from_dav.
     * Query: GET ?task_ids=id1,id2 atau task_ids[]=id1&task_ids[]=id2
     */
    public function sapDetail(Request $request): JsonResponse
    {
        $taskIds = $request->input('task_ids');
        if (is_string($taskIds)) {
            $taskIds = array_filter(array_map('trim', explode(',', $taskIds)));
        }
        if (! is_array($taskIds) || empty($taskIds)) {
            return response()->json(['data' => [], 'message' => 'task_ids required']);
        }
        $ids = [];
        foreach ($taskIds as $id) {
            $n = is_numeric($id) ? (int) $id : (int) str_replace(',', '', (string) $id);
            if ($n > 0) {
                $ids[] = $n;
            }
        }
        $ids = array_values(array_unique($ids));
        if (empty($ids)) {
            return response()->json(['data' => []]);
        }
        $idList = implode(',', $ids);
        $conditions = ["id IN ({$idList})"];
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT
            id,
            trim(ifNull(jenis_laporan, '')) AS jenis_laporan,
            trim(ifNull(status, '')) AS status,
            trim(ifNull(nama_pelapor, '')) AS nama_pelapor,
            trim(ifNull(sid_pelapor, '')) AS sid_pelapor,
            trim(ifNull(nama_lokasi, '')) AS nama_lokasi,
            trim(ifNull(nama_detail_lokasi, '')) AS nama_detail_lokasi,
            trim(ifNull(deskripsi, '')) AS deskripsi,
            trim(ifNull(ketidaksesuaian, '')) AS ketidaksesuaian,
            trim(ifNull(subketidaksesuaian, '')) AS subketidaksesuaian,
            trim(ifNull(nama_kategori, '')) AS nama_kategori,
            trim(ifNull(nama_goldenrule, '')) AS nama_goldenrule,
            trim(ifNull(nilai_resiko, '')) AS nilai_resiko,
            trim(ifNull(tindakan, '')) AS tindakan,
            trim(ifNull(departemen_pelapor, '')) AS departemen_pelapor,
            trim(ifNull(perusahaan_pelapor, '')) AS perusahaan_pelapor,
            trim(ifNull(nama_pic, '')) AS nama_pic,
            trim(ifNull(departemen_pic, '')) AS departemen_pic,
            trim(ifNull(perusahaan_pic, '')) AS perusahaan_pic,
            trim(ifNull(catatan_verifikasi, '')) AS catatan_verifikasi,
            url_photo,
            tanggal_pembuatan,
            bedraft_date,
            tanggal_janji,
            tanggal_aktual_penyelesaian,
            waktu_verifikasi
            FROM nitip.aaj_car_all_year_from_dav
            WHERE {$whereClause}
            ORDER BY toDateTime(ifNull(tanggal_pembuatan, bedraft_date)) DESC";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return response()->json(['data' => [], 'message' => 'ClickHouse not available']);
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return response()->json(['data' => [], 'message' => 'ClickHouse not connected']);
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return response()->json(['data' => []]);
            }
            $list = [];
            foreach ($results as $row) {
                $list[] = [
                    'id' => $this->getClickHouseRowValue($row, 'id'),
                    'jenis_laporan' => $this->getClickHouseRowValue($row, 'jenis_laporan'),
                    'status' => $this->getClickHouseRowValue($row, 'status'),
                    'nama_pelapor' => $this->getClickHouseRowValue($row, 'nama_pelapor'),
                    'sid_pelapor' => $this->getClickHouseRowValue($row, 'sid_pelapor'),
                    'nama_lokasi' => $this->getClickHouseRowValue($row, 'nama_lokasi'),
                    'nama_detail_lokasi' => $this->getClickHouseRowValue($row, 'nama_detail_lokasi'),
                    'deskripsi' => $this->getClickHouseRowValue($row, 'deskripsi'),
                    'ketidaksesuaian' => $this->getClickHouseRowValue($row, 'ketidaksesuaian'),
                    'subketidaksesuaian' => $this->getClickHouseRowValue($row, 'subketidaksesuaian'),
                    'nama_kategori' => $this->getClickHouseRowValue($row, 'nama_kategori'),
                    'nama_goldenrule' => $this->getClickHouseRowValue($row, 'nama_goldenrule'),
                    'nilai_resiko' => $this->getClickHouseRowValue($row, 'nilai_resiko'),
                    'tindakan' => $this->getClickHouseRowValue($row, 'tindakan'),
                    'departemen_pelapor' => $this->getClickHouseRowValue($row, 'departemen_pelapor'),
                    'perusahaan_pelapor' => $this->getClickHouseRowValue($row, 'perusahaan_pelapor'),
                    'nama_pic' => $this->getClickHouseRowValue($row, 'nama_pic'),
                    'departemen_pic' => $this->getClickHouseRowValue($row, 'departemen_pic'),
                    'perusahaan_pic' => $this->getClickHouseRowValue($row, 'perusahaan_pic'),
                    'catatan_verifikasi' => $this->getClickHouseRowValue($row, 'catatan_verifikasi'),
                    'url_photo' => $this->getClickHouseRowValue($row, 'url_photo'),
                    'tanggal_pembuatan' => $this->formatClickHouseDate($this->getClickHouseRowValue($row, 'tanggal_pembuatan')),
                    'bedraft_date' => $this->formatClickHouseDate($this->getClickHouseRowValue($row, 'bedraft_date')),
                    'tanggal_janji' => $this->formatClickHouseDate($this->getClickHouseRowValue($row, 'tanggal_janji')),
                    'tanggal_aktual_penyelesaian' => $this->formatClickHouseDate($this->getClickHouseRowValue($row, 'tanggal_aktual_penyelesaian')),
                    'waktu_verifikasi' => $this->formatClickHouseDate($this->getClickHouseRowValue($row, 'waktu_verifikasi')),
                ];
            }
            return response()->json(['data' => $list]);
        } catch (\Throwable $e) {
            Log::warning('DashboardController sapDetail: ' . $e->getMessage());
            return response()->json(['data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Detail OAK by lokasi, detail_lokasi, date. Match location + detail_location + toDate(submit_date).
     * GET ?lokasi=...&detail_lokasi=...&date=Y-m-d
     */
    public function oakDetail(Request $request): JsonResponse
    {
        $lokasi = $this->normalizeLocation($request->input('lokasi'));
        $detailLokasi = $this->normalizeLocation($request->input('detail_lokasi'));
        $dateStr = $request->input('date');
        if (! $dateStr || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return response()->json(['data' => [], 'message' => 'lokasi, detail_lokasi, date (Y-m-d) required']);
        }

        $locEsc = addslashes($lokasi);
        $detEsc = addslashes($detailLokasi);
        $dateEsc = addslashes($dateStr);
        $sql = "SELECT id, site, tipe, shift, method, activity, code_sib, kode_sid, latitude, location, material, platform,
                file_foto, longitude, nama_team, submit_by, submit_id, tool_type, url_photo, versi_apk, conclusion,
                is_be_draft, mobile_uuid, submit_date, bedraft_date, sib_register, sub_activity, kode_sid_team,
                conveyance_type, detail_location, tools_observasi, id_employee_team, kode_sid_pelapor, company_submit_by,
                lifting_equipment, location_description, jabatan_fungsional_team, jabatan_fungsional_submiter
                FROM nitip.aaj_vw_car_oak_register_ytd_only
                WHERE toDate(submit_date, 'Asia/Makassar') = toDate('{$dateEsc}')
                  AND trim(ifNull(toString(location), '')) = '{$locEsc}'
                  AND trim(ifNull(toString(detail_location), '')) = '{$detEsc}'
                ORDER BY toDateTime(ifNull(submit_date, bedraft_date)) DESC";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return response()->json(['data' => [], 'message' => 'ClickHouse not available']);
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return response()->json(['data' => [], 'message' => 'ClickHouse not connected']);
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return response()->json(['data' => []]);
            }
            $list = [];
            foreach ($results as $row) {
                $list[] = [
                    'id' => $this->getClickHouseRowValue($row, 'id'),
                    'site' => $this->getClickHouseRowValue($row, 'site'),
                    'tipe' => $this->getClickHouseRowValue($row, 'tipe'),
                    'shift' => $this->getClickHouseRowValue($row, 'shift'),
                    'activity' => $this->getClickHouseRowValue($row, 'activity'),
                    'location' => $this->getClickHouseRowValue($row, 'location'),
                    'detail_location' => $this->getClickHouseRowValue($row, 'detail_location'),
                    'nama_team' => $this->getClickHouseRowValue($row, 'nama_team'),
                    'submit_by' => $this->getClickHouseRowValue($row, 'submit_by'),
                    'submit_date' => $this->formatClickHouseDate($this->getClickHouseRowValue($row, 'submit_date')),
                    'conclusion' => $this->getClickHouseRowValue($row, 'conclusion'),
                    'url_photo' => $this->getClickHouseRowValue($row, 'url_photo'),
                    'tools_observasi' => $this->getClickHouseRowValue($row, 'tools_observasi'),
                    'company_submit_by' => $this->getClickHouseRowValue($row, 'company_submit_by'),
                ];
            }
            return response()->json(['data' => $list]);
        } catch (\Throwable $e) {
            Log::warning('DashboardController oakDetail: ' . $e->getMessage());
            return response()->json(['data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * API: Detail Observasi by lokasi, detail_lokasi, date. Sumber: nitip.aaj_database_observasi_from_bep_ytd_only (kolom Date).
     * GET ?lokasi=...&detail_lokasi=...&date=Y-m-d
     */
    public function observasiDetail(Request $request): JsonResponse
    {
        $lokasi = $this->normalizeLocation($request->input('lokasi'));
        $detailLokasi = $this->normalizeLocation($request->input('detail_lokasi'));
        $dateStr = $request->input('date');
        if (! $dateStr || ! preg_match('/^\d{4}-\d{2}-\d{2}$/', $dateStr)) {
            return response()->json(['data' => [], 'message' => 'lokasi, detail_lokasi, date (Y-m-d) required']);
        }

        $locEsc = addslashes($lokasi);
        $detEsc = addslashes($detailLokasi);
        $dateEsc = addslashes($dateStr);
        $sql = "SELECT _Task AS task_id, Date AS report_datetime, id_pelapor, nama_pelapor, kode_sid_pelapor, site, Lokasi AS lokasi, Detil_Lokasi AS detil_lokasi,
                jenis_kegiatan, tools_observasi, tindakan_perbaikan, umpan_balik, catatan, tipe, url_photo,
                nama_perusahaan, jabatan_fungsional
                FROM nitip.aaj_database_observasi_from_bep_ytd_only
                WHERE Date IS NOT NULL
                  AND toDate(Date, 'Asia/Makassar') = toDate('{$dateEsc}')
                  AND trim(ifNull(toString(Lokasi), '')) = '{$locEsc}'
                  AND trim(ifNull(toString(Detil_Lokasi), '')) = '{$detEsc}'
                ORDER BY toDateTime(Date) DESC";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return response()->json(['data' => [], 'message' => 'ClickHouse not available']);
            }
            $ch = $this->getClickHouseNitip();
            if (! method_exists($ch, 'query') || ! $ch->isConnected()) {
                return response()->json(['data' => [], 'message' => 'ClickHouse not connected']);
            }
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return response()->json(['data' => []]);
            }
            $list = [];
            foreach ($results as $row) {
                $list[] = [
                    'task_id' => $this->getClickHouseRowValue($row, 'task_id'),
                    'report_datetime' => $this->formatClickHouseDate($this->getClickHouseRowValue($row, 'report_datetime')),
                    'nama_pelapor' => $this->getClickHouseRowValue($row, 'nama_pelapor'),
                    'kode_sid_pelapor' => $this->getClickHouseRowValue($row, 'kode_sid_pelapor'),
                    'site' => $this->getClickHouseRowValue($row, 'site'),
                    'lokasi' => $this->getClickHouseRowValue($row, 'lokasi'),
                    'detil_lokasi' => $this->getClickHouseRowValue($row, 'detil_lokasi'),
                    'jenis_kegiatan' => $this->getClickHouseRowValue($row, 'jenis_kegiatan'),
                    'tools_observasi' => $this->getClickHouseRowValue($row, 'tools_observasi'),
                    'tindakan_perbaikan' => $this->getClickHouseRowValue($row, 'tindakan_perbaikan'),
                    'umpan_balik' => $this->getClickHouseRowValue($row, 'umpan_balik'),
                    'catatan' => $this->getClickHouseRowValue($row, 'catatan'),
                    'tipe' => $this->getClickHouseRowValue($row, 'tipe'),
                    'url_photo' => $this->getClickHouseRowValue($row, 'url_photo'),
                    'nama_perusahaan' => $this->getClickHouseRowValue($row, 'nama_perusahaan'),
                    'jabatan_fungsional' => $this->getClickHouseRowValue($row, 'jabatan_fungsional'),
                ];
            }
            return response()->json(['data' => $list]);
        } catch (\Throwable $e) {
            Log::warning('DashboardController observasiDetail: ' . $e->getMessage());
            return response()->json(['data' => [], 'message' => $e->getMessage()], 500);
        }
    }

    private function formatClickHouseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }
        if ($value instanceof \DateTimeInterface) {
            return $value->format('d/m/Y H:i');
        }
        if (is_string($value)) {
            return substr($value, 0, 19);
        }
        return (string) $value;
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
}
