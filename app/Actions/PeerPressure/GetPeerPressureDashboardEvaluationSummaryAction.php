<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Ringkasan evaluasi dari data aktual DB — Repeat Violator mengikuti periode chart (semua data vs bulan).
 */
final class GetPeerPressureDashboardEvaluationSummaryAction
{
    private const REPEAT_MIN_CASES = 3;

    private const RECENCY_HIGH_RISK_DAYS = 7;

    private const VARIETY_PATTERN_MIN = 3;

    /**
     * @return array{
     *   generated_at: string,
     *   total_kejadian: int,
     *   rows: list<array{
     *     key: string,
     *     metric: string,
     *     description: string,
     *     action_threshold: string,
     *     status: 'critical'|'warning'|'ok'|'neutral',
     *     detail_bullets: list<string>,
     *     violators_detail?: list<array{nama: string, sid: string, departemen: string, kasus: int, kejadian_list: list<array{kejadian_id: int, tanggal_temuan: string, tanggal_label: string, kategori_deviasi: string}>}>,
     *     recency_detail?: array{latest: array{kejadian_id: int, tanggal_label: string}, previous: array{kejadian_id: int, tanggal_label: string}, gap_days: int}
     *   }>,
     *   narrative: string,
     *   repeat_period_caption: string,
     *   chart_period_month: bool
     * }
     */
    public function __invoke(?int $chartYear = null, ?int $chartMonth = null): array
    {
        $total = PeerPressureKejadianEdukasi::query()->count();

        $y = $chartYear !== null && $chartMonth !== null
            ? max(GetPeerPressureDashboardWeeklyTrendAction::MIN_YEAR, min(GetPeerPressureDashboardWeeklyTrendAction::MAX_YEAR, $chartYear))
            : null;
        $m = $chartYear !== null && $chartMonth !== null ? max(1, min(12, $chartMonth)) : null;

        $repeatPeriodCaption = $y !== null && $m !== null
            ? $this->monthNameId($m) . ' ' . $y
            : 'Seluruh data';

        $rows = [
            $this->rowRepeatViolator($y, $m, $repeatPeriodCaption),
            $this->rowRecency($total),
            $this->rowDeviationVariety(),
            $this->rowPeerCorrelation(),
        ];

        $narrative = $this->buildNarrative($total, $rows);

        return [
            'generated_at' => now()->format('d M Y, H:i'),
            'total_kejadian' => $total,
            'rows' => $rows,
            'narrative' => $narrative,
            'repeat_period_caption' => $repeatPeriodCaption,
            'chart_period_month' => $y !== null && $m !== null,
        ];
    }

    private function monthNameId(int $month): string
    {
        return [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
            7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
        ][$month] ?? (string) $month;
    }

    private function tanggalTemuanIso(mixed $t): string
    {
        if ($t instanceof \DateTimeInterface) {
            return Carbon::instance($t)->toDateString();
        }
        $s = trim((string) $t);
        if ($s === '') {
            return '1970-01-01';
        }
        try {
            return Carbon::parse($s)->toDateString();
        } catch (\Throwable) {
            return '1970-01-01';
        }
    }

    private function formatTanggalTemuanLabel(mixed $t): string
    {
        if ($t instanceof \DateTimeInterface) {
            return Carbon::instance($t)->locale('id')->translatedFormat('d M Y');
        }
        $s = trim((string) $t);
        if ($s === '') {
            return '—';
        }
        try {
            return Carbon::parse($s)->locale('id')->translatedFormat('d M Y');
        } catch (\Throwable) {
            return $s;
        }
    }

