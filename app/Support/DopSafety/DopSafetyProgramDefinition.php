<?php

declare(strict_types=1);

namespace App\Support\DopSafety;

final class DopSafetyProgramDefinition
{
    /**
     * @return list<array{level: string, role: string, parameters: list<array{label: string, target: string}>}>
     */
    public static function kpiLevels(): array
    {
        return [
            [
                'level' => 'L1 & L2',
                'role' => 'GL & SH',
                'parameters' => [
                    ['label' => 'Semua aktivitas sesuai plan approval', 'target' => '100%'],
                    ['label' => 'Jumlah laporan sesuai aktivitas', 'target' => '100%'],
                    ['label' => 'Fraud pelaporan', 'target' => '0'],
                    ['label' => 'Blind spot safety', 'target' => '0'],
                    ['label' => 'Repetisi temuan', 'target' => '0'],
                ],
            ],
            [
                'level' => 'L3',
                'role' => 'Safety PAMA & BC',
                'parameters' => [
                    ['label' => 'Aktivitas dilakukan inspeksi', 'target' => '100%'],
                    ['label' => 'Laporan sesuai aktivitas di DOP', 'target' => '100%'],
                    ['label' => 'Temuan dilakukan follow-up', 'target' => '100%'],
                ],
            ],
            [
                'level' => 'L4',
                'role' => 'DH & PJO',
                'parameters' => [
                    ['label' => 'Aktivitas kritikal di-review & evaluasi', 'target' => '100%'],
                    ['label' => 'Intervensi berkelanjutan terhadap deviasi berisiko', 'target' => 'Wajib ada'],
                ],
            ],
        ];
    }

    /**
     * @return list<array{step: int, label: string, reject_label: string|null}>
     */
    public static function l1L2FlowSteps(): array
    {
        return [
            ['step' => 1, 'label' => 'OJI (Orientasi Jabatan & Izin Kerja)', 'reject_label' => 'Pekerjaan ditolak'],
            ['step' => 2, 'label' => 'Approval DOP', 'reject_label' => 'Revisi & ajukan ulang'],
            ['step' => 3, 'label' => 'GL START → Inspeksi Pra Kerja', 'reject_label' => null],
            ['step' => 4, 'label' => 'Observasi Saat Pekerjaan', 'reject_label' => null],
            ['step' => 5, 'label' => 'Inspeksi Pasca Pekerjaan', 'reject_label' => null],
            ['step' => 6, 'label' => 'Laporan BEATS → SELESAI', 'reject_label' => null],
        ];
    }

    /**
     * @return list<array{no: int, aspect: string, rule: string}>
     */
    public static function ojiRules(): array
    {
        return [
            ['no' => 1, 'aspect' => 'Dokumen OJI', 'rule' => 'Wajib diisi lengkap oleh GL sebelum memulai pekerjaan'],
            ['no' => 2, 'aspect' => 'Waktu Approval', 'rule' => 'Setiap awal shift sebelum jam kerja dimulai'],
            ['no' => 3, 'aspect' => 'Mekanisme', 'rule' => 'Approval dilakukan oleh Section Head / Dept. Head'],
            ['no' => 4, 'aspect' => 'Tidak Lolos', 'rule' => 'Pekerjaan TIDAK BOLEH dimulai hingga OJI di-approve'],
            ['no' => 5, 'aspect' => 'Dokumentasi', 'rule' => 'OJI tersimpan di sistem & review berkala setiap periode dinas'],
        ];
    }

    /**
     * @return list<array{no: int, rule: string}>
     */
    public static function dopDailyRules(): array
    {
        return [
            ['no' => 1, 'rule' => 'GL mengajukan DOP setiap H-1 sebelum pekerjaan dimulai'],
            ['no' => 2, 'rule' => 'DOP mencakup: daftar pekerjaan, lokasi, kompetensi personil & JSA'],
            ['no' => 3, 'rule' => 'Approval oleh Dept. Head Plant, Dept. Head SHE, dan Supt Safety BC'],
            ['no' => 4, 'rule' => 'DOP harus final sebelum pekerjaan dilakukan'],
            ['no' => 5, 'rule' => 'Revisi DOP hanya oleh SH dengan catatan alasan dan diajukan approval kembali'],
        ];
    }

