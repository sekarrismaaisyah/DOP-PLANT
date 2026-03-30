<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Services\PeerPressure\PeerPressureKaryawanNitipService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Detail modal profiling pelanggar: riwayat 6 bulan, recency gap, korelasi lokasi/peer/shift.
 */
final class GetPeerPressurePelanggarProfilingDetailAction
{
    private const WINDOW_MONTHS = 6;

    private const HIGH_RISK_CASES = 3;

    private const HIGH_RISK_GAP_DAYS = 7;

    public function __construct(
        private readonly PeerPressureKaryawanNitipService $karyawanNitip,
    ) {}

    /**
     * @return array{
     *   sid: string,
     *   nama: string,
     *   foto_url: string|null,
     *   npk: string,
     *   departemen: string,
     *   posisi: string,
     *   grup: string,
     *   status_label: string,
     *   status_level: 'high'|'moderate'|'normal',
     *   last_education_label: string,
     *   riwayat: list<array{tanggal_short: string, kategori: string, lokasi: string, status: string}>,
     *   recency_gap_days: list<int>,
     *   recency_trend: 'worsening'|'improving'|'stable',
     *   recency_caption: string,
     *   korelasi: list<string>,
     *   rekomendasi: list<string>,
     *   kasus_count: int,
     *   window_caption: string,
     *   per_orang: array{
     *     rows: list<array{
     *       kategori: string,
     *       kriteria: string,
     *       status: string,
     *       terpenuhi: bool,
     *       detail_sections: list<array{heading: string, items: list<array{kejadian_id: int, tanggal: string, kategori: string, lokasi: string}>}>
     *     }>,
     *     issue_ringkas: string,
     *     peer_sebagai_peer_kejadian: int
     *   }
     * }
     */
    public function __invoke(string $sid): array
    {
        $sidTrim = trim($sid);
        if ($sidTrim === '' || $sidTrim === '-') {
            abort(404, 'SID tidak valid.');
        }

        $sidKey = Str::lower($sidTrim);

        $from = Carbon::now()->subMonths(self::WINDOW_MONTHS)->startOfDay();

        $rows = DB::table('peer_pressure_peserta_edukasi as p')
            ->join('peer_pressure_kejadian_edukasi as k', 'k.id', '=', 'p.kejadian_edukasi_id')
            ->where('p.peran', 'pelanggar')
            ->whereRaw('LOWER(TRIM(p.sid)) = ?', [$sidKey])
            ->where('k.tanggal_temuan', '>=', $from->toDateString())
            ->orderBy('k.tanggal_temuan')
            ->orderBy('k.id')
            ->select([
                'k.id as kejadian_id',
                'k.tanggal_temuan',
                'k.jam_temuan',
                'k.lokasi_temuan',
                'k.kategori_deviasi',
                'k.status_pelaksanaan_edukasi',
                'k.departemen',
                'k.aktivitas_pekerjaan',
                'k.jenis_kelompok_kerja',
                'k.kelompok_aktivitas_pekerjaan',
                'k.tanggal_edukasi',
                'p.nama as nama_pelanggar',
            ])
            ->get();

        if ($rows->isEmpty()) {
            abort(404, 'Tidak ada data pelanggar untuk SID ini pada jendela 6 bulan terakhir.');
        }

        /** @var Collection<int, object> $byKejadian */
        $byKejadian = $rows->unique('kejadian_id')->values();

        $nama = (string) ($byKejadian->last()->nama_pelanggar ?? '—');
        $nama = trim($nama) !== '' ? trim($nama) : '—';

        $departemen = $this->modeString($byKejadian, 'departemen');
        $posisi = $this->modeString($byKejadian, 'aktivitas_pekerjaan');
        $grup = $this->modeGroup($byKejadian);

        $lastEdu = $byKejadian->map(fn ($r) => $this->carbonDate($r->tanggal_edukasi))->filter()->max();
        $lastEducationLabel = $lastEdu instanceof Carbon
            ? $lastEdu->format('d/m/Y')
            : '—';

        $kasusCount = $byKejadian->count();

        $violatorIncidentDetails = $this->violatorIncidentDetailsList($byKejadian);
        $peerIncidentDetails = $this->peerIncidentDetailsList($sidKey, $from);
        $peerKejadianCount = count($peerIncidentDetails);

        $perOrang = $this->buildPerOrangBlock(
            $kasusCount,
            $violatorIncidentDetails,
            $peerIncidentDetails,
        );

        $sortedDesc = $byKejadian->sortByDesc(function ($r) {
            $d = $this->carbonDate($r->tanggal_temuan);

            return $d ? $d->timestamp : 0;
        })->values();

        $riwayat = [];
        foreach ($sortedDesc as $r) {
            $dt = $this->carbonDate($r->tanggal_temuan);
            $riwayat[] = [
                'tanggal_short' => $dt ? $dt->format('d/m') : '—',
                'kategori' => $this->str($r->kategori_deviasi),
                'lokasi' => $this->str($r->lokasi_temuan),
                'status' => $this->normalizeStatusLabel($this->str($r->status_pelaksanaan_edukasi)),
            ];
        }

        $sortedAsc = $byKejadian->sortBy(function ($r) {
            $d = $this->carbonDate($r->tanggal_temuan);

            return $d ? $d->timestamp : 0;
        })->values();

        $gaps = [];
        for ($i = 1, $n = $sortedAsc->count(); $i < $n; $i++) {
            $prev = $this->carbonDate($sortedAsc[$i - 1]->tanggal_temuan);
            $cur = $this->carbonDate($sortedAsc[$i]->tanggal_temuan);
            if ($prev && $cur) {
                $gaps[] = max(0, (int) round((float) $prev->diffInDays($cur)));
            }
        }

        $recencyGapDays = array_slice($gaps, -8);
        [$trend, $recencyCaption] = $this->recencyTrend($gaps);

        $kejadianIds = $byKejadian->pluck('kejadian_id')->map(fn ($id) => (int) $id)->all();

        $topLoc = $this->topLocationShare($byKejadian);
        $topPeer = $this->topPeerShare($kejadianIds, $sidKey);
        $nightPct = $this->nightShiftPct($byKejadian);

        $korelasi = [];
        if ($topLoc !== null) {
            $korelasi[] = sprintf(
                '%d dari %d kasus di lokasi yang sama (%s)',
                $topLoc['count'],
                $kasusCount,
                $topLoc['label']
            );
        }
        if ($topPeer !== null) {
            $korelasi[] = sprintf(
                '%d dari %d kasus dengan peer yang sama (%s)',
                $topPeer['count'],
                $kasusCount,
                $topPeer['label']
            );
        }
        $korelasi[] = sprintf(
            '%s%% kasus terjadi shift malam (22:00–06:00)',
            number_format($nightPct, 0, ',', '.')
        );

        $lastGap = $gaps === [] ? null : (int) $gaps[array_key_last($gaps)];
        $statusLevel = $this->riskLevel($kasusCount, $lastGap);
        $statusLabel = match ($statusLevel) {
            'high' => '⚠️ HIGH RISK',
            'moderate' => 'MODERATE',
            default => 'NORMAL',
        };

        $fotoMap = $this->karyawanNitip->fotoUrlsByKodeSids([$sidTrim]);
        $fotoUrl = $fotoMap[$sidKey] ?? null;
        if ($fotoUrl === '') {
            $fotoUrl = null;
        }

        $windowCaption = 'Riwayat ' . self::WINDOW_MONTHS . ' bulan terakhir (hingga ' . Carbon::now()->locale('id')->translatedFormat('d M Y') . ')';

        return [
            'sid' => $sidTrim,
            'nama' => $nama,
            'foto_url' => $fotoUrl,
            'npk' => $sidTrim,
            'departemen' => $departemen,
            'posisi' => $posisi,
            'grup' => $grup,
            'status_label' => $statusLabel,
            'status_level' => $statusLevel,
            'last_education_label' => $lastEducationLabel,
            'riwayat' => $riwayat,
            'recency_gap_days' => array_map(static fn (mixed $d): int => (int) round((float) $d), $recencyGapDays),
            'recency_trend' => $trend,
            'recency_caption' => $recencyCaption,
            'korelasi' => $korelasi,
            'rekomendasi' => array_values(array_unique([...$perOrang['rekomendasi_kontekstual'], ...$this->defaultRecommendations()], SORT_REGULAR)),
            'kasus_count' => $kasusCount,
            'window_caption' => $windowCaption,
            'per_orang' => [
                'rows' => $perOrang['rows'],
                'issue_ringkas' => $perOrang['issue_ringkas'],
                'peer_sebagai_peer_kejadian' => $peerKejadianCount,
            ],
        ];
    }

