<?php

declare(strict_types=1);

namespace App\Services\Hira;

final class HiraImprovementRekayasaReplikasiSampleData
{
    /**
     * @return list<array<string, string>>
     */
    public function rows(): array
    {
        return [
            [
                'site' => 'BMO 2',
                'perusahaan' => 'BUMA',
                'aktivitas' => 'Mobilisasi Demobilisasi',
                'kategoriRekayasa' => 'Engineering Control',
                'originReplikasi' => 'BUMA BMO 2',
                'pengendalianRekayasa' => 'Penambahan trip stik portable pada unit low boy',
                'penjelasanProsesKerja' => 'Trip Stick Portable dipasang di low boy untuk menahan muatan agar tidak bergeser selama perjalanan.',
                'deteksi' => 'Tidak mendeteksi',
                'intervensi' => 'Menahan/mengurangi dampak',
                'levelEfektivitas' => 'Level 3',
                'nilaiRisikoAwal' => 'High',
                'prediksiPenurunanRisiko' => 'Turun 1 tangga',
                'prediksiRisikoSisa' => 'Medium',
                'target' => 'Replikasi ke seluruh site tambang',
                'totalPopulasi' => '12',
                'targetReplikasiKomitmen' => '10',
                'aktualReplikasi' => '4',
                'satuan' => 'Unit',
                'jumlahMitraReplikasi' => '2',
                'tercoverBehira' => 'Ya',
                'potensiPeningkatanLevelEfektivitas' => 'Level 4',
                'pengendalianPeningkatanLevelEfektivitas' => 'Integrasi sensor otomatis pada trip stick',
                'targetStandarisasiDueDate' => '2026-09-30',
            ],
            [
                'site' => 'GMO',
                'perusahaan' => 'G&E',
                'aktivitas' => 'Eksplorasi',
                'kategoriRekayasa' => 'Substitution',
                'originReplikasi' => 'G&E BC GMO',
                'pengendalianRekayasa' => 'Modifikasi unit bor manual menggunakan sling winch',
                'penjelasanProsesKerja' => 'Penarikan unit bor dengan sling winch mengurangi risiko tertimpa dan low back pain.',
                'deteksi' => 'Tidak mendeteksi',
                'intervensi' => 'Menahan/mengurangi dampak',
                'levelEfektivitas' => 'Level 2',
                'nilaiRisikoAwal' => 'Significant',
                'prediksiPenurunanRisiko' => 'Turun 1 tangga',
                'prediksiRisikoSisa' => 'High',
                'target' => 'Replikasi ke unit bor serupa',
                'totalPopulasi' => '8',
                'targetReplikasiKomitmen' => '6',
                'aktualReplikasi' => '1',
                'satuan' => 'Unit',
                'jumlahMitraReplikasi' => '1',
                'tercoverBehira' => 'Belum',
                'potensiPeningkatanLevelEfektivitas' => 'Level 3',
                'pengendalianPeningkatanLevelEfektivitas' => 'Standarisasi prosedur penarikan unit bor',
                'targetStandarisasiDueDate' => '2026-12-15',
            ],
        ];
    }
}
