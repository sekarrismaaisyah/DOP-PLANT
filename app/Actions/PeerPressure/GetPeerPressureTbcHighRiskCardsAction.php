<?php

declare(strict_types=1);

namespace App\Actions\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

/**
 * Kartu horizontal "TBC GENERAL" — kejadian yang relevan dengan TBC / high risk, atau terbaru sebagai fallback.
 *
 * @return list<array<string, mixed>>
 */
final class GetPeerPressureTbcHighRiskCardsAction
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    private const LIMIT = 6;

    public function __invoke(?int $year = null, ?int $month = null): array
    {
        $base = $this->scopedBase($year, $month);

        $tbc = (clone $base)
            ->where(function ($q): void {
                $q->whereRaw('LOWER(COALESCE(kategori_deviasi, \'\')) LIKE ?', ['%tbc%'])
                    ->orWhereRaw('LOWER(COALESCE(kategori_deviasi, \'\')) LIKE ?', ['%concerned%'])
                    ->orWhereRaw('LOWER(COALESCE(kategori_deviasi, \'\')) LIKE ?', ['%blindspot%'])
                    ->orWhereRaw('LOWER(COALESCE(kategori_deviasi, \'\')) LIKE ?', ['%high risk%']);
            })
            ->orderByDesc('tanggal_temuan')
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get();

        $rows = $tbc->count() >= 1 ? $tbc : (clone $base)
            ->orderByDesc('tanggal_temuan')
            ->orderByDesc('id')
            ->limit(self::LIMIT)
            ->get();

        $out = [];
        $i = 0;
        foreach ($rows as $k) {
            $i++;
            $out[] = $this->mapRow($k, $i);
        }

        return $out;
    }

    private function scopedBase(?int $year, ?int $month): Builder
    {
        $q = PeerPressureKejadianEdukasi::query()->with([
            'peserta' => static function ($p): void {
                $p->orderBy('urutan');
            },
        ]);

        if ($year !== null && $month !== null) {
            $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
            $m = max(1, min(12, $month));
            $start = Carbon::create($y, $m, 1)->startOfDay();
            $end = $start->copy()->endOfMonth();
            $q->where('tanggal_temuan', '>=', $start->toDateString())
                ->where('tanggal_temuan', '<=', $end->toDateString());
        }

        return $q;
    }

    /**
     * @return array<string, mixed>
     */
    private function mapRow(PeerPressureKejadianEdukasi $k, int $index): array
    {
        $kat = trim((string) ($k->kategori_deviasi ?? ''));
        $title = $kat !== '' ? $kat : '—';
        $rawStatus = strtoupper(trim((string) ($k->status_pelaksanaan_edukasi ?? '')));
        $isClosed = str_contains($rawStatus, 'CLOSE')
            || str_contains($rawStatus, 'SELESAI')
            || str_contains($rawStatus, 'CLOSED');
        $isOpen = str_contains($rawStatus, 'OPEN')
            || str_contains($rawStatus, 'PROGRESS')
            || str_contains($rawStatus, 'BERJALAN');

        $statusUi = $isClosed ? 'closed' : ($isOpen ? 'open' : 'other');
        $statusLabel = $isClosed ? 'Closed' : ($isOpen ? 'Open' : Str::limit((string) $k->status_pelaksanaan_edukasi, 24));

        $dateStr = $k->tanggal_temuan instanceof \DateTimeInterface
            ? Carbon::instance($k->tanggal_temuan)->format('Y-m-d')
            : (string) $k->tanggal_temuan;

        $krono = trim((string) ($k->kronologi_temuan ?? ''));
        $desc = $krono !== '' ? Str::limit($krono, 520, '…') : '—';

        return [
            'id' => $k->id,
            'index' => $index,
            'title_line' => $index . '. ' . $title,
            'header_color' => $this->headerColorForCategory($kat !== '' ? $kat : '—'),
            'image_url' => $k->evidence_url ? (string) $k->evidence_url : null,
            'date_label' => $dateStr,
            'description' => $desc,
            'lokasi' => trim((string) ($k->lokasi_temuan ?? '')) !== ''
                ? trim((string) $k->lokasi_temuan)
                : '—',
            'detail_lok' => trim((string) ($k->kelompok_lokasi_temuan ?? '')) !== ''
                ? trim((string) $k->kelompok_lokasi_temuan)
                : null,
            'pelapor' => trim((string) ($k->pemimpin_edukasi ?? '')) !== ''
                ? trim((string) $k->pemimpin_edukasi)
                : '—',
            'metode_lapor' => 'by Pengawasan Langsung',
            'status' => $statusUi,
            'status_label' => $statusLabel,
        ];
    }

    private function headerColorForCategory(string $label): string
    {
        $k = mb_strtolower(trim($label));

        if ($k === 'tidak diisi' || $k === '(kosong)' || $k === '—') {
            return 'hsl(215 14% 42%)';
        }

        if (
            str_contains($k, 'blindspot')
            || str_contains($k, 'tbc')
            || (str_contains($k, 'concerned') && str_contains($k, 'hazard'))
        ) {
            return 'hsl(173 58% 32%)';
        }

        if (str_contains($k, 'road')) {
            return 'hsl(262 48% 42%)';
        }

        if (str_contains($k, 'kendaraan') || str_contains($k, 'pengoperasian')) {
            return 'hsl(48 88% 42%)';
        }

        if (str_contains($k, 'pengawas') || str_contains($k, 'memadai')) {
            return 'hsl(221 72% 38%)';
        }

        /** @var list<string> */
        $palette = [
            'hsl(221 83% 38%)',
            'hsl(173 58% 32%)',
            'hsl(28 88% 42%)',
            'hsl(262 48% 42%)',
            'hsl(346 65% 44%)',
            'hsl(199 75% 38%)',
        ];
        $h = crc32($label);

        return $palette[abs($h) % count($palette)];
    }
}