    /**
     * Daftar kejadian unik sebagai pelanggar (untuk detail baris Per Orang).
     *
     * @param  Collection<int, object>  $byKejadian
     * @return list<array{kejadian_id: int, tanggal: string, kategori: string, lokasi: string}>
     */
    private function violatorIncidentDetailsList(Collection $byKejadian): array
    {
        $sorted = $byKejadian->sortByDesc(function ($r) {
            $d = $this->carbonDate($r->tanggal_temuan);

            return $d ? $d->timestamp : 0;
        })->values();

        $out = [];
        foreach ($sorted as $r) {
            $dt = $this->carbonDate($r->tanggal_temuan);
            $out[] = [
                'kejadian_id' => (int) $r->kejadian_id,
                'tanggal' => $dt ? $dt->format('d/m/Y') : '—',
                'kategori' => $this->str($r->kategori_deviasi),
                'lokasi' => $this->str($r->lokasi_temuan),
            ];
        }

        return $out;
    }

    /**
     * Kejadian di mana SID ini berperan sebagai peer — kategori/lokasi = temuan yang diedukasi (bukan pelanggar sendiri).
     *
     * @return list<array{kejadian_id: int, tanggal: string, kategori: string, lokasi: string}>
     */
    private function peerIncidentDetailsList(string $sidKey, Carbon $from): array
    {
        $raw = DB::table('peer_pressure_peserta_edukasi as p')
            ->join('peer_pressure_kejadian_edukasi as k', 'k.id', '=', 'p.kejadian_edukasi_id')
            ->where('p.peran', 'peer')
            ->whereRaw('LOWER(TRIM(p.sid)) = ?', [$sidKey])
            ->where('k.tanggal_temuan', '>=', $from->toDateString())
            ->orderByDesc('k.tanggal_temuan')
            ->orderByDesc('k.id')
            ->select([
                'k.id as kejadian_id',
                'k.tanggal_temuan',
                'k.kategori_deviasi',
                'k.lokasi_temuan',
            ])
            ->get();

        $seen = [];
        $out = [];
        foreach ($raw as $r) {
            $kid = (int) $r->kejadian_id;
            if (isset($seen[$kid])) {
                continue;
            }
            $seen[$kid] = true;
            $dt = $this->carbonDate($r->tanggal_temuan);
            $out[] = [
                'kejadian_id' => $kid,
                'tanggal' => $dt ? $dt->format('d/m/Y') : '—',
                'kategori' => $this->str($r->kategori_deviasi),
                'lokasi' => $this->str($r->lokasi_temuan),
            ];
        }

        return $out;
    }

