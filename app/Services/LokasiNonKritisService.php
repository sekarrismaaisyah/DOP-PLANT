<?php

namespace App\Services;

use App\Models\DailyOperationPlan;
use App\Models\LokasiNonKritis;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LokasiNonKritisService
{
    public function generate(string $tanggal): array
    {
        $kritisCount = 0;
        $nonKritisCount = 0;

        $clickHouse = app(ClickHouseService::class);
        if (!$clickHouse->isConnected()) {
            throw new \RuntimeException('ClickHouse tidak terhubung. Pastikan konfigurasi dan service berjalan.');
        }

        $kritisKeys = $this->buildKritisKeys($tanggal, $clickHouse);

        $sql = "SELECT toString(id_site) as id_site, toString(site) as site, toString(id_lokasi) as id_lokasi, toString(lokasi) as lokasi, toString(id_detil_lokasi) as id_detil_lokasi, toString(coalesce(detil_lokasi, '')) as detil_lokasi FROM hse_automation.lokasi_detail_lokasi WHERE status_site = 1 AND status_lokasi = 1 AND status_detil_lokasi = 1 ORDER BY site, lokasi, detil_lokasi";
        try {
            $rows = $clickHouse->query($sql) ?? [];
        } catch (\Throwable $e) {
            $sql = "SELECT toString(id_site) as id_site, toString(site) as site, toString(id_lokasi) as id_lokasi, toString(lokasi) as lokasi, toString(id_detil_lokasi) as id_detil_lokasi, toString(coalesce(`Detil Lokasi`, '')) as detil_lokasi FROM hse_automation.lokasi_detail_lokasi WHERE status_site = 1 AND status_lokasi = 1 AND status_detil_lokasi = 1 ORDER BY site, lokasi, detil_lokasi";
            $rows = $clickHouse->query($sql) ?? [];
        }

        if (empty($rows)) {
            Log::info("LokasiNonKritis: No rows from lokasi_detail_lokasi for date {$tanggal}");
            return ['kritis' => 0, 'non_kritis' => 0];
        }

        foreach ($rows as $row) {
            $site = $row['site'] ?? '';
            $lokasi = $row['lokasi'] ?? '';
            $detil = $row['detil_lokasi'] ?? '';
            $key = self::normalizeKey($site, $lokasi, $detil);

            $kategori = isset($kritisKeys[$key]) ? LokasiNonKritis::KATEGORI_KRITIS : LokasiNonKritis::KATEGORI_NON_KRITIS;
            if ($kategori === LokasiNonKritis::KATEGORI_KRITIS) {
                $kritisCount++;
            } else {
                $nonKritisCount++;
            }

            LokasiNonKritis::updateOrCreate(
                [
                    'tanggal' => $tanggal,
                    'id_site' => $row['id_site'] ?? null,
                    'id_lokasi' => $row['id_lokasi'] ?? null,
                    'id_detil_lokasi' => $row['id_detil_lokasi'] ?? null,
                ],
                [
                    'site' => $site ?: null,
                    'lokasi' => $lokasi ?: null,
                    'detil_lokasi' => $detil ?: null,
                    'kategori_area' => $kategori,
                ]
            );
        }

        Log::info("LokasiNonKritis generate: tanggal={$tanggal}, kritis={$kritisCount}, non_kritis={$nonKritisCount}");
        return ['kritis' => $kritisCount, 'non_kritis' => $nonKritisCount];
    }

    public static function normalizeKey(?string $site, ?string $lokasi, ?string $detil): string
    {
        $s = trim((string) $site);
        $l = trim((string) $lokasi);
        $d = trim((string) $detil);
        return mb_strtolower($s) . '|' . mb_strtolower($l) . '|' . mb_strtolower($d);
    }

    private function buildKritisKeys(string $tanggal, ClickHouseService $clickHouse): array
    {
        $keys = [];
        $date = Carbon::parse($tanggal)->toDateString();

        foreach (DailyOperationPlan::whereDate('tanggal', $date)->get(['unit_id', 'lokasi', 'detail_lokasi']) as $dop) {
            $key = self::normalizeKey($dop->unit_id, $dop->lokasi, $dop->detail_lokasi);
            $keys[$key] = true;
        }

        $startStr = addslashes($date);
        $endStr = addslashes($date);
        $sql = "SELECT toString(ra_site_name) as ra_site_name, toString(location_name) as location_name, toString(location_detail_name) as location_detail_name FROM hse_automation.ikk_work_permit WHERE toDate(start_date) <= toDate('{$endStr}') AND toDate(end_date) >= toDate('{$startStr}') AND status IN ('APPROVED') AND deleted_at IS NULL";
        try {
            $ikkRows = $clickHouse->query($sql) ?? [];
            foreach ($ikkRows as $r) {
                $key = self::normalizeKey($r['ra_site_name'] ?? '', $r['location_name'] ?? '', $r['location_detail_name'] ?? '');
                $keys[$key] = true;
            }
        } catch (\Throwable $e) {
            Log::warning('LokasiNonKritis: IKK fetch failed: ' . $e->getMessage());
        }

        return $keys;
    }
}
