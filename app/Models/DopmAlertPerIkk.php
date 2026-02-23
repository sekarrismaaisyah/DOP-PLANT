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
     * Untuk setiap IKK di daftar: jika belum ada IPK dan sudah jam ke-1/2/3 sejak mulai,
     * simpan satu baris ke database (Alert 1, 2, atau 3).
     * Dipanggil dari dashboard (tanggal = hari ini) atau scheduler.
     *
     * @param  array|Collection  $ikkList  Daftar IKK dengan code, start_date, status_matriks, dll.
     * @param  string  $tanggal  Y-m-d
     */
    public static function storeAlertsForDate($ikkList, string $tanggal): void
    {
        $items = $ikkList instanceof Collection ? $ikkList->all() : $ikkList;
        $tz = self::TZ;
        $now = Carbon::now($tz);
        $jamCek = (int) $now->format('G');
        $dateStart = Carbon::parse($tanggal, $tz)->startOfDay();

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

            // Jam ke berapa sejak mulai (1, 2, atau 3)
            $diffMinutes = $now->diffInMinutes($startDate, false);
            if ($diffMinutes <= 0) {
                continue; // belum mulai
            }
            $diffHours = $diffMinutes / 60;
            $jamKe = (int) min(3, max(1, floor($diffHours) + 1));

            // Cek apakah sudah ada IPK untuk IKK ini di tanggal ini
            $hasIpk = IpkIkk::where('kode_ikk', $kodeIkk)
                ->whereDate('ts', $tanggal)
                ->exists();
            if ($hasIpk) {
                continue; // sudah ada IPK, tidak simpan alert
            }

            $snapshot = self::buildIkkSnapshot($obj, $startDate);
            // Simpan Alert 1, 2, dan 3 sampai jam_ke saat ini (agar semua level tercatat di DB)
            for ($level = 1; $level <= $jamKe; $level++) {
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

    private static function buildIkkSnapshot(object $ikk, Carbon $startDate): array
    {
        $status = $ikk->status_matriks ?? 'Merah';
        $type = ($status === 'Merah') ? 'need_action' : 'warning';

        return [
            'code' => $ikk->code ?? null,
            'start_date_tanggal' => $startDate->format('d/m/Y'),
            'start_date_jam' => $startDate->format('H:i'),
            'site' => $ikk->site ?? null,
            'jenis_ijin_kerja_khusus' => $ikk->jenis_ijin_kerja_khusus ?? null,
            'nama_pekerjaan' => $ikk->nama_pekerjaan ?? null,
            'perusahaan' => $ikk->perusahaan ?? null,
            'location_name' => $ikk->location_name ?? null,
            'location_detail_name' => $ikk->location_detail_name ?? null,
            'alasan_matriks' => $ikk->alasan_matriks ?? null,
            'type' => $type,
        ];
    }

    /**
     * Ambil semua alert per IKK untuk satu tanggal, dikelompokkan per kode_ikk.
     * Return array of [ 'code' => ..., 'type' => ..., ikk_snapshot fields, 'levels' => [1,2,3] ]
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
                    'site' => $snap['site'] ?? null,
                    'jenis_ijin_kerja_khusus' => $snap['jenis_ijin_kerja_khusus'] ?? null,
                    'nama_pekerjaan' => $snap['nama_pekerjaan'] ?? null,
                    'perusahaan' => $snap['perusahaan'] ?? null,
                    'location_name' => $snap['location_name'] ?? null,
                    'location_detail_name' => $snap['location_detail_name'] ?? null,
                    'alasan_matriks' => $snap['alasan_matriks'] ?? null,
                    'levels' => [],
                ];
            }
            $byIkk[$kode]['levels'][$row->alert_level] = true;
        }

        return array_values($byIkk);
    }
}
