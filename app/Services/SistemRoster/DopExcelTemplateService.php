<?php

declare(strict_types=1);

namespace App\Services\SistemRoster;

/**
 * Header baris 1 template Excel DOP (harus sama dengan downloadTemplate() di DOPController).
 *
 * @phpstan-type HeaderList list<string>
 */
final class DopExcelTemplateService
{
    /** Jumlah kolom wajib (A–S) */
    public const REQUIRED_COLUMN_COUNT = 19;

    /**
     * Urutan judul kolom persis seperti template ekspor.
     *
     * @var HeaderList
     */
    public const EXPECTED_HEADERS = [
        'Tanggal',
        'Pekerjaan',
        'Aktivitas',
        'Site',
        'Unit ID',
        'Lokasi',
        'Latitude',
        'Longitude',
        'Detail Lokasi',
        'Potensi Risiko',
        'Pengendalian Bahaya',
        'Catatan',
        'CCTV IDs (pisahkan dengan koma)',
        'PIC Berau Coal - Shift',
        'PIC Berau Coal - Nama PIC',
        'PIC Berau Coal - Layer',
        'Pengawas Mitra Kerja - Shift',
        'Pengawas Mitra Kerja - Nama Pengawas',
        'Pengawas Mitra Kerja - Layer',
    ];

    /**
     * Validasi baris pertama (header) file impor. Mengembalikan daftar pesan error; kosong jika valid.
     *
     * @param  list<mixed>|null  $headerRow
     * @return list<string>
     */
    public function validateImportHeaders(?array $headerRow): array
    {
        $errors = [];
        if ($headerRow === null) {
            return ['Baris header tidak ditemukan. Gunakan file dari menu Download Template.'];
        }

        $padded = array_pad(array_slice($headerRow, 0, self::REQUIRED_COLUMN_COUNT), self::REQUIRED_COLUMN_COUNT, null);
        $extraCells = array_slice($headerRow, self::REQUIRED_COLUMN_COUNT);
        foreach ($extraCells as $idx => $cell) {
            if (trim((string) $cell) !== '') {
                $errors[] = 'Terdapat kolom tambahan setelah kolom ke-' . self::REQUIRED_COLUMN_COUNT . '. Hapus kolom di luar template DOP.';

                break;
            }
        }

        for ($i = 0; $i < self::REQUIRED_COLUMN_COUNT; $i++) {
            $expected = self::EXPECTED_HEADERS[$i];
            $actualRaw = $padded[$i];
            $actualNorm = $this->normalizeHeaderCell($actualRaw);
            $expectedNorm = $this->normalizeHeaderCell($expected);
            if ($actualNorm === '') {
                $errors[] = 'Kolom ' . ($i + 1) . ' kosong — seharusnya: "' . $expected . '".';

                continue;
            }
            if ($actualNorm !== $expectedNorm) {
                $errors[] = 'Kolom ' . ($i + 1) . ' tidak sesuai. Wajib: "' . $expected . '" — pada file: "' . trim((string) $actualRaw) . '".';
            }
        }

        return $errors;
    }

    private function normalizeHeaderCell(mixed $value): string
    {
        $s = trim((string) $value);
        $s = preg_replace('/\s+/u', ' ', $s) ?? $s;

        return mb_strtolower($s, 'UTF-8');
    }
}
