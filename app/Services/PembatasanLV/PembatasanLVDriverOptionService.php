<?php

namespace App\Services\PembatasanLV;

use App\Services\ClickHouseService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class PembatasanLVDriverOptionService
{
    private const VIEW = 'bep_vw_wp_karyawan';

    public function options(string $q = '', int $limit = 30): Collection
    {
        try {
            $ch = app(ClickHouseService::class, ['connectionName' => 'clickhouse_nitip']);
            if (! $ch->isConnected()) {
                return collect();
            }

            $sql = <<<'SQL'
SELECT
    kode_sid,
    nama,
    nik,
    nama_perusahaan,
    site,
    departement,
    dept_dic,
    dept_mainkon
FROM (
    SELECT
        trim(ifNull(toString(kode_sid), '')) AS kode_sid,
        trim(ifNull(toString(nama), '')) AS nama,
        trim(ifNull(toString(nik), '')) AS nik,
        trim(ifNull(toString(nama_perusahaan), '')) AS nama_perusahaan,
        trim(ifNull(toString(site), '')) AS site,
        trim(ifNull(toString(departement), '')) AS departement,
        trim(ifNull(toString(dept_dic), '')) AS dept_dic,
        trim(ifNull(toString(Dept_Mainkon), '')) AS dept_mainkon,
        ROW_NUMBER() OVER (
            PARTITION BY lowerUTF8(trim(ifNull(toString(kode_sid), '')))
            ORDER BY _airbyte_extracted_at DESC
        ) AS rn
    FROM bep_vw_wp_karyawan
    WHERE trim(ifNull(toString(nama), '')) != ''
      AND trim(ifNull(toString(kode_sid), '')) != ''
) ranked
WHERE rn = 1
  AND (
      ? = ''
      OR positionCaseInsensitive(nama, ?) > 0
      OR positionCaseInsensitive(kode_sid, ?) > 0
      OR positionCaseInsensitive(nik, ?) > 0
      OR positionCaseInsensitive(nama_perusahaan, ?) > 0
  )
ORDER BY nama
LIMIT ?
SQL;

            $rows = $ch->query($sql, [$q, $q, $q, $q, $q, $limit]);
            if (! is_array($rows)) {
                return collect();
            }

            return collect($rows)
                ->map(function (array $row): array {
                    $nama = trim((string) ($row['nama'] ?? ''));
                    $kodeSid = trim((string) ($row['kode_sid'] ?? ''));

                    return [
                        'id' => $kodeSid,
                        'nama' => $nama,
                        'kode_sid' => $kodeSid,
                        'nik' => trim((string) ($row['nik'] ?? '')),
                        'nama_perusahaan' => trim((string) ($row['nama_perusahaan'] ?? '')),
                        'site' => trim((string) ($row['site'] ?? '')),
                        'dept' => $this->resolveDept($row),
                    ];
                })
                ->filter(fn (array $row) => $row['nama'] !== '' && $row['kode_sid'] !== '')
                ->unique('kode_sid')
                ->values();
        } catch (\Throwable $e) {
            Log::warning('PembatasanLVDriverOptionService: '.$e->getMessage());

            return collect();
        }
    }

    public function findBySid(string $sid): ?array
    {
        $sid = trim($sid);
        if ($sid === '') {
            return null;
        }

        return $this->options($sid, 20)
            ->first(fn (array $row) => mb_strtolower($row['kode_sid']) === mb_strtolower($sid));
    }

    /**
     * @param  array<string, mixed>  $row
     */
    private function resolveDept(array $row): string
    {
        foreach (['departement', 'dept_dic', 'dept_mainkon'] as $key) {
            $value = trim((string) ($row[$key] ?? ''));
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }
}
