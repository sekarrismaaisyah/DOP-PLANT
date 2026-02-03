<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsidenLpiLayer extends Model
{
    use HasFactory;

    protected $table = 'insiden_lpi_layers';

    protected $fillable = [
        'insiden_lpi_id',
        'layer',
        'jenis_item_ipls',
        'detail_layer',
        'keterangan_layer',
    ];

    /**
     * Relasi ke InsidenLpi
     */
    public function insidenLpi(): BelongsTo
    {
        return $this->belongsTo(InsidenLpi::class, 'insiden_lpi_id');
    }
}
