<?php

declare(strict_types=1);

namespace App\Services\PembatasanLV;

use App\Models\PembatasanLVListAktivitasGmoDiLuarKabin;
use App\Models\PembatasanLVLogbookGmo;
use App\Services\ClickHouseService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class PembatasanLVEvaluasiService
{
    /**
     * @param  array{site?: string, tanggal?: string, shift?: string, perusahaan?: string}  $filters
     * @return array{
     *     filters: array{site: string, tanggal: string, tanggal_label: string, shift: string, perusahaan: string},
     *     summary: array{total_plan: int, total_aktual: int, total_sap: int, tingkat_pencatatan: float, aktivitas_tercatat: int, aktivitas_belum_tercatat: int, aktivitas_ada_deviasi: int},
     *     rows: list<array<string, mixed>>,
     *     logbook_rows: list<array<string, mixed>>,
     *     sap_rows: list<array<string, mixed>>,
     *     filter_options: array{sites: list<string>, shifts: list<string>, perusahaan: list<string>},
     *     sap_available: bool
     * }
     */
    public function buildDashboard(array $filters): array
    {
        $tanggalResolved = $this->resolveTanggal((string) ($filters['tanggal'] ?? ''));

        $resolvedFilters = [
            'site' => trim((string) ($filters['site'] ?? 'GMO')) ?: 'GMO',
            'tanggal' => $tanggalResolved['tanggal'],
            'tanggal_label' => $tanggalResolved['tanggal_label'],
            'shift' => trim((string) ($filters['shift'] ?? '')),
            'perusahaan' => trim((string) ($filters['perusahaan'] ?? '')),
        ];

        $planTableExists = Schema::hasTable('list_aktivitas_gmo_di_luar_kabin');
        $logbookTableExists = Schema::hasTable('logbook_gmo');

        $planRows = $planTableExists ? $this->fetchPlanRows($resolvedFilters) : collect();
        $logbookRows = $logbookTableExists ? $this->fetchLogbookRows($resolvedFilters) : collect();
        $sapRows = $this->fetchSapRows($resolvedFilters);
        $sapByPelapor = $this->groupSapByPelapor($sapRows);

        $evaluationRows = [];
        $aktivitasTercatat = 0;
        $aktivitasBelumTercatat = 0;
        $aktivitasAdaDeviasi = 0;

        foreach ($planRows as $plan) {
            $matchedLogbooks = $this->findMatchingLogbooks(
                $logbookRows,
                (string) $plan->perusahaan,
                (string) $plan->kategori_aktivitas_luar_kabin,
                (string) $plan->detail_aktivitas_luar_kabin,
            );

            $aktualCount = $matchedLogbooks->count();
            $aktualKaryawan = $matchedLogbooks
                ->pluck('nama_karyawan')
                ->map(fn ($nama) => trim((string) $nama))
                ->filter()
                ->unique()
                ->values()
                ->all();
            $aktualAlasan = $matchedLogbooks
                ->pluck('alasan')
                ->map(fn ($alasan) => trim((string) $alasan))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $expectsOnShift = $this->expectsActivityOnShift((string) $plan->frekuensi_aktivitas, $resolvedFilters['shift']);
            $linkedSap = $this->findLinkedSap($aktualKaryawan, $sapByPelapor);
            $status = $this->resolveStatus($expectsOnShift, $aktualCount, $linkedSap);

            if ($expectsOnShift) {
                if ($aktualCount > 0) {
                    $aktivitasTercatat++;
                } else {
                    $aktivitasBelumTercatat++;
                }
            }

            if ($linkedSap !== []) {
                $aktivitasAdaDeviasi++;
            }

            $evaluationRows[] = [
                'id' => $plan->id,
                'site' => $plan->site,
                'perusahaan' => $plan->perusahaan,
                'kategori' => $plan->kategori_aktivitas_luar_kabin,
                'detail' => $plan->detail_aktivitas_luar_kabin,
                'frekuensi' => $plan->frekuensi_aktivitas,
                'expects_on_shift' => $expectsOnShift,
                'aktual_count' => $aktualCount,
                'aktual_karyawan' => $aktualKaryawan,
                'aktual_alasan' => $aktualAlasan,
                'logbook_items' => $matchedLogbooks
                    ->map(fn (PembatasanLVLogbookGmo $row) => $this->mapLogbookRow($row))
                    ->values()
                    ->all(),
                'sap_count' => count($linkedSap),
                'sap_items' => $linkedSap,
                'status' => $status,
                'status_label' => $this->statusLabel($status),
            ];
        }

        $evaluationRows = $this->sortEvaluationRows($evaluationRows);

        $totalPlan = $planRows->count();
        $totalAktual = $logbookRows->count();
        $totalSap = count($sapRows);
        $relevantPlan = collect($evaluationRows)->where('expects_on_shift', true)->count();
        $tingkatPencatatan = $relevantPlan > 0
            ? round(($aktivitasTercatat / $relevantPlan) * 100, 1)
            : 0.0;

        return [
            'filters' => $resolvedFilters,
            'summary' => [
                'total_plan' => $totalPlan,
                'total_aktual' => $totalAktual,
                'total_sap' => $totalSap,
                'tingkat_pencatatan' => $tingkatPencatatan,
                'aktivitas_tercatat' => $aktivitasTercatat,
                'aktivitas_belum_tercatat' => $aktivitasBelumTercatat,
                'aktivitas_ada_deviasi' => $aktivitasAdaDeviasi,
            ],
            'rows' => $evaluationRows,
            'logbook_rows' => $logbookRows->map(fn (PembatasanLVLogbookGmo $row) => $this->mapLogbookRow($row))->values()->all(),
            'sap_rows' => $sapRows,
            'filter_options' => $this->filterOptions($planTableExists, $logbookTableExists),
            'sap_available' => $this->clickHouseNitip()?->isConnected() ?? false,
        ];
    }

    /**
     * Urutkan baris evaluasi: yang punya aktual di atas, lalu jumlah aktual tertinggi.
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    private function sortEvaluationRows(array $rows): array
    {
        usort($rows, function (array $a, array $b): int {
            $aCount = (int) ($a['aktual_count'] ?? 0);
            $bCount = (int) ($b['aktual_count'] ?? 0);
            $aHasAktual = $aCount > 0;
            $bHasAktual = $bCount > 0;

            if ($aHasAktual !== $bHasAktual) {
                return $aHasAktual ? -1 : 1;
            }

            if ($aCount !== $bCount) {
                return $bCount <=> $aCount;
            }

            $perusahaanCmp = strcasecmp((string) ($a['perusahaan'] ?? ''), (string) ($b['perusahaan'] ?? ''));
            if ($perusahaanCmp !== 0) {
                return $perusahaanCmp;
            }

            return strcasecmp((string) ($a['kategori'] ?? ''), (string) ($b['kategori'] ?? ''));
        });

        return $rows;
    }

    /**
     * @param  array{site: string, tanggal: string, tanggal_label: string, shift: string, perusahaan: string}  $filters
     * @return Collection<int, PembatasanLVListAktivitasGmoDiLuarKabin>
     */
    private function fetchPlanRows(array $filters): Collection
    {
        $query = PembatasanLVListAktivitasGmoDiLuarKabin::query()
            ->select([
                'id',
                'site',
                'perusahaan',
                'kategori_aktivitas_luar_kabin',
                'detail_aktivitas_luar_kabin',
                'frekuensi_aktivitas',
            ])
            ->orderBy('perusahaan')
            ->orderBy('kategori_aktivitas_luar_kabin');

        if ($filters['site'] !== '') {
            $query->where('site', $filters['site']);
        }

        if ($filters['perusahaan'] !== '') {
            $query->where('perusahaan', 'like', '%'.$filters['perusahaan'].'%');
        }

        return $query->get();
    }

    /**
     * @param  array{site: string, tanggal: string, tanggal_label: string, shift: string, perusahaan: string}  $filters
     * @return Collection<int, PembatasanLVLogbookGmo>
     */
    private function fetchLogbookRows(array $filters): Collection
    {
        $query = PembatasanLVLogbookGmo::query()
            ->select([
                'id',
                'tanggal',
                'jam',
                'shift',
                'perusahan',
                'nama_karyawan',
                'sid_karyawan',
                'alasan',
                'verifikasi_izin',
                'keterangan',
            ])
            ->whereRaw('DATE(`tanggal`) = ?', [$filters['tanggal']])
            ->orderBy('tanggal')
            ->orderBy('jam')
            ->orderBy('nama_karyawan');

        if ($filters['shift'] !== '') {
            $query->where('shift', $filters['shift']);
        }

        if ($filters['perusahaan'] !== '') {
            $query->where('perusahan', 'like', '%'.$filters['perusahaan'].'%');
        }

        return $query->get();
    }

    /**
     * @param  Collection<int, PembatasanLVLogbookGmo>  $logbookRows
     * @return Collection<int, PembatasanLVLogbookGmo>
     */
    private function findMatchingLogbooks(
        Collection $logbookRows,
        string $perusahaan,
        string $kategori,
        string $detail,
    ): Collection {
        return $logbookRows->filter(function (PembatasanLVLogbookGmo $row) use ($perusahaan, $kategori, $detail): bool {
            if (! $this->perusahaanMatches((string) $row->perusahan, $perusahaan)) {
                return false;
            }

            return $this->activityMatches($kategori, $detail, (string) ($row->alasan ?? ''));
        })->values();
    }

    private function activityMatches(string $planKategori, string $planDetail, string $alasan): bool
    {
        $alasanNorm = $this->normalizeActivityText($alasan);
        if ($alasanNorm === '') {
            return false;
        }

        $kategoriNorm = $this->normalizeActivityText($planKategori);
        $detailNorm = $this->normalizeActivityText($planDetail);

        if ($kategoriNorm !== '' && ($alasanNorm === $kategoriNorm || str_contains($alasanNorm, $kategoriNorm) || str_contains($kategoriNorm, $alasanNorm))) {
            return true;
        }

        if ($detailNorm !== '' && ($alasanNorm === $detailNorm || str_contains($alasanNorm, $detailNorm) || str_contains($detailNorm, $alasanNorm))) {
            return true;
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function mapLogbookRow(PembatasanLVLogbookGmo $row): array
    {
        $tanggalRaw = $row->getRawOriginal('tanggal');

        return [
            'id' => $row->id,
            'tanggal' => $tanggalRaw
                ? Carbon::parse((string) $tanggalRaw, config('app.timezone'))->format('d M Y')
                : '—',
            'jam' => $this->formatJam($row->jam),
            'shift' => $row->shift,
            'perusahan' => $row->perusahan,
            'nama_karyawan' => $row->nama_karyawan,
            'sid_karyawan' => $row->sid_karyawan,
            'alasan' => $row->alasan,
            'verifikasi_izin' => $row->verifikasi_izin === null ? null : (bool) $row->verifikasi_izin,
            'keterangan' => $row->keterangan,
        ];
    }

    private function formatJam(mixed $jam): ?string
    {
        if ($jam === null || $jam === '') {
            return null;
        }

        $jamStr = trim((string) $jam);
        if ($jamStr === '') {
            return null;
        }

        try {
            return Carbon::parse($jamStr)->format('H:i');
        } catch (\Throwable) {
            return $jamStr;
        }
    }

    /**
     * @param  array{site: string, tanggal: string, tanggal_label: string, shift: string, perusahaan: string}  $filters
     * @return list<array<string, mixed>>
     */
    private function fetchSapRows(array $filters): array
    {
        $ch = $this->clickHouseNitip();
        if ($ch === null || ! $ch->isConnected()) {
            return [];
        }

        $dateEsc = addslashes($filters['tanggal']);
        $siteEsc = addslashes($filters['site']);
        $conditions = [
            "tanggal_pembuatan IS NOT NULL AND toDate(tanggal_pembuatan, 'Asia/Makassar') = toDate('{$dateEsc}')",
            "trim(ifNull(jenis_laporan, '')) IN ('HAZARD', 'INSPEKSI', 'INSPEKSI_HAZARD')",
        ];

        if ($filters['site'] !== '') {
            $conditions[] = "trim(ifNull(toString(nama_site), '')) = '{$siteEsc}'";
        }

        $whereClause = implode(' AND ', $conditions);
        $sql = "SELECT
            id,
            toDate(tanggal_pembuatan, 'Asia/Makassar') AS tanggal,
            trim(ifNull(jenis_laporan, '')) AS jenis_laporan,
            trim(ifNull(status, '')) AS status,
            trim(ifNull(nama_pelapor, '')) AS nama_pelapor,
            trim(ifNull(sid_pelapor, '')) AS sid_pelapor,
            trim(ifNull(nama_lokasi, '')) AS nama_lokasi,
            trim(ifNull(nama_detail_lokasi, '')) AS nama_detail_lokasi,
            trim(ifNull(deskripsi, '')) AS deskripsi,
            trim(ifNull(ketidaksesuaian, '')) AS ketidaksesuaian,
            trim(ifNull(subketidaksesuaian, '')) AS subketidaksesuaian,
            trim(ifNull(perusahaan_pelapor, '')) AS perusahaan_pelapor,
            trim(ifNull(nilai_resiko, '')) AS nilai_resiko
            FROM nitip.aaj_car_all_year_from_dav
            WHERE {$whereClause}
            ORDER BY toDateTime(ifNull(tanggal_pembuatan, bedraft_date)) DESC
            LIMIT 500";

        try {
            $results = $ch->query($sql);
            if (empty($results) || ! is_array($results)) {
                return [];
            }

            $rows = [];
            foreach ($results as $row) {
                $perusahaanPelapor = trim((string) $this->rowValue($row, 'perusahaan_pelapor'));
                if ($filters['perusahaan'] !== '' && ! $this->perusahaanMatches($perusahaanPelapor, $filters['perusahaan'])) {
                    continue;
                }

                $rows[] = [
                    'id' => $this->rowValue($row, 'id'),
                    'tanggal' => $this->rowValue($row, 'tanggal'),
                    'jenis_laporan' => $this->rowValue($row, 'jenis_laporan'),
                    'status' => $this->rowValue($row, 'status'),
                    'nama_pelapor' => $this->rowValue($row, 'nama_pelapor'),
                    'sid_pelapor' => $this->rowValue($row, 'sid_pelapor'),
                    'nama_lokasi' => $this->rowValue($row, 'nama_lokasi'),
                    'nama_detail_lokasi' => $this->rowValue($row, 'nama_detail_lokasi'),
                    'deskripsi' => $this->rowValue($row, 'deskripsi'),
                    'ketidaksesuaian' => $this->rowValue($row, 'ketidaksesuaian'),
                    'subketidaksesuaian' => $this->rowValue($row, 'subketidaksesuaian'),
                    'perusahaan_pelapor' => $perusahaanPelapor,
                    'nilai_resiko' => $this->rowValue($row, 'nilai_resiko'),
                    'nama_pelapor_lower' => mb_strtolower(trim((string) $this->rowValue($row, 'nama_pelapor'))),
                ];
            }

            return $rows;
        } catch (\Throwable $e) {
            Log::warning('PembatasanLVEvaluasiService fetchSapRows: '.$e->getMessage());

            return [];
        }
    }

    /**
     * @param  list<array<string, mixed>>  $sapRows
     * @return array<string, list<array<string, mixed>>>
     */
    private function groupSapByPelapor(array $sapRows): array
    {
        $grouped = [];

        foreach ($sapRows as $sap) {
            $namaLower = (string) ($sap['nama_pelapor_lower'] ?? '');
            if ($namaLower === '') {
                continue;
            }

            if (! isset($grouped[$namaLower])) {
                $grouped[$namaLower] = [];
            }

            $grouped[$namaLower][] = $sap;
        }

        return $grouped;
    }

    /**
     * @param  list<string>  $karyawanList
     * @param  array<string, list<array<string, mixed>>>  $sapByPelapor
     * @return list<array<string, mixed>>
     */
    private function findLinkedSap(array $karyawanList, array $sapByPelapor): array
    {
        $linked = [];

        foreach ($karyawanList as $nama) {
            $key = mb_strtolower(trim($nama));
            if ($key === '' || ! isset($sapByPelapor[$key])) {
                continue;
            }

            foreach ($sapByPelapor[$key] as $sap) {
                $linked[$sap['id'] ?? spl_object_hash((object) $sap)] = $sap;
            }
        }

        return array_values($linked);
    }

    private function expectsActivityOnShift(string $frekuensi, string $selectedShift): bool
    {
        $frekuensiNorm = mb_strtolower(trim($frekuensi));

        if ($frekuensiNorm === '') {
            return true;
        }

        if (str_contains($frekuensiNorm, 'insidentil') || str_contains($frekuensiNorm, 'sesuai kebutuhan')) {
            return false;
        }

        if ($selectedShift === '') {
            return true;
        }

        $shiftNorm = mb_strtolower($selectedShift);

        if (str_contains($frekuensiNorm, 'shift 1') && $shiftNorm !== 'shift 1') {
            return false;
        }

        if (str_contains($frekuensiNorm, 'shift 2') && $shiftNorm !== 'shift 2') {
            return false;
        }

        return true;
    }

    /**
     * @param  list<array<string, mixed>>  $linkedSap
     */
    private function resolveStatus(bool $expectsOnShift, int $aktualCount, array $linkedSap): string
    {
        if ($linkedSap !== []) {
            return 'deviasi_sap';
        }

        if (! $expectsOnShift) {
            return 'tidak_dijadwalkan';
        }

        return $aktualCount > 0 ? 'tercatat' : 'belum_tercatat';
    }

    private function statusLabel(string $status): string
    {
        return match ($status) {
            'tercatat' => 'Tercatat',
            'belum_tercatat' => 'Belum Tercatat',
            'deviasi_sap' => 'Ada Deviasi SAP',
            'tidak_dijadwalkan' => 'Tidak Dijadwalkan Shift Ini',
            default => '—',
        };
    }

    private function normalizePerusahaan(string $perusahaan): string
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', trim($perusahaan)) ?? '');
    }

    private function normalizeActivityText(string $text): string
    {
        return mb_strtolower(preg_replace('/\s+/', ' ', trim($text)) ?? '');
    }

    private function perusahaanMatches(string $haystack, string $needle): bool
    {
        $haystackNorm = $this->normalizePerusahaan($haystack);
        $needleNorm = $this->normalizePerusahaan($needle);

        if ($haystackNorm === '' || $needleNorm === '') {
            return false;
        }

        return $haystackNorm === $needleNorm
            || str_contains($haystackNorm, $needleNorm)
            || str_contains($needleNorm, $haystackNorm);
    }

    /**
     * @return array{sites: list<string>, shifts: list<string>, perusahaan: list<string>}
     */
    private function filterOptions(bool $planTableExists, bool $logbookTableExists): array
    {
        $sites = $planTableExists
            ? PembatasanLVListAktivitasGmoDiLuarKabin::query()->distinct()->orderBy('site')->pluck('site')->filter()->values()->all()
            : ['GMO'];

        $shifts = $logbookTableExists
            ? PembatasanLVLogbookGmo::query()->distinct()->orderBy('shift')->pluck('shift')->filter()->values()->all()
            : ['Shift 1', 'Shift 2'];

        $perusahaanPlan = $planTableExists
            ? PembatasanLVListAktivitasGmoDiLuarKabin::query()->distinct()->orderBy('perusahaan')->pluck('perusahaan')->filter()->values()->all()
            : [];

        $perusahaanLogbook = $logbookTableExists
            ? PembatasanLVLogbookGmo::query()->distinct()->orderBy('perusahan')->pluck('perusahan')->filter()->values()->all()
            : [];

        return [
            'sites' => $sites,
            'shifts' => $shifts,
            'perusahaan' => collect(array_merge($perusahaanPlan, $perusahaanLogbook))
                ->unique()
                ->sort()
                ->values()
                ->all(),
        ];
    }

    /**
     * @return array{tanggal: string, tanggal_label: string}
     */
    private function resolveTanggal(string $tanggalInput): array
    {
        $timezone = (string) config('app.timezone', 'Asia/Makassar');

        try {
            $tanggal = Carbon::parse(trim($tanggalInput) !== '' ? $tanggalInput : 'now', $timezone);
        } catch (\Throwable) {
            $tanggal = Carbon::now($timezone);
        }

        return [
            'tanggal' => $tanggal->toDateString(),
            'tanggal_label' => $tanggal->format('d M Y'),
        ];
    }

    /**
     * Indeks pelapor SAP (SID & nama) untuk pencocokan cepat di overview orang.
     *
     * @param  array{site?: string, tanggal?: string}  $filters
     * @return array{available: bool, sids: array<string, true>, names: array<string, true>}
     */
    public function buildSapReporterIndex(array $filters): array
    {
        $tanggalResolved = $this->resolveTanggal((string) ($filters['tanggal'] ?? ''));
        $resolvedFilters = [
            'site' => trim((string) ($filters['site'] ?? '')),
            'tanggal' => $tanggalResolved['tanggal'],
            'tanggal_label' => $tanggalResolved['tanggal_label'],
            'shift' => '',
            'perusahaan' => '',
        ];

        $available = $this->clickHouseNitip()?->isConnected() ?? false;
        $sapRows = $available ? $this->fetchSapRows($resolvedFilters) : [];

        $sids = [];
        $names = [];

        foreach ($sapRows as $sap) {
            $sid = mb_strtolower(trim((string) ($sap['sid_pelapor'] ?? '')));
            $nama = mb_strtolower(trim((string) ($sap['nama_pelapor'] ?? '')));

            if ($sid !== '') {
                $sids[$sid] = true;
            }

            if ($nama !== '') {
                $names[$nama] = true;
            }
        }

        return [
            'available' => $available,
            'sids' => $sids,
            'names' => $names,
        ];
    }

    /**
     * @param  array{available: bool, sids: array<string, true>, names: array<string, true>}  $sapIndex
     */
    public function personHasSap(string $sid, string $nama, array $sapIndex): bool
    {
        if (! ($sapIndex['available'] ?? false)) {
            return false;
        }

        $sidKey = mb_strtolower(trim($sid));
        if ($sidKey !== '' && isset($sapIndex['sids'][$sidKey])) {
            return true;
        }

        $namaKey = mb_strtolower(trim($nama));

        return $namaKey !== '' && isset($sapIndex['names'][$namaKey]);
    }

    private function clickHouseNitip(): ?ClickHouseService
    {
        if (! class_exists(ClickHouseService::class)) {
            return null;
        }

        return app(ClickHouseService::class, ['connectionName' => 'clickhouse_nitip']);
    }

    private function rowValue(array $row, string $key): mixed
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
