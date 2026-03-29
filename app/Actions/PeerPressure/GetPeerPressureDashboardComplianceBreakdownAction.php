<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Support\PeerPressure\KategoriDeviasiBucket;
use App\Support\PeerPressure\PelaksanaanComplianceEvaluator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Daftar kejadian peer pressure yang masuk metrik Pelaksanaan Comply, dengan status comply per baris (paginasi).
 * Menyertakan agregat penyebab tidak comply untuk rekomendasi perbaikan.
 */
final class GetPeerPressureDashboardComplianceBreakdownAction
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    /** @var array<int, string> */
    private const MONTHS_ID = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April', 5 => 'Mei', 6 => 'Juni',
        7 => 'Juli', 8 => 'Agustus', 9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    /**
     * @return array{
     *   period_scope: 'all'|'month',
     *   period_caption: string,
     *   peer_pressure_compliance_pct: float,
     *   peer_pressure_compliance_total: int,
     *   peer_pressure_compliance_comply: int,
     *   recommendations: list<array{code: string, judul: string, jumlah: int, rekomendasi: string, daftar_kejadian: list<array{id: int, tanggal_temuan: string|null, kategori_deviasi: string|null, bucket_label: string}>}>,
     *   pagination: array{current_page: int, per_page: int, total: int, last_page: int, from: int|null, to: int|null},
     *   rows: list<array<string, mixed>>
     * }
     */
    public function __invoke(?int $year = null, ?int $month = null, int $page = 1, int $perPage = 15): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $start = Carbon::create($y, $m, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $startS = $start->toDateString();
            $endS = $end->toDateString();
            $base = PeerPressureKejadianEdukasi::query()
                ->where('tanggal_temuan', '>=', $startS)
                ->where('tanggal_temuan', '<=', $endS);

            return [
                'period_scope' => 'month',
                'period_caption' => (self::MONTHS_ID[$m] ?? (string) $m).' '.$y,
                ...$this->buildPaginated($base, $page, $perPage),
            ];
        }

        return [
            'period_scope' => 'all',
            'period_caption' => 'Seluruh data',
            ...$this->buildPaginated(PeerPressureKejadianEdukasi::query(), $page, $perPage),
        ];
    }

    /**
     * @return array{
     *   peer_pressure_compliance_pct: float,
     *   peer_pressure_compliance_total: int,
     *   peer_pressure_compliance_comply: int,
     *   recommendations: list<array{code: string, judul: string, jumlah: int, rekomendasi: string, daftar_kejadian: list<array{id: int, tanggal_temuan: string|null, kategori_deviasi: string|null, bucket_label: string}>}>,
     *   pagination: array{current_page: int, per_page: int, total: int, last_page: int, from: int|null, to: int|null},
     *   rows: list<array<string, mixed>>
     * }
     */
    private function buildPaginated(Builder $query, int $page, int $perPage): array
    {
        $tracked = array_flip(KategoriDeviasiBucket::trackedComplianceBuckets());

        $dbRows = (clone $query)
            ->orderByDesc('tanggal_temuan')
            ->orderByDesc('id')
            ->get([
                'id',
                'tanggal_temuan',
                'kategori_deviasi',
                'status_pelaksanaan_edukasi',
                'id_berecord',
            ]);

        $out = [];
        $comply = 0;
        $total = 0;
        /** @var array<string, int> $nonComplyByCode */
        $nonComplyByCode = [];
        /** @var array<string, list<array{id: int, tanggal_temuan: string|null, kategori_deviasi: string|null, bucket_label: string}>> $nonComplyRowsByCode */
        $nonComplyRowsByCode = [];

        foreach ($dbRows as $r) {
            $bucket = KategoriDeviasiBucket::bucket($r->kategori_deviasi);
            if (! isset($tracked[$bucket])) {
                continue;
            }

            $total++;
            $ev = PelaksanaanComplianceEvaluator::evaluate(
                $bucket,
                $r->status_pelaksanaan_edukasi,
                $r->id_berecord
            );
            if ($ev['comply']) {
                $comply++;
            } else {
                $code = $ev['reason_code'] ?? 'unknown';
                $nonComplyByCode[$code] = ($nonComplyByCode[$code] ?? 0) + 1;
            }

            $tanggal = $r->tanggal_temuan instanceof \DateTimeInterface
                ? Carbon::parse($r->tanggal_temuan)->toDateString()
                : (string) $r->tanggal_temuan;

            $row = [
                'id' => (int) $r->id,
                'tanggal_temuan' => $tanggal !== '' ? $tanggal : null,
                'kategori_deviasi' => $r->kategori_deviasi !== null ? (string) $r->kategori_deviasi : null,
                'bucket' => $bucket,
                'bucket_label' => self::bucketLabel($bucket),
                'status_pelaksanaan_edukasi' => $r->status_pelaksanaan_edukasi !== null
                    ? (string) $r->status_pelaksanaan_edukasi
                    : null,
                'id_berecord' => $r->id_berecord !== null && trim((string) $r->id_berecord) !== ''
                    ? (string) $r->id_berecord
                    : null,
                'comply' => $ev['comply'],
                'alasan' => $ev['alasan'],
                'reason_code' => $ev['reason_code'] ?? 'unknown',
            ];

            if (! $ev['comply']) {
                $rc = $ev['reason_code'] ?? 'unknown';
                if (! isset($nonComplyRowsByCode[$rc])) {
                    $nonComplyRowsByCode[$rc] = [];
                }
                $nonComplyRowsByCode[$rc][] = [
                    'id' => $row['id'],
                    'tanggal_temuan' => $row['tanggal_temuan'],
                    'kategori_deviasi' => $row['kategori_deviasi'],
                    'bucket_label' => $row['bucket_label'],
                ];
            }

            $out[] = $row;
        }

        $pct = $total > 0 ? round(100 * $comply / $total, 1) : 0.0;

        $lastPage = $total > 0 ? (int) ceil($total / $perPage) : 1;
        if ($page > $lastPage) {
            $page = $lastPage;
        }
        $offset = ($page - 1) * $perPage;
        $slice = array_slice($out, $offset, $perPage);
        $from = $total === 0 ? null : $offset + 1;
        $to = $total === 0 ? null : min($offset + count($slice), $total);

        return [
            'peer_pressure_compliance_pct' => $pct,
            'peer_pressure_compliance_total' => $total,
            'peer_pressure_compliance_comply' => $comply,
            'recommendations' => self::buildRecommendations($nonComplyByCode, $total - $comply, $nonComplyRowsByCode),
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'last_page' => max(1, $lastPage),
                'from' => $from,
                'to' => $to,
            ],
            'rows' => $slice,
        ];
    }

    /**
     * @param  array<string, int>  $nonComplyByCode
     * @param  array<string, list<array{id: int, tanggal_temuan: string|null, kategori_deviasi: string|null, bucket_label: string}>>  $nonComplyRowsByCode
     * @return list<array{code: string, judul: string, jumlah: int, rekomendasi: string, daftar_kejadian: list<array{id: int, tanggal_temuan: string|null, kategori_deviasi: string|null, bucket_label: string}>}>
     */
    private static function buildRecommendations(array $nonComplyByCode, int $nonComplyTotal, array $nonComplyRowsByCode): array
    {
        if ($nonComplyTotal <= 0) {
            return [
                [
                    'code' => 'all_ok',
                    'judul' => 'Semua kejadian terlacak memenuhi comply',
                    'jumlah' => 0,
                    'rekomendasi' => 'Pertahankan disiplin penutupan pelaksanaan edukasi dan pengisian id BeRecord sesuai aturan kategori.',
                    'daftar_kejadian' => [],
                ],
            ];
        }

        /** @var list<array{code: string, judul: string, jumlah: int, rekomendasi: string, daftar_kejadian: list<array<string, mixed>>}> $items */
        $items = [];
        $order = [
            'be_tanpa_id_berecord',
            'be_belum_selesai',
            'be_belum_selesai_dan_tanpa_id',
            'fb_belum_selesai',
            'unknown',
        ];

        $copy = $nonComplyByCode;
        foreach ($order as $code) {
            $n = (int) ($copy[$code] ?? 0);
            if ($n <= 0) {
                continue;
            }
            unset($copy[$code]);
            $meta = self::recommendationMeta($code);
            $items[] = [
                'code' => $code,
                'judul' => $meta['judul'],
                'jumlah' => $n,
                'rekomendasi' => $meta['rekomendasi'],
                'daftar_kejadian' => array_values($nonComplyRowsByCode[$code] ?? []),
            ];
        }

        foreach ($copy as $code => $n) {
            if ($n <= 0 || $code === 'ok') {
                continue;
            }
            $meta = self::recommendationMeta($code);
            $items[] = [
                'code' => $code,
                'judul' => $meta['judul'],
                'jumlah' => (int) $n,
                'rekomendasi' => $meta['rekomendasi'],
                'daftar_kejadian' => array_values($nonComplyRowsByCode[$code] ?? []),
            ];
        }

        return $items;
    }

    /**
     * @return array{judul: string, rekomendasi: string}
     */
    private static function recommendationMeta(string $code): array
    {
        return match ($code) {
            'be_tanpa_id_berecord' => [
                'judul' => 'Perlu isi id BeRecord (pelaksanaan sudah selesai)',
                'rekomendasi' => 'Uraian per kejadian di bawah menyebutkan tanggal temuan dan kategori; lengkapi kolom id BeRecord agar selaras dengan rekaman BeRecord.',
            ],
            'be_belum_selesai' => [
                'judul' => 'Perlu penutupan pelaksanaan (jalur BeRecord)',
                'rekomendasi' => 'Uraian per kejadian: tutup pelaksanaan dengan status yang mengandung CLOSED atau SELESAI.',
            ],
            'be_belum_selesai_dan_tanpa_id' => [
                'judul' => 'Belum selesai dan id BeRecord kosong',
                'rekomendasi' => 'Uraian per kejadian: selesaikan dulu pelaksanaan, lalu isi id BeRecord.',
            ],
            'fb_belum_selesai' => [
                'judul' => 'Perlu penutupan pelaksanaan (Fatigue & Blindspot)',
                'rekomendasi' => 'Kategori ini tidak memakai BeRecord. Uraian per kejadian: update status ke CLOSED/SELESAI setelah edukasi rampung.',
            ],
            default => [
                'judul' => 'Penyebab lain ('.$code.')',
                'rekomendasi' => 'Uraian per kejadian terkait:',
            ],
        };
    }

    private static function bucketLabel(string $bucket): string
    {
        return match ($bucket) {
            'tidak_speak_up_fatigue' => 'Tidak Speak Up Fatigue',
            'blindspot_to_be_concerned' => 'Blindspot To Be Concerned Hazards',
            'pelanggaran_pspp' => 'Pelanggaran PSPP',
            'pelanggaran_golden_rules' => 'Pelanggaran Golden Rules',
            'insiden' => 'Insiden',
            default => $bucket,
        };
    }
}
