<?php

declare(strict_types=1);

namespace App\Support\DopSafety;

/**
 * Struktur header tabel DOP Safety (SHIFT × SECTION × kolom pekerjaan).
 */
final class DopOjiTableStructure
{
  public const DATA_COLUMN_COUNT = 26;

  public const HEADER_ROW_COUNT = 4;

  public const EXCEL_META_COLUMN_COUNT = 3;

  public const EXCEL_SHIFT_SECTION_COLSPAN = 26;

  public const EXCEL_SHIFT_ROW = 1;

  public const EXCEL_SECTION_ROW = 2;

  public const EXCEL_COLUMN_HEADER_ROW_1 = 3;

  public const EXCEL_COLUMN_HEADER_ROW_2 = 4;

  public const EXCEL_DATA_START_ROW = 5;

  public const EXCEL_DATA_START_COLUMN = 4;

  /** Template upload form create/edit — hanya kolom item (tanpa meta & otorisasi). */
  public const EXCEL_ITEMS_ONLY_DATA_START_COLUMN = 1;

  public static function excelItemsOnlyShiftStartColumn(): int
  {
    return self::EXCEL_ITEMS_ONLY_DATA_START_COLUMN + 1;
  }

  public static function excelShiftSectionStartColumn(): int
  {
    return self::EXCEL_DATA_START_COLUMN + 1;
  }

  public static function excelAuthorizationStartColumn(): int
  {
    return self::EXCEL_DATA_START_COLUMN + self::DATA_COLUMN_COUNT;
  }

  /**
   * @return array{table_structure: array{columns: list<array<string, mixed>>, shifts: list<array<string, mixed>>, sections: list<array<string, mixed>>}}
   */
  public static function definition(): array
  {
    return [
      'table_structure' => [
        'columns' => [
          ['name' => 'No.', 'rowspan' => 2],
          ['name' => 'Kode Unit', 'rowspan' => 2],
          ['name' => 'Section', 'rowspan' => 2],
          ['name' => 'Lokasi', 'rowspan' => 2],
          ['name' => 'Detail Pekerjaan', 'rowspan' => 2],
          ['name' => 'IZIN KERJA', 'rowspan' => 2],
          ['name' => 'Alat Bantu / Peralatan', 'rowspan' => 2],
          [
            'name' => 'LIST PEKERJA',
            'colspan' => 2,
            'sub_columns' => [
              ['name' => 'NAMA'],
              ['name' => 'SID'],
            ],
          ],
          ['name' => 'CCTV', 'rowspan' => 2],
          ['name' => 'Group Leader (L1)', 'rowspan' => 2],
          ['name' => 'SID', 'rowspan' => 2, 'key' => 'group_leader_sid'],
          ['name' => 'Evidence 1', 'rowspan' => 2, 'key' => 'evidence_1'],
          ['name' => 'Evidence 2', 'rowspan' => 2, 'key' => 'evidence_2'],
          ['name' => 'Evidence 3', 'rowspan' => 2, 'key' => 'evidence_3'],
          ['name' => 'Evidence 4', 'rowspan' => 2, 'key' => 'evidence_4'],
          ['name' => 'Evidence 5', 'rowspan' => 2, 'key' => 'evidence_5'],
          ['name' => 'Section Head (L2)', 'rowspan' => 2],
          ['name' => 'SID', 'rowspan' => 2, 'key' => 'section_head_sid'],
          ['name' => 'SHE Leader (L3)', 'rowspan' => 2],
          ['name' => 'SID', 'rowspan' => 2, 'key' => 'she_leader_sid'],
          ['name' => 'Dept. Head (L4)', 'rowspan' => 2],
          ['name' => 'SID', 'rowspan' => 2, 'key' => 'dept_head_sid'],
          ['name' => 'PJA BC', 'rowspan' => 2],
          ['name' => 'Upload Pekerja', 'rowspan' => 2],
          ['name' => 'Reject Reason', 'rowspan' => 2],
          ['name' => 'Aksi', 'rowspan' => 2],
        ],
        'shifts' => [
          ['name' => 'SHIFT 1', 'colspan' => 18],
        ],
        'sections' => [
          ['name' => 'FIELD TRACK', 'colspan' => 18],
        ],
      ],
    ];
  }

  /**
   * Label kolom data (baris header paling bawah).
   *
   * @return list<string>
   */
  public static function leafHeaders(): array
  {
    return [
      'No.',
      'Kode Unit',
      'Section',
      'Lokasi',
      'Detail Pekerjaan',
      'IZIN KERJA',
      'Alat Bantu / Peralatan',
      'NAMA',
      'SID',
      'CCTV',
      'Group Leader (L1)',
      'SID',
      'Evidence 1',
      'Evidence 2',
      'Evidence 3',
      'Evidence 4',
      'Evidence 5',
      'Section Head (L2)',
      'SID',
      'SHE Leader (L3)',
      'SID',
      'Dept. Head (L4)',
      'SID',
      'PJA BC',
      'Upload Pekerja',
      'Reject Reason',
    ];
  }

  /**
   * Kolom meta dokumen di awal baris data Excel (di luar blok SHIFT).
   *
   * @return list<string>
   */
  public static function documentMetaHeaders(): array
  {
    return ['Site', 'Hari/Tanggal', 'Shift'];
  }

  /**
   * Kolom otorisasi dokumen di akhir baris data Excel.
   *
   * @return list<string>
   */
  public static function authorizationHeaders(): array
  {
    return [
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
  }

  public static function totalImportColumnCount(): int
  {
    return count(self::documentMetaHeaders())
      + self::DATA_COLUMN_COUNT
      + count(self::authorizationHeaders());
  }

  /**
   * @return list<string>
   */
  public static function flatImportHeaders(): array
  {
    return [
      ...self::documentMetaHeaders(),
      ...self::leafHeaders(),
      ...self::authorizationHeaders(),
    ];
  }

  /**
   * @param  list<mixed>  $workers
   * @return array{names: string, sids: string}
   */
  public static function workersToDisplayCells(array $workers): array
  {
    $names = [];
    $sids = [];

    foreach ($workers as $worker) {
      if (is_array($worker)) {
        $name = trim((string) ($worker['name'] ?? ''));
        $sid = trim((string) ($worker['sid'] ?? ''));
      } else {
        $name = trim((string) $worker);
        $sid = '';
      }

      if ($name === '' && $sid === '') {
        continue;
      }

      $names[] = $name;
      $sids[] = $sid;
    }

    return [
      'names' => implode('; ', $names),
      'sids' => implode('; ', $sids),
    ];
  }

  /**
   * @return list<array{name: string, sid: string}>
   */
  public static function parseWorkersFromCells(mixed $namesRaw, mixed $sidsRaw): array
  {
    $names = self::splitListCell($namesRaw);
    $sids = self::splitListCell($sidsRaw);
    $workers = [];

    $max = max(count($names), count($sids));
    for ($i = 0; $i < $max; $i++) {
      $name = trim((string) ($names[$i] ?? ''));
      $sid = trim((string) ($sids[$i] ?? ''));

      if ($name === '' && $sid === '') {
        continue;
      }

      $workers[] = ['name' => $name, 'sid' => $sid];
    }

    return $workers;
  }

  /**
   * @return list<string>
   */
  public static function splitListCell(mixed $value): array
  {
    if ($value === null || trim((string) $value) === '') {
      return [];
    }

    $parts = preg_split('/[,;|]/', (string) $value) ?: [];

    return array_values(array_filter(array_map(static fn ($p) => trim((string) $p), $parts)));
  }
}
