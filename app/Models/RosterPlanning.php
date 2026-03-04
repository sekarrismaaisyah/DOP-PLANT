<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RosterPlanning extends Model
{
    use HasFactory;

    protected $table = 'roster_plannings';

    protected $fillable = [
        'tanggal',
        'shift',
        'kategori_area',
        'source_type',
        'source_id',
        'site',
        'no_ikk',
        'aktivitas',
        'lokasi',
        'detail_lokasi',
        'id_detail_lokasi',
        'pengawas_langsung',
        'perusahaan_pic',
        'jenis_sap',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function karyawans(): HasMany
    {
        return $this->hasMany(RosterPlanningKaryawan::class, 'roster_planning_id');
    }

    public function getKaryawanCountAttribute(): int
    {
        return $this->karyawans()->count();
    }

    public function getKaryawanNamesAttribute(): string
    {
        $names = $this->karyawans()->pluck('nama_karyawan')->toArray();
        return !empty($names) ? implode(', ', $names) : '-';
    }
}
