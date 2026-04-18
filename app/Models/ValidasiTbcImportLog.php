<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValidasiTbcImportLog extends Model
{
    protected $table = 'validasi_tbc_import_logs';

    protected $fillable = [
        'uuid',
        'status',
        'rows_imported',
        'error_message',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rows_imported' => 'integer',
        ];
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }
}
