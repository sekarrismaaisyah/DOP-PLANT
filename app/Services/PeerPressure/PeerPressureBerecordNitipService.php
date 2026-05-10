<?php

declare(strict_types=1);

namespace App\Services\PeerPressure;

use App\Services\ClickHouseService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Membaca view ClickHouse nitip.bep_vw_berecord (koneksi clickhouse_nitip).
 */
final class PeerPressureBerecordNitipService
{
    private const MIN_YEAR = 2025;

    private const MAX_YEAR = 2026;

    /** Baris dengan nilai ini dianggap tidak masuk baseline pelaksanaan (tidak ada pelanggaran GR). */
    private const GOLDEN_RULES_NO_VIOLATION = 'Tidak Melanggar Golden Rules';

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
     * WHERE untuk kartu/tab deviasi BeRecord: periode (jika ada) + filter {@see baselineBeRecordWhereAndAppendParam} pada `golden_rules`.
     *
     * @return array{0: string, 1: list<string>}
     */
    private function deviationModalBeRecordWhere(?int $year = null, ?int $month = null): array
    {
        [$wherePeriod, $params] = $this->periodWhereAndParams($year, $month);
        $where = $wherePeriod !== '' ? $wherePeriod : 'WHERE 1=1';

        return $this->baselineBeRecordWhereAndAppendParam($where, $params);
    }

