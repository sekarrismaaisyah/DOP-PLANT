<?php

declare(strict_types=1);

namespace App\Services\PeerPressure;

use App\Models\PeerPressureKejadianEdukasi;
use App\Models\PeerPressurePesertaEdukasi;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

final class PeerPressureKejadianEdukasiExcelImportResult
{
    /**
     * @param  list<string>  $errors  Peringatan (jika sukses) atau daftar error validasi / fatal (jika gagal)
     */
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly array $errors,
        public readonly int $importedKejadian,
        public readonly int $importedPeserta,
    ) {
    }
}

/**
 * Impor Excel Data Peer Pressure — header wajib sama template; tanggal teks DD/MM/YYYY atau serial Excel.
 */
final class PeerPressureKejadianEdukasiExcelImportService
{
    private const COL = [
        'tanggal_temuan' => 0,
        'jam_temuan' => 1,
        'kelompok_lokasi_temuan' => 2,
        'lokasi_temuan' => 3,
        'kelompok_lokasi_edukasi' => 4,
        'lokasi_edukasi' => 5,
        'tanggal_edukasi' => 6,
        'jam_edukasi' => 7,
        'perusahaan' => 8,
        'tasklist_temuan' => 9,
        'kronologi_temuan' => 10,
        'kategori_deviasi' => 11,
        'pemimpin_edukasi' => 12,
        'id_berecord' => 13,
        'sid_pelanggar' => 14,
        'nama_pelanggar' => 15,
        'sid_peer' => 16,
        'nama_peer' => 17,
        'jenis_kelompok_kerja' => 18,
        'kelompok_aktivitas_pekerjaan' => 19,
        'aktivitas_pekerjaan' => 20,
        'departemen' => 21,
        'evidence' => 22,
        'durasi_menit' => 23,
        'status' => 24,
        'site' => 25,
    ];

    /** @var list<string> */
    private const EXPECTED_HEADERS = [
        'Tanggal Temuan',
        'Jam Temuan',
        'Kelompok Lokasi Temuan',
        'Lokasi Temuan',
        'Kelompok Lokasi Edukasi',
        'Lokasi Edukasi',
        'Tanggal Edukasi',
        'Jam Edukasi',
        'Perusahaan',
        'Tasklist Temuan (Jika Ada)',
        'Kronologi Temuan',
        'Kategori Deviasi',
        'Pemimpin Edukasi',
        'Id Berecord',
        'SID Pelanggar',
        'Nama Pelanggar',
        'SID Peer',
        'Nama Peer',
        'Jenis Kelompok Kerja',
        'Kelompok Aktivitas Pekerjaan',
        'Aktivitas Pekerjaan',
        'Departemen',
        'Evidence',
        'Durasi Edukasi (Menit)',
        'Status Pelaksanaan Edukasi',
        'Site',
    ];

    public function importFromUpload(UploadedFile $file): PeerPressureKejadianEdukasiExcelImportResult
    {
        $prevExcelCalendar = ExcelDate::getExcelCalendar();

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            ExcelDate::setExcelCalendar($spreadsheet->getExcelCalendar());

            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, false, false);

            if (count($rows) < 2) {
                return new PeerPressureKejadianEdukasiExcelImportResult(
                    false,
                    'File tidak berisi data (minimal 1 baris header + 1 baris data).',
                    ['Minimal harus ada baris header dan satu baris data.'],
                    0,
                    0
                );
            }

            $headerRow = $this->normalizeRowToZeroBased(array_shift($rows));
            $headerErrors = $this->validateHeaders($headerRow);
            if ($headerErrors !== []) {
                return new PeerPressureKejadianEdukasiExcelImportResult(
                    false,
                    'Header Excel tidak sesuai template.',
                    $headerErrors,
                    0,
                    0
                );
            }

            if (! $this->hasAnyDataRow($rows)) {
                return new PeerPressureKejadianEdukasiExcelImportResult(
                    false,
                    'Tidak ada baris data setelah header.',
                    ['Isi minimal satu baris kejadian (kolom Tanggal Temuan terisi) atau hapus baris kosong saja.'],
                    0,
                    0
                );
            }

