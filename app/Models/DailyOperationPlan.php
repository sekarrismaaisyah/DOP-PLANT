<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DailyOperationPlan extends Model
{
    use HasFactory;

    protected $table = 'daily_operation_plans';

    protected $fillable = [
        'pekerjaan',
        'foto_pekerjaan',
        'unit_id',
        'lokasi',
        'latitude',
        'longitude',
        'detail_lokasi',
        'potensi_resiko',
        'pengendalian_bahaya',
        'catatan',
        'tanggal',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Get the PIC Berau Coal entries for this DOP
     */
    public function picBerauCoal()
    {
        return $this->hasMany(DopPicBerauCoal::class, 'dop_id');
    }

    /**
     * Get the Pengawas Mitra Kerja entries for this DOP
     */
    public function pengawasMitraKerja()
    {
        return $this->hasMany(DopPengawasMitraKerja::class, 'dop_id');
    }

    /**
     * Get the CCTV that covers this DOP (many-to-many relationship)
     */
    public function cctvs()
    {
        return $this->belongsToMany(CctvData::class, 'dop_cctv', 'dop_id', 'cctv_id')
                    ->withTimestamps();
    }
}

