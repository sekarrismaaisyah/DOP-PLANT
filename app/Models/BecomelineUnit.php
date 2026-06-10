<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BecomelineUnit extends Model
{
    protected $table = 'becomeline_unit';

    protected $primaryKey = 'id_unit';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'perusahaan',
        'sid_unit',
        'no_lambung',
        'kategori_unit',
        'jenis_unit',
        'merk_unit',
        'tipe_detail_unit',
    ];
}