            $validationErrors = $this->collectBlockingErrors($rows);
            if ($validationErrors !== []) {
                return new PeerPressureKejadianEdukasiExcelImportResult(
                    false,
                    'Import dibatalkan — perbaiki file lalu unggah lagi.',
                    $validationErrors,
                    0,
                    0
                );
            }

            $importedKejadian = 0;
            $importedPeserta = 0;
            $warnings = [];

            DB::beginTransaction();

            $rowNum = 2;
            $current = null;
            $urutan = 0;

            foreach ($rows as $row) {
                $row = $this->normalizeRowToZeroBased($row);
                if ($this->rowIsEmpty($row)) {
                    $rowNum++;
                    continue;
                }

                $tanggalTemuanRaw = $this->cell($row, self::COL['tanggal_temuan']);

                if ($tanggalTemuanRaw !== '') {
                    $parsed = $this->tryParseKejadianRow($row, $rowNum);
                    if (! $parsed['ok']) {
                        DB::rollBack();

                        return new PeerPressureKejadianEdukasiExcelImportResult(
                            false,
                            'Import gagal saat penyimpanan.',
                            $parsed['errors'],
                            0,
                            0
                        );
                    }

                    $current = PeerPressureKejadianEdukasi::query()->create($parsed['attributes']);
                    $importedKejadian++;
                    $urutan = 0;

                    $importedPeserta += $this->appendPesertaForRow($current->id, $row, $urutan);

                    $rowNum++;
                    continue;
                }

                if ($current === null) {
                    DB::rollBack();

                    return new PeerPressureKejadianEdukasiExcelImportResult(
                        false,
                        'Import gagal.',
                        ["Baris {$rowNum}: baris lanjutan tanpa kejadian sebelumnya (kolom \"Tanggal Temuan\" kosong)."],
                        0,
                        0
                    );
                }

                $this->mergeContinuationFieldsIfEmpty($current, $row);

                $n = $this->appendPesertaForRow($current->id, $row, $urutan);
                if ($n === 0) {
                    $warnings[] = "Baris {$rowNum}: baris lanjutan tidak berisi SID/Nama pelanggar atau peer.";
                }
                $importedPeserta += $n;
                $rowNum++;
            }

            DB::commit();

            $msg = "Import selesai: {$importedKejadian} kejadian, {$importedPeserta} baris peserta.";
            if ($warnings !== []) {
                $msg .= ' Peringatan: ' . implode(' ', array_slice($warnings, 0, 8));
                if (count($warnings) > 8) {
                    $msg .= ' …';
                }
            }

