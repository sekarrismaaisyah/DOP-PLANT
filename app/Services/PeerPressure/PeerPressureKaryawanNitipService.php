<?php

declare(strict_types=1);

namespace App\Services\PeerPressure;

use App\Services\ClickHouseService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Membaca view ClickHouse nitip.bep_vw_wp_karyawan (foto profil per kode_sid).
 */
final class PeerPressureKaryawanNitipService
{
    /**
     * URL foto per kode_sid (kunci lower-case untuk pencocokan).
     *
     * @param  array<int, string|null>  $kodeSids
     * @return array<string, string> key = Str::lower(trim(sid)), value = URL foto
     */
    public function fotoUrlsByKodeSids(array $kodeSids): array
    {
        $lowerUnique = [];
        foreach ($kodeSids as $sid) {
            $t = trim((string) $sid);
            if ($t === '' || $t === '-') {
                continue;
            }
            $lowerUnique[Str::lower($t)] = true;
        }

        $inList = array_keys($lowerUnique);
        if ($inList === []) {
            return [];
        }

        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            Log::info('PeerPressureKaryawanNitipService: ClickHouse nitip tidak terhubung');

            return [];
        }

        $placeholders = implode(',', array_fill(0, count($inList), '?'));

        $sql = <<<SQL
SELECT
  kode_sid,
  foto
FROM bep_vw_wp_karyawan
WHERE lowerUTF8(trim(kode_sid)) IN ({$placeholders})
SQL;

        try {
            $rows = $ch->query($sql, $inList);
            if (! is_array($rows) || $rows === []) {
                return [];
            }

            $out = [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $ks = isset($row['kode_sid']) ? trim((string) $row['kode_sid']) : '';
                $foto = isset($row['foto']) ? trim((string) $row['foto']) : '';
                if ($ks === '' || $foto === '') {
                    continue;
                }
                $key = Str::lower($ks);
                if (! isset($out[$key])) {
                    $out[$key] = $foto;
                }
            }

            return $out;
        } catch (Throwable $e) {
            Log::warning('PeerPressureKaryawanNitipService: query foto gagal', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }
}
