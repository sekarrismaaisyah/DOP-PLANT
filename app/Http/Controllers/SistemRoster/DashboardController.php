<?php

namespace App\Http\Controllers\SistemRoster;

use App\Http\Controllers\Controller;
use App\Models\RosterPlanning;
use App\Services\ClickHouseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman Performance Dashboard Sistem Roster.
     * Status OK/NOT OK diambil dari match nama_karyawan dengan nama_pelapor di ClickHouse (aaj_car_all_year_from_dav).
     */
    public function index(): View
    {
        $assignedPlannings = RosterPlanning::with('karyawans')
            ->where('status', 'assigned')
            ->whereDate('tanggal', today())
            ->orderByDesc('tanggal')
            ->orderByDesc('updated_at')
            ->get();

        $carTasksToday = $this->getCarTasksFromClickHouseToday();
        foreach ($assignedPlannings as $planning) {
            $matchResult = $this->planningMatchCarPelaporWithIds($planning, $carTasksToday);
            $planning->setAttribute('car_status', $matchResult['ok'] ? 'ok' : 'notok');
            $planning->setAttribute('car_task_id', $matchResult['task_id']);
        }

        $buildCoverage = function ($plannings) {
            return $plannings
                ->groupBy(fn ($p) => trim($p->lokasi ?? '') . '|' . trim($p->detail_lokasi ?? ''))
                ->map(function ($items) {
                    $first = $items->first();
                    $okCount = $items->where('car_status', 'ok')->count();
                    $total = $items->count();
                    $pct = $total > 0 ? (int) round($okCount / $total * 100) : 0;
                    return (object) [
                        'lokasi' => $first->lokasi ?? '',
                        'detail_lokasi' => $first->detail_lokasi ?? '',
                        'site' => trim($first->site ?? ''),
                        'total' => $total,
                        'ok_count' => $okCount,
                        'pct' => $pct,
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
                    'jenis_sap' => $planning->jenis_sap ?? null,
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
                        'jenis_sap' => $planning->jenis_sap ?? null,
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
        ]);
    }

    /**
     * Data heatmap: per (date, site) -> planned count & actual count (match CAR nama + tanggal_pembuatan).
     *
     * @return array<int, array{date: string, site: string, planned: int, actual: int}>
     */
    private function buildHeatmapData(): array
    {
        $start = now()->subMonths(2)->startOfMonth()->format('Y-m-d');
        $end = now()->addMonths(2)->endOfMonth()->format('Y-m-d');

        $plannings = RosterPlanning::with('karyawans')
            ->where('status', 'assigned')
            ->whereBetween('tanggal', [$start, $end])
            ->orderBy('tanggal')
            ->get();

        $carByDate = $this->getCarNamaPelaporByDateRange($start, $end);

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
            $pelaporThatDay = $carByDate[$date] ?? [];
            $isActual = false;
            foreach ($p->karyawans ?? [] as $k) {
                $nama = trim((string) ($k->nama_karyawan ?? ''));
                if ($nama !== '' && isset($pelaporThatDay[mb_strtolower($nama)])) {
                    $isActual = true;
                    break;
                }
            }
            if ($isActual) {
                $byKey[$key]['actual']++;
            }
        }

        return array_values($byKey);
    }

    /**
     * ClickHouse: untuk setiap tanggal di range, daftar nama_pelapor (lowercase) yang ada.
     *
     * @return array<string, array<string, true>> key = date Y-m-d, value = map nama_lower => true
     */
    private function getCarNamaPelaporByDateRange(string $start, string $end): array
    {
        $conditions = [
            "trim(ifNull(status, '')) = 'SUBMITTED'",
            "tanggal_pembuatan IS NOT NULL",
            "toDate(tanggal_pembuatan, 'Asia/Makassar') >= toDate('" . addslashes($start) . "')",
            "toDate(tanggal_pembuatan, 'Asia/Makassar') <= toDate('" . addslashes($end) . "')",
        ];
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT toDate(tanggal_pembuatan, 'Asia/Makassar') AS dt, trim(ifNull(nama_pelapor, '')) AS nama_pelapor "
            . "FROM hse_automation.aaj_car_all_year_from_dav WHERE {$whereClause}";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = app(ClickHouseService::class);
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
     * Ambil id + nama_pelapor dari hse_automation.aaj_car_all_year_from_dav untuk tanggal hari ini (tanggal_pembuatan).
     *
     * @return array<int, array{id: mixed, nama_lower: string}> list of {id, nama_lower} for matching
     */
    private function getCarTasksFromClickHouseToday(): array
    {
        $todayStr = now()->format('Y-m-d');
        $conditions = [
            "trim(ifNull(status, '')) = 'SUBMITTED'",
            "tanggal_pembuatan IS NOT NULL AND toDate(tanggal_pembuatan, 'Asia/Makassar') = toDate('" . addslashes($todayStr) . "')",
        ];
        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT id, trim(ifNull(nama_pelapor, '')) AS nama_pelapor FROM hse_automation.aaj_car_all_year_from_dav WHERE {$whereClause}";

        try {
            if (! class_exists(ClickHouseService::class)) {
                return [];
            }
            $ch = app(ClickHouseService::class);
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
                if ($nama !== '') {
                    $list[] = ['id' => $id, 'nama_lower' => mb_strtolower($nama)];
                }
            }
            return $list;
        } catch (\Throwable $e) {
            Log::warning('DashboardController getCarTasksFromClickHouseToday: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Cek match nama + dapatkan task id dari hasil match (nama dan tanggal hari ini).
     *
     * @param  array<int, array{id: mixed, nama_lower: string}>  $carTasksToday
     * @return array{ok: bool, task_id: string}
     */
    private function planningMatchCarPelaporWithIds(RosterPlanning $planning, array $carTasksToday): array
    {
        $taskIds = [];
        $namaToIds = [];
        foreach ($carTasksToday as $item) {
            $namaToIds[$item['nama_lower']][] = $item['id'];
        }
        foreach ($planning->karyawans ?? [] as $k) {
            $nama = trim((string) ($k->nama_karyawan ?? ''));
            if ($nama !== '') {
                $key = mb_strtolower($nama);
                if (isset($namaToIds[$key])) {
                    $taskIds = array_merge($taskIds, $namaToIds[$key]);
                }
            }
        }
        $taskIds = array_values(array_unique($taskIds));
        $taskIdDisplay = $taskIds !== [] ? implode(', ', $taskIds) : '';

        return [
            'ok' => $taskIds !== [],
            'task_id' => $taskIdDisplay,
        ];
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