            return new PeerPressureKejadianEdukasiExcelImportResult(true, $msg, $warnings, $importedKejadian, $importedPeserta);
        } catch (Exception $e) {
            DB::rollBack();

            return new PeerPressureKejadianEdukasiExcelImportResult(
                false,
                'Import gagal: ' . $e->getMessage(),
                [$e->getMessage()],
                0,
                0
            );
        } finally {
            ExcelDate::setExcelCalendar($prevExcelCalendar);
        }
    }

    public function streamTemplateDownload(): StreamedResponse
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Peer Pressure');

        $headers = self::EXPECTED_HEADERS;
        $col = 1;
        foreach ($headers as $h) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col) . '1', $h);
            $col++;
        }
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $sheet->getStyle('A1:' . $lastCol . '1')->getFont()->setBold(true);

        $example = [
            '15/01/2026',
            '08:30',
            'Pit',
            'Line A',
            'Mess',
            'Mess Site X',
            '16/01/2026',
            '09:00',
            'PT Contoh',
            'Ceklist harian',
            'Ringkasan kronologi temuan wajib diisi.',
            'Golden Rules',
            'Nama Leader',
            'BR-12345',
            'SID001',
            'Nama Pelanggar',
            'SID002',
            'Nama Peer',
            'Kontraktor',
            'Operasi',
            'Loading',
            'Dept Safety',
            'https://example.com/evidence',
            '45',
            'SELESAI',
            'SITE-A',
        ];
        foreach ($example as $i => $val) {
            $c = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValueExplicit($c . '2', (string) $val, DataType::TYPE_STRING);
        }

        $sheet->mergeCells('A3:' . $lastCol . '4');
        $sheet->setCellValue(
            'A3',
            "PETUNJUK FORMAT\n"
            . "• Tanggal Temuan & Tanggal Edukasi: teks DD/MM/YYYY (contoh 15/01/2026) atau tanggal serial Excel.\n"
            . "• Jam Temuan & Jam Edukasi: format 24 jam HH:MM atau HH:MM:SS (contoh 08:30).\n"
            . "• Durasi Edukasi (Menit): angka > 0.\n"
            . "• Baris berikutnya dengan kolom Tanggal Temuan kosong = tambahan pelanggar/peer untuk kejadian di atas.\n"
            . "• Hapus atau timpa baris contoh (baris 2) dengan data Anda sebelum upload."
        );
        $sheet->getStyle('A3')->getAlignment()->setWrapText(true);
        $sheet->getStyle('A3')->getFont()->setSize(10);

        for ($c = 1; $c <= count($headers); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }
        $sheet->getRowDimension(3)->setRowHeight(90);

        $info = $spreadsheet->createSheet();
        $info->setTitle('Referensi Kolom');
        $info->setCellValue('A1', 'Kolom (header)');
        $info->setCellValue('B1', 'Wajib / Opsional');
        $info->setCellValue('C1', 'Format / catatan');
        $info->getStyle('A1:C1')->getFont()->setBold(true);
        $rowsHelp = [
            ['Tanggal Temuan', 'Wajib untuk baris awal kejadian', 'DD/MM/YYYY atau serial tanggal Excel'],
            ['Jam Temuan', 'Wajib', 'HH:MM atau HH:MM:SS (24 jam)'],
            ['Tanggal Edukasi', 'Wajib', 'DD/MM/YYYY atau serial tanggal Excel'],
            ['Jam Edukasi', 'Wajib', 'HH:MM atau HH:MM:SS'],
            ['Kronologi Temuan', 'Wajib', 'Teks'],
            ['Durasi Edukasi (Menit)', 'Wajib', 'Bilangan bulat > 0'],
            ['Status Pelaksanaan Edukasi', 'Wajib', 'Contoh: SELESAI, OPEN (maks. 50 karakter)'],
            ['Site', 'Opsional', 'Maks. 255 karakter'],
            ['Evidence', 'Opsional', 'URL lengkap jika ada'],
        ];
        $r = 2;
        foreach ($rowsHelp as $h) {
            $info->setCellValue('A' . $r, $h[0]);
            $info->setCellValue('B' . $r, $h[1]);
            $info->setCellValue('C' . $r, $h[2]);
            $r++;
        }
        foreach (range('A', 'C') as $colLetter) {
            $info->getColumnDimension($colLetter)->setAutoSize(true);
        }

        $spreadsheet->setActiveSheetIndex(0);

        $filename = 'peer_pressure_data_peer_pressure_template_' . date('Y-m-d') . '.xlsx';
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * @param  list<array<int, mixed>>  $rows
     */
    private function hasAnyDataRow(array $rows): bool
    {
        foreach ($rows as $row) {
            $r = $this->normalizeRowToZeroBased($row);
            if (! $this->rowIsEmpty($r)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  array<int, mixed>  $headerRow
     * @return list<string>
     */
    private function validateHeaders(array $headerRow): array
    {
        $errors = [];
        foreach (self::EXPECTED_HEADERS as $i => $expected) {
            $actual = isset($headerRow[$i]) ? trim((string) $headerRow[$i]) : '';
            if ($i === 0 && str_starts_with($actual, "\xEF\xBB\xBF")) {
                $actual = trim(substr($actual, 3));
            }
            if ($actual !== $expected) {
                $colLetter = Coordinate::stringFromColumnIndex($i + 1);
                $errors[] = "Header kolom {$colLetter}: diharapkan \"{$expected}\", ditemukan \"" . ($actual === '' ? '(kosong)' : $actual) . '".';
            }
        }

        return $errors;
    }

    /**
     * @param  list<array<int, mixed>>  $rows
     * @return list<string>
     */
    private function collectBlockingErrors(array $rows): array
    {
        $errors = [];
        $rowNum = 2;
        $hasSuccessfulHeader = false;

        foreach ($rows as $row) {
            $row = $this->normalizeRowToZeroBased($row);
            if ($this->rowIsEmpty($row)) {
                $rowNum++;
                continue;
            }

            $tanggalTemuanRaw = $this->cell($row, self::COL['tanggal_temuan']);

            if ($tanggalTemuanRaw !== '') {
                $parsed = $this->tryParseKejadianRow($row, $rowNum);
                if (! $parsed['ok']) {
                    foreach ($parsed['errors'] as $e) {
                        $errors[] = $e;
                    }
                } else {
                    $hasSuccessfulHeader = true;
                }
            } elseif (! $hasSuccessfulHeader) {
                $errors[] = "Baris {$rowNum}: baris lanjutan tanpa kejadian sebelumnya (kolom \"Tanggal Temuan\" kosong). Isi tanggal temuan di baris pertama setiap kejadian.";
            }

            $rowNum++;
        }

        return $errors;
    }

    /**
     * @param  array<int, mixed>  $row
     * @return array{ok: true, attributes: array<string, mixed>}|array{ok: false, errors: list<string>}
     */
    private function tryParseKejadianRow(array $row, int $rowNum): array
    {
        $errors = [];
        $prefix = "Baris {$rowNum}";

        $rawTanggalTemuan = $row[self::COL['tanggal_temuan']] ?? null;
        $rawJamTemuan = $row[self::COL['jam_temuan']] ?? null;
        $rawTanggalEdukasi = $row[self::COL['tanggal_edukasi']] ?? null;
        $rawJamEdukasi = $row[self::COL['jam_edukasi']] ?? null;

        $tanggalTemuan = $this->parseDateCell($rawTanggalTemuan);
        if ($tanggalTemuan === null) {
            $errors[] = "{$prefix}: kolom \"Tanggal Temuan\" tidak valid. Gunakan DD/MM/YYYY (contoh 15/01/2026) atau tanggal Excel. Nilai sel: " . $this->previewCell($rawTanggalTemuan) . '.';
        }

        $jamTemuan = $this->parseTimeCell($rawJamTemuan);
        if ($jamTemuan === null) {
            $errors[] = "{$prefix}: kolom \"Jam Temuan\" tidak valid. Gunakan HH:MM atau HH:MM:SS (24 jam). Nilai sel: " . $this->previewCell($rawJamTemuan) . '.';
        }

        $tanggalEdukasi = $this->parseDateCell($rawTanggalEdukasi);
        if ($tanggalEdukasi === null) {
            $errors[] = "{$prefix}: kolom \"Tanggal Edukasi\" tidak valid. Gunakan DD/MM/YYYY. Nilai sel: " . $this->previewCell($rawTanggalEdukasi) . '.';
        }

        $jamEdukasi = $this->parseTimeCell($rawJamEdukasi);
        if ($jamEdukasi === null) {
            $errors[] = "{$prefix}: kolom \"Jam Edukasi\" tidak valid. Gunakan HH:MM atau HH:MM:SS. Nilai sel: " . $this->previewCell($rawJamEdukasi) . '.';
        }

        if ($errors !== []) {
            return ['ok' => false, 'errors' => $errors];
        }

        $durasiRaw = $this->cell($row, self::COL['durasi_menit']);
        $durasi = $durasiRaw !== '' ? (int) preg_replace('/[^\d]/', '', $durasiRaw) : 0;
        if ($durasi < 1) {
            return ['ok' => false, 'errors' => ["{$prefix}: kolom \"Durasi Edukasi (Menit)\" wajib angka lebih dari 0. Nilai: " . ($durasiRaw === '' ? '(kosong)' : $durasiRaw) . '.']];
        }

        $kronologi = $this->cell($row, self::COL['kronologi_temuan']);
        if ($kronologi === '') {
            return ['ok' => false, 'errors' => ["{$prefix}: kolom \"Kronologi Temuan\" wajib diisi."]];
        }

        $status = $this->cell($row, self::COL['status']);
        if (mb_strlen($status) > 50) {
            return ['ok' => false, 'errors' => ["{$prefix}: kolom \"Status Pelaksanaan Edukasi\" maksimal 50 karakter."]];
        }

        $attributes = [
            'tanggal_temuan' => $tanggalTemuan,
            'jam_temuan' => $jamTemuan,
            'kelompok_lokasi_temuan' => $this->cell($row, self::COL['kelompok_lokasi_temuan']) ?: '-',
            'lokasi_temuan' => $this->cell($row, self::COL['lokasi_temuan']) ?: '-',
            'kelompok_lokasi_edukasi' => $this->cell($row, self::COL['kelompok_lokasi_edukasi']) ?: '-',
            'lokasi_edukasi' => $this->cell($row, self::COL['lokasi_edukasi']) ?: '-',
            'tanggal_edukasi' => $tanggalEdukasi,
            'jam_edukasi' => $jamEdukasi,
            'perusahaan' => $this->cell($row, self::COL['perusahaan']) ?: '-',
            'site' => $this->nullableString($row, self::COL['site']),
            'tasklist_temuan' => $this->nullableString($row, self::COL['tasklist_temuan']),
            'kronologi_temuan' => $kronologi,
            'kategori_deviasi' => $this->cell($row, self::COL['kategori_deviasi']) ?: '-',
            'pemimpin_edukasi' => $this->cell($row, self::COL['pemimpin_edukasi']) ?: '-',
            'id_berecord' => $this->nullableString($row, self::COL['id_berecord']),
            'jenis_kelompok_kerja' => $this->nullableString($row, self::COL['jenis_kelompok_kerja']),
            'kelompok_aktivitas_pekerjaan' => $this->nullableString($row, self::COL['kelompok_aktivitas_pekerjaan']),
            'aktivitas_pekerjaan' => $this->nullableString($row, self::COL['aktivitas_pekerjaan']),
            'departemen' => $this->nullableString($row, self::COL['departemen']),
            'evidence_url' => $this->nullableString($row, self::COL['evidence']),
            'durasi_edukasi_menit' => $durasi,
            'status_pelaksanaan_edukasi' => $status !== '' ? $status : 'OPEN',
        ];

        return ['ok' => true, 'attributes' => $attributes];
    }

    private function previewCell(mixed $v): string
    {
        if ($v === null) {
            return '(null)';
        }
        if (is_float($v) || is_int($v)) {
            return (string) $v;
        }
        $s = trim((string) $v);

        return $s === '' ? '(kosong)' : mb_substr($s, 0, 80) . (mb_strlen($s) > 80 ? '…' : '');
    }

    /**
     * @param  array<int|string, mixed>  $row
     * @return array<int, mixed>
     */
    private function normalizeRowToZeroBased(array $row): array
    {
        if ($row === []) {
            return [];
        }
        $keys = array_keys($row);
        $firstKey = $keys[0] ?? 0;
        if (is_string($firstKey) && preg_match('/^[A-Z]{1,3}$/', $firstKey)) {
            ksort($row, SORT_STRING);
        }

        return array_values($row);
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function rowIsEmpty(array $row): bool
    {
        foreach ($row as $v) {
            if ($v !== null && trim((string) $v) !== '') {
                return false;
            }
        }

        return true;
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function cell(array $row, int $index): string
    {
        if (! isset($row[$index]) || $row[$index] === null) {
            return '';
        }
        if (is_float($row[$index]) || is_int($row[$index])) {
            return trim((string) $row[$index]);
        }

        return trim((string) $row[$index]);
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function nullableString(array $row, int $index): ?string
    {
        $s = $this->cell($row, $index);

        return $s === '' ? null : $s;
    }

    private function parseDateCell(mixed $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $v)->format('Y-m-d');
            } catch (Exception $e) {
                return null;
            }
        }
        $s = trim((string) $v);
        if ($s === '') {
            return null;
        }
        if (preg_match('/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{4})$/', $s, $m)) {
            try {
                $day = (int) $m[1];
                $month = (int) $m[2];
                $year = (int) $m[3];

                return Carbon::createFromDate($year, $month, $day)->format('Y-m-d');
            } catch (Exception $e) {
                return null;
            }
        }
        foreach (['Y-m-d', 'd/m/Y', 'j/n/Y', 'd-m-Y', 'j-n-Y'] as $fmt) {
            try {
                return Carbon::createFromFormat($fmt, $s)->format('Y-m-d');
            } catch (Exception $e) {
            }
        }

        return null;
    }

    private function parseTimeCell(mixed $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        if (is_numeric($v)) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $v)->format('H:i:s');
            } catch (Exception $e) {
                return null;
            }
        }
        $s = trim((string) $v);
        if ($s === '') {
            return null;
        }
        if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $s)) {
            $parts = explode(':', $s);
            $h = str_pad((string) ((int) $parts[0]), 2, '0', STR_PAD_LEFT);
            $m = str_pad((string) ((int) ($parts[1] ?? 0)), 2, '0', STR_PAD_LEFT);
            $sec = isset($parts[2]) ? str_pad((string) ((int) $parts[2]), 2, '0', STR_PAD_LEFT) : '00';

            return "{$h}:{$m}:{$sec}";
        }
        try {
            return Carbon::parse($s)->format('H:i:s');
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function appendPesertaForRow(int $kejadianId, array $row, int &$urutan): int
    {
        $added = 0;
        $sidP = $this->cell($row, self::COL['sid_pelanggar']);
        $namaP = $this->cell($row, self::COL['nama_pelanggar']);
        $sidPeer = $this->cell($row, self::COL['sid_peer']);
        $namaPeer = $this->cell($row, self::COL['nama_peer']);

        if ($sidP !== '' || $namaP !== '') {
            PeerPressurePesertaEdukasi::query()->create([
                'kejadian_edukasi_id' => $kejadianId,
                'sid' => $sidP !== '' ? $sidP : '-',
                'nama' => $namaP !== '' ? $namaP : '-',
                'peran' => 'pelanggar',
                'urutan' => ++$urutan,
            ]);
            $added++;
        }

        if ($sidPeer !== '' || $namaPeer !== '') {
            PeerPressurePesertaEdukasi::query()->create([
                'kejadian_edukasi_id' => $kejadianId,
                'sid' => $sidPeer !== '' ? $sidPeer : '-',
                'nama' => $namaPeer !== '' ? $namaPeer : '-',
                'peran' => 'peer',
                'urutan' => ++$urutan,
            ]);
            $added++;
        }

        return $added;
    }

    /**
     * @param  array<int, mixed>  $row
     */
    private function mergeContinuationFieldsIfEmpty(PeerPressureKejadianEdukasi $k, array $row): void
    {
        $updates = [];
        $map = [
            self::COL['site'] => 'site',
            self::COL['jenis_kelompok_kerja'] => 'jenis_kelompok_kerja',
            self::COL['kelompok_aktivitas_pekerjaan'] => 'kelompok_aktivitas_pekerjaan',
            self::COL['aktivitas_pekerjaan'] => 'aktivitas_pekerjaan',
            self::COL['departemen'] => 'departemen',
        ];
        foreach ($map as $idx => $field) {
            $v = $this->cell($row, $idx);
            if ($v !== '' && ($k->{$field} === null || $k->{$field} === '')) {
                $updates[$field] = $v;
            }
        }
        if ($updates !== []) {
            $k->update($updates);
            $k->refresh();
        }
    }
}
