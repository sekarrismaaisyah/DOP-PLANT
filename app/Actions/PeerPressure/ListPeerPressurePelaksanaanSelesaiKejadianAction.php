<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Daftar kejadian dengan pelaksanaan peer pressure selesai (CLOSED/SELESAI), untuk tabel modal dashboard.
 */
final class ListPeerPressurePelaksanaanSelesaiKejadianAction
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
     *   pagination: array{current_page: int, per_page: int, total: int, last_page: int, from: int|null, to: int|null},
     *   rows: list<array<string, mixed>>
     * }
     */
    public function __invoke(?int $year, ?int $month, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        $query = PeerPressureKejadianEdukasi::query()
            ->where(function ($q): void {
                $q->whereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%CLOSE%'])
                    ->orWhereRaw('UPPER(TRIM(status_pelaksanaan_edukasi)) LIKE ?', ['%SELESAI%']);
            });

        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $start = Carbon::create($y, $m, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $query->where('tanggal_temuan', '>=', $start->toDateString())
                ->where('tanggal_temuan', '<=', $end->toDateString());

            /** @var LengthAwarePaginator<int, PeerPressureKejadianEdukasi> $paginator */
            $paginator = $query
                ->with([
                    'peserta' => static function ($rel): void {
                        $rel->select(['id', 'kejadian_edukasi_id', 'sid', 'nama', 'peran', 'urutan'])
                            ->orderBy('urutan');
                    },
                ])
                ->orderByDesc('tanggal_temuan')
                ->orderByDesc('jam_temuan')
                ->orderByDesc('id')
                ->paginate($perPage, ['*'], 'page', $page);

            return [
                'period_scope' => 'month',
                'period_caption' => (self::MONTHS_ID[$m] ?? (string) $m).' '.$y,
                ...$this->formatResponse($paginator),
            ];
        }

        /** @var LengthAwarePaginator<int, PeerPressureKejadianEdukasi> $paginator */
        $paginator = $query
            ->with([
                'peserta' => static function ($rel): void {
                    $rel->select(['id', 'kejadian_edukasi_id', 'sid', 'nama', 'peran', 'urutan'])
                        ->orderBy('urutan');
                },
            ])
            ->orderByDesc('tanggal_temuan')
            ->orderByDesc('jam_temuan')
            ->orderByDesc('id')
            ->paginate($perPage, ['*'], 'page', $page);

        return [
            'period_scope' => 'all',
            'period_caption' => 'Seluruh data',
            ...$this->formatResponse($paginator),
        ];
    }

    /**
     * @param  LengthAwarePaginator<int, PeerPressureKejadianEdukasi>  $paginator
     * @return array{
     *   pagination: array{current_page: int, per_page: int, total: int, last_page: int, from: int|null, to: int|null},
     *   rows: list<array<string, mixed>>
     * }
     */
    private function formatResponse(LengthAwarePaginator $paginator): array
    {
        $rows = [];
        $i = 0;
        foreach ($paginator->items() as $k) {
            if (! $k instanceof PeerPressureKejadianEdukasi) {
                continue;
            }
            $pelanggar = $k->peserta->firstWhere('peran', 'pelanggar');
            $peers = $k->peserta->where('peran', 'peer')->values();
            $badge = $k->dashboardStatusBadge();
            $rows[] = [
                'id' => $k->id,
                'row_index' => $i % 2,
                'formatted_temuan_datetime' => $k->formattedTemuanDatetime(),
                'lokasi_temuan' => $k->lokasi_temuan,
                'kategori_deviasi' => $k->kategori_deviasi,
                'pelanggar_line' => $pelanggar ? $pelanggar->sid.' | '.($pelanggar->nama ?: '—') : '—',
                'dept_line' => ($k->departemen ?: '—').' / '.($k->aktivitas_pekerjaan ?: '—'),
                'peer_initials' => $peers->take(5)->map(fn ($p) => $p->initials())->values()->all(),
                'peer_extra' => max(0, $peers->count() - 5),
                'leader' => $k->pemimpin_edukasi,
                'durasi_edukasi_menit' => $k->durasi_edukasi_menit,
                'evidence_url' => $k->evidence_url,
                'status_pelaksanaan_edukasi' => $k->status_pelaksanaan_edukasi,
                'status_badge' => $badge,
            ];
            $i++;
        }

        return [
            'pagination' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
            'rows' => $rows,
        ];
    }
}
