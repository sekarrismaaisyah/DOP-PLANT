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
    /** Kolom SELECT untuk tabel baca-saja (urutan tampilan). */
    private const VIEW_COLUMNS = [
        'id',
        'nama',
        'BeRecord',
        'kode_sid',
        'diskripsi',
        'perusahaan',
        'j_strutural',
        'work_permit',
        'golden_rules',
        'j_fungsional',
        'pic_approval',
        'status_permit',
        'tipe_berecord',
        'pic_verifikasi',
        'alamat_province',
        'status_berecord',
        'kategori_berecord',
        'end_date_be_record',
        'id_status_karyawan',
        'kategori_kecelakaan',
        'start_date_be_record',
        'status_proses_berecord',
    ];

    /**
     * Daftar kolom untuk header Blade (label singkat opsional).
     *
     * @return array<string, string> key = nama kolom CH, value = label UI
     */
    public static function columnLabels(): array
    {
        return [
            'id' => 'id',
            'nama' => 'nama',
            'BeRecord' => 'BeRecord',
            'kode_sid' => 'kode_sid',
            'diskripsi' => 'diskripsi',
            'perusahaan' => 'perusahaan',
            'j_strutural' => 'j_strutural',
            'work_permit' => 'work_permit',
            'golden_rules' => 'golden_rules',
            'j_fungsional' => 'j_fungsional',
            'pic_approval' => 'pic_approval',
            'status_permit' => 'status_permit',
            'tipe_berecord' => 'tipe_berecord',
            'pic_verifikasi' => 'pic_verifikasi',
            'alamat_province' => 'alamat_province',
            'status_berecord' => 'status_berecord',
            'kategori_berecord' => 'kategori_berecord',
            'end_date_be_record' => 'end_date_be_record',
            'id_status_karyawan' => 'id_status_karyawan',
            'kategori_kecelakaan' => 'kategori_kecelakaan',
            'start_date_be_record' => 'start_date_be_record',
            'status_proses_berecord' => 'status_proses_berecord',
        ];
    }

    /**
     * Paginasi baris dari bep_vw_berecord (pencarian substring pada beberapa kolom teks).
     *
     * @return array{rows: list<array<string, string|null>>, total: int, connected: bool}
     */
    public function paginateView(int $page, int $perPage, string $q): array
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            return ['rows' => [], 'total' => 0, 'connected' => false];
        }

        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 100);
        $offset = ($page - 1) * $perPage;

        $selectList = $this->buildSelectListSql();
        $whereSql = '';
        $params = [];
        $qTrim = trim($q);
        if ($qTrim !== '') {
            $whereSql = 'WHERE ' . $this->buildSearchPredicate();
            $params[] = $qTrim;
        }

        try {
            $countSql = 'SELECT count() AS c FROM bep_vw_berecord ' . $whereSql;
            $countRows = $ch->query($countSql, $params);
            $total = 0;
            if (is_array($countRows) && isset($countRows[0]) && is_array($countRows[0])) {
                $c = $countRows[0]['c'] ?? $countRows[0]['C'] ?? null;
                $total = is_numeric($c) ? (int) $c : 0;
            }

            $dataSql = 'SELECT ' . $selectList . ' FROM bep_vw_berecord ' . $whereSql
                . ' ORDER BY `_airbyte_extracted_at` DESC LIMIT ' . (int) $perPage . ' OFFSET ' . (int) $offset;

            $dataRows = $ch->query($dataSql, $params);
            $rows = [];
            if (is_array($dataRows)) {
                foreach ($dataRows as $row) {
                    if (is_array($row)) {
                        $rows[] = $this->normalizeRow($row);
                    }
                }
            }

            return ['rows' => $rows, 'total' => $total, 'connected' => true];
        } catch (Throwable $e) {
            Log::warning('PeerPressureBerecordNitipService: paginateView gagal', [
                'message' => $e->getMessage(),
            ]);

            return ['rows' => [], 'total' => 0, 'connected' => true, 'error' => $e->getMessage()];
        }
    }

    private function buildSelectListSql(): string
    {
        $parts = [];
        foreach (self::VIEW_COLUMNS as $col) {
            $escaped = '`' . str_replace('`', '', $col) . '`';
            $parts[] = 'toString(' . $escaped . ') AS ' . $escaped;
        }

        return implode(', ', $parts);
    }

    private function buildSearchPredicate(): string
    {
        $cols = [
            'nama', 'kode_sid', 'diskripsi', 'perusahaan', 'work_permit', 'golden_rules',
            'j_strutural', 'j_fungsional', 'pic_approval', 'status_permit', 'tipe_berecord',
            'pic_verifikasi', 'alamat_province', 'status_berecord', 'kategori_berecord',
            'status_proses_berecord', 'kategori_kecelakaan',
        ];
        $args = [];
        foreach ($cols as $c) {
            $args[] = "toString(ifNull(`{$c}`, ''))";
        }

        return 'lowerUTF8(toString(concat_ws(\' \', ' . implode(', ', $args) . '))) LIKE concat(\'%\', lowerUTF8(?), \'%\')';
    }

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
