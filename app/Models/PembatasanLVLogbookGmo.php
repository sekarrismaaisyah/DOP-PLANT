<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembatasanLVLogbookGmo extends Model
{
    public $timestamps = false;

    protected $table = 'logbook_gmo';

    protected $fillable = [
        'tanggal',
        'jam',
        'shift',
        'perusahan',
        'nama_karyawan',
        'sid_karyawan',
        'nama_pengawas',
        'sid_pengawas_pemberi_izin',
        'alasan',
        'keterangan',
        'verifikasi_izin',
        'created_at',
    ];

    protected $casts = [
        'verifikasi_izin' => 'boolean',
        'created_at' => 'datetime',
    ];
}