    /**
     * Kriteria "Per Orang" + narasi issue + rekomendasi tambahan (disatukan di controller dengan default).
     *
     * @param  list<array{kejadian_id: int, tanggal: string, kategori: string, lokasi: string}>  $violatorIncidents
     * @param  list<array{kejadian_id: int, tanggal: string, kategori: string, lokasi: string}>  $peerIncidents
     * @return array{
     *   rows: list<array<string, mixed>>,
     *   issue_ringkas: string,
     *   rekomendasi_kontekstual: list<string>
     * }
     */
    private function buildPerOrangBlock(
        int $kasusViolator,
        array $violatorIncidents,
        array $peerIncidents,
    ): array {
        $peerKejadianCount = count($peerIncidents);
        $complianceCount = $kasusViolator;
        $repetition = $kasusViolator > 1;
        $awareness = $peerKejadianCount > 0 && $kasusViolator >= 1;
        $awareness2 = $peerKejadianCount > 0 && $kasusViolator > 1;

        $sectPelanggar = [
            'heading' => 'Sebagai pelanggar (temuan pada diri Anda)',
            'items' => $violatorIncidents,
        ];
        $sectPeer = [
            'heading' => 'Sebagai peer (Anda mendampingi peer pressure pada temuan orang lain)',
            'items' => $peerIncidents,
        ];

        $rows = [
            [
                'kategori' => 'Compliance peer pressure',
                'kriteria' => 'Dia ada catatan pelanggaran, sudah di peer pressure berapa?',
                'status' => sprintf('%d kali (peer pressure terhadap pelanggar ini)', $complianceCount),
                'terpenuhi' => $complianceCount > 0,
                'detail_sections' => $violatorIncidents !== [] ? [$sectPelanggar] : [],
            ],
            [
                'kategori' => 'Repetition',
                'kriteria' => 'Dia ada catatan pelanggaran > 1',
                'status' => $repetition ? sprintf('Ya (%d kejadian)', $kasusViolator) : 'Tidak',
                'terpenuhi' => $repetition,
                'detail_sections' => ($repetition && $violatorIncidents !== []) ? [$sectPelanggar] : [],
            ],
            [
                'kategori' => 'Awareness',
                'kriteria' => 'Pernah jadi peer, tapi melanggar',
                'status' => $awareness ? 'Ya' : 'Tidak',
                'terpenuhi' => $awareness,
                'detail_sections' => [
                    $sectPelanggar,
                    $sectPeer,
                ],
            ],
            [
                'kategori' => 'Awareness 2',
                'kriteria' => 'Pernah jadi peer, tapi melanggar (berulang)',
                'status' => $awareness2 ? 'Ya' : 'Tidak',
                'terpenuhi' => $awareness2,
                'detail_sections' => [
                    $sectPelanggar,
                    $sectPeer,
                ],
            ],
        ];

        $issueParts = [];
        $issueParts[] = sprintf(
            'Dalam jendela %d bulan terakhir, pelanggar ini memiliki %d catatan sebagai pelanggar (peer pressure dilaksanakan %d kali).',
            self::WINDOW_MONTHS,
            $kasusViolator,
            $complianceCount
        );
        if ($peerKejadianCount > 0) {
            $issueParts[] = sprintf('Sebagai peer, ikut dalam %d kejadian edukasi (peran peer).', $peerKejadianCount);
        }
        if ($repetition) {
            $issueParts[] = 'Repetition terpenuhi: lebih dari satu pelanggaran.';
        }
        if ($awareness2) {
            $issueParts[] = 'Awareness 2: pernah sebagai peer namun pelanggaran berulang — perlu perhatian khusus.';
        } elseif ($awareness) {
            $issueParts[] = 'Awareness: pernah sebagai peer namun tercatat melanggar.';
        }

        $rek = [];
        if ($repetition || $awareness2) {
            $rek[] = 'Prioritaskan coaching satu lawan satu dan kontrak perilaku tertulis; libatkan atasan langsung.';
        }
        if ($awareness || $awareness2) {
            $rek[] = 'Manfaatkan pengalaman peran peer untuk refleksi: diskusi mengapa norma keselamatan tidak dipatuhi saat menjadi pelanggar.';
        }
        if ($kasusViolator >= 3) {
            $rek[] = 'Evaluasi disiplin progresif sesuai kebijakan perusahaan karena frekuensi kasus tinggi.';
        }

        return [
            'rows' => $rows,
            'issue_ringkas' => implode(' ', $issueParts),
            'rekomendasi_kontekstual' => $rek,
        ];
    }