    /**
     * @return array{key: string, metric: string, description: string, action_threshold: string, status: 'critical'|'warning'|'ok'|'neutral', detail_bullets: list<string>, violators_detail?: list<array{nama: string, sid: string, departemen: string, kasus: int, kejadian_list: list<array{kejadian_id: int, tanggal_temuan: string, tanggal_label: string, kategori_deviasi: string}>}>}
     */
    /**
     * @param positive-int|null $year
     * @param int<1,12>|null $month
     */
    private function rowRepeatViolator(?int $year, ?int $month, string $repeatPeriodCaption): array
    {
        $base = DB::table('peer_pressure_peserta_edukasi as p')
            ->join('peer_pressure_kejadian_edukasi as k', 'k.id', '=', 'p.kejadian_edukasi_id')
            ->where('p.peran', 'pelanggar');

        if ($year !== null && $month !== null) {
            $start = Carbon::create($year, $month, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $base->where('k.tanggal_temuan', '>=', $start->toDateString())
                ->where('k.tanggal_temuan', '<=', $end->toDateString());
            $desc = 'Pelanggar dengan ≥ 3 kasus dalam bulan terpilih (sesuai periode chart)';
        } else {
            $desc = 'Pelanggar dengan ≥ 3 kasus (seluruh data, tanggal temuan)';
        }

        $raw = $base
            ->orderByDesc('k.tanggal_temuan')
            ->orderByDesc('k.id')
            ->select([
                'p.sid',
                'p.nama',
                'k.departemen',
                'k.id as kejadian_id',
                'k.tanggal_temuan',
                'k.kategori_deviasi',
            ])
            ->get();

        /** @var \Illuminate\Support\Collection<string, \Illuminate\Support\Collection<int, object>> $bySid */
        $bySid = $raw->groupBy('sid');

        $violatorsDetail = [];
        foreach ($bySid as $sid => $items) {
            $nKejadianUnik = $items->pluck('kejadian_id')->unique()->count();
            if ($nKejadianUnik < self::REPEAT_MIN_CASES) {
                continue;
            }

            $latest = $items->sortByDesc(static function ($i): string {
                $t = $i->tanggal_temuan ?? '';
                if ($t instanceof \DateTimeInterface) {
                    $t = $t->format('Y-m-d');
                } else {
                    $t = (string) $t;
                }
                $id = (int) ($i->kejadian_id ?? 0);

                return $t . '-' . str_pad((string) $id, 12, '0', STR_PAD_LEFT);
            })->first();

            $namaRaw = $latest && isset($latest->nama) ? trim((string) $latest->nama) : '';
            $nama = $namaRaw !== '' ? $namaRaw : '—';

            $depts = $items->pluck('departemen')
                ->map(static function ($d): string {
                    if ($d === null) {
                        return '';
                    }
                    if ($d instanceof \DateTimeInterface) {
                        return '';
                    }

                    return trim((string) $d);
                })
                ->filter(static fn (string $d): bool => $d !== '')
                ->unique()
                ->values();

            $departemenLabel = $depts->isEmpty() ? '—' : $depts->implode(', ');

            $kejadianList = [];
            foreach ($items->groupBy('kejadian_id') as $kid => $grp) {
                $first = $grp->first();
                $rawDate = $first->tanggal_temuan ?? null;
                $katRaw = $first->kategori_deviasi ?? null;
                $katStr = $katRaw !== null && !($katRaw instanceof \DateTimeInterface) ? trim((string) $katRaw) : '';

                $kejadianList[] = [
                    'kejadian_id' => (int) $kid,
                    'tanggal_temuan' => $this->tanggalTemuanIso($rawDate),
                    'tanggal_label' => $this->formatTanggalTemuanLabel($rawDate),
                    'kategori_deviasi' => $katStr !== '' ? $katStr : '—',
                ];
            }

            usort($kejadianList, static function (array $a, array $b): int {
                $c = strcmp($b['tanggal_temuan'], $a['tanggal_temuan']);

                return $c !== 0 ? $c : $b['kejadian_id'] <=> $a['kejadian_id'];
            });

            $kasus = count($kejadianList);

            $violatorsDetail[] = [
                'nama' => $nama,
                'sid' => (string) $sid,
                'departemen' => $departemenLabel,
                'kasus' => $kasus,
                'kejadian_list' => $kejadianList,
            ];
        }

        usort($violatorsDetail, static fn (array $a, array $b): int => $b['kasus'] <=> $a['kasus']);

        $pelanggarCount = count($violatorsDetail);
        $critical = $pelanggarCount > 0;

        $threshold = $critical
            ? '🔴 Immediate Coaching · ' . $pelanggarCount . ' pelanggar memenuhi kriteria'
            : '🟢 Tidak memenuhi ambang · 0 pelanggar (≥ ' . self::REPEAT_MIN_CASES . ' kasus dalam periode: ' . $repeatPeriodCaption . ')';

        $bullets = [];
        if ($pelanggarCount === 0) {
            $bullets[] = 'Tidak ada pelanggar dengan ≥ ' . self::REPEAT_MIN_CASES . ' kasus dalam periode ini.';
        }

        $out = [
            'key' => 'repeat_violator',
            'metric' => 'Repeat Violator',
            'description' => $desc,
            'action_threshold' => $threshold,
            'status' => $critical ? 'critical' : 'ok',
            'detail_bullets' => $bullets,
        ];

        if ($violatorsDetail !== []) {
            $out['violators_detail'] = $violatorsDetail;
        }

        return $out;
    }

    /**
     * @return array{key: string, metric: string, description: string, action_threshold: string, status: 'critical'|'warning'|'ok'|'neutral', detail_bullets: list<string>, recency_detail?: array{latest: array{kejadian_id: int, tanggal_label: string}, previous: array{kejadian_id: int, tanggal_label: string}, gap_days: int}}
     */
    private function rowRecency(int $total): array
    {
        if ($total < 2) {
            return [
                'key' => 'recency',
                'metric' => 'Recency Score',
                'description' => 'Jarak hari antara pelanggaran terakhir & sebelumnya (global, tanggal temuan)',
                'action_threshold' => '— Data belum cukup (perlu ≥ 2 kejadian)',
                'status' => 'neutral',
                'detail_bullets' => [
                    'Saat ini hanya ' . $total . ' kejadian di database.',
                ],
            ];
        }

        $lastTwo = PeerPressureKejadianEdukasi::query()
            ->orderByDesc('tanggal_temuan')
            ->orderByDesc('id')
            ->limit(2)
            ->get(['tanggal_temuan', 'id']);

        $d0 = Carbon::parse($lastTwo[0]->tanggal_temuan)->startOfDay();
        $d1 = Carbon::parse($lastTwo[1]->tanggal_temuan)->startOfDay();
        $gap = (int) abs($d0->diffInDays($d1));

        $highRisk = $gap < self::RECENCY_HIGH_RISK_DAYS;
        $threshold = $highRisk
            ? '🔴 < 7 hari = High Risk (selisih ' . $gap . ' hari)'
            : '🟢 ≥ 7 hari (selisih ' . $gap . ' hari — risiko relatif lebih rendah)';

        return [
            'key' => 'recency',
            'metric' => 'Recency Score',
            'description' => 'Jarak hari antara pelanggaran terakhir & sebelumnya (global, tanggal temuan)',
            'action_threshold' => $threshold,
            'status' => $highRisk ? 'critical' : 'ok',
            'detail_bullets' => [],
            'recency_detail' => [
                'latest' => [
                    'kejadian_id' => (int) $lastTwo[0]->id,
                    'tanggal_label' => $this->formatTanggalTemuanLabel($lastTwo[0]->tanggal_temuan),
                ],
                'previous' => [
                    'kejadian_id' => (int) $lastTwo[1]->id,
                    'tanggal_label' => $this->formatTanggalTemuanLabel($lastTwo[1]->tanggal_temuan),
                ],
                'gap_days' => $gap,
            ],
        ];
    }

    /**
     * @return array{key: string, metric: string, description: string, action_threshold: string, status: 'critical'|'warning'|'ok'|'neutral', detail_bullets: list<string>}
     */
    private function rowDeviationVariety(): array
    {
        $n = (int) DB::table('peer_pressure_kejadian_edukasi')
            ->whereNotNull('kategori_deviasi')
            ->where('kategori_deviasi', '!=', '')
            ->selectRaw('COUNT(DISTINCT kategori_deviasi) as c')
            ->value('c');

        $categories = DB::table('peer_pressure_kejadian_edukasi')
            ->whereNotNull('kategori_deviasi')
            ->where('kategori_deviasi', '!=', '')
            ->selectRaw('kategori_deviasi, COUNT(*) as jumlah')
            ->groupBy('kategori_deviasi')
            ->orderByDesc('jumlah')
            ->limit(12)
            ->get();

        $pattern = $n >= self::VARIETY_PATTERN_MIN;
        $threshold = $pattern
            ? '⚠️ ≥ 3 jenis = Pattern Issue (' . $n . ' jenis berbeda)'
            : ($n > 0
                ? '🟢 Di bawah ambang pola (' . $n . ' jenis berbeda)'
                : '— Belum ada kategori_deviasi terisi');

        $bullets = [
            'Query: COUNT(DISTINCT kategori_deviasi) pada peer_pressure_kejadian_edukasi.',
        ];
        foreach ($categories as $row) {
            $bullets[] = $row->kategori_deviasi . ': ' . (int) $row->jumlah . ' kejadian.';
        }

        return [
            'key' => 'deviation_variety',
            'metric' => 'Deviation Variety',
            'description' => 'Jumlah jenis deviasi berbeda yang tercatat (kategori_deviasi)',
            'action_threshold' => $threshold,
            'status' => $pattern ? 'warning' : ($n > 0 ? 'ok' : 'neutral'),
            'detail_bullets' => $bullets,
        ];
    }

    /**
     * @return array{key: string, metric: string, description: string, action_threshold: string, status: 'critical'|'warning'|'ok'|'neutral', detail_bullets: list<string>}
     */
    private function rowPeerCorrelation(): array
    {
        $pairCounts = [];
        PeerPressureKejadianEdukasi::query()
            ->select(['id'])
            ->with([
                'peserta' => static function ($q): void {
                    $q->whereIn('peran', ['pelanggar', 'peer'])->orderBy('urutan');
                },
            ])
            ->chunkById(200, function ($rows) use (&$pairCounts): void {
                foreach ($rows as $k) {
                    $pel = $k->peserta->firstWhere('peran', 'pelanggar');
                    if ($pel === null) {
                        continue;
                    }
                    foreach ($k->peserta->where('peran', 'peer') as $peer) {
                        $key = $pel->sid . '|' . $peer->sid;
                        $pairCounts[$key] = ($pairCounts[$key] ?? 0) + 1;
                    }
                }
            });

        $repeatedPairs = array_filter($pairCounts, static fn (int $c): bool => $c >= 2);
        $pasanganBerulang = count($repeatedPairs);
        $maxPair = $pairCounts !== [] ? max($pairCounts) : 0;

        arsort($repeatedPairs);
        $topRepeated = array_slice($repeatedPairs, 0, 8, true);

        $flag = $pasanganBerulang > 0;
        $threshold = $flag
            ? '⚠️ Investigasi grup dynamics (' . $pasanganBerulang . ' pasangan pelanggar–peer berulang)'
            : '🟢 Tidak ada pasangan pelanggar–peer yang muncul di >1 kejadian';

        $bullets = [
            'Definisi pasangan: SID pelanggar + SID peer dalam satu kejadian (multi peer = beberapa pasangan per kejadian).',
            'Total pasangan unik (semua kejadian): ' . count($pairCounts) . '.',
            'Pasangan dengan frekuensi ≥ 2: ' . $pasanganBerulang . '.',
            'Frekuensi maksimal satu pasangan: ' . $maxPair . ' kejadian.',
        ];
        foreach ($topRepeated as $key => $c) {
            $parts = explode('|', $key, 2);
            $v = $parts[0] ?? '';
            $p = $parts[1] ?? '';
            $bullets[] = 'Pelanggar ' . $v . ' + Peer ' . $p . ': ' . $c . ' kali.';
        }

        return [
            'key' => 'peer_correlation',
            'metric' => 'Peer Correlation',
            'description' => 'Apakah pelanggar sering muncul dengan peer yang sama?',
            'action_threshold' => $threshold,
            'status' => $flag ? 'warning' : 'ok',
            'detail_bullets' => $bullets,
        ];
    }

    /**
     * @param list<array{status: string, ...}> $rows
     */
    private function buildNarrative(int $total, array $rows): string
    {
        if ($total === 0) {
            return 'Belum ada data kejadian di database untuk dievaluasi.';
        }

        $critical = count(array_filter($rows, static fn (array $r): bool => ($r['status'] ?? '') === 'critical'));
        $warning = count(array_filter($rows, static fn (array $r): bool => ($r['status'] ?? '') === 'warning'));

        if ($critical > 0) {
            return 'Ringkasan dari ' . $total . ' kejadian: ada ' . $critical . ' metrik kritis.';
        }
        if ($warning > 0) {
            return 'Ringkasan dari ' . $total . ' kejadian: ada ' . $warning . ' peringatan (variasi deviasi / pola peer).';
        }

        return 'Ringkasan dari ' . $total . ' kejadian: parameter utama sesuai ambang saat ini.';
    }

}
