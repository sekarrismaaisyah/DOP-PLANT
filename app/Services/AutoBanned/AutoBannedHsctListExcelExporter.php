<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Enums\AutoBannedHsctEmailType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AutoBannedHsctListExcelExporter
{
    /**
     * @param  array<int, array{sid: string, karyawan: string, site: string, perusahaan: string, reason: string}>  $employees
     * @return array{path: string, filename: string}
     */
    public function createTempFile(
        array $employees,
        string $week,
        string $year,
        AutoBannedHsctEmailType $emailType,
        int $reminderNumber = 1,
    ): array {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('List Banned');

        $headers = ['No', 'Nama', 'SID', 'Site', 'Perusahaan', 'Alasan Banned'];
        foreach ($headers as $colIndex => $header) {
            $cell = $sheet->getCell([$colIndex + 1, 1]);
            $cell->setValue($header);
            $sheet->getStyle($cell->getCoordinate())->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => '1E293B']],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'F8FAFC'],
                ],
            ]);
        }

        foreach ($employees as $index => $employee) {
            $row = $index + 2;
            $sheet->fromArray([
                $index + 1,
                $employee['karyawan'] ?? '',
                $employee['sid'] ?? '',
                $employee['site'] ?? '',
                $employee['perusahaan'] ?? '',
                $employee['reason'] ?? '',
            ], null, 'A'.$row);
        }

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = $this->buildFilename($week, $year, $emailType, $reminderNumber);
        $path = $this->createTempPath();

        (new Xlsx($spreadsheet))->save($path);
        $spreadsheet->disconnectWorksheets();

        return [
            'path' => $path,
            'filename' => $filename,
        ];
    }

    public function deleteTempFile(?string $path): void
    {
        if ($path !== null && $path !== '' && is_file($path)) {
            @unlink($path);
        }
    }

    private function buildFilename(
        string $week,
        string $year,
        AutoBannedHsctEmailType $emailType,
        int $reminderNumber,
    ): string {
        $suffix = $emailType === AutoBannedHsctEmailType::Initial
            ? 'awal'
            : 'reminder-'.$reminderNumber;

        return sprintf(
            'auto-banned-hsct-%s-%s-%s.xlsx',
            strtolower($week),
            $year,
            $suffix,
        );
    }

    private function createTempPath(): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'ab_hsct_');
        if ($tempPath === false) {
            throw new \RuntimeException('Gagal menyiapkan file Excel sementara.');
        }

        $xlsxPath = $tempPath.'.xlsx';
        if (! rename($tempPath, $xlsxPath)) {
            @unlink($tempPath);

            throw new \RuntimeException('Gagal menyiapkan file Excel sementara.');
        }

        return $xlsxPath;
    }
}
