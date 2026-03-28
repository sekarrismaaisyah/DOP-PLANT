<?php

declare(strict_types=1);

namespace App\Services\PeerPressure;

use App\Services\ClickHouseService;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Membaca view ClickHouse nitip.bep_vw_berecord (koneksi clickhouse_nitip).
 */
final class PeerPressureBerecordNitipService
{
    /**
     * Satu baris terbaru berdasarkan kode_sid (dipetakan ke SID pelanggar di MySQL).
     *
     * @return array<string, string|null>|null
     */
    public function findLatestByKodeSid(string $kodeSid): ?array
    {
        $sid = trim($kodeSid);
        if ($sid === '' || $sid === '-') {
            return null;
        }

        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            Log::info('PeerPressureBerecordNitipService: ClickHouse nitip tidak terhubung');

            return null;
        }

        try {
            $sql = <<<'SQL'
SELECT
  toString(id) AS id,
  nama AS nama,
  toString(`BeRecord`) AS be_record,
  kode_sid AS kode_sid,
  diskripsi AS diskripsi,
  perusahaan AS perusahaan,
  j_strutural AS j_strutural,
  work_permit AS work_permit,
  golden_rules AS golden_rules,
  j_fungsional AS j_fungsional,
  pic_approval AS pic_approval,
  status_permit AS status_permit,
  tipe_berecord AS tipe_berecord,
  pic_verifikasi AS pic_verifikasi,
  alamat_province AS alamat_province,
  status_berecord AS status_berecord,
  kategori_berecord AS kategori_berecord,
  toString(end_date_be_record) AS end_date_be_record,
  toString(id_status_karyawan) AS id_status_karyawan,
  kategori_kecelakaan AS kategori_kecelakaan,
  toString(start_date_be_record) AS start_date_be_record,
  status_proses_berecord AS status_proses_berecord
FROM bep_vw_berecord
WHERE lowerUTF8(trim(kode_sid)) = lowerUTF8(?)
ORDER BY start_date_be_record DESC
LIMIT 1
SQL;

            $rows = $ch->query($sql, [$sid]);
            if (! is_array($rows) || $rows === []) {
                return null;
            }

            $first = $rows[0];

            return is_array($first) ? $this->normalizeRow($first) : null;
        } catch (Throwable $e) {
            Log::warning('PeerPressureBerecordNitipService: query gagal', [
                'sid' => $sid,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  array<string|int, mixed>  $row
     * @return array<string, string|null>
     */
    private function normalizeRow(array $row): array
    {
        $out = [];
        foreach ($row as $key => $v) {
            $k = is_string($key) ? $key : (string) $key;
            if ($v === null) {
                $out[$k] = null;
            } elseif (is_scalar($v) || $v instanceof \Stringable) {
                $out[$k] = trim((string) $v) === '' ? null : trim((string) $v);
            } else {
                $out[$k] = json_encode($v, JSON_UNESCAPED_UNICODE) ?: null;
            }
        }

        return $out;
    }
}
