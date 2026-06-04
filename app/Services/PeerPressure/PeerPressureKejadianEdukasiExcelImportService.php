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
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
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

    private const TEMPLATE_HEADER_FILL = '548235';

    private const TEMPLATE_HEADER_FONT = 'FFFFFF';

    /** @var list<string> */
    private const COLUMN_COMMENTS = [
        "Wajib di baris awal kejadian.\nFormat DD/MM/YYYY atau tanggal Excel.\nContoh pengisian: 15/01/2026",
        "Wajib diisi.\nFormat 24 jam HH:MM atau HH:MM:SS.\nContoh pengisian: 08:30",
        "Cantumkan kelompok lokasi tempat temuan.\nContoh pengisian: Pit",
        "Cantumkan lokasi temuan spesifik.\nContoh pengisian: Line A",
        "Cantumkan kelompok lokasi edukasi.\nContoh pengisian: Mess",
        "Cantumkan lokasi edukasi spesifik.\nContoh pengisian: Mess Site X",
        "Wajib diisi.\nFormat DD/MM/YYYY atau tanggal Excel.\nContoh pengisian: 16/01/2026",
        "Wajib diisi.\nFormat 24 jam HH:MM atau HH:MM:SS.\nContoh pengisian: 09:00",
        "Cantumkan nama perusahaan terkait kejadian.\nContoh pengisian: PT Contoh",
        "Opsional. Isi tasklist temuan jika ada.\nContoh pengisian: Ceklist harian",
        "Wajib diisi.\nRingkasan kronologi temuan secara singkat dan jelas.",
        "Cantumkan kategori deviasi.\nContoh pengisian: Golden Rules",
        "Cantumkan nama pemimpin edukasi.\nContoh pengisian: Nama Leader",
        "Opsional. ID Berecord jika tersedia.\nContoh pengisian: BR-12345",
        "SID karyawan pelanggar.\nContoh pengisian: SID001",
        "Nama pelanggar sesuai SID.\nContoh pengisian: Nama Pelanggar",
        "SID karyawan peer.\nContoh pengisian: SID002",
        "Nama peer sesuai SID.\nContoh pengisian: Nama Peer",
        "Opsional. Jenis kelompok kerja.\nContoh pengisian: Kontraktor",
        "Opsional. Kelompok aktivitas pekerjaan.\nContoh pengisian: Operasi",
        "Opsional. Aktivitas pekerjaan.\nContoh pengisian: Loading",
        "Opsional. Departemen terkait.\nContoh pengisian: Dept Safety",
        "Opsional. URL bukti/evidence lengkap jika ada.\nContoh pengisian: https://example.com/evidence",
        "Wajib diisi.\nDurasi edukasi dalam menit (angka bulat > 0).\nContoh pengisian: 45",
        "Wajib diisi.\nContoh pengisian: SELESAI atau OPEN (maks. 50 karakter).",
        "Opsional. Kode atau nama site.\nContoh pengisian: SITE-A",
    ];

    public function importFromUpload(UploadedFile $file): PeerPressureKejadianEdukasiExcelImportResult
    {
        $prevExcelCalendar = ExcelDate::getExcelCalendar();

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            ExcelDate::setExcelCalendar($spreadsheet->getExcelCalendar());

            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray(null, true, true, false);

            if (count($rows) < 2) {
                return new PeerPressureKejadianEdukasiExcelImportResult(
                    false,
                    'File tidak berisi data (minimal 1 baris header + 1 baris data).',
                    ['Minimal harus ada baris header dan satu baris data.'],
                    0,
                    0
                );
            }

            $headerResolution = $this->resolveHeaderRow($rows);
            if ($headerResolution['errors'] !== []) {
                return new PeerPressureKejadianEdukasiExcelImportResult(
                    false,
                    'Header Excel tidak sesuai template.',
                    $headerResolution['errors'],
                    0,
                    0
                );
            }

            $rows = $headerResolution['dataRows'];
            $firstDataExcelRow = $headerResolution['firstDataExcelRow'];

            if (! $this->hasAnyDataRow($rows)) {
                return new PeerPressureKejadianEdukasiExcelImportResult(
                    false,
                    'Tidak ada baris data setelah header.',
                    ['Isi minimal satu baris kejadian (kolom Tanggal Temuan terisi) atau hapus baris kosong saja.'],
                    0,
                    0
                );
            }

            $validationErrors = $this->collectBlockingErrors($rows, $firstDataExcelRow);
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

            $rowNum = $firstDataExcelRow;
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
        $headerRow = 2;
        $lastCol = Coordinate::stringFromColumnIndex(count($headers));
        $lastColLetter = $lastCol;

        $instructionText = '1. Lembar ini digunakan untuk mengimpor data kejadian dan edukasi Peer Pressure ke sistem. '
            . '2. Isi semua kolom sesuai petunjuk pada komentar di setiap header kolom (arahkan kursor ke tanda segitiga merah). '
            . '3. Satu kejadian dimulai saat kolom Tanggal Temuan terisi; baris berikutnya dengan Tanggal Temuan kosong ditambahkan sebagai pelanggar/peer pada kejadian yang sama. '
            . '4. Tanggal Temuan & Tanggal Edukasi: DD/MM/YYYY atau format tanggal Excel. Jam Temuan & Jam Edukasi: HH:MM (24 jam). '
            . '5. Hapus atau timpa baris contoh (baris 3–4) dengan data Anda sebelum upload.';

        $sheet->mergeCells('A1:' . $lastColLetter . '1');
        $sheet->setCellValue('A1', $instructionText);
        $this->applyTemplateBannerStyle($sheet, 'A1:' . $lastColLetter . '1');
        $sheet->getRowDimension(1)->setRowHeight(48);

        $col = 1;
        foreach ($headers as $h) {
            $cellRef = Coordinate::stringFromColumnIndex($col) . $headerRow;
            $sheet->setCellValue($cellRef, $h);
            $col++;
        }
        $this->applyTemplateBannerStyle($sheet, 'A' . $headerRow . ':' . $lastColLetter . $headerRow);
        $this->addHeaderColumnComments($sheet, $headerRow);

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
        $exampleRow = 3;
        foreach ($example as $i => $val) {
            $c = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValueExplicit($c . $exampleRow, (string) $val, DataType::TYPE_STRING);
        }

        $continuationRow = 4;
        $continuation = [
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            'SID003',
            'Pelanggar Tambahan',
            'SID004',
            'Peer Tambahan',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
            '',
        ];
        foreach ($continuation as $i => $val) {
            if ($val === '') {
                continue;
            }
            $c = Coordinate::stringFromColumnIndex($i + 1);
            $sheet->setCellValueExplicit($c . $continuationRow, (string) $val, DataType::TYPE_STRING);
        }

        for ($c = 1; $c <= count($headers); $c++) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($c))->setAutoSize(true);
        }

        $sheet->freezePane('A3');

        $info = $spreadsheet->createSheet();
        $info->setTitle('Referensi Kolom');
        $info->setCellValue('A1', 'Kolom (header)');
        $info->setCellValue('B1', 'Wajib / Opsional');
        $info->setCellValue('C1', 'Format / catatan');
        $info->getStyle('A1:C1')->getFont()->setBold(true);
        $rowsHelp = [
            ['Tanggal Temuan', 'Wajib untuk baris awal kejadian', 'DD/MM/YYYY atau serial tanggal Excel'],
            ['Jam Temuan', 'Wajib', 'HH:MM atau HH:MM:SS (24 jam)'],
            ['Kelompok Lokasi Temuan', 'Opsional (default "-")', 'Teks'],
            ['Lokasi Temuan', 'Opsional (default "-")', 'Teks'],
            ['Kelompok Lokasi Edukasi', 'Opsional (default "-")', 'Teks'],
            ['Lokasi Edukasi', 'Opsional (default "-")', 'Teks'],
            ['Tanggal Edukasi', 'Wajib', 'DD/MM/YYYY atau serial tanggal Excel'],
            ['Jam Edukasi', 'Wajib', 'HH:MM atau HH:MM:SS'],
            ['Perusahaan', 'Opsional (default "-")', 'Teks'],
            ['Tasklist Temuan (Jika Ada)', 'Opsional', 'Teks'],
            ['Kronologi Temuan', 'Wajib', 'Teks'],
            ['Kategori Deviasi', 'Opsional (default "-")', 'Teks'],
            ['Pemimpin Edukasi', 'Opsional (default "-")', 'Teks'],
            ['Id Berecord', 'Opsional', 'Teks'],
            ['SID Pelanggar', 'Opsional per baris', 'Teks — baris lanjutan tanpa tanggal temuan'],
            ['Nama Pelanggar', 'Opsional per baris', 'Teks'],
            ['SID Peer', 'Opsional per baris', 'Teks'],
            ['Nama Peer', 'Opsional per baris', 'Teks'],
            ['Jenis Kelompok Kerja', 'Opsional', 'Teks'],
            ['Kelompok Aktivitas Pekerjaan', 'Opsional', 'Teks'],
            ['Aktivitas Pekerjaan', 'Opsional', 'Teks'],
            ['Departemen', 'Opsional', 'Teks'],
            ['Evidence', 'Opsional', 'URL lengkap jika ada'],
            ['Durasi Edukasi (Menit)', 'Wajib', 'Bilangan bulat > 0'],
            ['Status Pelaksanaan Edukasi', 'Wajib', 'Contoh: SELESAI, OPEN (maks. 50 karakter)'],
            ['Site', 'Opsional', 'Maks. 255 karakter'],
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
     * @return array{dataRows: list<array<int, mixed>>, firstDataExcelRow: int, errors: list<string>}
     */
    private function resolveHeaderRow(array $rows): array
    {
        $candidates = [0, 1];
        $lastErrors = [];

        foreach ($candidates as $headerIndex) {
            if (! isset($rows[$headerIndex])) {
                continue;
            }

            $headerRow = $this->normalizeRowToZeroBased($rows[$headerIndex]);
            $headerErrors = $this->validateHeaders($headerRow);
            if ($headerErrors === []) {
                return [
                    'dataRows' => array_values(array_slice($rows, $headerIndex + 1)),
                    'firstDataExcelRow' => $headerIndex + 2,
                    'errors' => [],
                ];
            }

            $lastErrors = $headerErrors;
        }

        return [
            'dataRows' => [],
            'firstDataExcelRow' => 2,
            'errors' => $lastErrors,
        ];
    }

    private function applyTemplateBannerStyle(Worksheet $sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => self::TEMPLATE_HEADER_FONT],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => self::TEMPLATE_HEADER_FILL],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_LEFT,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
        ]);
    }

    private function addHeaderColumnComments(Worksheet $sheet, int $headerRow): void
    {
        foreach (self::EXPECTED_HEADERS as $i => $header) {
            $cellRef = Coordinate::stringFromColumnIndex($i + 1) . $headerRow;
            $commentText = self::COLUMN_COMMENTS[$i] ?? 'Isi sesuai petunjuk kolom ' . $header . '.';

            $comment = $sheet->getComment($cellRef);
            $comment->setAuthor('Admin Peer Pressure');
            $comment->getText()->createTextRun($commentText);
            $comment->setWidth('260pt');
            $comment->setHeight('80pt');
        }
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
    private function collectBlockingErrors(array $rows, int $firstDataExcelRow = 2): array
    {
        $errors = [];
        $rowNum = $firstDataExcelRow;
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

        $tanggalTemuanParsed = $this->parseDateCellStrict($rawTanggalTemuan, 'tanggal temuan');
        if (! $tanggalTemuanParsed['ok']) {
            $errors[] = "{$prefix}: {$tanggalTemuanParsed['error']}. Nilai sel: " . $this->previewCell($rawTanggalTemuan) . '.';
        }
        $tanggalTemuan = $tanggalTemuanParsed['ok'] ? $tanggalTemuanParsed['value'] : null;

        $jamTemuan = $this->parseTimeCell($rawJamTemuan);
        if ($jamTemuan === null) {
            $errors[] = "{$prefix}: kolom \"Jam Temuan\" tidak valid. Gunakan HH:MM atau HH:MM:SS (24 jam). Nilai sel: " . $this->previewCell($rawJamTemuan) . '.';
        }

        $tanggalEdukasiParsed = $this->parseDateCellStrict($rawTanggalEdukasi, 'tanggal edukasi');
        if (! $tanggalEdukasiParsed['ok']) {
            $errors[] = "{$prefix}: {$tanggalEdukasiParsed['error']}. Nilai sel: " . $this->previewCell($rawTanggalEdukasi) . '.';
        }
        $tanggalEdukasi = $tanggalEdukasiParsed['ok'] ? $tanggalEdukasiParsed['value'] : null;

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

    /**
     * @return array{ok: true, value: string}|array{ok: false, error: string}
     */
    private function parseDateCellStrict(mixed $v, string $fieldLabel): array
    {
        if ($v instanceof \DateTimeInterface) {
            $v = $v->format('d/m/Y');
        }

        if ($v === null || $v === '') {
            return ['ok' => false, 'error' => 'format ' . $fieldLabel . ' salah'];
        }

        if (is_numeric($v)) {
            try {
                $date = ExcelDate::excelToDateTimeObject((float) $v)->format('Y-m-d');

                return ['ok' => true, 'value' => $date];
            } catch (Exception $e) {
                return ['ok' => false, 'error' => 'format ' . $fieldLabel . ' salah'];
            }
        }

        $s = trim((string) $v);
        if ($s === '') {
            return ['ok' => false, 'error' => 'format ' . $fieldLabel . ' salah'];
        }

        if (preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $s, $m)) {
            $year = (int) $m[1];
            $month = (int) $m[2];
            $day = (int) $m[3];
            if (checkdate($month, $day, $year)) {
                return ['ok' => true, 'value' => sprintf('%04d-%02d-%02d', $year, $month, $day)];
            }

            return ['ok' => false, 'error' => 'format ' . $fieldLabel . ' salah'];
        }

        if (preg_match('/^(\d{1,2})[\/.\-](\d{1,2})[\/.\-](\d{4})$/', $s, $m)) {
            $part1 = (int) $m[1];
            $part2 = (int) $m[2];
            $year = (int) $m[3];

            if ($part1 <= 12 && $part2 > 12) {
                return ['ok' => false, 'error' => 'format ' . $fieldLabel . ' salah'];
            }

            if ($part1 > 31 || $part2 > 12 || $part1 < 1 || $part2 < 1) {
                return ['ok' => false, 'error' => 'format ' . $fieldLabel . ' salah'];
            }

            $day = $part1;
            $month = $part2;

            if (! checkdate($month, $day, $year)) {
                return ['ok' => false, 'error' => 'format ' . $fieldLabel . ' salah'];
            }

            return ['ok' => true, 'value' => sprintf('%04d-%02d-%02d', $year, $month, $day)];
        }

        return ['ok' => false, 'error' => 'format ' . $fieldLabel . ' salah'];
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
