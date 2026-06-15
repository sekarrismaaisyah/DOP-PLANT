<?php

declare(strict_types=1);

namespace App\Services\Hira;

use App\Models\HiraImprovementRekayasaRow;
use Illuminate\Support\Facades\DB;

final class HiraImprovementRekayasaService
{
    public const EXPORT_HEADERS = [
        'Aktivitas',
        'Site Perusahaan',
        'Pengendalian Rekayasa',
        'Deteksi',
        'Intervensi',
        'Prediksi Penurunan Risiko',
        'Penjelasan/Proses Kerja',
    ];

    /**
     * @return list<array<string, mixed>>
     */
    public function listForScope(string $company, int $periodYear): array
    {
        if (! $this->hasRows($company, $periodYear)) {
            $this->seedDefaults($company, $periodYear);
        }

        return HiraImprovementRekayasaRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (HiraImprovementRekayasaRow $row) => $this->toClientRow($row))
            ->values()
            ->all();
    }

    public function hasRows(string $company, int $periodYear): bool
    {
        return HiraImprovementRekayasaRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->exists();
    }

    public function seedDefaults(string $company, int $periodYear): void
    {
        $samples = HiraImprovementRekayasaSampleData::rows();

        DB::transaction(function () use ($samples, $company, $periodYear) {
            foreach ($samples as $index => $sample) {
                HiraImprovementRekayasaRow::query()->create(
                    $this->mapClientToAttributes($sample, $company, $periodYear, $index),
                );
            }
        });
    }

    public function resetToSample(string $company, int $periodYear): void
    {
        DB::transaction(function () use ($company, $periodYear) {
            HiraImprovementRekayasaRow::query()
                ->where('company', $company)
                ->where('period_year', $periodYear)
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
            $keptIds = [];

            foreach ($clientRows as $index => $clientRow) {
                $id = isset($clientRow['id']) ? (int) $clientRow['id'] : 0;
                $attrs = $this->mapClientToAttributes($clientRow, $company, $periodYear, $index);

                if ($id > 0) {
                    $model = HiraImprovementRekayasaRow::query()
                        ->where('company', $company)
                        ->where('period_year', $periodYear)
                        ->whereKey($id)
                        ->first();

                    if ($model) {
                        $model->update($attrs);
                        $keptIds[] = $model->id;

                        continue;
                    }
                }

                $model = HiraImprovementRekayasaRow::query()->create($attrs);
                $keptIds[] = $model->id;
            }

            HiraImprovementRekayasaRow::query()
                ->where('company', $company)
                ->where('period_year', $periodYear)
                ->whereNotIn('id', $keptIds)
                ->delete();

            return $this->listForScope($company, $periodYear);
        });
    }

    public function deleteRow(string $company, int $periodYear, int $id): bool
    {
        return (bool) HiraImprovementRekayasaRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->whereKey($id)
            ->delete();
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function importFromText(string $company, int $periodYear, string $text): array
    {
        $this->assertImportableText($text);

        $parsed = $this->parseImportText($text);

        if ($parsed === []) {
            throw new \InvalidArgumentException(
                'Tidak ada baris valid pada file impor. Pastikan baris pertama berisi header '
                . implode(', ', self::EXPORT_HEADERS)
                . ' dan minimal kolom Aktivitas atau Site Perusahaan terisi. '
                . 'Gunakan Download Template (CSV), edit, lalu upload tanpa mengubah format ke .xlsx.',
            );
        }

        return $this->syncRows($company, $periodYear, $parsed);
    }

    private function assertImportableText(string $text): void
    {
        if (trim($text) === '') {
            throw new \InvalidArgumentException('File impor kosong.');
        }

        if (str_starts_with($text, 'PK')) {
            throw new \InvalidArgumentException(
                'Format .xlsx tidak didukung. Unduh template CSV, isi data, lalu upload file .csv.',
            );
        }

        if (str_starts_with($text, "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1")) {
            throw new \InvalidArgumentException(
                'Format Excel biner (.xls asli) tidak didukung. Simpan ulang sebagai CSV (UTF-8) lalu upload.',
            );
        }
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function parseImportText(string $text): array
    {
        $raw = stripos($text, '<table') !== false
            ? $this->parseHtmlTableImport($text)
            : $this->parseCsvImport($text);

        $parsed = [];
        foreach ($raw as $i => $entry) {
            $mapped = $this->mapImportRow($entry['assoc'], $i, $entry['cells']);
            if ($mapped !== null) {
                $parsed[] = $mapped;
            }
        }

        return $parsed;
    }

    /**
     * @param  array<string, string>  $row
     * @param  list<string>  $cells
     * @return array<string, string>|null
     */
    private function mapImportRow(array $row, int $index, array $cells = []): ?array
    {
        $cells = array_map(
            fn (string $cell) => $this->normalizeImportValue($cell),
            $cells !== [] ? $cells : array_map(
                fn ($value) => $this->normalizeImportValue((string) $value),
                array_values($row),
            ),
        );

        if (count($cells) >= 7) {
            $aktivitas = $cells[0];
            $site = $cells[1];
            $pengendalian = $cells[2];
            $deteksi = $cells[3];
            $intervensi = $cells[4];
            $prediksi = $cells[5];
            $penjelasan = $cells[6];
        } else {
            $aktivitas = $this->extractImportField($row, ['aktivitas']);
            $site = $this->extractImportField($row, ['site perusahaan', 'site']);
            $pengendalian = $this->extractImportField($row, ['pengendalian rekayasa', 'pengendalian']);
            $deteksi = $this->extractImportField($row, ['deteksi']);
            $intervensi = $this->extractImportField($row, ['intervensi']);
            $prediksi = $this->extractImportField($row, ['prediksi penurunan risiko', 'prediksi']);
            $penjelasan = $this->extractImportField($row, [
                'penjelasan/proses kerja',
                'penjelasan / proses kerja',
                'penjelasan proses kerja',
            ]);
        }

        if ($aktivitas === '' && $site === '' && $pengendalian === '' && $deteksi === ''
            && $intervensi === '' && $prediksi === '' && $penjelasan === '') {
            return null;
        }

        return [
            'aktivitas' => $aktivitas,
            'sitePerusahaan' => $site,
            'pengendalianRekayasa' => $pengendalian,
            'deteksi' => $deteksi,
            'intervensi' => $intervensi,
            'prediksiPenurunanRisiko' => $prediksi,
            'penjelasanProsesKerja' => $penjelasan,
        ];
    }

    /**
     * @param  array<string, string>  $row
     * @param  list<string>  $aliases
     */
    private function extractImportField(array $row, array $aliases): string
    {
        $aliasKeys = array_map(fn (string $alias) => $this->normalizeHeaderKey($alias), $aliases);

        foreach ($row as $key => $value) {
            $normalizedKey = $this->normalizeHeaderKey((string) $key);
            if (in_array($normalizedKey, $aliasKeys, true)) {
                return $this->normalizeImportValue((string) $value);
            }
        }

        return '';
    }

    private function normalizeImportValue(string $value): string
    {
        $value = preg_replace('/^\xEF\xBB\xBF/u', '', $value) ?? $value;
        $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $value = str_replace("\xc2\xa0", ' ', $value);
        $value = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $value) ?? $value;

        return trim($value);
    }

    private function normalizeHeaderKey(string $label): string
    {
        $label = preg_replace('/^\xEF\xBB\xBF/u', '', $label) ?? $label;
        $label = html_entity_decode($label, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $label = str_replace("\xc2\xa0", ' ', $label);
        $label = mb_strtolower(trim($label));
        $label = preg_replace('/\s+/u', ' ', $label) ?? $label;

        return $label;
    }

    /**
     * @return list<array<string, string>>
     */
    private function parseHtmlTableImport(string $text): array
    {
        $doc = new \DOMDocument();
        @$doc->loadHTML($text);
        $trs = $doc->getElementsByTagName('tr');
        $rows = [];

        foreach ($trs as $tr) {
            $cells = $this->extractTableRowCells($tr);
            if ($cells !== []) {
                $rows[] = $cells;
            }
        }

        if ($rows === []) {
            return [];
        }

        $headerIndex = $this->findHeaderRowIndex($rows);
        $head = $this->normalizeImportHeader($rows[$headerIndex] ?? []);
        if ($head === []) {
            return [];
        }

        $raw = [];
        foreach (array_slice($rows, $headerIndex + 1) as $row) {
            $entry = $this->buildImportRowEntry($head, $row);
            if ($entry !== null) {
                $raw[] = $entry;
            }
        }

        return $raw;
    }

    /**
     * @return list<array{assoc: array<string, string>, cells: list<string>}>
     */
    private function parseCsvImport(string $text): array
    {
        if (trim($text) === '') {
            return [];
        }

        $delimiter = $this->detectCsvDelimiter($text);

        $handle = fopen('php://memory', 'r+');
        if ($handle === false) {
            return [];
        }

        fwrite($handle, $text);
        rewind($handle);

        $head = $this->readCsvHeaderRow($handle, $delimiter);
        if ($head === []) {
            fclose($handle);

            return [];
        }

        $raw = [];
        while (($cells = fgetcsv($handle, 0, $delimiter)) !== false) {
            if ($cells === [null]) {
                continue;
            }

            $hasValue = false;
            foreach ($cells as $cell) {
                if ($this->normalizeImportValue((string) $cell) !== '') {
                    $hasValue = true;
                    break;
                }
            }
            if (! $hasValue) {
                continue;
            }

            $entry = $this->buildImportRowEntry($head, $cells);
            if ($entry !== null) {
                $raw[] = $entry;
            }
        }

        fclose($handle);

        return $raw;
    }

    /**
     * @param  resource  $handle
     * @return list<string>
     */
    private function readCsvHeaderRow($handle, string $delimiter): array
    {
        while (($head = $this->normalizeImportHeader(fgetcsv($handle, 0, $delimiter) ?: [])) !== []) {
            if (count($head) === 1 && str_starts_with(strtolower($head[0]), 'sep=')) {
                continue;
            }

            if ($this->findHeaderRowIndex([$head]) === 0) {
                return $head;
            }
        }

        return [];
    }

    /**
     * @param  list<string>  $head
     * @param  list<string|null>  $cells
     * @return array{assoc: array<string, string>, cells: list<string>}|null
     */
    private function buildImportRowEntry(array $head, array $cells): ?array
    {
        [$head, $cells] = $this->alignImportColumns($head, $cells);
        if ($head === []) {
            return null;
        }

        return [
            'assoc' => $this->combineImportRow($head, $cells),
            'cells' => $cells,
        ];
    }

    /**
     * @param  list<string>  $head
     * @param  list<string|null>  $cells
     * @return array{0: list<string>, 1: list<string>}
     */
    private function alignImportColumns(array $head, array $cells): array
    {
        $head = $this->normalizeImportHeader($head);
        $cells = array_map(
            fn ($cell) => $this->normalizeImportValue((string) $cell),
            $cells,
        );

        while ($head !== [] && $head[0] === '') {
            array_shift($head);
            if ($cells !== []) {
                array_shift($cells);
            }
        }

        while (count($cells) > count($head) && ($cells[0] ?? '') === '' && count($head) >= 7) {
            array_shift($cells);
        }

        if (count($cells) > 7) {
            $tail = array_slice($cells, 6);
            $cells = array_merge(array_slice($cells, 0, 6), [implode(', ', $tail)]);
        }

        return [$head, $cells];
    }

    /**
     * @param  list<string|null>  $head
     * @return list<string>
     */
    private function normalizeImportHeader(array $head): array
    {
        $normalized = [];
        foreach ($head as $index => $column) {
            $label = $this->normalizeImportValue((string) $column);
            $normalized[] = $label;
        }

        while ($normalized !== [] && end($normalized) === '') {
            array_pop($normalized);
        }

        return $normalized;
    }

    /**
     * @param  list<string>  $head
     * @param  list<string|null>  $cells
     * @return array<string, string>
     */
    private function combineImportRow(array $head, array $cells): array
    {
        $count = count($head);
        if ($count === 0) {
            return [];
        }

        $values = array_map(
            fn ($cell) => $this->normalizeImportValue((string) $cell),
            array_pad(array_slice($cells, 0, $count), $count, ''),
        );

        return array_combine($head, $values) ?: [];
    }

    /**
     * @return list<string>
     */
    private function extractTableRowCells(\DOMElement $tr): array
    {
        $cells = [];
        foreach ($tr->childNodes as $child) {
            if (! $child instanceof \DOMElement) {
                continue;
            }
            if (! in_array($child->nodeName, ['td', 'th'], true)) {
                continue;
            }
            $text = preg_replace('/\s+/u', ' ', $child->textContent) ?? '';
            $cells[] = trim($text);
        }

        return $cells;
    }

    /**
     * @param  list<list<string>>  $rows
     */
    private function findHeaderRowIndex(array $rows): int
    {
        foreach ($rows as $index => $row) {
            foreach ($row as $cell) {
                if ($this->normalizeHeaderKey((string) $cell) === 'aktivitas') {
                    return $index;
                }
            }
        }

        return 0;
    }

    private function detectCsvDelimiter(string $text): string
    {
        $lines = preg_split('/\r\n|\n|\r/', $text) ?: [];
        $scores = [
            ',' => 0,
            ';' => 0,
            "\t" => 0,
        ];

        foreach (array_slice($lines, 0, 5) as $line) {
            if (trim($line) === '' || str_starts_with(strtolower(trim($line)), 'sep=')) {
                continue;
            }

            $scores[','] += substr_count($line, ',');
            $scores[';'] += substr_count($line, ';');
            $scores["\t"] += substr_count($line, "\t");
        }

        arsort($scores);

        return (string) array_key_first($scores);
    }

    /**
     * @return list<array<string, string>>
     */
    public function exportRows(string $company, int $periodYear): array
    {
        return HiraImprovementRekayasaRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (HiraImprovementRekayasaRow $row) => $this->toExportRow($row))
            ->values()
            ->all();
    }

    /**
     * @return list<array<string, string>>
     */
    public function templateRows(): array
    {
        return array_map(
            fn (array $row) => $this->clientToExportRow($row),
            HiraImprovementRekayasaSampleData::rows(),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toClientRow(HiraImprovementRekayasaRow $row): array
    {
        return [
            'id' => $row->id,
            'aktivitas' => $row->aktivitas,
            'sitePerusahaan' => $row->site_perusahaan,
            'pengendalianRekayasa' => $row->pengendalian_rekayasa ?? '',
            'deteksi' => $row->deteksi,
            'intervensi' => $row->intervensi,
            'prediksiPenurunanRisiko' => $row->prediksi_penurunan_risiko,
            'penjelasanProsesKerja' => $row->penjelasan_proses_kerja ?? '',
        ];
    }

    /**
     * @param  array<string, mixed>  $client
     * @return array<string, mixed>
     */
    private function mapClientToAttributes(array $client, string $company, int $periodYear, int $sortOrder): array
    {
        return [
            'company' => $company,
            'period_year' => $periodYear,
            'aktivitas' => trim((string) ($client['aktivitas'] ?? '')),
            'site_perusahaan' => trim((string) ($client['sitePerusahaan'] ?? '')),
            'pengendalian_rekayasa' => trim((string) ($client['pengendalianRekayasa'] ?? '')),
            'deteksi' => trim((string) ($client['deteksi'] ?? '')),
            'intervensi' => trim((string) ($client['intervensi'] ?? '')),
            'prediksi_penurunan_risiko' => trim((string) ($client['prediksiPenurunanRisiko'] ?? '')),
            'penjelasan_proses_kerja' => trim((string) ($client['penjelasanProsesKerja'] ?? '')),
            'sort_order' => $sortOrder,
        ];
    }

    /**
     * @return array<string, string>
     */
    private function toExportRow(HiraImprovementRekayasaRow $row): array
    {
        return $this->clientToExportRow($this->toClientRow($row));
    }

    /**
     * @param  array<string, mixed>  $client
     * @return array<string, string>
     */
    private function clientToExportRow(array $client): array
    {
        return [
            'Aktivitas' => (string) ($client['aktivitas'] ?? ''),
            'Site Perusahaan' => (string) ($client['sitePerusahaan'] ?? ''),
            'Pengendalian Rekayasa' => (string) ($client['pengendalianRekayasa'] ?? ''),
            'Deteksi' => (string) ($client['deteksi'] ?? ''),
            'Intervensi' => (string) ($client['intervensi'] ?? ''),
            'Prediksi Penurunan Risiko' => (string) ($client['prediksiPenurunanRisiko'] ?? ''),
            'Penjelasan/Proses Kerja' => (string) ($client['penjelasanProsesKerja'] ?? ''),
        ];
    }
}
