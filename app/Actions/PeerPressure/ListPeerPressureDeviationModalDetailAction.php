<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Models\SpeakUpFatigue;
use App\Models\ValidasiTbc;
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Detail terpaginasi untuk tab modal deviasi: BeRecord (PSPP/GR), Validasi TBC (tasklist terisi), Speak Up Fatigue.
 */
final class ListPeerPressureDeviationModalDetailAction
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    public const TYPE_BERECORD = 'berecord';

    public const TYPE_VALIDASI_BLINDSPOT = 'validasi_blindspot';

    public const TYPE_SPEAK_UP_FATIGUE = 'speak_up_fatigue';

    /** @return list<string> */
    public static function allowedTypes(): array
    {
        return [self::TYPE_BERECORD, self::TYPE_VALIDASI_BLINDSPOT, self::TYPE_SPEAK_UP_FATIGUE];
    }

    /**
     * @return array{
     *   type: string,
     *   period_scope: 'all'|'month',
     *   pagination: array{current_page: int, per_page: int, total: int, last_page: int, from: int|null, to: int|null},
     *   rows: list<array<string, mixed>>
     * }
     */
    public function __invoke(string $type, ?int $year, ?int $month, int $page, int $perPage): array
    {
        $page = max(1, $page);
        $perPage = min(50, max(5, $perPage));

        $scopedMonth = $year !== null && $month !== null;
        $periodScope = $scopedMonth ? 'month' : 'all';

        return match ($type) {
            self::TYPE_BERECORD => array_merge(['type' => $type, 'period_scope' => $periodScope], $this->berecordRows($year, $month, $page, $perPage)),
            self::TYPE_VALIDASI_BLINDSPOT => array_merge(['type' => $type, 'period_scope' => $periodScope], $this->validasiBlindspotRows($year, $month, $page, $perPage)),
            self::TYPE_SPEAK_UP_FATIGUE => array_merge(['type' => $type, 'period_scope' => $periodScope], $this->speakUpFatigueRows($year, $month, $page, $perPage)),
            default => throw new \InvalidArgumentException('Invalid deviation detail type.'),
        };
    }

    /**
     * @return array{pagination: array<string, mixed>, rows: list<array<string, mixed>>}
     */
    private function berecordRows(?int $year, ?int $month, int $page, int $perPage): array
    {
        $q = PeerPressureKejadianEdukasi::query()
            ->where(function ($sub): void {
                $sub->whereRaw('LOWER(COALESCE(kategori_deviasi, \'\')) LIKE ?', ['%pspp%'])
                    ->orWhereRaw('LOWER(COALESCE(kategori_deviasi, \'\')) LIKE ?', ['%golden%']);
            });

        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $start = Carbon::create($y, $m, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $q->where('tanggal_temuan', '>=', $start->toDateString())
                ->where('tanggal_temuan', '<=', $end->toDateString());
        }

        /** @var LengthAwarePaginator<int, PeerPressureKejadianEdukasi> $paginator */
        $paginator = $q->orderByDesc('tanggal_temuan')
            ->orderByDesc('id')
            ->paginate($perPage, [
                'id',
                'tanggal_temuan',
                'lokasi_temuan',
                'kategori_deviasi',
                'departemen',
                'status_pelaksanaan_edukasi',
                'id_berecord',
            ], 'page', $page);

        $rows = [];
        foreach ($paginator->items() as $k) {
            if (! $k instanceof PeerPressureKejadianEdukasi) {
                continue;
            }
            $rows[] = [
                'id' => $k->id,
                'tanggal_temuan' => $k->tanggal_temuan?->format('Y-m-d'),
                'lokasi_temuan' => $k->lokasi_temuan,
                'kategori_deviasi' => $k->kategori_deviasi,
                'departemen' => $k->departemen,
                'status_pelaksanaan_edukasi' => $k->status_pelaksanaan_edukasi,
                'id_berecord' => $k->id_berecord,
            ];
        }

        return $this->paginationPayload($paginator, $rows);
    }

    /**
     * @return array{pagination: array<string, mixed>, rows: list<array<string, mixed>>}
     */
    private function validasiBlindspotRows(?int $year, ?int $month, int $page, int $perPage): array
    {
        $q = ValidasiTbc::query()
            ->whereRaw('LENGTH(TRIM(COALESCE(tasklist, \'\'))) > 0');

        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $start = Carbon::create($y, $m, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $q->where('created_at', '>=', $start)->where('created_at', '<=', $end);
        }

        /** @var LengthAwarePaginator<int, ValidasiTbc> $paginator */
        $paginator = $q->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);

        $rows = [];
        foreach ($paginator->items() as $r) {
            if (! $r instanceof ValidasiTbc) {
                continue;
            }
            $tl = (string) ($r->tasklist ?? '');
            $rows[] = [
                'id' => $r->id,
                'validator' => $r->validator,
                'gr_pspp' => $r->gr_pspp,
                'tasklist' => $tl,
                'tasklist_short' => mb_strlen($tl) > 160 ? mb_substr($tl, 0, 160).'…' : $tl,
                'created_at' => $r->created_at?->format('Y-m-d H:i'),
            ];
        }

        return $this->paginationPayload($paginator, $rows);
    }

    /**
     * @return array{pagination: array<string, mixed>, rows: list<array<string, mixed>>}
     */
    private function speakUpFatigueRows(?int $year, ?int $month, int $page, int $perPage): array
    {
        $q = SpeakUpFatigue::query();

        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $start = Carbon::create($y, $m, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $q->where('tanggal', '>=', $start->toDateString())
                ->where('tanggal', '<=', $end->toDateString());
        }

        /** @var LengthAwarePaginator<int, SpeakUpFatigue> $paginator */
        $paginator = $q->orderByDesc('tanggal')->orderByDesc('id')->paginate($perPage, ['*'], 'page', $page);

        $rows = [];
        foreach ($paginator->items() as $r) {
            if (! $r instanceof SpeakUpFatigue) {
                continue;
            }
            $rows[] = [
                'id' => $r->id,
                'site' => $r->site,
                'perusahaan' => $r->perusahaan,
                'sid' => $r->sid,
                'nama' => $r->nama,
                'tanggal' => $r->tanggal?->format('Y-m-d'),
                'waktu' => $r->waktu !== null ? (string) $r->waktu : null,
            ];
        }

        return $this->paginationPayload($paginator, $rows);
    }

    /**
     * @param  LengthAwarePaginator<int, mixed>  $paginator
     * @param  list<array<string, mixed>>  $rows
     * @return array{pagination: array<string, mixed>, rows: list<array<string, mixed>>}
     */
    private function paginationPayload(LengthAwarePaginator $paginator, array $rows): array
    {
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
