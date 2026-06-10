<?php

namespace App\Services\PembatasanLV;

use App\Models\PembatasanBatasLvPerLokasi;
use App\Models\PembatasanLvInputasi;

class PembatasanLVKapasitasLokasiService
{
    /**
     * @return array{
     *     has_batas: bool,
     *     can_input: bool,
     *     batas_lv: int|null,
     *     terpakai: int,
     *     tersisa: int|null,
     *     lokasi: string|null,
     *     detail_lokasi: string|null,
     *     site: string|null,
     *     message: string|null
     * }
     */
    public function check(string $lokasi, ?string $detailLokasi = null): array
    {
        $batasRow = $this->findBatasRow($lokasi, $detailLokasi);

        if ($batasRow === null) {
            return [
                'has_batas' => false,
                'can_input' => true,
                'batas_lv' => null,
                'terpakai' => 0,
                'tersisa' => null,
                'lokasi' => trim($lokasi) !== '' ? trim($lokasi) : null,
                'detail_lokasi' => trim((string) $detailLokasi) !== '' ? trim((string) $detailLokasi) : null,
                'site' => null,
                'message' => null,
            ];
        }

        $terpakai = $this->countAktifForBatas($batasRow);
        $batas = max((int) $batasRow->batas_lv, 0);
        $tersisa = max(0, $batas - $terpakai);
        $canInput = $terpakai < $batas;

        return [
            'has_batas' => true,
            'can_input' => $canInput,
            'batas_lv' => $batas,
            'terpakai' => $terpakai,
            'tersisa' => $tersisa,
            'lokasi' => $batasRow->lokasi,
            'detail_lokasi' => $batasRow->detail_lokasi,
            'site' => $batasRow->site,
            'message' => $canInput
                ? null
                : "Kapasitas lokasi penuh ({$terpakai}/{$batas} LV masih di area). Tunggu salah satu unit checkout terlebih dahulu.",
        ];
    }

    public function assertCanInput(string $lokasi, ?string $detailLokasi = null): void
    {
        $result = $this->check($lokasi, $detailLokasi);

        if ($result['has_batas'] && ! $result['can_input']) {
            throw new \InvalidArgumentException($result['message'] ?? 'Kapasitas lokasi sudah penuh.');
        }
    }

    public function findBatasRow(string $lokasi, ?string $detailLokasi = null): ?PembatasanBatasLvPerLokasi
    {
        $lokasiNorm = $this->normalize($lokasi);
        if ($lokasiNorm === '') {
            return null;
        }

        $detailNorm = $this->normalize($detailLokasi);

        $candidates = PembatasanBatasLvPerLokasi::query()
            ->whereRaw('LOWER(TRIM(lokasi)) = ?', [$lokasiNorm])
            ->orderBy('id')
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        if ($detailNorm !== '') {
            $exact = $candidates->first(
                fn (PembatasanBatasLvPerLokasi $row) => $this->normalize($row->detail_lokasi) === $detailNorm
            );
            if ($exact !== null) {
                return $exact;
            }
        }

        return $candidates->first(
            fn (PembatasanBatasLvPerLokasi $row) => $this->isLokasiLevelBatas($row, $lokasiNorm)
        ) ?? $candidates->first();
    }

    public function countAktifForBatas(PembatasanBatasLvPerLokasi $batas): int
    {
        $lokasiNorm = $this->normalize($batas->lokasi);
        $detailNorm = $this->normalize($batas->detail_lokasi);

        $query = PembatasanLvInputasi::query()
            ->whereNull('checkout_at')
            ->whereRaw('LOWER(TRIM(lokasi)) = ?', [$lokasiNorm]);

        if ($this->isLokasiLevelBatas($batas, $lokasiNorm)) {
            return $query->count();
        }

        return $query
            ->whereRaw('LOWER(TRIM(COALESCE(detail_lokasi, ""))) = ?', [$detailNorm])
            ->count();
    }

    private function isLokasiLevelBatas(PembatasanBatasLvPerLokasi $batas, string $lokasiNorm): bool
    {
        $detailNorm = $this->normalize($batas->detail_lokasi);

        return $detailNorm === '' || $detailNorm === $lokasiNorm;
    }

    private function normalize(?string $value): string
    {
        return mb_strtolower(trim((string) $value));
    }
}
