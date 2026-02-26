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
     * Cek kondisi alert berdasarkan IPK, OKK Layer 1, dan OKK Layer 2+.
     *
     * Kondisi alert:
     * 1. Tidak ada IPK atau OKK sama sekali
     * 2. Hanya ada IPK saja (tanpa OKK)
     * 3. Hanya ada OKK saja (tanpa IPK)
     * 4. Ada IPK + OKK dari Layer 1, tapi tidak ada OKK dari Layer 2+ sesuai yang tercantum di IKK
     *
     * @return array ['should_alert' => bool, 'alert_reason' => string]
     */
    public static function checkAlertCondition(
        string $kodeIkk,
        string $tanggal,
        ?string $namaLayer1,
        ?string $namaLayer2,
        ?string $namaLayer3,
        ?string $namaLayer4,
        ?string $sidLayer1,
        ?string $sidLayer2,
        ?string $sidLayer3,
        ?string $sidLayer4
    ): array {
        $hasIpk = IpkIkk::where('kode_ikk', $kodeIkk)
            ->whereDate('ts', $tanggal)
            ->exists();

        $okkList = Okk::where('kode_ikk', $kodeIkk)
            ->whereDate('ts', $tanggal)
            ->get();

        $hasOkk = $okkList->count() > 0;

        $hasOkkLayer1 = false;
        $hasOkkLayer2Up = false;

        $layer1Names = array_filter([$namaLayer1 ? trim(strtolower($namaLayer1)) : null]);
        $layer1Sids = array_filter([$sidLayer1 ? trim(strtoupper($sidLayer1)) : null]);
        $layer2UpNames = array_filter([
            $namaLayer2 ? trim(strtolower($namaLayer2)) : null,
            $namaLayer3 ? trim(strtolower($namaLayer3)) : null,
            $namaLayer4 ? trim(strtolower($namaLayer4)) : null,
        ]);
        $layer2UpSids = array_filter([
            $sidLayer2 ? trim(strtoupper($sidLayer2)) : null,
            $sidLayer3 ? trim(strtoupper($sidLayer3)) : null,
            $sidLayer4 ? trim(strtoupper($sidLayer4)) : null,
        ]);

        foreach ($okkList as $okk) {
            $namaPengawas = trim(strtolower($okk->nama_pengawas ?? ''));
            $layerPengawas = trim(strtolower($okk->layer_pengawas ?? ''));
            $kodeSid = trim(strtoupper($okk->kode_sid ?? ''));

            $isLayer1 = false;
            if (in_array($layerPengawas, ['1', 'layer 1', 'layer1'], true)) {
                $isLayer1 = true;
            } elseif (! empty($layer1Names)) {
                foreach ($layer1Names as $ln) {
                    if ($ln && $namaPengawas && str_contains($namaPengawas, $ln)) {
                        $isLayer1 = true;
                        break;
                    }
                }
            }
            if (! $isLayer1 && ! empty($layer1Sids) && $kodeSid) {
                foreach ($layer1Sids as $sid) {
                    if ($sid && $kodeSid === $sid) {
                        $isLayer1 = true;
                        break;
                    }
                }
            }

            $isLayer2Up = false;
            if (in_array($layerPengawas, ['2', '3', '4', 'layer 2', 'layer 3', 'layer 4', 'layer2', 'layer3', 'layer4'], true)) {
                $isLayer2Up = true;
            } elseif (! empty($layer2UpNames)) {
                foreach ($layer2UpNames as $ln) {
                    if ($ln && $namaPengawas && str_contains($namaPengawas, $ln)) {
                        $isLayer2Up = true;
                        break;
                    }
                }
            }
            if (! $isLayer2Up && ! empty($layer2UpSids) && $kodeSid) {
                foreach ($layer2UpSids as $sid) {
                    if ($sid && $kodeSid === $sid) {
                        $isLayer2Up = true;
                        break;
                    }
                }
            }

            if ($isLayer1) {
                $hasOkkLayer1 = true;
            }
            if ($isLayer2Up) {
                $hasOkkLayer2Up = true;
            }
        }

        if (! $hasIpk && ! $hasOkk) {
            return ['should_alert' => true, 'alert_reason' => 'Tidak ada IPK atau OKK sama sekali'];
        }

        if ($hasIpk && ! $hasOkk) {
            return ['should_alert' => true, 'alert_reason' => 'Hanya ada IPK, tidak ada OKK'];
        }

        if (! $hasIpk && $hasOkk) {
            return ['should_alert' => true, 'alert_reason' => 'Hanya ada OKK, tidak ada IPK'];
        }

        $hasLayer2UpInIkk = ! empty($namaLayer2) || ! empty($namaLayer3) || ! empty($namaLayer4)
                        || ! empty($sidLayer2) || ! empty($sidLayer3) || ! empty($sidLayer4);

        if ($hasIpk && $hasOkkLayer1 && $hasLayer2UpInIkk && ! $hasOkkLayer2Up) {
            return ['should_alert' => true, 'alert_reason' => 'Ada IPK + OKK Layer 1, tapi tidak ada OKK dari Layer 2/3/4'];
        }

        return ['should_alert' => false, 'alert_reason' => 'Lengkap'];
    }

    /**
     * Simpan alert per IKK berdasarkan kondisi:
     * 1. Tidak ada IPK atau OKK sama sekali
     * 2. Hanya ada IPK saja (tanpa OKK)
     * 3. Hanya ada OKK saja (tanpa IPK)
     * 4. Ada IPK + OKK Layer 1, tapi tidak ada OKK dari Layer 2+ sesuai yang tercantum di IKK
     *
     * Alert level fixed maksimal 3:
     * - Alert 1 = jam ke-1 sejak start_date
     * - Alert 2 = jam ke-2 sejak start_date
     * - Alert 3 = jam ke-3 sejak start_date
     *
     * Dipanggil dari command dopm:alert-snapshot (setiap 30 menit WITA).
     * Jika sudah ada intervensi di level tertentu, level di atasnya tidak akan disimpan lagi.
     *
     * @param  array|Collection  $ikkList  Daftar IKK dengan code, start_date, end_date, layer info, dll. (dari ClickHouse)
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
