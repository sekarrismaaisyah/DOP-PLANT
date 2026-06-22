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
        $sheet->setTitle($emailType === AutoBannedHsctEmailType::UnbanRequest ? 'List Unban' : 'List Banned');

        $headers = $emailType === AutoBannedHsctEmailType::UnbanRequest
            ? ['No', 'Nama', 'SID', 'Site', 'Perusahaan', 'Alasan Banned', 'Alasan Pengajuan', 'Diajukan Oleh']
            : ['No', 'Nama', 'SID', 'Site', 'Perusahaan', 'Alasan Banned'];

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

            if ($emailType === AutoBannedHsctEmailType::UnbanRequest) {
                $sheet->fromArray([
                    $index + 1,
                    $employee['karyawan'] ?? '',
                    $employee['sid'] ?? '',
                    $employee['site'] ?? '',
                    $employee['perusahaan'] ?? '',
                    $employee['reason'] ?? '',
                    $employee['alasan_pengajuan'] ?? '',
                    $employee['submitted_by'] ?? '',
                ], null, 'A'.$row);
            } else {
                $sheet->fromArray([
                    $index + 1,
                    $employee['karyawan'] ?? '',
                    $employee['sid'] ?? '',
                    $employee['site'] ?? '',
                    $employee['perusahaan'] ?? '',
                    $employee['reason'] ?? '',
                ], null, 'A'.$row);
            }
        }

        $lastColumn = $emailType === AutoBannedHsctEmailType::UnbanRequest ? 'H' : 'F';
        foreach (range('A', $lastColumn) as $column) {
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
        if ($emailType === AutoBannedHsctEmailType::UnbanRequest) {
            $weekSlug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $week) ?: 'batch');

            return sprintf(
                'auto-banned-hsct-unban-%s-%s.xlsx',
                $weekSlug,
                preg_replace('/[^0-9]+/', '', $year) ?: 'all',
            );
        }

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
