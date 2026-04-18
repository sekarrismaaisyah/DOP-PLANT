<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidasiTbc extends Model
{
    protected $table = 'validasi_tbc';

    protected $fillable = [
        'validator',
        'tasklist',
        'to_be_concerned_hazard',
        'gr_pspp',
        'catatan',
        'no_item_pspp',
        'kategori_gr',
        'kategori_gr_valid_kpi',
        'blindspot_terlapor_bc',
        'pic_aktual',
        'kronologi_singkat',
        'rootcause_aktual',
        'detail_rootcause_aktual',
        'tindakan_perbaikan_aktual',
    ];
}
