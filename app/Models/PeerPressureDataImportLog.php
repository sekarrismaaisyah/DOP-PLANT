<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeerPressureDataImportLog extends Model
{
    protected $table = 'peer_pressure_data_import_logs';

    protected $fillable = [
        'user_id',
        'user_name',
        'original_filename',
        'status',
        'message',
        'validation_errors',
        'imported_kejadian',
        'imported_peserta',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'validation_errors' => 'array',
            'imported_kejadian' => 'integer',
            'imported_peserta' => 'integer',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSuccess(): bool
    {
        return $this->status === 'success';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * @return list<string>
     */
    public function affectedColumnLabels(): array
    {
        $labels = [];

        foreach ($this->validation_errors ?? [] as $error) {
            if (! is_string($error)) {
                continue;
            }

            if (preg_match('/kolom "([^"]+)"/u', $error, $matches)) {
                $labels[] = $matches[1];
            } elseif (preg_match('/format tanggal temuan salah/u', $error)) {
                $labels[] = 'Tanggal Temuan';
            } elseif (preg_match('/format tanggal edukasi salah/u', $error)) {
                $labels[] = 'Tanggal Edukasi';
            } elseif (preg_match('/Header kolom ([A-Z]+)/u', $error, $matches)) {
                $labels[] = 'Header kolom ' . $matches[1];
            } elseif (preg_match('/header/i', $error)) {
                $labels[] = 'Header Excel';
            }
        }

        return array_values(array_unique($labels));
    }
}
