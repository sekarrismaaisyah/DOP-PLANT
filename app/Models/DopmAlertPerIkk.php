<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DopmAlertPerIkk extends Model
{
    protected $table = 'dopm_alert_per_ikk';

    protected $fillable = [
        'tanggal',
        'kode_ikk',
        'alert_level',
        'jam_cek',
        'ikk_snapshot',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'alert_level' => 'integer',
        'jam_cek' => 'integer',
        'ikk_snapshot' => 'array',
    ];

    private const TZ = 'Asia/Makassar';

    /**
     * Maksimal alert level (fixed = 3).
     */
    public const MAX_ALERT_LEVEL = 3;

    /**
     * Cek kondisi alert berdasarkan keberadaan IPK.
     *
     * Kondisi alert: Tidak ada IPK untuk kode_ikk tersebut di tanggal itu.
     *
     * @return array ['should_alert' => bool, 'alert_reason' => string]
     */
    public static function checkAlertCondition(
        string $kodeIkk,
        string $tanggal,
        ?string $namaLayer1 = null,
        ?string $namaLayer2 = null,
        ?string $namaLayer3 = null,
        ?string $namaLayer4 = null,
        ?string $sidLayer1 = null,
        ?string $sidLayer2 = null,
        ?string $sidLayer3 = null,
        ?string $sidLayer4 = null
    ): array {
        $hasIpk = IpkIkk::where('kode_ikk', $kodeIkk)
            ->whereDate('ts', $tanggal)
            ->exists();

        if (! $hasIpk) {
            return ['should_alert' => true, 'alert_reason' => 'Tidak ada IPK'];
        }

        return ['should_alert' => false, 'alert_reason' => 'Sudah ada IPK'];
    }

    /**
     * Simpan alert per IKK jika tidak ada IPK.
     *
     * Alert level fixed maksimal 3:
     * - Alert 1 = jam ke-1 sejak start_date, belum ada IPK
     * - Alert 2 = jam ke-2 sejak start_date, belum ada IPK
     * - Alert 3 = jam ke-3 sejak start_date, belum ada IPK
     *
     * Dipanggil dari command dopm:alert-snapshot (setiap 30 menit WITA).
     * Jika sudah ada intervensi di level tertentu, level di atasnya tidak akan disimpan lagi.
     *
     * @param  array|Collection  $ikkList  Daftar IKK dengan code, start_date, end_date, dll. (dari ClickHouse)
     * @param  string  $tanggal  Y-m-d
     */
    public static function storeAlertsForDate($ikkList, string $tanggal): void
    {
        $items = $ikkList instanceof Collection ? $ikkList->all() : $ikkList;
        $tz = self::TZ;
        $now = Carbon::now($tz);
        $jamCek = (int) $now->format('G');

        $maxIntervensiByIkk = DopmAlertIntervensi::getMaxIntervensiLevelByIkk($tanggal);

        foreach ($items as $ikk) {
            $obj = is_object($ikk) ? $ikk : (object) $ikk;
            $kodeIkk = trim((string) ($obj->code ?? ''));
            if ($kodeIkk === '') {
                continue;
            }

            $startDateRaw = $obj->start_date ?? null;
            if ($startDateRaw === null || $startDateRaw === '') {
                continue;
            }
            $startDate = self::parseDateTime($startDateRaw, $tz);
            if ($startDate === null) {
                continue;
            }
            $startDate = $startDate->setTimezone($tz);

            $endDateRaw = $obj->end_date ?? null;
            $endDate = $endDateRaw ? self::parseDateTime($endDateRaw, $tz) : null;
            $endDate = $endDate?->setTimezone($tz);

            $diffMinutes = $startDate->diffInMinutes($now, false);
            if ($diffMinutes < 0) {
                continue;
            }

            $diffHours = $diffMinutes / 60;
            $jamKe = (int) min(self::MAX_ALERT_LEVEL, max(1, floor($diffHours) + 1));

            $alertCheck = self::checkAlertCondition(
                $kodeIkk,
                $tanggal,
                $obj->nama_layer_1 ?? null,
                $obj->nama_layer_2 ?? null,
                $obj->nama_layer_3 ?? null,
                $obj->nama_layer_4 ?? null,
                $obj->sid_layer_1 ?? null,
                $obj->sid_layer_2 ?? null,
                $obj->sid_layer_3 ?? null,
                $obj->sid_layer_4 ?? null
            );

            if (! $alertCheck['should_alert']) {
                continue;
            }

            $snapshot = self::buildIkkSnapshot($obj, $startDate, $endDate);
            $snapshot['alert_reason'] = $alertCheck['alert_reason'];
            $snapshot['max_alert_level'] = self::MAX_ALERT_LEVEL;

            $maxLevel = $jamKe;
            $maxIntervensi = $maxIntervensiByIkk[$kodeIkk] ?? null;
            if ($maxIntervensi !== null) {
                $maxLevel = min($maxLevel, (int) $maxIntervensi);
            }

            for ($level = 1; $level <= $maxLevel; $level++) {
                self::updateOrCreate(
                    [
                        'tanggal' => $tanggal,
                        'kode_ikk' => $kodeIkk,
                        'alert_level' => $level,
                    ],
                    [
                        'jam_cek' => $jamCek,
                        'ikk_snapshot' => $snapshot,
                    ]
                );
            }
        }
    }

    private static function parseDateTime(mixed $value, string $tz): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        try {
            if (is_numeric($value)) {
                return Carbon::createFromTimestamp((int) $value, $tz);
            }
            if ($value instanceof \DateTimeInterface) {
                return Carbon::instance($value)->setTimezone($tz);
            }
            return Carbon::parse($value, $tz);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private static function buildIkkSnapshot(object $ikk, Carbon $startDate, ?Carbon $endDate = null): array
    {
        $status = $ikk->status_matriks ?? 'Merah';
        $type = ($status === 'Merah') ? 'need_action' : 'warning';

        return [
            'code' => $ikk->code ?? null,
            'start_date_tanggal' => $startDate->format('d/m/Y'),
            'start_date_jam' => $startDate->format('H:i'),
            'end_date_tanggal' => $endDate?->format('d/m/Y'),
            'end_date_jam' => $endDate?->format('H:i'),
            'site' => $ikk->site ?? null,
            'jenis_ijin_kerja_khusus' => $ikk->jenis_ijin_kerja_khusus ?? null,
            'nama_pekerjaan' => $ikk->nama_pekerjaan ?? null,
            'perusahaan' => $ikk->perusahaan ?? null,
            'location_name' => $ikk->location_name ?? null,
            'location_detail_name' => $ikk->location_detail_name ?? null,
            'alasan_matriks' => $ikk->alasan_matriks ?? null,
            'type' => $type,
            'nama_layer_1' => $ikk->nama_layer_1 ?? null,
            'nama_layer_2' => $ikk->nama_layer_2 ?? null,
            'nama_layer_3' => $ikk->nama_layer_3 ?? null,
            'nama_layer_4' => $ikk->nama_layer_4 ?? null,
            'sid_layer_1' => $ikk->sid_layer_1 ?? null,
            'sid_layer_2' => $ikk->sid_layer_2 ?? null,
            'sid_layer_3' => $ikk->sid_layer_3 ?? null,
            'sid_layer_4' => $ikk->sid_layer_4 ?? null,
        ];
    }

    /**
     * Ambil semua alert per IKK untuk satu tanggal, dikelompokkan per kode_ikk.
     * Return array of [ 'code' => ..., 'type' => ..., ikk_snapshot fields, 'levels' => [1,2,3,...] ]
     */
    public static function getGroupedByIkkForDate(string $tanggal): array
    {
        $rows = self::query()
            ->where('tanggal', $tanggal)
            ->orderBy('kode_ikk')
            ->orderBy('alert_level')
            ->get();

        $byIkk = [];
        foreach ($rows as $row) {
            $kode = $row->kode_ikk;
            if (! isset($byIkk[$kode])) {
                $snap = $row->ikk_snapshot ?? [];
                $byIkk[$kode] = [
                    'code' => $kode,
                    'type' => $snap['type'] ?? 'need_action',
                    'start_date_tanggal' => $snap['start_date_tanggal'] ?? null,
                    'start_date_jam' => $snap['start_date_jam'] ?? null,
                    'end_date_tanggal' => $snap['end_date_tanggal'] ?? null,
                    'end_date_jam' => $snap['end_date_jam'] ?? null,
                    'site' => $snap['site'] ?? null,
                    'jenis_ijin_kerja_khusus' => $snap['jenis_ijin_kerja_khusus'] ?? null,
                    'nama_pekerjaan' => $snap['nama_pekerjaan'] ?? null,
                    'perusahaan' => $snap['perusahaan'] ?? null,
                    'location_name' => $snap['location_name'] ?? null,
                    'location_detail_name' => $snap['location_detail_name'] ?? null,
                    'alasan_matriks' => $snap['alasan_matriks'] ?? null,
                    'alert_reason' => $snap['alert_reason'] ?? null,
                    'max_alert_level' => $snap['max_alert_level'] ?? self::MAX_ALERT_LEVEL,
                    'nama_layer_1' => $snap['nama_layer_1'] ?? null,
                    'nama_layer_2' => $snap['nama_layer_2'] ?? null,
                    'nama_layer_3' => $snap['nama_layer_3'] ?? null,
                    'nama_layer_4' => $snap['nama_layer_4'] ?? null,
                    'sid_layer_1' => $snap['sid_layer_1'] ?? null,
                    'sid_layer_2' => $snap['sid_layer_2'] ?? null,
                    'sid_layer_3' => $snap['sid_layer_3'] ?? null,
                    'sid_layer_4' => $snap['sid_layer_4'] ?? null,
                    'levels' => [],
                ];
            }
            $byIkk[$kode]['levels'][$row->alert_level] = true;
        }

        return array_values($byIkk);
    }
}
