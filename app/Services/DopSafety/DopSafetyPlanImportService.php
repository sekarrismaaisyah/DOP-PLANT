<?php

declare(strict_types=1);

namespace App\Services\DopSafety;

use App\Enums\DopSafetyPlanStatus;
use App\Support\DopSafety\DopSafetyPlanTableStructure;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DopSafetyPlanImportService
{
    private const COL_SITE = 0;
    private const COL_COMPANY = 1;      // <-- BARU
    private const COL_DEPARTMENT = 2;   // <-- BARU
    private const COL_PLAN_DATE = 3;    // Geser jadi 3
    private const COL_SHIFT = 4;        // Geser jadi 4
    private const COL_ITEM_NO = 5;
    private const COL_UNIT_CODE = 6;
    private const COL_SECTION = 7;
    private const COL_LOCATION = 8;
    private const COL_JOB_DETAIL = 9;
    private const COL_WORK_PERMIT = 10;
    private const COL_TOOLS = 11;
    private const COL_WORKER_NAME = 12;
    private const COL_WORKER_SID = 13;
    private const COL_CCTV = 14;
    private const COL_GROUP_LEADER = 15;
    private const COL_GROUP_LEADER_SID = 16;
    private const COL_SECTION_HEAD = 17;
    private const COL_SECTION_HEAD_SID = 18;
    private const COL_SHE_LEADER = 19;
    private const COL_SHE_LEADER_SID = 20;
    private const COL_DEPT_HEAD = 21;
    private const COL_DEPT_HEAD_SID = 22;
    private const COL_PJA_BC = 23;
    private const COL_AUTH_LOCATION_DATE = 24;
    private const COL_CREATED_BY_NAME = 25;
    private const COL_CREATED_BY_POSITION = 26;
    private const COL_ACK_1_NAME = 27;
    private const COL_ACK_1_POSITION = 28;
    private const COL_ACK_2_NAME = 29;
    private const COL_ACK_2_POSITION = 30;
    private const COL_ACK_3_NAME = 31;
    private const COL_ACK_3_POSITION = 32;

    public function __construct(
        private readonly DopSafetyPlanExcelTemplateService $templateService,
        private readonly DopSafetyPlanPersistenceService $persistenceService,
    ) {}

    /**
     * @return array{imported: int, documents: int, errors: list<string>, header_invalid?: bool}
     */
    public function importFromFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $headerErrors = $this->templateService->validateImportHeaders($rows);
        if ($headerErrors !== []) {
            return [
                'imported' => 0,
                'documents' => 0,
                'errors' => $headerErrors,
                'header_invalid' => true,
            ];
        }

        $dataStartRow = $this->templateService->resolveDataStartRowIndex($rows);
        $dataRows = array_slice($rows, $dataStartRow);
        $grouped = [];
        $errors = [];
        $rowNumber = $dataStartRow + 1;

        foreach ($dataRows as $row) {
            if ($this->isEmptyRow($row)) {
                $rowNumber++;

                continue;
            }

            if ($this->isNoteRow($row)) {
                $rowNumber++;

                continue;
            }

            try {
                $parsed = $this->parseRow($row, $rowNumber);
                $key = $parsed['document_key'];
                if (! isset($grouped[$key])) {
                    $grouped[$key] = [
                        'header' => $parsed['header'],
                        'items' => [],
                    ];
                } else {
                    $grouped[$key]['header'] = $this->mergeHeader($grouped[$key]['header'], $parsed['header']);
                }
                $grouped[$key]['items'][] = $parsed['item'];
            } catch (\InvalidArgumentException $e) {
                $errors[] = $e->getMessage();
            }

            $rowNumber++;
        }

        $imported = 0;
        $documents = 0;

        foreach ($grouped as $group) {
            if ($group['items'] === []) {
                continue;
            }

            $items = array_values($group['items']);
            foreach ($items as $idx => &$item) {
                $item['item_no'] = $idx + 1;
            }
            unset($item);

            try {
                $this->persistenceService->upsertByDocumentKey(
                    $group['header'],
                    $items,
                    Auth::id(),
                );
                $imported += count($group['items']);
                $documents++;
            } catch (\Throwable $e) {
                $errors[] = 'Gagal simpan dokumen ' . ($group['header']['site'] ?? '') . ' / ' . ($group['header']['plan_date'] ?? '') . ': ' . $e->getMessage();
            }
        }

        return [
            'imported' => $imported,
            'documents' => $documents,
            'errors' => $errors,
            'header_invalid' => false,
        ];
    }

    /**
     * @param  list<mixed>  $row
     */
    private function isEmptyRow(array $row): bool
    {
        return empty(array_filter($row, static fn ($c) => $c !== null && trim((string) $c) !== ''));
    }

    /**
     * @param  list<mixed>  $row
     */
    private function isNoteRow(array $row): bool
    {
        $first = trim((string) ($row[0] ?? ''));

        return str_starts_with(mb_strtoupper($first), 'CATATAN');
    }

    /**
     * @param  list<mixed>  $row
     * @return array{document_key: string, header: array<string, mixed>, item: array<string, mixed>}
     */
    private function parseRow(array $row, int $rowNumber): array
    {
        $row = array_pad(
            array_slice($row, 0, DopSafetyPlanExcelTemplateService::requiredColumnCount()),
            DopSafetyPlanExcelTemplateService::requiredColumnCount(),
            null,
        );

        $site = trim((string) ($row[self::COL_SITE] ?? ''));
        $company = trim((string) ($row[self::COL_COMPANY] ?? ''));       // <-- TANGKAP COMPANY
        $department = trim((string) ($row[self::COL_DEPARTMENT] ?? ''));
        $planDateRaw = $row[self::COL_PLAN_DATE] ?? null;
        $shiftRaw = trim((string) ($row[self::COL_SHIFT] ?? ''));

        if ($site === '' || $company === '' || $department === '' || $planDateRaw === null || trim((string) $planDateRaw) === '') {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Site, Hari/Tanggal, dan Shift wajib diisi.");
        }

        $planDate = $this->parseDate($planDateRaw, $rowNumber);
        $shift = $this->parseShift($shiftRaw, $rowNumber);

        $sectionName = trim((string) ($row[self::COL_SECTION] ?? ''));
        $unitCode = trim((string) ($row[self::COL_UNIT_CODE] ?? ''));
        $location = trim((string) ($row[self::COL_LOCATION] ?? ''));
        $jobDetail = trim((string) ($row[self::COL_JOB_DETAIL] ?? ''));

        if ($sectionName === '' || $location === '' || $jobDetail === '') {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Section, Lokasi, dan Detail Pekerjaan wajib diisi.");
        }

        $allowedSections = config('dop_safety.sections', []);
        if (! in_array($sectionName, $allowedSections, true)) {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Section \"{$sectionName}\" tidak valid.");
        }

        $header = [
            'site' => $site,
            'company' => $company,       // <-- MASUKKAN KE HEADER
            'department' => $department,
            'plan_date' => $planDate,
            'shift' => $shift,
            'status' => DopSafetyPlanStatus::PendingApproval->value,
            'auth_location_date' => trim((string) ($row[self::COL_AUTH_LOCATION_DATE] ?? '')) ?: null,
            'created_by_name' => trim((string) ($row[self::COL_CREATED_BY_NAME] ?? '')) ?: null,
            'created_by_position' => trim((string) ($row[self::COL_CREATED_BY_POSITION] ?? '')) ?: null,
            'acknowledged_1_name' => trim((string) ($row[self::COL_ACK_1_NAME] ?? '')) ?: null,
            'acknowledged_1_position' => trim((string) ($row[self::COL_ACK_1_POSITION] ?? '')) ?: null,
            'acknowledged_2_name' => trim((string) ($row[self::COL_ACK_2_NAME] ?? '')) ?: null,
            'acknowledged_2_position' => trim((string) ($row[self::COL_ACK_2_POSITION] ?? '')) ?: null,
            'acknowledged_3_name' => trim((string) ($row[self::COL_ACK_3_NAME] ?? '')) ?: null,
            'acknowledged_3_position' => trim((string) ($row[self::COL_ACK_3_POSITION] ?? '')) ?: null,
        ];

        $item = [
            'item_no' => (int) ($row[self::COL_ITEM_NO] ?? 0) ?: null,
            'section_name' => $sectionName,
            'unit_code' => $unitCode !== '' ? $unitCode : 'N/A',
            'location' => $location,
            'job_detail' => $jobDetail,
            'work_permit' => trim((string) ($row[self::COL_WORK_PERMIT] ?? '')) ?: 'N/A',
            'tools' => $this->templateService->parseListCell($row[self::COL_TOOLS] ?? null),
            'workers' => DopSafetyPlanTableStructure::parseWorkersFromCells(
                $row[self::COL_WORKER_NAME] ?? null,
                $row[self::COL_WORKER_SID] ?? null,
            ),
            'cctv' => trim((string) ($row[self::COL_CCTV] ?? '')) ?: null,
            'group_leader' => trim((string) ($row[self::COL_GROUP_LEADER] ?? '')) ?: null,
            'group_leader_sid' => trim((string) ($row[self::COL_GROUP_LEADER_SID] ?? '')) ?: null,
            'section_head' => trim((string) ($row[self::COL_SECTION_HEAD] ?? '')) ?: null,
            'section_head_sid' => trim((string) ($row[self::COL_SECTION_HEAD_SID] ?? '')) ?: null,
            'she_leader' => trim((string) ($row[self::COL_SHE_LEADER] ?? '')) ?: null,
            'she_leader_sid' => trim((string) ($row[self::COL_SHE_LEADER_SID] ?? '')) ?: null,
            'dept_head' => trim((string) ($row[self::COL_DEPT_HEAD] ?? '')) ?: null,
            'dept_head_sid' => trim((string) ($row[self::COL_DEPT_HEAD_SID] ?? '')) ?: null,
            'pja_bc' => trim((string) ($row[self::COL_PJA_BC] ?? '')) ?: null,
        ];

        return [
            'document_key' => $site . '|' . $company . '|' . $department . '|' . $planDate . '|' . $shift,
            'header' => $header,
            'item' => $item,
        ];
    }

    private function parseDate(mixed $value, int $rowNumber): string
    {
        if (is_numeric($value)) {
            return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
        }

        $parsed = date_create((string) $value);
        if ($parsed === false) {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Format tanggal tidak valid.");
        }

        return $parsed->format('Y-m-d');
    }

    private function parseShift(string $value, int $rowNumber): int
    {
        $normalized = preg_replace('/\D/', '', $value) ?? '';
        $shift = (int) ($normalized !== '' ? $normalized : $value);

        if (! in_array($shift, [1, 2], true)) {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Shift harus 1 atau 2.");
        }

        return $shift;
    }

    /**
     * @param  array<string, mixed>  $existing
     * @param  array<string, mixed>  $incoming
     * @return array<string, mixed>
     */
    private function mergeHeader(array $existing, array $incoming): array
    {
        foreach ($incoming as $key => $value) {
            if (in_array($key, ['site', 'company', 'department', 'plan_date', 'shift', 'status'], true)) {
                continue;
            }
            if ($value !== null && $value !== '') {
                $existing[$key] = $value;
            }
        }

        return $existing;
    }
}