    /**
     * @return list<array{executor: string, shift: string, target: string, method: string, output: string}>
     */
    public static function l3InspectionMatrix(): array
    {
        return [
            [
                'executor' => 'Safety PAMA',
                'shift' => 'Semua Shift',
                'target' => '100% aktivitas DOP',
                'method' => 'Kunjungan lapangan + observasi',
                'output' => 'BEATS Inspeksi / Hazard / Observasi',
            ],
            [
                'executor' => 'Safety BC',
                'shift' => 'Shift 1 (Pagi)',
                'target' => 'Min. 50% aktivitas DOP',
                'method' => 'Kunjungan lapangan + observasi',
                'output' => 'BEATS Inspeksi / Hazard / Observasi',
            ],
            [
                'executor' => 'Safety BC',
                'shift' => 'Shift 2 (Malam)',
                'target' => 'Min. 30% aktivitas DOP',
                'method' => 'Kunjungan lapangan + observasi',
                'output' => 'BEATS Inspeksi / Hazard / Observasi',
            ],
        ];
    }

    /**
     * @return array{pre: list<string>, during: list<string>, post: list<string>}
     */
    public static function inspectionChecklists(): array
    {
        return [
            'pre' => [
                'Daftar Pekerja — absensi aktual vs DOP',
                'Pemahaman JSA — test lisan / tanya jawab',
                'Inspeksi Tool & Alat — checklist kondisi alat',
                'Inspeksi Area Kerja — kondisi lingkungan & hazard',
                'APD Lengkap — sesuai requirement JSA',
                'Prosedur Dipahami — SOP terakhir disosialisasi',
            ],
            'during' => [
                'Posisi & Postur Kerja',
                'Penggunaan APD',
                'Penggunaan dan kesesuaian Tools / Alat',
                'Kepatuhan Prosedur SOP',
            ],
            'post' => [
                'Kualitas Pekerjaan & Fungsi Peralatan',
                'Tools, Material & LOTO Dikembalikan',
                'Area Kerja Bersih & Housekeeping',
                'Dokumentasi & Penutupan Work Order',
                'Serah Terima dengan User/Operasi',
                'Interaksi Antar Personil',
                'Quality, Housekeeping, Prosedur',
            ],
        ];
    }

    /**
     * @return list<string>
     */
    public static function observationChecklist(): array
    {
        return [
            'Aktivitas tercantum dalam DOP',
            'Personel sesuai daftar pekerja',
            'Izin kerja dan JSA tersedia',
            'Tools & Equipment sesuai kebutuhan',
            'Lokasi pekerjaan sesuai DOP',
        ];
    }

    /**
     * @return list<array{role: string, target: string}>
     */
    public static function observationCoverageTargets(): array
    {
        return [
            ['role' => 'GL & SH', 'target' => '100% coverage observasi aktivitas di DOP berdasarkan area kerjanya'],
            ['role' => 'SHE', 'target' => '100% coverage observasi aktivitas berdasarkan DOP'],
            ['role' => 'DH & PJO', 'target' => 'Random sampling aktivitas 1x per hari'],
        ];
    }

    /**
     * @return list<array{period: string, section: string, theme: string}>
     */
    public static function fgdSchedule(): array
    {
        return [
            ['period' => 'Minggu 1–2', 'section' => 'Wheel, Track, SPEX, Tyre', 'theme' => 'Penyusunan JSA'],
            ['period' => 'Minggu 3–4', 'section' => 'Wheel, Track, SPEX, Tyre', 'theme' => 'Standar Tools'],
            ['period' => 'Minggu 5–6', 'section' => 'Wheel, Track, SPEX, Tyre', 'theme' => 'High-Risk Activity Maintenance'],
            ['period' => 'Minggu 7–8', 'section' => 'Wheel, Track, SPEX, Tyre', 'theme' => 'Isolasi Energi'],
        ];
    }

    /**
     * @return list<string>
     */
    public static function l4ReviewDuties(): array
    {
        return [
            'Review dan evaluasi planning harian sesuai standar keselamatan kerja',
            'Memberikan arahan tindakan perbaikan dan pengendalian untuk menjamin kepatuhan',
            'Mengarahkan perbaikan berkelanjutan (continuous improvement)',
        ];
    }
}
