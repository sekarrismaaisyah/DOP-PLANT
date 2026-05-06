<?php

declare(strict_types=1);

namespace App\Services\SidMeeting;

use App\Services\ClickHouseService;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Membaca view ClickHouse nitip: bep_vw_wp_karyawan (data karyawan per kode_sid).
 */
final class SidMeetingWpKaryawanNitipService
{
    public function isNitipConnected(): bool
    {
        return (new ClickHouseService('clickhouse_nitip'))->isConnected();
    }

    /**
     * Satu baris terbaru per kode_sid (urut sinkronisasi Airbyte terakhir).
     *
     * @return array<string, string|null>|null
     */
    public function findByKodeSid(string $kodeSid): ?array
    {
        $sid = trim($kodeSid);
        if ($sid === '') {
            return null;
        }

        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            Log::info('SidMeetingWpKaryawanNitipService: ClickHouse nitip tidak terhubung');

            return null;
        }

        $sql = <<<'SQL'
SELECT
  kode_sid,
  nik,
  foto,
  nama,
  site,
  usia,
  divisi,
  departement,
  nama_perusahaan,
  jabatan_fungsional,
  jabatan_struktural,
  status_karyawan,
  kategori_karyawan,
  kategori,
  work_permit,
  level_jabatan,
  mainkon,
  dedikasi
FROM bep_vw_wp_karyawan
WHERE lowerUTF8(trim(kode_sid)) = lowerUTF8(trim(?))
ORDER BY _airbyte_extracted_at DESC
LIMIT 1
SQL;

        try {
            $rows = $ch->query($sql, [$sid]);
            if (! is_array($rows) || $rows === []) {
                return null;
            }

            $row = $rows[0];
            if (! is_array($row)) {
                return null;
            }

            return $this->normalizeRow($row);
        } catch (Throwable $e) {
            Log::warning('SidMeetingWpKaryawanNitipService: query gagal', [
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, string|null>
     */
    private function normalizeRow(array $row): array
    {
        $s = static function ($v): ?string {
            if ($v === null) {
                return null;
            }

            $t = trim((string) $v);

            return $t === '' ? null : $t;
        };

        return [
            'kode_sid' => $s($row['kode_sid'] ?? null),
            'nik' => $s($row['nik'] ?? null),
            'foto' => $s($row['foto'] ?? null),
            'nama' => $s($row['nama'] ?? null),
            'site' => $s($row['site'] ?? null),
            'usia' => $s($row['usia'] ?? null),
            'divisi' => $s($row['divisi'] ?? null),
            'departement' => $s($row['departement'] ?? null),
            'nama_perusahaan' => $s($row['nama_perusahaan'] ?? null),
            'jabatan_fungsional' => $s($row['jabatan_fungsional'] ?? null),
            'jabatan_struktural' => $s($row['jabatan_struktural'] ?? null),
            'status_karyawan' => $s($row['status_karyawan'] ?? null),
            'kategori_karyawan' => $s($row['kategori_karyawan'] ?? null),
            'kategori' => $s($row['kategori'] ?? null),
            'work_permit' => $s($row['work_permit'] ?? null),
            'level_jabatan' => $s($row['level_jabatan'] ?? null),
            'mainkon' => $s($row['mainkon'] ?? null),
            'dedikasi' => $s($row['dedikasi'] ?? null),
        ];
    }
}