    /**
     * @return list<string>
     */
    private function defaultRecommendations(): array
    {
        return [
            'Jadwalkan coaching formal dengan HSE Manager',
            'Evaluasi roster kerja (potensi fatigue kronis)',
            'Review kompetensi & sertifikasi operator',
            'Pertimbangkan rotasi posisi sementara',
        ];
    }

    /**
     * @param  Collection<int, object>  $rows
     */
    private function modeString(Collection $rows, string $col): string
    {
        $freq = [];
        foreach ($rows as $r) {
            $v = isset($r->{$col}) ? trim((string) $r->{$col}) : '';
            if ($v === '' || $v === '-') {
                continue;
            }
            $freq[$v] = ($freq[$v] ?? 0) + 1;
        }
        if ($freq === []) {
            return '—';
        }
        arsort($freq);

        return array_key_first($freq);
    }

    /**
     * @param  Collection<int, object>  $rows
     */
    private function modeGroup(Collection $rows): string
    {
        foreach ($rows as $r) {
            $k = isset($r->kelompok_aktivitas_pekerjaan) ? trim((string) $r->kelompok_aktivitas_pekerjaan) : '';
            if ($k !== '' && $k !== '-') {
                return $k;
            }
        }

        return $this->modeString($rows, 'jenis_kelompok_kerja');
    }

    private function str(mixed $v): string
    {
        if ($v === null) {
            return '—';
        }
        if ($v instanceof \DateTimeInterface) {
            return '';
        }

        $s = trim((string) $v);

        return $s === '' ? '—' : $s;
    }

    private function carbonDate(mixed $v): ?Carbon
    {
        if ($v instanceof \DateTimeInterface) {
            return Carbon::instance($v)->startOfDay();
        }
        $s = trim((string) $v);
        if ($s === '') {
            return null;
        }
        try {
            return Carbon::parse($s)->startOfDay();
        } catch (\Throwable) {
            return null;
        }
    }

