<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class PeerPressureKejadianEdukasi extends Model
{
    protected $table = 'peer_pressure_kejadian_edukasi';

    protected $fillable = [
        'tanggal_temuan',
        'jam_temuan',
        'kelompok_lokasi_temuan',
        'lokasi_temuan',
        'kelompok_lokasi_edukasi',
        'lokasi_edukasi',
        'tanggal_edukasi',
        'jam_edukasi',
        'perusahaan',
        'site',
        'tasklist_temuan',
        'kronologi_temuan',
        'kategori_deviasi',
        'pemimpin_edukasi',
        'id_berecord',
        'jenis_kelompok_kerja',
        'kelompok_aktivitas_pekerjaan',
        'aktivitas_pekerjaan',
        'departemen',
        'evidence_url',
        'durasi_edukasi_menit',
        'status_pelaksanaan_edukasi',
    ];

    protected $casts = [
        'tanggal_temuan' => 'date',
        'tanggal_edukasi' => 'date',
    ];

    public function peserta(): HasMany
    {
        return $this->hasMany(PeerPressurePesertaEdukasi::class, 'kejadian_edukasi_id')->orderBy('urutan');
    }

    public function formattedTemuanDatetime(): string
    {
        $dateStr = $this->tanggal_temuan instanceof \DateTimeInterface
            ? Carbon::instance($this->tanggal_temuan)->format('Y-m-d')
            : (string) $this->tanggal_temuan;

        $timeStr = $this->jam_temuan;
        if ($timeStr instanceof \DateTimeInterface) {
            $timeStr = $timeStr->format('H:i:s');
        } else {
            $timeStr = (string) $timeStr;
        }

        return Carbon::parse($dateStr . ' ' . $timeStr)->timezone(config('app.timezone'))->format('M j, Y, H:i');
    }

    public function formattedEdukasiDatetime(): string
    {
        $dateStr = $this->tanggal_edukasi instanceof \DateTimeInterface
            ? Carbon::instance($this->tanggal_edukasi)->format('Y-m-d')
            : (string) $this->tanggal_edukasi;

        $timeStr = $this->jam_edukasi;
        if ($timeStr instanceof \DateTimeInterface) {
            $timeStr = $timeStr->format('H:i:s');
        } else {
            $timeStr = (string) $timeStr;
        }

        return Carbon::parse($dateStr . ' ' . $timeStr)->timezone(config('app.timezone'))->format('M j, H:i');
    }

    /**
     * @return array{label: string, spanClass: string}
     */
    public function dashboardStatusBadge(): array
    {
        $raw = strtoupper(trim((string) $this->status_pelaksanaan_edukasi));

        if (str_contains($raw, 'CLOSE') || str_contains($raw, 'SELESAI') || str_contains($raw, 'CLOSED')) {
            return [
                'label' => 'Closed Case',
                'spanClass' => 'bg-[#dcfce7] text-[#16a34a] text-[10px] px-3.5 py-1.5 rounded-full font-bold uppercase tracking-wider border border-[#16a34a]/20 shadow-sm',
            ];
        }

        if (str_contains($raw, 'PROGRESS') || str_contains($raw, 'OPEN') || str_contains($raw, 'BERJALAN')) {
            return [
                'label' => 'In Progress',
                'spanClass' => 'bg-[#fef3c7] text-[#d97706] text-[10px] px-3.5 py-1.5 rounded-full font-bold uppercase tracking-wider border border-[#d97706]/20 shadow-sm',
            ];
        }

        return [
            'label' => Str::limit((string) $this->status_pelaksanaan_edukasi, 42),
            'spanClass' => 'bg-surface-container-high text-on-surface-variant text-[10px] px-3.5 py-1.5 rounded-full font-bold uppercase tracking-wider border border-outline-variant/20 shadow-sm',
        ];
    }
}
