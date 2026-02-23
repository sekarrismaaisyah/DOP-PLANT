<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class DopmAlertLog extends Model
{
    use HasFactory;

    protected $table = 'dopm_alert_log';

    protected $fillable = [
        'tanggal',
        'jam',
        'need_action_count',
        'warning_count',
        'snapshot',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'jam' => 'integer',
        'need_action_count' => 'integer',
        'warning_count' => 'integer',
        'snapshot' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Parse start_date/end_date (string, timestamp, atau object) ke Carbon; null jika gagal.
     */
    private static function parseDateTime(mixed $value): ?Carbon
    {
        if ($value === null || $value === '') {
            return null;
        }
        $tz = config('app.timezone', 'UTC');
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

    /**
     * Simpan snapshot alert per jam dari daftar IKK (Need Action = Merah, Warning = Kuning).
     * Hanya IKK yang start_date-nya sudah dimulai pada jam tersebut yang dimasukkan:
     * untuk jam H, hanya include IKK dengan start_date < (tanggal jam H+1). Jadi IKK yang
     * belum mulai (start_date di masa depan) tidak masuk ke alert.
     * Dipanggil dari DOPMController::dashboard() saat filterDate = hari ini.
     *
     * @param  array|Collection  $ikkList  Daftar IKK dengan property status_matriks ('Merah'|'Kuning'|'Hijau') dan start_date
     * @param  string  $tanggal  Tanggal Y-m-d
     * @return self
     */
    public static function storeSnapshotForHour($ikkList, string $tanggal): self
    {
        $items = $ikkList instanceof Collection ? $ikkList->all() : $ikkList;
        $jam = (int) now()->format('G');
        $tz = config('app.timezone', 'UTC');
        // Akhir jam ini: tanggal jam (H+1):00 — IKK hanya masuk jika start_date < waktu ini
        $hourEnd = Carbon::parse($tanggal, $tz)->startOfDay()->addHours($jam + 1);

        $needAction = 0;
        $warning = 0;
        $snapshotMerah = [];
        $snapshotKuning = [];

        foreach ($items as $ikk) {
            $obj = is_object($ikk) ? $ikk : (object) $ikk;
            $status = $obj->status_matriks ?? null;

            // Hanya masukkan alert jika start_date sudah dimulai pada jam ini (start_date < awal jam berikutnya).
            // Jika start_date tidak ada, IKK dianggap belum mulai → tidak masuk alert.
            $startDateRaw = $obj->start_date ?? null;
            if ($startDateRaw === null || $startDateRaw === '') {
                continue; // tidak ada start_date → belum mulai, jangan masuk alert
            }
            $startDate = self::parseDateTime($startDateRaw);
            if ($startDate === null || $startDate->gte($hourEnd)) {
                continue; // belum mulai pada jam ini → jangan masuk alert
            }

            $row = [
                'id' => $obj->id ?? null,
                'code' => $obj->code ?? null,
                'start_date_tanggal' => $startDate->format('d/m/Y'),
                'start_date_jam' => $startDate->format('H:i'),
                'site' => $obj->site ?? null,
                'jenis_ijin_kerja_khusus' => $obj->jenis_ijin_kerja_khusus ?? null,
                'nama_pekerjaan' => $obj->nama_pekerjaan ?? null,
                'perusahaan' => $obj->perusahaan ?? null,
                'location_name' => $obj->location_name ?? null,
                'location_detail_name' => $obj->location_detail_name ?? null,
                'alasan_matriks' => $obj->alasan_matriks ?? null,
            ];
            if ($status === 'Merah') {
                $needAction++;
                $snapshotMerah[] = $row;
            } elseif ($status === 'Kuning') {
                $warning++;
                $snapshotKuning[] = $row;
            }
        }

        return self::updateOrCreate(
            [
                'tanggal' => $tanggal,
                'jam' => $jam,
            ],
            [
                'need_action_count' => $needAction,
                'warning_count' => $warning,
                'snapshot' => [
                    'need_action' => $snapshotMerah,
                    'warning' => $snapshotKuning,
                ],
            ]
        );
    }
}