    /**
     * Jumlah `id` unik untuk kartu deviasi BeRecord (sama filter golden_rules dengan baseline pelaksanaan).
     */
    public function countDistinctIdsGoldenRulesBaseline(?int $year = null, ?int $month = null): int
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            return 0;
        }

        [$where, $params] = $this->deviationModalBeRecordWhere($year, $month);

        try {
            $sql = 'SELECT uniqExact(`id`) AS c FROM bep_vw_berecord '.$where;
            $rows = $ch->query($sql, $params);
            if (! is_array($rows) || $rows === []) {
                return 0;
            }
            $first = $rows[0];
            if (! is_array($first)) {
                return 0;
            }
            $c = $first['c'] ?? $first['C'] ?? null;

            return is_numeric($c) ? (int) $c : 0;
        } catch (Throwable $e) {
            Log::warning('PeerPressureBerecordNitipService: countDistinctIdsGoldenRulesBaseline gagal', [
                'message' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Jumlah nilai unik kolom `id` pada view ClickHouse `bep_vw_berecord`.
     * Jika tahun & bulan diisi, dibatasi ke baris yang `start_date_be_record` jatuh di bulan tersebut (tanggal yang bisa di-parse).
     */
    public function countDistinctIds(?int $year = null, ?int $month = null): int
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            return 0;
        }

        [$whereSql, $params] = $this->periodWhereAndParams($year, $month);

        try {
            $sql = 'SELECT uniqExact(`id`) AS c FROM bep_vw_berecord ' . $whereSql;
            $rows = $ch->query($sql, $params);
            if (! is_array($rows) || $rows === []) {
                return 0;
            }
            $first = $rows[0];
            if (! is_array($first)) {
                return 0;
            }
            $c = $first['c'] ?? $first['C'] ?? null;

            return is_numeric($c) ? (int) $c : 0;
        } catch (Throwable $e) {
            Log::warning('PeerPressureBerecordNitipService: countDistinctIds gagal', [
                'message' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Paginasi baris `bep_vw_berecord` untuk modal deviasi: periode + filter `golden_rules` sama {@see countDistinctIdsGoldenRulesBaseline}.
     *
     * @return array{rows: list<array<string, string|null>>, total: int, connected: bool, error?: string}
     */
    public function paginateDeviationModal(?int $year = null, ?int $month = null, int $page = 1, int $perPage = 10): array
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            return ['rows' => [], 'total' => 0, 'connected' => false];
        }

        $page = max(1, $page);
        $perPage = min(max(1, $perPage), 50);
        $offset = ($page - 1) * $perPage;
        [$whereSql, $params] = $this->deviationModalBeRecordWhere($year, $month);
        $selectList = $this->buildSelectListSql();

        try {
            $countSql = 'SELECT count() AS c FROM bep_vw_berecord '.$whereSql;
            $countRows = $ch->query($countSql, $params);
            $total = 0;
            if (is_array($countRows) && isset($countRows[0]) && is_array($countRows[0])) {
                $c = $countRows[0]['c'] ?? $countRows[0]['C'] ?? null;
                $total = is_numeric($c) ? (int) $c : 0;
            }

            $dataSql = 'SELECT '.$selectList.' FROM bep_vw_berecord '.$whereSql
                .' ORDER BY toDateOrNull(toString(`start_date_be_record`)) DESC, `id` DESC'
                .' LIMIT '.(int) $perPage.' OFFSET '.(int) $offset;

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
            Log::warning('PeerPressureBerecordNitipService: paginateDeviationModal gagal', [
                'message' => $e->getMessage(),
            ]);

            return ['rows' => [], 'total' => 0, 'connected' => true, 'error' => $e->getMessage()];
        }
    }

    /**
     * @return array{0: string, 1: list<string>}
     */
    private function periodWhereAndParams(?int $year, ?int $month): array
    {
        if ($year === null || $month === null) {
            return ['', []];
        }

        $y = max(self::MIN_YEAR, min(self::MAX_YEAR, $year));
        $m = max(1, min(12, $month));
        $start = Carbon::create($y, $m, 1)->startOfDay();
        $end = $start->copy()->endOfMonth();

        return [
            'WHERE toDateOrNull(toString(`start_date_be_record`)) >= toDate(?) AND toDateOrNull(toString(`start_date_be_record`)) <= toDate(?)',
            [$start->toDateString(), $end->toDateString()],
        ];
    }

    /**
     * Baseline BeRecord memakai kolom `golden_rules`: terisi (bukan null/kosong) dan bukan {@see GOLDEN_RULES_NO_VIOLATION}.
     *
     * @param  list<string>  $params  parameter query (akan ditambah satu nilai untuk perbandingan teks)
     * @return array{0: string, 1: list<string>}
     */
    private function baselineBeRecordWhereAndAppendParam(string $where, array $params): array
    {
        $where .= ' AND isNotNull(`golden_rules`)'
            .' AND length(trim(toString(`golden_rules`))) > 0'
            .' AND lowerUTF8(trim(toString(`golden_rules`))) != lowerUTF8(?)';
        $params[] = self::GOLDEN_RULES_NO_VIOLATION;

        return [$where, $params];
    }

    /**
     * Baseline BeRecord: nilai unik ter-normalisasi (lower+trim) kolom `BeRecord` non-kosong, filter periode sama seperti KPI lain.
     *
     * @return list<string>
     */
    public function distinctNormalizedBeRecordValues(?int $year = null, ?int $month = null): array
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            return [];
        }

        [$wherePeriod, $params] = $this->periodWhereAndParams($year, $month);
        $where = 'WHERE length(trim(toString(`BeRecord`))) > 0';
        if ($wherePeriod !== '') {
            $where .= ' AND '.substr($wherePeriod, strlen('WHERE '));
        }
        [$where, $params] = $this->baselineBeRecordWhereAndAppendParam($where, $params);

        try {
            $sql = 'SELECT DISTINCT lowerUTF8(trim(toString(`BeRecord`))) AS b FROM bep_vw_berecord '.$where.' ORDER BY b';
            $rows = $ch->query($sql, $params);
            if (! is_array($rows)) {
                return [];
            }
            $out = [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $b = $row['b'] ?? $row['B'] ?? null;
                if ($b === null || trim((string) $b) === '') {
                    continue;
                }
                $out[] = strtolower(trim((string) $b));
            }

            return array_values(array_unique($out));
        } catch (Throwable $e) {
            Log::warning('PeerPressureBerecordNitipService: distinctNormalizedBeRecordValues gagal', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    public function countDistinctNormalizedBeRecord(?int $year = null, ?int $month = null): int
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            return 0;
        }

        [$wherePeriod, $params] = $this->periodWhereAndParams($year, $month);
        $where = 'WHERE length(trim(toString(`BeRecord`))) > 0';
        if ($wherePeriod !== '') {
            $where .= ' AND '.substr($wherePeriod, strlen('WHERE '));
        }
        [$where, $params] = $this->baselineBeRecordWhereAndAppendParam($where, $params);

        try {
            $sql = 'SELECT uniqExact(lowerUTF8(trim(toString(`BeRecord`)))) AS c FROM bep_vw_berecord '.$where;
            $rows = $ch->query($sql, $params);
            if (! is_array($rows) || $rows === []) {
                return 0;
            }
            $first = $rows[0];
            if (! is_array($first)) {
                return 0;
            }
            $c = $first['c'] ?? $first['C'] ?? null;

            return is_numeric($c) ? (int) $c : 0;
        } catch (Throwable $e) {
            Log::warning('PeerPressureBerecordNitipService: countDistinctNormalizedBeRecord gagal', [
                'message' => $e->getMessage(),
            ]);

            return 0;
        }
    }

    /**
     * Pemetaan BeRecord ter-normalisasi → label perusahaan (kolom `perusahaan` di CH; satu nilai per grup).
     * Hanya baris baseline: {@see baselineBeRecordWhereAndAppendParam} (`golden_rules` terisi dan bukan “Tidak Melanggar Golden Rules”).
     *
     * @return array<string, string>
     */
    public function mapNormalizedBeRecordToCompany(?int $year = null, ?int $month = null): array
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            return [];
        }

        [$wherePeriod, $params] = $this->periodWhereAndParams($year, $month);
        $where = 'WHERE length(trim(toString(`BeRecord`))) > 0';
        if ($wherePeriod !== '') {
            $where .= ' AND '.substr($wherePeriod, strlen('WHERE '));
        }
        [$where, $params] = $this->baselineBeRecordWhereAndAppendParam($where, $params);

        try {
            $sql = 'SELECT lowerUTF8(trim(toString(`BeRecord`))) AS b, any(trim(toString(ifNull(`perusahaan`, \'\')))) AS co'
                .' FROM bep_vw_berecord '.$where.' GROUP BY b ORDER BY b';
            $rows = $ch->query($sql, $params);
            if (! is_array($rows)) {
                return [];
            }
            $out = [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $b = $row['b'] ?? $row['B'] ?? null;
                if ($b === null || trim((string) $b) === '') {
                    continue;
                }
                $key = strtolower(trim((string) $b));
                $co = $row['co'] ?? $row['CO'] ?? '';
                $out[$key] = trim((string) $co);
            }

            return $out;
        } catch (Throwable $e) {
            Log::warning('PeerPressureBerecordNitipService: mapNormalizedBeRecordToCompany gagal', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Pemetaan BeRecord ter-normalisasi → kode_sid (kolom `kode_sid` di CH; satu nilai per grup, sama seperti {@see mapNormalizedBeRecordToCompany}).
     *
     * @return array<string, string>
     */
    public function mapNormalizedBeRecordToKodeSid(?int $year = null, ?int $month = null): array
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            return [];
        }

        [$wherePeriod, $params] = $this->periodWhereAndParams($year, $month);
        $where = 'WHERE length(trim(toString(`BeRecord`))) > 0';
        if ($wherePeriod !== '') {
            $where .= ' AND '.substr($wherePeriod, strlen('WHERE '));
        }
        [$where, $params] = $this->baselineBeRecordWhereAndAppendParam($where, $params);

        try {
            $sql = 'SELECT lowerUTF8(trim(toString(`BeRecord`))) AS b, any(trim(toString(ifNull(`kode_sid`, \'\')))) AS ks'
                .' FROM bep_vw_berecord '.$where.' GROUP BY b ORDER BY b';
            $rows = $ch->query($sql, $params);
            if (! is_array($rows)) {
                return [];
            }
            $out = [];
            foreach ($rows as $row) {
                if (! is_array($row)) {
                    continue;
                }
                $b = $row['b'] ?? $row['B'] ?? null;
                if ($b === null || trim((string) $b) === '') {
                    continue;
                }
                $key = strtolower(trim((string) $b));
                $ks = $row['ks'] ?? $row['KS'] ?? '';
                $out[$key] = trim((string) $ks);
            }

            return $out;
        } catch (Throwable $e) {
            Log::warning('PeerPressureBerecordNitipService: mapNormalizedBeRecordToKodeSid gagal', [
                'message' => $e->getMessage(),
            ]);

            return [];
        }
    }

    /**
     * Site terbaru (berdasarkan _airbyte_extracted_at) per kode_sid dari nitip.bep_vw_wp_karyawan.
     *
     * @param  list<string>  $lowerTrimmedKodeSids
     * @return array<string, string> lower kode_sid => site (non-kosong)
     */
    public function mapKodeSidLowerToSiteFromWpKaryawan(array $lowerTrimmedKodeSids): array
    {
        $ch = new ClickHouseService('clickhouse_nitip');
        if (! $ch->isConnected()) {
            return [];
        }

        $unique = [];
        foreach ($lowerTrimmedKodeSids as $s) {
            $t = strtolower(trim((string) $s));
            if ($t !== '') {
                $unique[$t] = true;
            }
        }
        $keys = array_keys($unique);
        if ($keys === []) {
            return [];
        }

        $out = [];
        foreach (array_chunk($keys, 400) as $chunk) {
            $placeholders = implode(',', array_fill(0, count($chunk), '?'));
            $sql = 'SELECT lowerUTF8(trim(toString(kode_sid))) AS ks, argMax(trim(toString(ifNull(site, \'\'))), _airbyte_extracted_at) AS site'
                .' FROM bep_vw_wp_karyawan'
                .' WHERE length(trim(toString(kode_sid))) > 0'
                .' AND lowerUTF8(trim(toString(kode_sid))) IN ('.$placeholders.')'
                .' GROUP BY ks';

            try {
                $rows = $ch->query($sql, $chunk);
                if (! is_array($rows)) {
                    continue;
                }
                foreach ($rows as $row) {
                    if (! is_array($row)) {
                        continue;
                    }
                    $ks = $row['ks'] ?? $row['KS'] ?? null;
                    $site = $row['site'] ?? $row['SITE'] ?? null;
                    if ($ks === null) {
                        continue;
                    }
                    $ksK = strtolower(trim((string) $ks));
                    $siteT = trim((string) ($site ?? ''));
                    if ($ksK !== '' && $siteT !== '') {
                        $out[$ksK] = $siteT;
                    }
                }
            } catch (Throwable $e) {
                Log::warning('PeerPressureBerecordNitipService: mapKodeSidLowerToSiteFromWpKaryawan gagal', [
                    'message' => $e->getMessage(),
                ]);
            }
        }

        return $out;
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
