<?php

declare(strict_types=1);

namespace App\Services\Hira;

use App\Models\HiraImprovementRekayasaRow;
use App\Models\HiraImprovementRekayasaRowReplikasiDetail;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

final class HiraImprovementRekayasaMergedExportService
{
    public const MERGED_EXPORT_HEADERS = [
        'ID',
        'Company',
        'Aktivitas (Rekayasa)',
        'Site Perusahaan (Rekayasa)',
        'Site',
        'Perusahaan',
        'Aktivitas',
        'Kategori Rekayasa',
        'Origin Replikasi (JIKA REPLIKASI)',
        'Pengendalian Rekayasa',
        'Penjelasan/Proses Kerja',
        'Deteksi',
        'Intervensi',
        'Level Efektivitas',
        'Nilai Risiko Awal',
        'Prediksi Penurunan Risiko',
        'Prediksi Risiko Sisa',
        'Target',
        'Total Populasi',
        'Target Replikasi by Komitmen',
        'Aktual Replikasi',
        'Satuan',
        'Jumlah Mitra Yang Mereplikasi (MK Tambang, Marine, Ekplorasi Only)',
        'Apakah sudah tercover di BeHIRA?',
        'Potensi Peningkatan Level Efektifitas',
        'Pengendalian Rekayasa dengan Peningkatan Level Efektifitas',
        'Target Standarisasi (Due date)',
    ];

    /**
     * @return list<array<string, string>>
     */
    public function exportRowsForScope(string $company, int $periodYear): array
    {
        $rows = HiraImprovementRekayasaRow::query()
            ->with('replikasiDetail')
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        return $rows
            ->map(fn (HiraImprovementRekayasaRow $row) => $this->buildMergedExportRow($row, $row->replikasiDetail))
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public function exportRowById(string $company, int $periodYear, int $rekayasaRowId): array
    {
        $row = HiraImprovementRekayasaRow::query()
            ->with('replikasiDetail')
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->whereKey($rekayasaRowId)
            ->firstOrFail();

        return $this->buildMergedExportRow($row, $row->replikasiDetail);
    }

    /**
     * @param  list<array<string, string>>  $rows
     */
    public function buildSpreadsheet(array $rows, string $title = 'Rekayasa Merged'): Spreadsheet
    {
        $spreadsheet = new Spreadsheet;
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle($title);

        $col = 1;
        foreach (self::MERGED_EXPORT_HEADERS as $header) {
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($col).'1', $header);
            $col++;
        }

        $lastCol = Coordinate::stringFromColumnIndex(count(self::MERGED_EXPORT_HEADERS));
        $sheet->getStyle('A1:'.$lastCol.'1')->getFont()->setBold(true);

        $rowNum = 2;
        foreach ($rows as $row) {
            $col = 1;
            foreach (self::MERGED_EXPORT_HEADERS as $header) {
                $sheet->setCellValue(
                    Coordinate::stringFromColumnIndex($col).$rowNum,
                    $row[$header] ?? '',
                );
                $col++;
            }
            $rowNum++;
        }

        foreach (range(1, count(self::MERGED_EXPORT_HEADERS)) as $columnIndex) {
            $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($columnIndex))->setAutoSize(true);
        }

