<?php

namespace App\Models;

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
     * Simpan snapshot alert per jam dari daftar IKK (Need Action = Merah, Warning = Kuning).
     * Dipanggil dari DOPMController::dashboard() saat filterDate = hari ini.
     *
     * @param  array|Collection  $ikkList  Daftar IKK dengan property status_matriks ('Merah'|'Kuning'|'Hijau')
     * @param  string  $tanggal  Tanggal Y-m-d
     * @return self
     */
    public static function storeSnapshotForHour($ikkList, string $tanggal): self
    {
        $items = $ikkList instanceof Collection ? $ikkList->all() : $ikkList;
        $needAction = 0;
        $warning = 0;
        $snapshotMerah = [];
        $snapshotKuning = [];

        foreach ($items as $ikk) {
            $obj = is_object($ikk) ? $ikk : (object) $ikk;
            $status = $obj->status_matriks ?? null;
            $row = [
                'id' => $obj->id ?? null,
                'code' => $obj->code ?? null,
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

        $jam = (int) now()->format('G');

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
