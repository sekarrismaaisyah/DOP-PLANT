<?php

declare(strict_types=1);

namespace App\Services\DopSafety;

use App\Support\DopSafety\DopSafetyPlanTableStructure;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * Template & parser Excel item pekerjaan saja (untuk form create/edit DOP).
 */
final class DopSafetyPlanItemsExcelService
{
    private const COL_ITEM_NO = 0;

    private const COL_UNIT_CODE = 1;

    private const COL_SECTION = 2;

    private const COL_LOCATION = 3;

    private const COL_JOB_DETAIL = 4;

    private const COL_WORK_PERMIT = 5;

    private const COL_TOOLS = 6;

    private const COL_WORKER_NAME = 7;

    private const COL_WORKER_SID = 8;

    private const COL_CCTV = 9;

    private const COL_GROUP_LEADER = 10;

    private const COL_GROUP_LEADER_SID = 11;

    private const COL_SECTION_HEAD = 12;

    private const COL_SECTION_HEAD_SID = 13;

    private const COL_SHE_LEADER = 14;

    private const COL_SHE_LEADER_SID = 15;

    private const COL_DEPT_HEAD = 16;

    private const COL_DEPT_HEAD_SID = 17;

    private const COL_PJA_BC = 18;

    public function __construct(
        private readonly DopSafetyPlanExcelTemplateService $excelTemplateService,
    ) {}

    public function buildSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Item Pekerjaan');

        $this->writeTableStructureHeaders($sheet);
        $this->writeExampleRows($sheet);
        $this->writeNotes($sheet);

