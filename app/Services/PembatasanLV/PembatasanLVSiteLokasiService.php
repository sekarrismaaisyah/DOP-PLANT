<?php

namespace App\Services\PembatasanLV;

use App\Services\ClickHouseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PembatasanLVSiteLokasiService
{
    private const VIEW = 'nitip.bep_vw_site_lokasi_detil_lokasi';

    public function lokasiOptions(string $q = '', int $limit = 50): Collection
    {
        $rows = $this->fetchMasterRows();

        return $rows
            ->pluck('lokasi')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->when($q !== '', fn (Collection $items) => $items->filter(
                fn (string $value) => stripos($value, $q) !== false
            ))
            ->take($limit)
            ->values();
    }

    public function detailLokasiOptions(string $lokasi = '', string $q = '', int $limit = 50): Collection
    {
        $rows = $this->fetchMasterRows();

        if ($lokasi !== '') {
            $normalizedLokasi = $this->normalize($lokasi);
            $rows = $rows->filter(
                fn (array $row) => $this->normalize($row['lokasi']) === $normalizedLokasi
            );
        }

        return $rows
            ->pluck('detail_lokasi')
            ->filter()
            ->unique()
            ->sort()
            ->values()
            ->when($q !== '', fn (Collection $items) => $items->filter(
                fn (string $value) => stripos($value, $q) !== false
            ))
            ->take($limit)
            ->values();
    }

    /**
     * @return Collection<int, array{lokasi: string, detail_lokasi: string, site: string}>
     */
    private function fetchMasterRows(): Collection
    {
        try {
            $ch = app(ClickHouseService::class, ['connectionName' => 'clickhouse_nitip']);
            if (! $ch->isConnected()) {
                return collect();
            }

            $sql = 'SELECT '
                ."trim(ifNull(toString(site), '')) AS site, "
                ."trim(ifNull(toString(lokasi), '')) AS lokasi, "
                ."trim(ifNull(toString(Detil_Lokasi), '')) AS detail_lokasi "
                .'FROM '.self::VIEW.' '
                ."WHERE trim(ifNull(toString(status_site), '')) = '1' "
                ."AND trim(ifNull(toString(status_lokasi), '')) = '1' "
                ."AND trim(ifNull(toString(status_detil_lokasi), '')) = '1' "
                ."AND lokasi != ''";

            $rows = $ch->query($sql);
            if (! is_array($rows)) {
                return collect();
            }

            return collect($rows)->map(function (array $row): array {
                return [
                    'site' => trim((string) ($row['site'] ?? '')),
                    'lokasi' => trim((string) ($row['lokasi'] ?? '')),
                    'detail_lokasi' => trim((string) ($row['detail_lokasi'] ?? '')),
                ];
            })->filter(fn (array $row) => $row['lokasi'] !== '');
        } catch (\Throwable $e) {
            Log::warning('PembatasanLVSiteLokasiService: '.$e->getMessage());

            return collect();
        }
    }

    private function normalize(string $value): string
    {
        return mb_strtolower(trim($value));
    }
}
