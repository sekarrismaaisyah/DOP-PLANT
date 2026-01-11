<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DopPengawasMitraKerja extends Model
{
    use HasFactory;

    protected $table = 'dop_pengawas_mitra_kerja';

    protected $fillable = [
        'dop_id',
        'shift',
        'nama_pengawas',
        'layer',
    ];

    /**
     * Get the DOP that owns this Pengawas entry
     */
    public function dailyOperationPlan()
    {
        return $this->belongsTo(DailyOperationPlan::class, 'dop_id');
    }
}