    private function normalizeStatusLabel(string $raw): string
    {
        $u = strtoupper(trim($raw));
        if ($u === '' || $u === '—') {
            return '—';
        }
        if (str_contains($u, 'CLOSE') || str_contains($u, 'SELESAI')) {
            return 'CLOSED';
        }
        if (str_contains($u, 'PROGRESS') || str_contains($u, 'OPEN') || str_contains($u, 'BERJALAN')) {
            return 'OPEN';
        }

        return Str::limit($raw, 14);
    }

    /**
     * @param  list<int>  $gaps
     * @return array{0: 'worsening'|'improving'|'stable', 1: string}
     */
    private function recencyTrend(array $gaps): array
    {
        if ($gaps === []) {
            return ['stable', 'Belum cukup data untuk tren (minimal 2 kejadian).'];
        }
        $n = count($gaps);
        if ($n === 1) {
            return ['stable', 'Satu interval sejak kejadian sebelumnya: ' . $gaps[0] . ' hari.'];
        }
        $first = $gaps[0];
        $last = $gaps[$n - 1];
        if ($last < $first) {
            return ['worsening', 'Trend memburuk (interval antar kejadian semakin pendek).'];
        }
        if ($last > $first) {
            return ['improving', 'Interval antar kejadian memanjang dibanding periode awal.'];
        }

        return ['stable', 'Interval antar kejadian relatif stabil.'];
    }

    private function riskLevel(int $kasus, ?int $lastGapDays): string
    {
        if ($kasus >= self::HIGH_RISK_CASES) {
            return 'high';
        }
        if ($lastGapDays !== null && $lastGapDays <= self::HIGH_RISK_GAP_DAYS && $kasus >= 2) {
            return 'high';
        }
        if ($kasus >= 2) {
            return 'moderate';
        }

        return 'normal';
    }

    /**
     * @param  Collection<int, object>  $byKejadian
     * @return array{label: string, count: int}|null
     */
    private function topLocationShare(Collection $byKejadian): ?array
    {
        $freq = [];
        foreach ($byKejadian as $r) {
            $loc = isset($r->lokasi_temuan) ? trim((string) $r->lokasi_temuan) : '';
            if ($loc === '') {
                $loc = 'Tidak diisi';
            }
            $freq[$loc] = ($freq[$loc] ?? 0) + 1;
        }
        if ($freq === []) {
            return null;
        }
        arsort($freq);
        $label = array_key_first($freq);
        $count = $freq[$label];

        return ['label' => $label, 'count' => $count];
    }

    /**
     * Satu peer per kejadian (urutan terkecil) agar frekuensi = jumlah insiden dengan peer tersebut.
     *
     * @param  list<int>  $kejadianIds
     * @return array{label: string, count: int}|null
     */
    private function topPeerShare(array $kejadianIds, string $violatorSidKey): ?array
    {
        if ($kejadianIds === []) {
            return null;
        }
        $freq = [];
        foreach ($kejadianIds as $kid) {
            $p = DB::table('peer_pressure_peserta_edukasi')
                ->where('kejadian_edukasi_id', $kid)
                ->where('peran', 'peer')
                ->orderBy('urutan')
                ->orderBy('id')
                ->select(['nama', 'sid'])
                ->first();
            if ($p === null) {
                continue;
            }
            $pk = Str::lower(trim((string) $p->sid));
            if ($pk === '' || $pk === $violatorSidKey) {
                continue;
            }
            $nm = trim((string) $p->nama);
            $label = $nm !== '' ? $nm : (string) $p->sid;
            $freq[$label] = ($freq[$label] ?? 0) + 1;
        }
        if ($freq === []) {
            return null;
        }
        arsort($freq);
        $label = array_key_first($freq);

        return ['label' => $label, 'count' => $freq[$label]];
    }

    /**
     * @param  Collection<int, object>  $byKejadian
     */
    private function nightShiftPct(Collection $byKejadian): float
    {
        $total = $byKejadian->count();
        if ($total === 0) {
            return 0.0;
        }
        $night = 0;
        foreach ($byKejadian as $r) {
            if ($this->isNightShift($r->jam_temuan ?? '')) {
                $night++;
            }
        }

        return round(($night / $total) * 100, 1);
    }

    private function isNightShift(mixed $jam): bool
    {
        $s = trim((string) $jam);
        if ($s === '') {
            return false;
        }
        try {
            $c = Carbon::parse('2000-01-01 ' . $s);
        } catch (\Throwable) {
            return false;
        }
        $mins = (int) $c->format('H') * 60 + (int) $c->format('i');

        return $mins >= 22 * 60 || $mins < 6 * 60;
    }

}
