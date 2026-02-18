<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisoryAlertLog extends Model
{
    use HasFactory;

    protected $table = 'supervisory_alert_log';

    protected $fillable = [
        'tanggal',
        'id_lokasi',
        'nama_lokasi',
        'risk_level',
        'has_sap_report',
        'has_online_cctv',
        'is_high_risk_area',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'has_sap_report' => 'boolean',
        'has_online_cctv' => 'boolean',
        'is_high_risk_area' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Risk level konstan (sama dengan di frontend) */
    const RISK_HIGH = 'HIGH';
    const RISK_MEDIUM = 'MEDIUM';
    const RISK_NORMAL = 'NORMAL';

    /**
     * Hanya simpan jika status bukan hijau (NORMAL).
     * Return true jika record disimpan/updated, false jika di-skip karena NORMAL.
     */
    public static function storeIfNotGreen(array $data): bool
    {
        $riskLevel = $data['risk_level'] ?? null;
        if ($riskLevel === self::RISK_NORMAL) {
            return false;
        }
        self::updateOrCreate(
            [
                'tanggal' => $data['tanggal'],
                'nama_lokasi' => $data['nama_lokasi'],
            ],
            [
                'id_lokasi' => $data['id_lokasi'] ?? null,
                'risk_level' => $riskLevel,
                'has_sap_report' => $data['has_sap_report'] ?? false,
                'has_online_cctv' => $data['has_online_cctv'] ?? false,
                'is_high_risk_area' => $data['is_high_risk_area'] ?? false,
            ]
        );
        return true;
    }
}
