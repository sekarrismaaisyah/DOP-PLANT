<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DopPicBerauCoal extends Model
{
    use HasFactory;

    protected $table = 'dop_pic_berau_coal';

    protected $fillable = [
        'dop_id',
        'shift',
        'nama_pic',
        'layer',
    ];

    /**
     * Get the DOP that owns this PIC entry
     */
    public function dailyOperationPlan()
    {
        return $this->belongsTo(DailyOperationPlan::class, 'dop_id');
    }
}