        return $spreadsheet;
    }

    /**
     * Template impor: hanya identitas baris rekayasa, kolom replikasi dikosongkan.
     *
     * @return array<string, string>
     */
    public function buildTemplateExportRow(HiraImprovementRekayasaRow $rekayasaRow): array
    {
        return $this->assembleMergedExportRow($rekayasaRow, $this->blankDetailClient());
    }

    /**
     * @return array<string, string>
     */
    public function buildMergedExportRow(
        HiraImprovementRekayasaRow $rekayasaRow,
        ?HiraImprovementRekayasaRowReplikasiDetail $detail = null,
    ): array {
        $detailClient = $detail !== null
            ? $this->detailToClient($detail)
            : $this->blankDetailClient();

        return $this->assembleMergedExportRow($rekayasaRow, $detailClient);
    }

    /**
     * @param  array<string, string>  $detailClient
     * @return array<string, string>
     */
    private function assembleMergedExportRow(
        HiraImprovementRekayasaRow $rekayasaRow,
        array $detailClient,
    ): array {
        return [
            'ID' => (string) $rekayasaRow->id,
            'Company' => (string) $rekayasaRow->company,
            'Aktivitas (Rekayasa)' => (string) $rekayasaRow->aktivitas,
            'Site Perusahaan (Rekayasa)' => (string) $rekayasaRow->site_perusahaan,
            'Site' => (string) ($detailClient['site'] ?? ''),
            'Perusahaan' => (string) ($detailClient['perusahaan'] ?? ''),
            'Aktivitas' => (string) ($detailClient['aktivitas'] ?? ''),
            'Kategori Rekayasa' => (string) ($detailClient['kategoriRekayasa'] ?? ''),
            'Origin Replikasi (JIKA REPLIKASI)' => (string) ($detailClient['originReplikasi'] ?? ''),
            'Pengendalian Rekayasa' => (string) ($detailClient['pengendalianRekayasa'] ?? ''),
            'Penjelasan/Proses Kerja' => (string) ($detailClient['penjelasanProsesKerja'] ?? ''),
            'Deteksi' => (string) ($detailClient['deteksi'] ?? ''),
            'Intervensi' => (string) ($detailClient['intervensi'] ?? ''),
            'Level Efektivitas' => (string) ($detailClient['levelEfektivitas'] ?? ''),
            'Nilai Risiko Awal' => (string) ($detailClient['nilaiRisikoAwal'] ?? ''),
            'Prediksi Penurunan Risiko' => (string) ($detailClient['prediksiPenurunanRisiko'] ?? ''),
            'Prediksi Risiko Sisa' => (string) ($detailClient['prediksiRisikoSisa'] ?? ''),
            'Target' => (string) ($detailClient['target'] ?? ''),
            'Total Populasi' => (string) ($detailClient['totalPopulasi'] ?? ''),
            'Target Replikasi by Komitmen' => (string) ($detailClient['targetReplikasiKomitmen'] ?? ''),
            'Aktual Replikasi' => (string) ($detailClient['aktualReplikasi'] ?? ''),
            'Satuan' => (string) ($detailClient['satuan'] ?? ''),
            'Jumlah Mitra Yang Mereplikasi (MK Tambang, Marine, Ekplorasi Only)' => (string) ($detailClient['jumlahMitraReplikasi'] ?? ''),
            'Apakah sudah tercover di BeHIRA?' => (string) ($detailClient['tercoverBehira'] ?? ''),
            'Potensi Peningkatan Level Efektifitas' => (string) ($detailClient['potensiPeningkatanLevelEfektivitas'] ?? ''),
            'Pengendalian Rekayasa dengan Peningkatan Level Efektifitas' => (string) ($detailClient['pengendalianPeningkatanLevelEfektivitas'] ?? ''),
            'Target Standarisasi (Due date)' => (string) ($detailClient['targetStandarisasiDueDate'] ?? ''),
        ];
    }

    /**
     * @return array<string, string>
     */
    private function detailToClient(HiraImprovementRekayasaRowReplikasiDetail $detail): array
    {
        return [
            'site' => $detail->site,
            'perusahaan' => $detail->perusahaan,
            'aktivitas' => $detail->aktivitas,
            'kategoriRekayasa' => $detail->kategori_rekayasa,
            'originReplikasi' => $detail->origin_replikasi,
            'pengendalianRekayasa' => $detail->pengendalian_rekayasa ?? '',
            'penjelasanProsesKerja' => $detail->penjelasan_proses_kerja ?? '',
            'deteksi' => $detail->deteksi,
            'intervensi' => $detail->intervensi,
            'levelEfektivitas' => $detail->level_efektivitas,
            'nilaiRisikoAwal' => $detail->nilai_risiko_awal,
            'prediksiPenurunanRisiko' => $detail->prediksi_penurunan_risiko,
            'prediksiRisikoSisa' => $detail->prediksi_risiko_sisa,
            'target' => $detail->target,
            'totalPopulasi' => $detail->total_populasi,
            'targetReplikasiKomitmen' => $detail->target_replikasi_komitmen,
            'aktualReplikasi' => $detail->aktual_replikasi,
            'satuan' => $detail->satuan,
            'jumlahMitraReplikasi' => $detail->jumlah_mitra_replikasi,
            'tercoverBehira' => $detail->tercover_behira,
            'potensiPeningkatanLevelEfektivitas' => $detail->potensi_peningkatan_level_efektivitas,
            'pengendalianPeningkatanLevelEfektivitas' => $detail->pengendalian_pen_tingkatan_level_efektivitas ?? '',
            'targetStandarisasiDueDate' => $detail->target_standar_isasi_due_date,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function blankDetailClient(): array
    {
        return [
            'site' => '',
            'perusahaan' => '',
            'aktivitas' => '',
            'kategoriRekayasa' => '',
            'originReplikasi' => '',
            'pengendalianRekayasa' => '',
            'penjelasanProsesKerja' => '',
            'deteksi' => '',
            'intervensi' => '',
            'levelEfektivitas' => '',
            'nilaiRisikoAwal' => '',
            'prediksiPenurunanRisiko' => '',
            'prediksiRisikoSisa' => '',
            'target' => '',
            'totalPopulasi' => '',
            'targetReplikasiKomitmen' => '',
            'aktualReplikasi' => '',
            'satuan' => '',
            'jumlahMitraReplikasi' => '',
            'tercoverBehira' => '',
            'potensiPeningkatanLevelEfektivitas' => '',
            'pengendalianPeningkatanLevelEfektivitas' => '',
            'targetStandarisasiDueDate' => '',
        ];
    }
}
