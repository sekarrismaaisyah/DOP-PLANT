<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IntervensiControlRoom extends Model
{
    use HasFactory;

    protected $table = 'intervensi_control_room';

    protected $fillable = [
        'control_room',
        'cctv_id',
        'pic_id',
        'pic_username',
        'pic_nama',
        'pic_telepon',
        'issue',
        'resolution',
        'evidence_path',
        'status',
        'status_done',
        'closed_at',
        'closed_by',
        'created_by',
        'created_by_email',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    /**
     * Get the CCTV associated with this intervensi (many-to-many relationship)
     */
    public function cctvs()
    {
        $pivotColumns = ['status_done'];
        
        // Check if resolution and evidence_path columns exist in pivot table
        // Add them only if migration has been run
        try {
            $columns = \Illuminate\Support\Facades\Schema::getColumnListing('intervensi_control_room_cctv');
            if (in_array('resolution', $columns)) {
                $pivotColumns[] = 'resolution';
            }
            if (in_array('evidence_path', $columns)) {
                $pivotColumns[] = 'evidence_path';
            }
        } catch (\Exception $e) {
            // If table doesn't exist or error, just use status_done
        }
        
        return $this->belongsToMany(CctvData::class, 'intervensi_control_room_cctv', 'intervensi_id', 'cctv_id')
                    ->withPivot($pivotColumns)
                    ->withTimestamps();
    }
}

