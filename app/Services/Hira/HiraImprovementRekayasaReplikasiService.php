<?php

declare(strict_types=1);

namespace App\Services\Hira;

use App\Models\HiraImprovementRekayasaRow;
use App\Models\HiraImprovementRekayasaRowReplikasiDetail;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

final class HiraImprovementRekayasaReplikasiService
{
    /**
     * @var array<string, string>
     */
    private const CLIENT_KEY_BY_HEADER = [
        'id' => 'rekayasaRowId',
        'id rekayasa' => 'rekayasaRowId',
        'rekayasa row id' => 'rekayasaRowId',
        'company' => '_company',
        'aktivitas (rekayasa)' => '_rekayasaAktivitas',
        'site perusahaan (rekayasa)' => '_rekayasaSitePerusahaan',
        'site' => 'site',
        'perusahaan' => 'perusahaan',
        'aktivitas' => 'aktivitas',
        'kategori rekayasa' => 'kategoriRekayasa',
        'origin replikasi (jika replikasi)' => 'originReplikasi',
        'origin replikasi' => 'originReplikasi',
        'pengendalian rekayasa' => 'pengendalianRekayasa',
        'penjelasan/proses kerja' => 'penjelasanProsesKerja',
        'penjelasan / proses kerja' => 'penjelasanProsesKerja',
        'deteksi' => 'deteksi',
        'intervensi' => 'intervensi',
        'level efektivitas' => 'levelEfektivitas',
        'nilai risiko awal' => 'nilaiRisikoAwal',
        'prediksi penurunan risiko' => 'prediksiPenurunanRisiko',
        'prediksi risiko sisa' => 'prediksiRisikoSisa',
        'target' => 'target',
        'total populasi' => 'totalPopulasi',
        'target replikasi by komitmen' => 'targetReplikasiKomitmen',
        'aktual replikasi' => 'aktualReplikasi',
        'satuan' => 'satuan',
        'jumlah mitra yang mereplikasi (mk tambang, marine, ekplorasi only)' => 'jumlahMitraReplikasi',
        'jumlah mitra yang mereplikasi' => 'jumlahMitraReplikasi',
        'apakah sudah tercover di behira?' => 'tercoverBehira',
        'apakah sudah tercover di behira' => 'tercoverBehira',
        'potensi peningkatan level efektifitas' => 'potensiPeningkatanLevelEfektivitas',
        'pengendalian rekayasa dengan peningkatan level efektifitas' => 'pengendalianPeningkatanLevelEfektivitas',
        'target standarisasi (due date)' => 'targetStandarisasiDueDate',
        'target standarisasi' => 'targetStandarisasiDueDate',
    ];

    public function __construct(
        private readonly HiraImprovementRekayasaReplikasiSampleData $sampleData,
        private readonly HiraImprovementRekayasaMergedExportService $mergedExportService,
    ) {}

    /**
     * @return list<array<string, mixed>>
     */
    public function listForScope(string $company, int $periodYear): array
    {
        $this->ensureRekayasaRowsExist($company, $periodYear);

        if (! $this->hasDetails($company, $periodYear)) {
            $this->seedDefaults($company, $periodYear);
        }

        return $this->rekayasaRowsQuery($company, $periodYear)
            ->get()
            ->map(fn (HiraImprovementRekayasaRow $row) => $this->toClientRow($row))
            ->values()
            ->all();
    }

    public function hasDetails(string $company, int $periodYear): bool
    {
        return HiraImprovementRekayasaRowReplikasiDetail::query()
            ->whereHas('rekayasaRow', function ($query) use ($company, $periodYear) {
                $query->where('company', $company)->where('period_year', $periodYear);
            })
            ->exists();
    }

    public function seedDefaults(string $company, int $periodYear): void
    {
        $samples = $this->sampleData->rows();

        DB::transaction(function () use ($samples, $company, $periodYear) {
            $rekayasaRows = $this->rekayasaRowsQuery($company, $periodYear)->get();

            foreach ($rekayasaRows as $rekayasaRow) {
                $sample = $this->findSampleForRekayasaRow($rekayasaRow, $samples);
                if ($sample === null) {
                    continue;
                }

                HiraImprovementRekayasaRowReplikasiDetail::query()->updateOrCreate(
                    ['rekayasa_row_id' => $rekayasaRow->id],
                    $this->mapClientToDetailAttributes($sample),
                );
            }
        });
    }

    public function resetToSample(string $company, int $periodYear): void
    {
        DB::transaction(function () use ($company, $periodYear) {
            $rekayasaRowIds = HiraImprovementRekayasaRow::query()
                ->where('company', $company)
                ->where('period_year', $periodYear)
                ->pluck('id');

            HiraImprovementRekayasaRowReplikasiDetail::query()
                ->whereIn('rekayasa_row_id', $rekayasaRowIds)
                ->delete();

            $this->seedDefaults($company, $periodYear);
        });
    }

