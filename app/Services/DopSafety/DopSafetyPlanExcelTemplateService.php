<?php

declare(strict_types=1);

namespace App\Services\DopSafety;

use App\Support\DopSafety\DopSafetyPlanTableStructure;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Template Excel DOP Safety — layout baris/kolom mengikuti table_structure JSON.
 *
 * Baris 1      : SHIFT (17 kolom, tanpa No.)
 * Baris 2      : SECTION (17 kolom, tanpa No.)
 * Baris 3–4    : Header kolom (rowspan 2 + LIST PEKERJA colspan 2)
 * Baris 5+     : Data (30 kolom: meta 3 + tabel 18 + otorisasi 9)
 */
final class DopSafetyPlanExcelTemplateService
{
    public static function requiredColumnCount(): int
    {
        return DopSafetyPlanTableStructure::totalImportColumnCount();
    }

    /**
     * @return list<string>
     */
    public static function expectedHeaders(): array
    {
        return DopSafetyPlanTableStructure::flatImportHeaders();
    }

    public function buildSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('DOP Items');

        $this->writeTableStructureHeaders($sheet);
        $this->writeExampleRows($sheet);
        $this->writeNotes($sheet);

        return $spreadsheet;
    }

    /**
     * @param  list<list<mixed>>  $rows
     * @return list<string>
     */
    public function validateImportHeaders(array $rows): array
    {
        return $this->validateStructuralHeaders($rows);
    }

    /**
     * @return list<string>
     */
    public function parseListCell(mixed $value): array
    {
        return DopSafetyPlanTableStructure::splitListCell($value);
    }

    public function resolveDataStartRowIndex(array $rows): int
    {
        return DopSafetyPlanTableStructure::EXCEL_DATA_START_ROW - 1;
    }

    /**
     * @param  list<list<mixed>>  $rows
     * @return list<string>
     */
    private function validateStructuralHeaders(array $rows): array
    {
        $errors = [];
        $structure = DopSafetyPlanTableStructure::definition()['table_structure'];
        $shiftRow = DopSafetyPlanTableStructure::EXCEL_SHIFT_ROW - 1;
        $sectionRow = DopSafetyPlanTableStructure::EXCEL_SECTION_ROW - 1;
        $colRow1 = DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1 - 1;
        $colRow2 = DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_2 - 1;

        $shiftStart = DopSafetyPlanTableStructure::excelShiftSectionStartColumn();
        $shiftName = (string) ($structure['shifts'][0]['name'] ?? 'SHIFT 1');
        $shiftCell = $rows[$shiftRow][$shiftStart - 1] ?? null;
        if ($this->normalizeHeaderCell($shiftCell) !== $this->normalizeHeaderCell($shiftName)) {
            $errors[] = 'Baris ' . DopSafetyPlanTableStructure::EXCEL_SHIFT_ROW . ' tidak sesuai — seharusnya banner "' . $shiftName . '".';
        }

        $sectionName = (string) ($structure['sections'][0]['name'] ?? 'FIELD TRACK');
        $sectionCell = $rows[$sectionRow][$shiftStart - 1] ?? null;
        if ($this->normalizeHeaderCell($sectionCell) !== $this->normalizeHeaderCell($sectionName)) {
            $errors[] = 'Baris ' . DopSafetyPlanTableStructure::EXCEL_SECTION_ROW . ' tidak sesuai — seharusnya banner "' . $sectionName . '".';
        }

        $dataStartCol = DopSafetyPlanTableStructure::EXCEL_DATA_START_COLUMN;
        $leafHeaders = DopSafetyPlanTableStructure::leafHeaders();
        $leafIndex = 0;

        foreach ($structure['columns'] as $column) {
            if (isset($column['sub_columns'])) {
                $parentCell = $rows[$colRow1][$dataStartCol + $leafIndex - 1] ?? null;
                if ($this->normalizeHeaderCell($parentCell) !== $this->normalizeHeaderCell((string) ($column['name'] ?? ''))) {
                    $errors[] = 'Header grup "' . ($column['name'] ?? '') . '" tidak ditemukan pada baris ' . DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1 . '.';
                }

                foreach ($column['sub_columns'] as $sub) {
                    $subCell = $rows[$colRow2][$dataStartCol + $leafIndex - 1] ?? null;
                    $expectedSub = (string) ($sub['name'] ?? '');
                    if ($this->normalizeHeaderCell($subCell) !== $this->normalizeHeaderCell($expectedSub)) {
                        $errors[] = 'Sub-kolom "' . $expectedSub . '" tidak sesuai pada baris ' . DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_2 . '.';
                    }
                    $leafIndex++;
                }

                continue;
            }

            $cell = $rows[$colRow1][$dataStartCol + $leafIndex - 1] ?? null;
            $expected = (string) ($column['name'] ?? '');
            if ($this->normalizeHeaderCell($cell) !== $this->normalizeHeaderCell($expected)) {
                $errors[] = 'Kolom "' . $expected . '" tidak sesuai pada baris ' . DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1 . '.';
            }
            $leafIndex++;
        }

        return $errors;
    }

    private function writeTableStructureHeaders(Worksheet $sheet): void
    {
        $structure = DopSafetyPlanTableStructure::definition()['table_structure'];
        $dataStartCol = DopSafetyPlanTableStructure::EXCEL_DATA_START_COLUMN;
        $shiftStartCol = DopSafetyPlanTableStructure::excelShiftSectionStartColumn();
        $shiftSpan = DopSafetyPlanTableStructure::EXCEL_SHIFT_SECTION_COLSPAN;
        $shiftEndCol = $shiftStartCol + $shiftSpan - 1;
        $authStartCol = DopSafetyPlanTableStructure::excelAuthorizationStartColumn();

        $headerStyle = $this->headerStyle('3952BC', 'FFFFFF');
        $subHeaderStyle = $this->headerStyle('E8EAF6', '1A237E');
        $metaStyle = $this->headerStyle('D5E8F6', '1A237E');

        foreach (DopSafetyPlanTableStructure::documentMetaHeaders() as $i => $label) {
            $col = $i + 1;
            $cell = $this->cellRef($col, 1);
            $sheet->setCellValue($cell, $label);
            $sheet->mergeCells($this->cellRef($col, 1) . ':' . $this->cellRef($col, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_2));
            $sheet->getStyle($cell)->applyFromArray($metaStyle);
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setWidth(max(14, mb_strlen($label) + 6));
        }

        foreach (DopSafetyPlanTableStructure::authorizationHeaders() as $i => $label) {
            $col = $authStartCol + $i;
            $cell = $this->cellRef($col, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1);
            $sheet->setCellValue($cell, $label);
            $sheet->mergeCells(
                $this->cellRef($col, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1)
                . ':' . $this->cellRef($col, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_2),
            );
            $sheet->getStyle($cell)->applyFromArray($subHeaderStyle);
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($col))->setWidth(max(14, min(30, mb_strlen($label) + 4)));
        }

        $shift = $structure['shifts'][0] ?? ['name' => 'SHIFT 1'];
        $shiftCell = $this->cellRef($shiftStartCol, DopSafetyPlanTableStructure::EXCEL_SHIFT_ROW);
        $sheet->setCellValue($shiftCell, (string) ($shift['name'] ?? 'SHIFT 1'));
        $sheet->mergeCells($this->cellRef($shiftStartCol, DopSafetyPlanTableStructure::EXCEL_SHIFT_ROW) . ':' . $this->cellRef($shiftEndCol, DopSafetyPlanTableStructure::EXCEL_SHIFT_ROW));
        $sheet->getStyle($shiftCell)->applyFromArray($headerStyle);

        $section = $structure['sections'][0] ?? ['name' => 'FIELD TRACK'];
        $sectionCell = $this->cellRef($shiftStartCol, DopSafetyPlanTableStructure::EXCEL_SECTION_ROW);
        $sheet->setCellValue($sectionCell, (string) ($section['name'] ?? 'FIELD TRACK'));
        $sheet->mergeCells($this->cellRef($shiftStartCol, DopSafetyPlanTableStructure::EXCEL_SECTION_ROW) . ':' . $this->cellRef($shiftEndCol, DopSafetyPlanTableStructure::EXCEL_SECTION_ROW));
        $sheet->getStyle($sectionCell)->applyFromArray($headerStyle);

        $colIndex = $dataStartCol;
        foreach ($structure['columns'] as $column) {
            if (isset($column['sub_columns'])) {
                $subCount = count($column['sub_columns']);
                $startCol = $colIndex;
                $endCol = $colIndex + $subCount - 1;
                $parentCell = $this->cellRef($startCol, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1);
                $sheet->setCellValue($parentCell, (string) ($column['name'] ?? ''));
                $sheet->mergeCells(
                    $this->cellRef($startCol, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1)
                    . ':' . $this->cellRef($endCol, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1),
                );
                $sheet->getStyle($parentCell)->applyFromArray($headerStyle);

                foreach ($column['sub_columns'] as $sub) {
                    $cell = $this->cellRef($colIndex, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_2);
                    $sheet->setCellValue($cell, (string) ($sub['name'] ?? ''));
                    $sheet->getStyle($cell)->applyFromArray($subHeaderStyle);
                    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setWidth(14);
                    $colIndex++;
                }

                continue;
            }

            $cell = $this->cellRef($colIndex, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1);
            $sheet->setCellValue($cell, (string) ($column['name'] ?? ''));
            $sheet->mergeCells(
                $this->cellRef($colIndex, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_1)
                . ':' . $this->cellRef($colIndex, DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_2),
            );
            $sheet->getStyle($cell)->applyFromArray($headerStyle);
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colIndex))->setWidth(
                max(12, min(28, mb_strlen((string) ($column['name'] ?? '')) + 4)),
            );
            $colIndex++;
        }

        for ($row = DopSafetyPlanTableStructure::EXCEL_SHIFT_ROW; $row <= DopSafetyPlanTableStructure::EXCEL_COLUMN_HEADER_ROW_2; $row++) {
            $sheet->getRowDimension($row)->setRowHeight(28);
        }

        $sheet->freezePane($this->cellRef(1, DopSafetyPlanTableStructure::EXCEL_DATA_START_ROW));
    }

    private function writeExampleRows(Worksheet $sheet): void
    {
        $examples = [
            [
                'GMO', 'PT PAMA', 'PLANT', '2026-06-24', '1',
                '1', 'DT4304', 'FIELD TRACK', 'WORKSHOP', 'Ganti roller track unit DT4304', 'N/A',
                'Chain Block, Impact Wrench', 'Ahmad; Budi', 'SID001; SID002', 'CCTV-12',
                'Rudi GL', 'GL001', 'Siti SH', 'SH001', 'Hendra SHE', 'SHE001', 'Pak DH Plant', 'DH001', 'Pak PJA BC',
                'GMO, 23 Jun 2026', 'Budi GL', 'Group Leader Wheel/Track',
                'Siti SH', 'Section Head Track', 'Dept Head Plant', 'Dept. Head Plant',
                'Supt Safety BC', 'Supt Safety BC',
            ],
            [
                'GMO', 'PT PAMA', 'PLANT', '2026-06-24', '1',
                '2', 'EX1296', 'FIELD TRACK', 'PIT WEST', 'Overhaul final drive EX1296', 'Hot Work',
                'Torque Wrench, Crane 10T', 'Candra', 'SID003', 'CCTV-05',
                'Rudi GL', 'GL001', 'Siti SH', 'SH001', 'Hendra SHE', 'SHE001', 'Pak DH Plant', 'DH001', 'Pak PJA BC',
                '', '', '', '', '', '', '', '', '',
            ],
        ];

        $startRow = DopSafetyPlanTableStructure::EXCEL_DATA_START_ROW;
        foreach ($examples as $rowIdx => $row) {
            $excelRow = $rowIdx + $startRow;
            foreach ($row as $colIdx => $value) {
                $sheet->setCellValue($this->cellRef($colIdx + 1, $excelRow), $value);
            }
        }
    }

    private function writeNotes(Worksheet $sheet): void
    {
        $startRow = DopSafetyPlanTableStructure::EXCEL_DATA_START_ROW + 4;
        $sheet->setCellValue('A' . $startRow, 'CATATAN TEMPLATE DOP:');
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true);

        $notes = [
            '1. Baris 1–4 = header tabel (SHIFT, SECTION, kolom). Data mulai baris ' . DopSafetyPlanTableStructure::EXCEL_DATA_START_ROW . '.',
            '2. Site + Hari/Tanggal + Shift yang sama = satu dokumen DOP.',
            '3. Shift: isi angka 1 atau 2.',
            '4. Section: FIELD TRACK, WORKSHOP TRACK, FIELD WHEEL, MAIN WORKSHOP WHEEL, FIELD SPEX, WORKSHOP TYRE, WORKSHOP SPEX, WORKSHOP FABRIKASI.',
            '5. LIST PEKERJA: kolom NAMA dan SID — pisahkan beberapa pekerja dengan titik koma (;).',
            '6. Kolom otorisasi dokumen cukup diisi pada baris pertama tiap dokumen.',
            '7. ' . config('dop_safety.disclaimer'),
        ];

        foreach ($notes as $i => $note) {
            $sheet->setCellValue('A' . ($startRow + 1 + $i), $note);
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function headerStyle(string $background, string $fontColor): array
    {
        return [
            'font' => ['bold' => true, 'color' => ['rgb' => $fontColor]],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => $background],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN],
            ],
        ];
    }

    private function cellRef(int $column, int $row): string
    {
        return Coordinate::stringFromColumnIndex($column) . $row;
    }

    private function normalizeHeaderCell(mixed $value): string
    {
        $s = trim((string) $value);
        $s = preg_replace('/\s+/u', ' ', $s) ?? $s;

        return mb_strtolower($s, 'UTF-8');
    }
}