        return $spreadsheet;
    }

    /**
     * @return array{items: list<array<string, string>>, errors: list<string>, header_invalid: bool}
     */
    public function parseFromFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $headerErrors = $this->validateStructuralHeaders($rows);
        if ($headerErrors !== []) {
            return [
                'items' => [],
                'errors' => $headerErrors,
                'header_invalid' => true,
            ];
        }

        $dataStartRow = DopSafetyPlanTableStructure::EXCEL_DATA_START_ROW - 1;
        $dataRows = array_slice($rows, $dataStartRow);
        $items = [];
        $errors = [];
        $rowNumber = DopSafetyPlanTableStructure::EXCEL_DATA_START_ROW;

        foreach ($dataRows as $row) {
            if (! is_array($row)) {
                $rowNumber++;

                continue;
            }

            if ($this->isEmptyRow($row)) {
                $rowNumber++;

                continue;
            }

            if ($this->isNoteRow($row)) {
                break;
            }

            try {
                $items[] = $this->parseRow($row, $rowNumber);
            } catch (\InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }

            $rowNumber++;
        }

        if ($items === [] && $errors === []) {
            $errors[] = 'Tidak ada baris item pekerjaan yang dapat diimport.';
        }

        return [
            'items' => $items,
            'errors' => $errors,
            'header_invalid' => false,
        ];
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
        $shiftStart = DopSafetyPlanTableStructure::excelItemsOnlyShiftStartColumn();

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

        $dataStartCol = DopSafetyPlanTableStructure::EXCEL_ITEMS_ONLY_DATA_START_COLUMN;
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

    /**
     * @param  list<mixed>  $row
     * @return array<string, string>
     */
    private function parseRow(array $row, int $rowNumber): array
    {
        $row = array_pad(
            array_slice($row, 0, DopSafetyPlanTableStructure::DATA_COLUMN_COUNT),
            DopSafetyPlanTableStructure::DATA_COLUMN_COUNT,
            null,
        );

        $sectionName = trim((string) ($row[self::COL_SECTION] ?? ''));
        $location = trim((string) ($row[self::COL_LOCATION] ?? ''));
        $jobDetail = trim((string) ($row[self::COL_JOB_DETAIL] ?? ''));

        if ($sectionName === '' || $location === '' || $jobDetail === '') {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Section, Lokasi, dan Detail Pekerjaan wajib diisi.");
        }

        $allowedSections = config('dop_safety.sections', []);
        if (! in_array($sectionName, $allowedSections, true)) {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Section \"{$sectionName}\" tidak valid.");
        }

        $workers = DopSafetyPlanTableStructure::workersToDisplayCells(
            DopSafetyPlanTableStructure::parseWorkersFromCells(
                $row[self::COL_WORKER_NAME] ?? null,
                $row[self::COL_WORKER_SID] ?? null,
            ),
        );

        $tools = $this->excelTemplateService->parseListCell($row[self::COL_TOOLS] ?? null);

        return [
            'section_name' => $sectionName,
            'unit_code' => trim((string) ($row[self::COL_UNIT_CODE] ?? '')),
            'location' => $location,
            'job_detail' => $jobDetail,
            'work_permit' => trim((string) ($row[self::COL_WORK_PERMIT] ?? '')) ?: 'N/A',
            'tools' => implode(', ', $tools),
            'worker_names' => $workers['names'],
            'worker_sids' => $workers['sids'],
            'cctv' => trim((string) ($row[self::COL_CCTV] ?? '')),
            'group_leader' => trim((string) ($row[self::COL_GROUP_LEADER] ?? '')),
            'group_leader_sid' => trim((string) ($row[self::COL_GROUP_LEADER_SID] ?? '')),
            'section_head' => trim((string) ($row[self::COL_SECTION_HEAD] ?? '')),
            'section_head_sid' => trim((string) ($row[self::COL_SECTION_HEAD_SID] ?? '')),
            'she_leader' => trim((string) ($row[self::COL_SHE_LEADER] ?? '')),
            'she_leader_sid' => trim((string) ($row[self::COL_SHE_LEADER_SID] ?? '')),
            'dept_head' => trim((string) ($row[self::COL_DEPT_HEAD] ?? '')),
            'dept_head_sid' => trim((string) ($row[self::COL_DEPT_HEAD_SID] ?? '')),
            'pja_bc' => trim((string) ($row[self::COL_PJA_BC] ?? '')),
        ];
    }

    /**
     * @param  list<mixed>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        $slice = array_slice($row, 0, DopSafetyPlanTableStructure::DATA_COLUMN_COUNT);

        return empty(array_filter($slice, static fn ($c) => $c !== null && trim((string) $c) !== ''));
    }

    /**
     * @param  list<mixed>  $row
     */
    private function isNoteRow(array $row): bool
    {
        $first = trim((string) ($row[0] ?? ''));

        return str_starts_with(mb_strtoupper($first), 'CATATAN');
    }

    private function writeTableStructureHeaders(Worksheet $sheet): void
    {
        $structure = DopSafetyPlanTableStructure::definition()['table_structure'];
        $dataStartCol = DopSafetyPlanTableStructure::EXCEL_ITEMS_ONLY_DATA_START_COLUMN;
        $shiftStartCol = DopSafetyPlanTableStructure::excelItemsOnlyShiftStartColumn();
        $shiftSpan = DopSafetyPlanTableStructure::EXCEL_SHIFT_SECTION_COLSPAN;
        $shiftEndCol = $shiftStartCol + $shiftSpan - 1;

        $headerStyle = $this->headerStyle('F3F4F6', '111827');
        $subHeaderStyle = $this->headerStyle('FFFFFF', '374151');

        $shift = $structure['shifts'][0] ?? ['name' => 'SHIFT 1'];
        $shiftCell = $this->cellRef($shiftStartCol, DopSafetyPlanTableStructure::EXCEL_SHIFT_ROW);
        $sheet->setCellValue($shiftCell, (string) ($shift['name'] ?? 'SHIFT 1'));
        $sheet->mergeCells(
            $this->cellRef($shiftStartCol, DopSafetyPlanTableStructure::EXCEL_SHIFT_ROW)
            . ':' . $this->cellRef($shiftEndCol, DopSafetyPlanTableStructure::EXCEL_SHIFT_ROW),
        );
        $sheet->getStyle($shiftCell)->applyFromArray($headerStyle);

        $section = $structure['sections'][0] ?? ['name' => 'FIELD TRACK'];
        $sectionCell = $this->cellRef($shiftStartCol, DopSafetyPlanTableStructure::EXCEL_SECTION_ROW);
        $sheet->setCellValue($sectionCell, (string) ($section['name'] ?? 'FIELD TRACK'));
        $sheet->mergeCells(
            $this->cellRef($shiftStartCol, DopSafetyPlanTableStructure::EXCEL_SECTION_ROW)
            . ':' . $this->cellRef($shiftEndCol, DopSafetyPlanTableStructure::EXCEL_SECTION_ROW),
        );
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
                '1', 'DT4304', 'FIELD TRACK', 'WORKSHOP', 'Ganti roller track unit DT4304', 'N/A',
                'Chain Block, Impact Wrench', 'Ahmad; Budi', 'SID001; SID002', 'CCTV-12',
                'Rudi GL', 'GL001', 'Siti SH', 'SH001', 'Hendra SHE', 'SHE001', 'Pak DH Plant', 'DH001', 'Pak PJA BC',
            ],
            [
                '2', 'EX1296', 'FIELD TRACK', 'PIT WEST', 'Overhaul final drive EX1296', 'Hot Work',
                'Torque Wrench, Crane 10T', 'Candra', 'SID003', 'CCTV-05',
                'Rudi GL', 'GL001', 'Siti SH', 'SH001', 'Hendra SHE', 'SHE001', 'Pak DH Plant', 'DH001', 'Pak PJA BC',
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
        $sheet->setCellValue('A' . $startRow, 'CATATAN TEMPLATE ITEM PEKERJAAN:');
        $sheet->getStyle('A' . $startRow)->getFont()->setBold(true);

        $notes = [
            '1. File ini hanya berisi item pekerjaan — isi Header Dokumen di form web.',
            '2. Baris 1–4 = header tabel. Data mulai baris ' . DopSafetyPlanTableStructure::EXCEL_DATA_START_ROW . '.',
            '3. Section: FIELD TRACK, WORKSHOP TRACK, FIELD WHEEL, MAIN WORKSHOP WHEEL, FIELD SPEX, WORKSHOP TYRE, WORKSHOP SPEX, WORKSHOP FABRIKASI.',
            '4. LIST PEKERJA: kolom NAMA dan SID — pisahkan beberapa pekerja dengan titik koma (;).',
            '5. Alat Bantu: pisahkan dengan koma jika lebih dari satu.',
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
