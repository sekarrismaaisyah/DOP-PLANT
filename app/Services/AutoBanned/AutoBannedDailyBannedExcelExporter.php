<?php

declare(strict_types=1);

namespace App\Services\AutoBanned;

use App\Support\AutoBanned\ScrDailyBannedColumns;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AutoBannedDailyBannedExcelExporter
{
    /**
     * @param  array<int, array<string, mixed>>  $rows
     * @return array{path: string, filename: string}
     */
    public function createTempFile(
        array $rows,
        string $filterDate,
        string $filterShift,
    ): array {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Daily Banned');

        $headers = [
            'No',
            'SID',
            'NIK',
            'Nama',
            'Perusahaan',
            'Site',
            'Status Banned',
            'Alasan Banned',
            'HZR',
            'INS',
            'OBS/OAK',
            'RFID',
            'Kualitas RFID',
            'SAP Label',
            'Status Onsite',
            'Sumber Aktivitas',
            'Shift',
            'Tanggal Filter',
        ];

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

        foreach ($rows as $index => $row) {
            $sheet->fromArray([
                $index + 1,
                $row['sid'] ?? '',
                $row['nik'] ?? '',
                $row['nama'] ?? '',
                $row['perusahaan'] ?? '',
                $row['site'] ?? '',
                $row['banned_status'] ?? '',
                $row['banned_reason'] ?? '',
                $row['hzr'] ?? '',
                $row['ins'] ?? '',
                $row['obs_oak'] ?? '',
                $row['rfid'] ?? '',
                $row['rfid_quality'] ?? '',
                $row['sap_label'] ?? '',
                $row['onsite_status'] ?? '',
                $row['activity_source'] ?? '',
                $row['filter_shift'] ?? '',
                $row['filter_date'] ?? '',
            ], null, 'A'.($index + 2));
        }

        foreach (range('A', 'R') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = $this->buildFilename($filterDate, $filterShift);
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

    private function buildFilename(string $filterDate, string $filterShift): string
    {
        $shiftSlug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $filterShift) ?: 'shift');

        return sprintf(
            'daily-banned-%s-%s.xlsx',
            $filterDate,
            trim($shiftSlug, '-'),
        );
    }

    private function createTempPath(): string
    {
        $tempPath = tempnam(sys_get_temp_dir(), 'ab_daily_');
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

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\ScrDailyBanned>  $collection
     * @return array<int, array<string, mixed>>
     */
    public function formatRowsFromModels($collection): array
    {
        return $collection
            ->map(fn ($row): array => [
                'sid' => (string) ($row->{ScrDailyBannedColumns::SID} ?? ''),
                'nik' => (string) ($row->{ScrDailyBannedColumns::NIK} ?? ''),
                'nama' => (string) ($row->{ScrDailyBannedColumns::NAMA} ?? ''),
                'perusahaan' => (string) ($row->{ScrDailyBannedColumns::PERUSAHAAN} ?? ''),
                'site' => (string) ($row->{ScrDailyBannedColumns::SITE} ?? ''),
                'banned_status' => (string) ($row->{ScrDailyBannedColumns::BANNED_STATUS} ?? ''),
                'banned_reason' => (string) ($row->{ScrDailyBannedColumns::BANNED_REASON} ?? ''),
                'hzr' => (string) ($row->{ScrDailyBannedColumns::HZR} ?? ''),
                'ins' => (string) ($row->{ScrDailyBannedColumns::INS} ?? ''),
                'obs_oak' => (string) ($row->{ScrDailyBannedColumns::OBS_OAK} ?? ''),
                'rfid' => (string) ($row->{ScrDailyBannedColumns::RFID} ?? ''),
                'rfid_quality' => (string) ($row->{ScrDailyBannedColumns::RFID_QUALITY} ?? ''),
                'sap_label' => (string) ($row->{ScrDailyBannedColumns::SAP_LABEL} ?? ''),
                'onsite_status' => (string) ($row->{ScrDailyBannedColumns::ONSITE_STATUS} ?? ''),
                'activity_source' => (string) ($row->{ScrDailyBannedColumns::ACTIVITY_SOURCE} ?? ''),
                'filter_shift' => (string) ($row->filter_shift ?? ''),
                'filter_date' => $row->filter_date?->toDateString() ?? (string) ($row->filter_date ?? ''),
            ])
            ->values()
            ->all();
    }
}
