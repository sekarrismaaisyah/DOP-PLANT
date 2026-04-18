<?php

declare(strict_types=1);

namespace App\Services\PeerPressure;

use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Throwable;

/**
 * Impor Excel Validasi TBC — dipakai synchronous (opsional) atau dari {@see \App\Jobs\ImportValidasiTbcJob}.
 */
final class ValidasiTbcImportService
{
    public const SHEET_IMPORT = 'ValidasiTbc';

    /** Header baris 1 template — urutan harus sama persis. */
    public const HEADER_IMPORT = [
        'Validator',
        'Tasklist',
        'TobeConcernedHazard',
        'GR/PSPP',
        'Catatan',
        'No Item PSPP',
        'Kategori GR',
        'Kategori GR valid KPI',
        'Blindspot terlapor BC',
        'PIC Aktual (pelaku/pelanggar)',
        'Kronologi Singkat (summary dari Deskripsi)',
        'Rootcause Aktual',
        'Detail Rootcause Aktual',
        'Tindakan Perbaikan Aktual',
    ];

    private const BATCH_INSERT_SIZE = 250;

    /**
     * Membaca file .xlsx/.xls dari path absolut dan menyisipkan baris ke DB (batch).
     *
     * @throws \InvalidArgumentException template / data tidak valid
     * @throws Throwable dari PhpSpreadsheet / DB
     */
    public function importFromSpreadsheetPath(string $absolutePath): int
    {
        if (! is_readable($absolutePath)) {
            throw new \InvalidArgumentException('File tidak dapat dibaca.');
        }

        $spreadsheet = IOFactory::load($absolutePath);
        try {
            $sheet = $spreadsheet->getSheet(0);
            $rows = $sheet->toArray();
        } finally {
            $spreadsheet->disconnectWorksheets();
            unset($spreadsheet);
        }

        if ($rows === []) {
            throw new \InvalidArgumentException('File kosong atau tidak terbaca.');
        }

        $this->assertHeaderMatches($rows[0] ?? [], self::HEADER_IMPORT, 'sheet pertama');

        $dataRows = array_slice($rows, 1);
        $imported = 0;
        $batch = [];
        $now = now();

        foreach ($dataRows as $row) {
            if ($this->isRowEmpty($row)) {
                continue;
            }

            $attrs = $this->attributesFromImportRow($row);
            if ($this->isAttributesAllEmpty($attrs)) {
                continue;
            }

            $batch[] = array_merge($attrs, [
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            $imported++;

            if (count($batch) >= self::BATCH_INSERT_SIZE) {
                DB::table('validasi_tbc')->insert($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            DB::table('validasi_tbc')->insert($batch);
        }

        if ($imported === 0) {
            throw new \InvalidArgumentException(
                'Tidak ada baris data. Isi minimal satu baris (selain header).'
            );
        }

        return $imported;
    }

    /**
     * @param  list<mixed>|array<int|string, mixed>  $row
     * @return array<string, string|null>
     */
    private function attributesFromImportRow(array $row): array
    {
        $keys = [
            'validator',
            'tasklist',
            'to_be_concerned_hazard',
            'gr_pspp',
            'catatan',
            'no_item_pspp',
            'kategori_gr',
            'kategori_gr_valid_kpi',
            'blindspot_terlapor_bc',
            'pic_aktual',
            'kronologi_singkat',
            'rootcause_aktual',
            'detail_rootcause_aktual',
            'tindakan_perbaikan_aktual',
        ];

        $out = [];
        foreach ($keys as $i => $key) {
            $s = $this->cellStr($row, $i);
            $out[$key] = $s !== '' ? $s : null;
        }

        return $out;
    }

    /**
     * @param  array<string, string|null>  $attrs
     */
    private function isAttributesAllEmpty(array $attrs): bool
    {
        foreach ($attrs as $v) {
            if ($v !== null && $v !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<mixed>|array<int|string, mixed>  $row
     * @param  list<string>  $expected
     */
    private function assertHeaderMatches(array $row, array $expected, string $context): void
    {
        foreach ($expected as $i => $label) {
            $actual = isset($row[$i]) ? trim((string) $row[$i]) : '';
            if ($actual !== $label) {
                throw new \InvalidArgumentException(
                    'Template tidak sesuai (' . $context . '): kolom ' . ($i + 1) . ' harus "' . $label . '" (terbaca: "' . ($actual === '' ? '(kosong)' : $actual) . '"). Unduh template resmi.'
                );
            }
        }
    }

    /**
     * @param  list<mixed>|array<int|string, mixed>  $row
     */
    private function isRowEmpty(array $row): bool
    {
        foreach ($row as $cell) {
            if ($cell !== null && trim((string) $cell) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  list<mixed>|array<int|string, mixed>  $row
     */
    private function cellStr(array $row, int $index): string
    {
        if (! array_key_exists($index, $row)) {
            return '';
        }

        return trim((string) $row[$index]);
    }
}