    /**
     * @param  list<array<string, mixed>>  $clientRows
     * @return list<array<string, mixed>>
     */
    public function syncRows(string $company, int $periodYear, array $clientRows): array
    {
        return DB::transaction(function () use ($company, $periodYear, $clientRows) {
            foreach ($clientRows as $clientRow) {
                $rekayasaRow = $this->resolveRekayasaRow($company, $periodYear, $clientRow);
                if ($rekayasaRow === null) {
                    continue;
                }

                HiraImprovementRekayasaRowReplikasiDetail::query()->updateOrCreate(
                    ['rekayasa_row_id' => $rekayasaRow->id],
                    $this->mapClientToDetailAttributes($clientRow),
                );
            }

            return $this->listForScope($company, $periodYear);
        });
    }

    public function deleteRow(string $company, int $periodYear, int $rekayasaRowId): bool
    {
        return (bool) HiraImprovementRekayasaRowReplikasiDetail::query()
            ->where('rekayasa_row_id', $rekayasaRowId)
            ->whereHas('rekayasaRow', function ($query) use ($company, $periodYear) {
                $query->where('company', $company)->where('period_year', $periodYear);
            })
            ->delete();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function importFromExcelFile(string $company, int $periodYear, string $filePath): array
    {
        $spreadsheet = IOFactory::load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if ($rows === []) {
            throw new \InvalidArgumentException('File Excel kosong.');
        }

        $headerIndex = $this->findHeaderRowIndex($rows);
        $headerRow = $rows[$headerIndex] ?? [];
        $columnMap = $this->buildColumnMap($headerRow);

        if ($columnMap === []) {
            throw new \InvalidArgumentException(
                'Header Excel tidak dikenali. Gunakan template Excel yang disediakan.',
            );
        }

        $parsed = [];
        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $mapped = $this->mapExcelRow($row, $columnMap);
            if ($mapped !== null) {
                $parsed[] = $mapped;
            }
        }

        if ($parsed === []) {
            throw new \InvalidArgumentException('Tidak ada baris valid pada file Excel.');
        }

        return $this->syncRows($company, $periodYear, $parsed);
    }

    /**
     * @return list<array<string, string>>
     */
    public function exportRows(string $company, int $periodYear): array
    {
        return $this->mergedExportService->exportRowsForScope($company, $periodYear);
    }

    /**
     * @return list<array<string, string>>
     */
    public function templateRows(string $company, int $periodYear): array
    {
        $this->ensureRekayasaRowsExist($company, $periodYear);

        return $this->rekayasaRowsQuery($company, $periodYear)
            ->get()
            ->map(fn (HiraImprovementRekayasaRow $row) => $this->mergedExportService->buildTemplateExportRow($row))
            ->values()
            ->all();
    }

    /**
     * @param  list<int>  $rekayasaRowIds
     * @return list<array<string, string>>
     */
    public function templateRowsForIds(string $company, int $periodYear, array $rekayasaRowIds): array
    {
        $rekayasaRowIds = array_values(array_unique(array_filter(
            array_map(static fn ($id) => (int) $id, $rekayasaRowIds),
            static fn (int $id) => $id > 0,
        )));

        if ($rekayasaRowIds === []) {
            throw new \InvalidArgumentException('Pilih minimal satu baris yang sudah tersimpan.');
        }

        $rows = $this->rekayasaRowsQuery($company, $periodYear)
            ->whereIn('id', $rekayasaRowIds)
            ->get();

        if ($rows->isEmpty()) {
            throw new \InvalidArgumentException('Baris terpilih tidak ditemukan pada scope perusahaan/tahun ini.');
        }

        return $rows
            ->map(fn (HiraImprovementRekayasaRow $row) => $this->mergedExportService->buildTemplateExportRow($row))
            ->values()
            ->all();
    }

    public function buildSpreadsheet(array $rows): Spreadsheet
    {
        return $this->mergedExportService->buildSpreadsheet($rows, 'Rekayasa Replikasi');
    }

