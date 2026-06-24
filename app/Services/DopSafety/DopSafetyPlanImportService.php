<?php

declare(strict_types=1);

namespace App\Services\DopSafety;

use App\Enums\DopSafetyPlanStatus;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class DopSafetyPlanImportService
{
    public function __construct(
        private readonly DopSafetyPlanExcelTemplateService $templateService,
        private readonly DopSafetyPlanPersistenceService $persistenceService,
    ) {}

    /**
     * @return array{imported: int, documents: int, errors: list<string>}
     */
    public function importFromFile(string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        $headerErrors = $this->templateService->validateImportHeaders($rows[0] ?? null);
        if ($headerErrors !== []) {
            return [
                'imported' => 0,
                'documents' => 0,
                'errors' => $headerErrors,
                'header_invalid' => true,
            ];
        }

        $dataRows = array_slice($rows, 1);
        $grouped = [];
        $errors = [];
        $rowNumber = 2;

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
        $row = array_pad(array_slice($row, 0, DopSafetyPlanExcelTemplateService::REQUIRED_COLUMN_COUNT), DopSafetyPlanExcelTemplateService::REQUIRED_COLUMN_COUNT, null);

        $site = trim((string) ($row[0] ?? ''));
        $planDateRaw = $row[1] ?? null;
        $shiftRaw = trim((string) ($row[2] ?? ''));

        if ($site === '' || $planDateRaw === null || trim((string) $planDateRaw) === '') {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Site, Hari/Tanggal, dan Shift wajib diisi.");
        }

        $planDate = $this->parseDate($planDateRaw, $rowNumber);
        $shift = $this->parseShift($shiftRaw, $rowNumber);

        $sectionName = trim((string) ($row[3] ?? ''));
        $unitCode = trim((string) ($row[5] ?? ''));
        $location = trim((string) ($row[7] ?? ''));
        $jobDetail = trim((string) ($row[8] ?? ''));

        if ($sectionName === '' || $location === '' || $jobDetail === '') {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Section Kerja, Lokasi, dan Detail Pekerjaan wajib diisi.");
        }

        $unitCategory = strtoupper(trim((string) ($row[6] ?? '')));
        $allowedCategories = config('dop_safety.unit_categories', []);
        if ($unitCategory !== '' && ! in_array($unitCategory, $allowedCategories, true)) {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Kategori Unit tidak valid ({$unitCategory}).");
        }

        $allowedSections = config('dop_safety.sections', []);
        if (! in_array($sectionName, $allowedSections, true)) {
            throw new \InvalidArgumentException("Baris {$rowNumber}: Section Kerja \"{$sectionName}\" tidak valid.");
        }

        $header = [
            'site' => $site,
            'plan_date' => $planDate,
            'shift' => $shift,
            'status' => DopSafetyPlanStatus::PendingApproval->value,
            'auth_location_date' => trim((string) ($row[18] ?? '')) ?: null,
            'created_by_name' => trim((string) ($row[19] ?? '')) ?: null,
            'created_by_position' => trim((string) ($row[20] ?? '')) ?: null,
            'acknowledged_1_name' => trim((string) ($row[21] ?? '')) ?: null,
            'acknowledged_1_position' => trim((string) ($row[22] ?? '')) ?: null,
            'acknowledged_2_name' => trim((string) ($row[23] ?? '')) ?: null,
            'acknowledged_2_position' => trim((string) ($row[24] ?? '')) ?: null,
            'acknowledged_3_name' => trim((string) ($row[25] ?? '')) ?: null,
            'acknowledged_3_position' => trim((string) ($row[26] ?? '')) ?: null,
        ];

        $item = [
            'item_no' => (int) ($row[4] ?? 0) ?: null,
            'section_name' => $sectionName,
            'unit_code' => $unitCode !== '' ? $unitCode : 'N/A',
            'unit_category' => $unitCategory !== '' ? $unitCategory : $this->inferCategory($sectionName),
            'location' => $location,
            'job_detail' => $jobDetail,
            'work_permit' => trim((string) ($row[9] ?? '')) ?: 'N/A',
            'tools' => $this->templateService->parseListCell($row[10] ?? null),
            'workers' => $this->templateService->parseListCell($row[11] ?? null),
            'cctv' => trim((string) ($row[12] ?? '')) ?: null,
            'group_leader' => trim((string) ($row[13] ?? '')) ?: null,
            'section_head' => trim((string) ($row[14] ?? '')) ?: null,
            'she_leader' => trim((string) ($row[15] ?? '')) ?: null,
            'dept_head' => trim((string) ($row[16] ?? '')) ?: null,
            'pja_bc' => trim((string) ($row[17] ?? '')) ?: null,
        ];

        return [
            'document_key' => $site . '|' . $planDate . '|' . $shift,
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

    private function inferCategory(string $sectionName): string
    {
        $upper = mb_strtoupper($sectionName);

        foreach (config('dop_safety.unit_categories', []) as $cat) {
            if (str_contains($upper, $cat)) {
                return $cat;
            }
        }

        return 'TRACK';
    }

    /**
     * @param  array<string, mixed>  $existing
     * @param  array<string, mixed>  $incoming
     * @return array<string, mixed>
     */
    private function mergeHeader(array $existing, array $incoming): array
    {
        foreach ($incoming as $key => $value) {
            if (in_array($key, ['site', 'plan_date', 'shift', 'status'], true)) {
                continue;
            }
            if ($value !== null && $value !== '') {
                $existing[$key] = $value;
            }
        }

        return $existing;
    }
}
