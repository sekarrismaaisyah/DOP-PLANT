<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterAktivitas extends Model
{
    use HasFactory;

    protected $table = 'master_aktivitas';

    protected $fillable = [
        'nama_aktivitas',
        'periode_check',
    ];
}
