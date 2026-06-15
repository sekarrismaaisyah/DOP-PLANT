<?php

declare(strict_types=1);

namespace App\Services\Hira;

final class HiraImprovementRekayasaSampleData
{
    /**
     * @return list<array<string, string>>
     */
    public static function rows(): array
    {
        return [
            [
                'aktivitas' => 'Mobilisasi Demobilisasi',
                'sitePerusahaan' => 'BUMA BMO 2',
                'pengendalianRekayasa' => 'Penambahan trip stik portable pada unit low boy',
                'deteksi' => 'Tidak mendeteksi',
                'intervensi' => 'Menahan/mengurangi dampak',
                'prediksiPenurunanRisiko' => 'Turun 1 tangga',
                'penjelasanProsesKerja' => 'Trip Stick Portable adalah perangkat pengaman tambahan (portable safety device) yang dipasang di bagian pinggir atau atas low boy (trailer pengangkut alat berat atau material besar) untuk menahan muatan agar tidak bergeser atau tergelincir ke samping selama perjalanan.',
            ],
            [
                'aktivitas' => 'Eksplorasi',
                'sitePerusahaan' => 'G&E BC GMO',
                'pengendalianRekayasa' => 'Modifikasi Unit Bor Manual (TOHO, TDC, YBM) untuk metode Penarikan Unit Bor menggunakan Sling Winch',
                'deteksi' => 'Tidak mendeteksi',
                'intervensi' => 'Menahan/mengurangi dampak',
                'prediksiPenurunanRisiko' => 'Turun 1 tangga',
                'penjelasanProsesKerja' => 'Modifikasi unit bor manual untuk penarikan dengan sling winch meningkatkan keselamatan dan efisiensi, mengurangi risiko tertimpa unit bor, low back pain, terbentu unit bor serta mendukung mobilisasi yang aman dan andal.',
            ],
        ];
    }
}