    /**
     * @return array<string, mixed>
     */
    public function toClientRow(HiraImprovementRekayasaRow $rekayasaRow): array
    {
        $merged = $this->mergedExportService->buildMergedExportRow(
            $rekayasaRow,
            $rekayasaRow->replikasiDetail,
        );

        return [
            'rekayasaRowId' => $rekayasaRow->id,
            'detailId' => $rekayasaRow->replikasiDetail?->id,
            'company' => $rekayasaRow->company,
            'rekayasaAktivitas' => $rekayasaRow->aktivitas,
            'rekayasaSitePerusahaan' => $rekayasaRow->site_perusahaan,
            'site' => $merged['Site'],
            'perusahaan' => $merged['Perusahaan'],
            'aktivitas' => $merged['Aktivitas'],
            'kategoriRekayasa' => $merged['Kategori Rekayasa'],
            'originReplikasi' => $merged['Origin Replikasi (JIKA REPLIKASI)'],
            'pengendalianRekayasa' => $merged['Pengendalian Rekayasa'],
            'penjelasanProsesKerja' => $merged['Penjelasan/Proses Kerja'],
            'deteksi' => $merged['Deteksi'],
            'intervensi' => $merged['Intervensi'],
            'levelEfektivitas' => $merged['Level Efektivitas'],
            'nilaiRisikoAwal' => $merged['Nilai Risiko Awal'],
            'prediksiPenurunanRisiko' => $merged['Prediksi Penurunan Risiko'],
            'prediksiRisikoSisa' => $merged['Prediksi Risiko Sisa'],
            'target' => $merged['Target'],
            'totalPopulasi' => $merged['Total Populasi'],
            'targetReplikasiKomitmen' => $merged['Target Replikasi by Komitmen'],
            'aktualReplikasi' => $merged['Aktual Replikasi'],
            'satuan' => $merged['Satuan'],
            'jumlahMitraReplikasi' => $merged['Jumlah Mitra Yang Mereplikasi (MK Tambang, Marine, Ekplorasi Only)'],
            'tercoverBehira' => $merged['Apakah sudah tercover di BeHIRA?'],
            'potensiPeningkatanLevelEfektivitas' => $merged['Potensi Peningkatan Level Efektifitas'],
            'pengendalianPeningkatanLevelEfektivitas' => $merged['Pengendalian Rekayasa dengan Peningkatan Level Efektifitas'],
            'targetStandarisasiDueDate' => $merged['Target Standarisasi (Due date)'],
        ];
    }

