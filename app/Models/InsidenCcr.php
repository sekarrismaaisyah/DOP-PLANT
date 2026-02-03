<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InsidenCcr extends Model
{
    use HasFactory;

    protected $table = 'insiden_ccr';

    protected $fillable = [
        'ccr_id',
        'no_kecelakaan',
        'ccr_jenis_insiden',
        'ccr_waktu_pelaporan',
        'ccr_waktu_insiden',
        'ccr_kronologi',
        'ccr_nama_call_taker',
        'ccr_perusahaan_call_taker',
        'ccr_nama_pelapor',
        'ccr_perusahaan_pelapor',
        'ccr_lokasi_perusahaan',
        'ccr_site',
        'ccr_lokasi',
        'ccr_detil_lokasi',
        'ccr_keterangan_lokasi',
        'ccr_status',
        'ccr_pic_investigasi',
        'ccr_pic_investigasi_perusahaan',
        'ket_not_investigasi',
    ];

    protected $casts = [
        'ccr_waktu_pelaporan' => 'datetime',
        'ccr_waktu_insiden' => 'datetime',
    ];

    /**
     * Relasi ke InsidenLpi (one to many)
     */
    public function insidenLpis(): HasMany
    {
        return $this->hasMany(InsidenLpi::class, 'insiden_ccr_id');
    }
}
