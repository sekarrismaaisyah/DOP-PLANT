<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CctvP2hChecklist extends Model
{
    use HasFactory;

    protected $table = 'cctv_p2h_checklist';

    protected $fillable = [
        'control_room',
        'tanggal_pemeriksaan',
        'shift',
        'jenis_cctv',
        'nama_pengawas',
        'pemeriksaan_fisik',
        'pemeriksaan_fungsi',
        'detail_cctv',
        'catatan_lain',
        'status',
    ];

    protected $casts = [
        'tanggal_pemeriksaan' => 'date',
        'jenis_cctv' => 'array',
        'pemeriksaan_fisik' => 'array',
        'pemeriksaan_fungsi' => 'array',
        'detail_cctv' => 'array',
    ];

    /**
     * Check if control room has been P2H today for a specific shift
     */
    public static function hasP2hToday($controlRoom, $shift, $date = null)
    {
        $date = $date ?? now()->toDateString();
        
        return self::where('control_room', $controlRoom)
            ->whereDate('tanggal_pemeriksaan', $date)
            ->where('shift', $shift)
            ->where('status', 'completed')
            ->exists();
    }

    /**
     * Get latest P2H for a control room
     */
    public static function getLatestP2h($controlRoom)
    {
        return self::where('control_room', $controlRoom)
            ->orderBy('tanggal_pemeriksaan', 'desc')
            ->orderBy('shift', 'desc')
            ->first();
    }
}