    private function ensureRekayasaRowsExist(string $company, int $periodYear): void
    {
        $rekayasaService = app(HiraImprovementRekayasaService::class);
        if (! $rekayasaService->hasRows($company, $periodYear)) {
            $rekayasaService->seedDefaults($company, $periodYear);
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder<HiraImprovementRekayasaRow>
     */
    private function rekayasaRowsQuery(string $company, int $periodYear)
    {
        return HiraImprovementRekayasaRow::query()
            ->with('replikasiDetail')
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @param  list<array<string, string>>  $samples
     * @return array<string, string>|null
     */
    private function findSampleForRekayasaRow(HiraImprovementRekayasaRow $rekayasaRow, array $samples): ?array
    {
        foreach ($samples as $sample) {
            if (strcasecmp(trim($sample['aktivitas'] ?? ''), trim($rekayasaRow->aktivitas)) === 0) {
                return $sample;
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $clientRow
     * @return array<string, mixed>
     */
    private function mapClientToDetailAttributes(array $clientRow): array
    {
        return [
            'site' => trim((string) ($clientRow['site'] ?? '')),
            'perusahaan' => trim((string) ($clientRow['perusahaan'] ?? '')),
            'aktivitas' => trim((string) ($clientRow['aktivitas'] ?? '')),
            'kategori_rekayasa' => trim((string) ($clientRow['kategoriRekayasa'] ?? '')),
            'origin_replikasi' => trim((string) ($clientRow['originReplikasi'] ?? '')),
            'pengendalian_rekayasa' => trim((string) ($clientRow['pengendalianRekayasa'] ?? '')),
            'penjelasan_proses_kerja' => trim((string) ($clientRow['penjelasanProsesKerja'] ?? '')),
            'deteksi' => trim((string) ($clientRow['deteksi'] ?? '')),
            'intervensi' => trim((string) ($clientRow['intervensi'] ?? '')),
            'level_efektivitas' => trim((string) ($clientRow['levelEfektivitas'] ?? '')),
            'nilai_risiko_awal' => trim((string) ($clientRow['nilaiRisikoAwal'] ?? '')),
            'prediksi_penurunan_risiko' => trim((string) ($clientRow['prediksiPenurunanRisiko'] ?? '')),
            'prediksi_risiko_sisa' => trim((string) ($clientRow['prediksiRisikoSisa'] ?? '')),
            'target' => trim((string) ($clientRow['target'] ?? '')),
            'total_populasi' => trim((string) ($clientRow['totalPopulasi'] ?? '')),
            'target_replikasi_komitmen' => trim((string) ($clientRow['targetReplikasiKomitmen'] ?? '')),
            'aktual_replikasi' => trim((string) ($clientRow['aktualReplikasi'] ?? '')),
            'satuan' => trim((string) ($clientRow['satuan'] ?? '')),
            'jumlah_mitra_replikasi' => trim((string) ($clientRow['jumlahMitraReplikasi'] ?? '')),
            'tercover_behira' => trim((string) ($clientRow['tercoverBehira'] ?? '')),
            'potensi_peningkatan_level_efektivitas' => trim((string) ($clientRow['potensiPeningkatanLevelEfektivitas'] ?? '')),
            'pengendalian_pen_tingkatan_level_efektivitas' => trim((string) ($clientRow['pengendalianPeningkatanLevelEfektivitas'] ?? '')),
            'target_standar_isasi_due_date' => trim((string) ($clientRow['targetStandarisasiDueDate'] ?? '')),
        ];
    }

    /**
     * @param  array<string, mixed>  $clientRow
     */
    private function resolveRekayasaRow(string $company, int $periodYear, array $clientRow): ?HiraImprovementRekayasaRow
    {
        $rekayasaRowId = (int) ($clientRow['rekayasaRowId'] ?? 0);
        if ($rekayasaRowId > 0) {
            return HiraImprovementRekayasaRow::query()
                ->where('company', $company)
                ->where('period_year', $periodYear)
                ->whereKey($rekayasaRowId)
                ->first();
        }

        $aktivitas = trim((string) ($clientRow['aktivitas'] ?? $clientRow['_rekayasaAktivitas'] ?? ''));
        $sitePerusahaan = trim((string) ($clientRow['_rekayasaSitePerusahaan'] ?? ''));

        if ($aktivitas === '') {
            return null;
        }

        $query = HiraImprovementRekayasaRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->where('aktivitas', $aktivitas);

        if ($sitePerusahaan !== '') {
            $query->where('site_perusahaan', $sitePerusahaan);
        }

        return $query->first();
    }

    /**
     * @param  list<array<int, mixed>>  $rows
     */
    private function findHeaderRowIndex(array $rows): int
    {
        foreach ($rows as $index => $row) {
            foreach ($row as $cell) {
                $normalized = $this->normalizeHeaderKey((string) $cell);
                if (in_array($normalized, ['id', 'site', 'aktivitas', 'perusahaan', 'company'], true)) {
                    return $index;
                }
            }
        }

        return 0;
    }

    /**
     * @param  array<int, mixed>  $headerRow
     * @return array<int, string>
     */
    private function buildColumnMap(array $headerRow): array
    {
        $map = [];

        foreach ($headerRow as $index => $label) {
            $key = $this->resolveClientKey((string) $label);
            if ($key !== null) {
                $map[(int) $index] = $key;
            }
        }

        return $map;
    }

    /**
     * @param  array<int, mixed>  $row
     * @param  array<int, string>  $columnMap
     * @return array<string, string>|null
     */
    private function mapExcelRow(array $row, array $columnMap): ?array
    {
        $client = [];

        foreach ($columnMap as $index => $key) {
            if (str_starts_with($key, '_')) {
                continue;
            }
            $client[$key] = $this->normalizeCellValue($row[$index] ?? '');
        }

        foreach ($columnMap as $index => $key) {
            if ($key === '_rekayasaAktivitas' && empty($client['rekayasaRowId'])) {
                $client['aktivitas'] = $this->normalizeCellValue($row[$index] ?? '');
            }
            if ($key === '_rekayasaSitePerusahaan') {
                $client['_rekayasaSitePerusahaan'] = $this->normalizeCellValue($row[$index] ?? '');
            }
        }

        if (($client['rekayasaRowId'] ?? '') === '' && ($client['aktivitas'] ?? '') === '') {
            return null;
        }

        return $client;
    }

    private function resolveClientKey(string $label): ?string
    {
        $normalized = $this->normalizeHeaderKey($label);
        if ($normalized === '' || $normalized === 'no') {
            return null;
        }

        return self::CLIENT_KEY_BY_HEADER[$normalized] ?? null;
    }

    private function normalizeHeaderKey(string $label): string
    {
        $label = preg_replace('/^\xEF\xBB\xBF/u', '', $label) ?? $label;
        $label = html_entity_decode($label, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $label = str_replace("\xc2\xa0", ' ', $label);
        $label = preg_replace('/^\*+\s*/u', '', $label) ?? $label;
        $label = mb_strtolower(trim($label));
        $label = preg_replace('/\s+/u', ' ', $label) ?? $label;

        return $label;
    }

    private function normalizeCellValue(mixed $value): string
    {
        if ($value === null) {
            return '';
        }

        if (is_float($value) && $value > 25569 && $value < 60000) {
            try {
                return ExcelDate::excelToDateTimeObject($value)->format('Y-m-d');
            } catch (\Throwable) {
                // keep raw value
            }
        }

        $text = trim((string) $value);
        $text = str_replace("\xc2\xa0", ' ', $text);

        return $text;
    }
}
