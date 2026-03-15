<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Becomline extends Model
{
    use HasFactory;

    protected $table = 'becomline';

    protected $fillable = [
        'perusahaan_pemilik',
        'site_operasional',
        'jenis_unit_spip',
        'expired',
        'status_permit_spip',
        'no_registrasi',
    ];

    protected $casts = [
        'expired' => 'date',
    ];
}
