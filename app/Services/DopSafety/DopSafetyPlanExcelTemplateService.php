<?php

declare(strict_types=1);

namespace App\Services\DopSafety;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Template Excel DOP Safety — flat row per item pekerjaan.
 *
 * @phpstan-type HeaderList list<string>
 */
final class DopSafetyPlanExcelTemplateService
{
    public const REQUIRED_COLUMN_COUNT = 27;

    /** @var HeaderList */
    public const EXPECTED_HEADERS = [
        'Site',
        'Hari/Tanggal',
        'Shift',
        'Section Kerja',
        'No',
        'Kode Unit',
        'Kategori Unit',
        'Lokasi',
        'Detail Pekerjaan',
        'Izin Kerja',
        'Alat Bantu / Peralatan',
        'Pekerja',
        'CCTV',
        'Group Leader (L1)',
        'Section Head (L2)',
        'SHE Leader (L3)',
        'Dept. Head (L4)',
        'PJA BC',
        'Lokasi & Tanggal Pembuatan',
        'Dibuat Oleh — Nama',
        'Dibuat Oleh — Jabatan',
        'Mengetahui 1 — Nama',
        'Mengetahui 1 — Jabatan',
        'Mengetahui 2 — Nama',
        'Mengetahui 2 — Jabatan',
        'Mengetahui 3 — Nama',
        'Mengetahui 3 — Jabatan',
    ];

    public function buildSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DOP Items');

        $this->writeHeaders($sheet);
        $this->writeExampleRows($sheet);
        $this->writeNotes($sheet);

        return $spreadsheet;
    }

    /**
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

        for ($i = 0; $i < self::REQUIRED_COLUMN_COUNT; $i++) {
            $expected = self::EXPECTED_HEADERS[$i];
            $actualNorm = $this->normalizeHeaderCell($padded[$i]);
            $expectedNorm = $this->normalizeHeaderCell($expected);

            if ($actualNorm === '') {
                $errors[] = 'Kolom ' . ($i + 1) . ' kosong — seharusnya: "' . $expected . '".';

                continue;
            }

            if ($actualNorm !== $expectedNorm) {
                $errors[] = 'Kolom ' . ($i + 1) . ' tidak sesuai. Wajib: "' . $expected . '" — pada file: "' . trim((string) $padded[$i]) . '".';
            }
        }

        return $errors;
    }

    /**
     * @return list<string>
     */
    public function parseListCell(mixed $value): array
    {
        if ($value === null || trim((string) $value) === '') {
            return [];
        }

        $parts = preg_split('/[,;|]/', (string) $value) ?: [];

        return array_values(array_filter(array_map(static fn ($p) => trim((string) $p), $parts)));
    }

    private function writeHeaders(Worksheet $sheet): void
    {
        foreach (self::EXPECTED_HEADERS as $i => $header) {
            $cell = Coordinate::stringFromColumnIndex($i + 1) . '1';
            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '3952BC'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => ['borderStyle' => Border::BORDER_THIN],
                ],
            ]);
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($i + 1))->setWidth(max(14, min(28, mb_strlen($header) + 4)));
        }

        $sheet->getRowDimension(1)->setRowHeight(36);
        $sheet->freezePane('A2');
    }

    private function writeExampleRows(Worksheet $sheet): void
    {
        $examples = [
            [
                'GMO', '2026-06-24', '1', 'WORKSHOP TRACK', '1', 'DT4304', 'TRACK',
                'WORKSHOP', 'Ganti roller track unit DT4304', 'N/A',
                'Chain Block, Impact Wrench', 'Ahmad, Budi', 'CCTV-12',
                'Rudi GL', 'Siti SH', 'Hendra SHE', 'Pak DH Plant', 'Pak PJA BC',
                'GMO, 23 Jun 2026', 'Budi GL', 'Group Leader Wheel/Track',
                'Siti SH', 'Section Head Track', 'Dept Head Plant', 'Dept. Head Plant',
                'Supt Safety BC', 'Supt Safety BC',
            ],
            [
                'GMO', '2026-06-24', '1', 'MAIN WORKSHOP WHEEL', '2', 'EX1296', 'WHEEL',
                'PIT WEST', 'Overhaul final drive EX1296', 'Hot Work',
                'Torque Wrench, Crane 10T', 'Candra, Doni', 'CCTV-05',
                'Rudi GL', 'Siti SH', 'Hendra SHE', 'Pak DH Plant', 'Pak PJA BC',
                '', '', '', '', '', '', '', '', '',
            ],
        ];

        foreach ($examples as $rowIdx => $row) {
            $excelRow = $rowIdx + 2;
            foreach ($row as $colIdx => $value) {
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colIdx + 1) . $excelRow, $value);
            }
        }
    }

    private function writeNotes(Worksheet $sheet): void
    {
        $startRow = 5;
        $sheet->setCellValue('A' . $startRow, 'CATATAN TEMPLATE DOP:');
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true);

        $notes = [
            '1. Site + Hari/Tanggal + Shift yang sama = satu dokumen DOP.',
            '2. Shift: isi angka 1 atau 2.',
            '3. Section Kerja: FIELD TRACK, WORKSHOP TRACK, FIELD WHEEL, MAIN WORKSHOP WHEEL, FIELD SPEX, WORKSHOP TYRE, WORKSHOP SPEX, WORKSHOP FABRIKASI.',
            '4. Kategori Unit: TRACK / WHEEL / TYRE / SPEX.',
            '5. Alat Bantu & Pekerja: pisahkan dengan koma jika lebih dari satu.',
            '6. Kolom otorisasi dokumen (kolom 19–27) cukup diisi pada baris pertama tiap dokumen.',
            '7. ' . config('dop_safety.disclaimer'),
            '8. Label cetak: ' . config('dop_safety.watermark'),
        ];

        foreach ($notes as $i => $note) {
            $sheet->setCellValue('A' . ($startRow + 1 + $i), $note);
        }
    }

    private function normalizeHeaderCell(mixed $value): string
    {
        $s = trim((string) $value);
        $s = preg_replace('/\s+/u', ' ', $s) ?? $s;

        return mb_strtolower($s, 'UTF-8');
    }
}
