<?php

declare(strict_types=1);

namespace App\Services\Hira;

use App\Models\HiraImprovementDetailRow;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final class HiraImprovementDetailService
{
    public const OPT_SITES = ['HO', 'BMO 1', 'BMO 2', 'BMO 3', 'GMO', 'LMO', 'SMO', 'Marine', 'HOTE', 'PMO'];

    public const OPT_RNR = ['R', 'NR'];

    public const OPT_FAKTOR = ['Men', 'Met', 'Mac', 'Mat', 'Env'];

    public const OPT_STATUS = ['Not Started', 'In Progress', 'Implemented', 'Verified Effective', 'Closed'];

    public const OPT_TP = ['ELM', 'SUB', 'ENG', 'ADM', 'APD'];

    public const OPT_EXPOSURE = ['Unit', 'Man Power', 'Man Hour'];

    public const OPT_CONTROL = [
        'L1 - Eliminasi',
        'L2 - Mencegah',
        'L3 - Mendeteksi + Intervensi Manusia',
        'L4 - Mitigasi Pasif',
        'L5 - Deteksi Manual',
    ];

    public const OPT_TARGET = ['Low', 'Medium', 'High', 'Significant'];

    /**
     * @return list<array<string, mixed>>
     */
    public function listForScope(string $company, int $periodYear): array
    {
        if (! $this->hasRows($company, $periodYear)) {
            $this->seedDefaults($company, $periodYear);
        }

        return HiraImprovementDetailRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (HiraImprovementDetailRow $row) => $this->toClientRow($row))
            ->values()
            ->all();
    }

    public function hasRows(string $company, int $periodYear): bool
    {
        return HiraImprovementDetailRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->exists();
    }

    public function seedDefaults(string $company, int $periodYear): void
    {
        $samples = HiraImprovementDetailSampleData::rows();

        DB::transaction(function () use ($samples, $company, $periodYear) {
            foreach ($samples as $index => $sample) {
                HiraImprovementDetailRow::query()->create(
                    $this->mapClientToAttributes($sample, $company, $periodYear, $index),
                );
            }
        });
    }

    public function resetToSample(string $company, int $periodYear): void
    {
        DB::transaction(function () use ($company, $periodYear) {
            HiraImprovementDetailRow::query()
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
                    $model = HiraImprovementDetailRow::query()
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

                $model = HiraImprovementDetailRow::query()->create($attrs);
                $keptIds[] = $model->id;
            }

            HiraImprovementDetailRow::query()
                ->where('company', $company)
                ->where('period_year', $periodYear)
                ->whereNotIn('id', $keptIds)
                ->delete();

            return $this->listForScope($company, $periodYear);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function createRow(string $company, int $periodYear, array $clientRow, int $sortOrder = 0): array
    {
        $attrs = $this->mapClientToAttributes($clientRow, $company, $periodYear, $sortOrder);
        $model = HiraImprovementDetailRow::query()->create($attrs);

        return $this->toClientRow($model);
    }

    public function deleteRow(string $company, int $periodYear, int $id): bool
    {
        return (bool) HiraImprovementDetailRow::query()
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
        $parsed = $this->parseImportText($text);

        if ($parsed === []) {
            throw new \InvalidArgumentException('Tidak ada baris valid pada file impor.');
        }

        return $this->syncRows($company, $periodYear, $parsed);
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function parseImportText(string $text): array
    {
        $raw = [];

        if (stripos($text, '<table') !== false) {
            $doc = new \DOMDocument();
            @$doc->loadHTML($text);
            $trs = $doc->getElementsByTagName('tr');
            $rows = [];
            foreach ($trs as $tr) {
                $cells = [];
                foreach ($tr->childNodes as $child) {
                    if ($child instanceof \DOMElement && in_array($child->nodeName, ['td', 'th'], true)) {
                        $cells[] = trim($child->textContent);
                    }
                }
                if ($cells !== []) {
                    $rows[] = $cells;
                }
            }
            $head = array_shift($rows) ?? [];
            foreach ($rows as $row) {
                $raw[] = array_combine($head, array_pad($row, count($head), '')) ?: [];
            }
        } else {
            $raw = $this->parseCsv($text);
        }

        $parsed = [];
        foreach ($raw as $i => $r) {
            $plan = trim((string) ($r['Improvement Plan'] ?? $r['Pengendalian Lanjutan / Improvement'] ?? ''));
            $section = trim((string) ($r['Sections'] ?? ''));
            $hazard = trim((string) ($r['Bahaya/Aspek Lingkungan/Penyebab Potensial'] ?? ''));

            if ($plan === '' && $section === '' && $hazard === '') {
                continue;
            }

            $parsed[] = [
                'improvementPlan' => $plan !== '' ? $plan : 'Imported Improvement ' . ($i + 1),
                'section' => $section !== '' ? $section : 'Section',
                'activity' => trim((string) ($r['Aktivitas'] ?? 'Aktivitas')),
                'subActivity' => trim((string) ($r['Sub Aktivitas'] ?? 'Sub Aktivitas')),
                'subSubActivity' => trim((string) ($r['Sub sub Aktivitas'] ?? 'Sub-sub Aktivitas')),
                'rnr' => trim((string) ($r['R/NR'] ?? 'R')),
                'site' => trim((string) ($r['Site'] ?? 'HO')),
                'faktor' => trim((string) ($r['Faktor'] ?? 'Men')),
                'hazard' => $hazard !== '' ? $hazard : 'Bahaya/Aspek Lingkungan/Penyebab Potensial',
                'eventPotential' => trim((string) ($r['Kejadian / Potensi'] ?? 'Kejadian / Potensi')),
                'kepAwal' => (string) ($r['Kep Awal'] ?? '1'),
                'konseqAwal' => (string) ($r['Konseq Awal'] ?? '1'),
                'tpAwal' => trim((string) ($r['TP Awal'] ?? 'ADM')),
                'existingControl' => trim((string) ($r['Pengendalian yang dilakukan (Tertinggi)'] ?? '')),
                'ownerExisting' => trim((string) ($r['Pemilik Pengendalian'] ?? '')),
                'controlLevel' => trim((string) ($r['Level Efektivitas'] ?? 'L3 - Mendeteksi + Intervensi Manusia')),
                'exposureType' => trim((string) ($r['Jenis Exposure'] ?? 'Unit')),
                'exposureBeforeValue' => (string) ($r['Exposure Aktual'] ?? '0'),
                'exposureControlValue' => (string) ($r['Exposure Pengendalian'] ?? '0'),
                'kepSisa' => (string) ($r['Kep Sisa'] ?? '1'),
                'konseqSisa' => (string) ($r['Konseq Sisa'] ?? '1'),
                'targetRisk' => trim((string) ($r['Target Nilai Risiko'] ?? 'Medium')),
                'tpLanjutan' => trim((string) ($r['TP Lanjutan'] ?? 'ADM')),
                'ownerLanjutan' => trim((string) ($r['Pemilik Lanjutan'] ?? 'Owner')),
                'startDate' => trim((string) ($r['Tanggal Mulai'] ?? '')),
                'targetDate' => trim((string) ($r['Target Tanggal'] ?? '')),
                'status' => trim((string) ($r['Status'] ?? 'Not Started')),
            ];
        }

        return $parsed;
    }

    /**
     * @return list<array<string, string>>
     */
    public function exportRows(string $company, int $periodYear): array
    {
        $rows = HiraImprovementDetailRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $out = [];
        foreach ($rows as $i => $row) {
            $client = $this->toClientRow($row);
            $calc = $this->calc($client);
            $out[] = [
                'No' => (string) ($i + 1),
                'Improvement Plan' => (string) $client['improvementPlan'],
                'Sections' => (string) $client['section'],
                'Aktivitas' => (string) $client['activity'],
                'Sub Aktivitas' => (string) $client['subActivity'],
                'Sub sub Aktivitas' => (string) $client['subSubActivity'],
                'R/NR' => (string) $client['rnr'],
                'Site' => (string) $client['site'],
                'Faktor' => (string) $client['faktor'],
                'Bahaya/Aspek Lingkungan/Penyebab Potensial' => (string) $client['hazard'],
                'Kejadian / Potensi' => (string) $client['eventPotential'],
                'Kep Awal' => (string) $calc['ka'],
                'Konseq Awal' => (string) $calc['ca'],
                'Nilai Risiko Awal' => (string) $calc['scoreA'],
                'Nilai Resiko awal' => (string) $calc['bA']['label'],
                'TP Awal' => (string) $client['tpAwal'],
                'Pengendalian yang dilakukan (Tertinggi)' => (string) $client['existingControl'],
                'Pemilik Pengendalian' => (string) $client['ownerExisting'],
                'Pengendalian Lanjutan / Improvement' => (string) $client['improvementPlan'],
                'Level Efektivitas' => (string) $client['controlLevel'],
                'Jenis Exposure' => (string) $client['exposureType'],
                'Exposure Aktual' => (string) $calc['exA'],
                'Exposure Pengendalian' => (string) $calc['exC'],
                '% Exposure Covered' => $this->pct($calc['cover']),
                'Kep Sisa' => (string) $calc['ks'],
                'Konseq Sisa' => (string) $calc['cs'],
                'Nilai Risiko Sisa' => (string) $calc['scoreS'],
                'Level Sisa' => (string) $calc['bS']['label'],
                'Target Nilai Risiko' => (string) $client['targetRisk'],
                'TP Lanjutan' => (string) $client['tpLanjutan'],
                'Pemilik Lanjutan' => (string) $client['ownerLanjutan'],
                'Tanggal Mulai' => (string) $client['startDate'],
                'Target Tanggal' => (string) $client['targetDate'],
                'Status' => (string) $client['status'],
                'Decision' => (string) $calc['decision'],
            ];
        }

        return $out;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    public function calc(array $row): array
    {
        $ka = $this->clampInt($row['kepAwal'] ?? 0, 0, 5);
        $ca = $this->clampInt($row['konseqAwal'] ?? 0, 0, 5);
        $controlLevel = (string) ($row['controlLevel'] ?? '');
        $ks = $controlLevel === 'L1 - Eliminasi'
            ? 0
            : $this->clampInt($row['kepSisa'] ?? 0, 0, 5);
        $cs = $controlLevel === 'L1 - Eliminasi'
            ? 0
            : $this->clampInt($row['konseqSisa'] ?? 0, 0, 5);
        $exA = max($this->num($row['exposureBeforeValue'] ?? 0), 0.0);
        $exC = min(max($this->num($row['exposureControlValue'] ?? 0), 0.0), $exA);
        $cover = $exA > 0 ? $exC / $exA : 0.0;
        $scoreA = $ka * $ca;
        $scoreS = $ks * $cs;
        $bA = $this->band($scoreA);
        $bS = $this->band($scoreS);
        $targetRisk = (string) ($row['targetRisk'] ?? 'Medium');
        $status = (string) ($row['status'] ?? '');

        $decision = 'Monitor';
        $dcls = 'blue';
        if ($this->rank($bS['label']) > $this->rank($targetRisk)) {
            $decision = 'Belum Capai Target';
            $dcls = 'significant';
        } elseif ($cover < 0.75) {
            $decision = 'Lanjutkan Coverage';
            $dcls = 'high';
        } elseif (! in_array($status, ['Verified Effective', 'Closed'], true)) {
            $decision = 'Verifikasi Efektivitas';
            $dcls = 'medium';
        } elseif ($bS['label'] === 'Low') {
            $decision = 'Accept / Close';
            $dcls = 'low';
        }

        return [
            'ka' => $ka,
            'ca' => $ca,
            'ks' => $ks,
            'cs' => $cs,
            'exA' => $exA,
            'exC' => $exC,
            'cover' => $cover,
            'scoreA' => $scoreA,
            'scoreS' => $scoreS,
            'bA' => $bA,
            'bS' => $bS,
            'decision' => $decision,
            'dcls' => $dcls,
        ];
    }

    /**
     * @return array{label: string, cls: string}
     */
    public function band(int $score): array
    {
        if ($score >= 17) {
            return ['label' => 'Significant', 'cls' => 'significant'];
        }
        if ($score >= 10) {
            return ['label' => 'High', 'cls' => 'high'];
        }
        if ($score >= 5) {
            return ['label' => 'Medium', 'cls' => 'medium'];
        }
        if ($score >= 1) {
            return ['label' => 'Low', 'cls' => 'low'];
        }

        return ['label' => 'Eliminated', 'cls' => 'low'];
    }

    public function rank(string $label): int
    {
        return match ($label) {
            'Eliminated' => 0,
            'Low' => 1,
            'Medium' => 2,
            'High' => 3,
            'Significant' => 4,
            default => 0,
        };
    }

    public function pct(float $value): string
    {
        return number_format($value * 100, 1, ',', '.') . '%';
    }

    /**
     * @return array<string, mixed>
     */
    public function toClientRow(HiraImprovementDetailRow $row): array
    {
        return [
            'id' => $row->id,
            'improvementPlan' => $row->improvement_plan,
            'section' => $row->section,
            'activity' => $row->activity,
            'subActivity' => $row->sub_activity,
            'subSubActivity' => $row->sub_sub_activity,
            'rnr' => $row->rnr,
            'site' => $row->site,
            'faktor' => $row->faktor,
            'hazard' => $row->hazard ?? '',
            'eventPotential' => $row->event_potential ?? '',
            'kepAwal' => (string) $row->kep_awal,
            'konseqAwal' => (string) $row->konseq_awal,
            'tpAwal' => $row->tp_awal,
            'existingControl' => $row->existing_control ?? '',
            'ownerExisting' => $row->owner_existing ?? '',
            'controlLevel' => $row->control_level,
            'exposureType' => $row->exposure_type,
            'exposureBeforeValue' => (string) $row->exposure_before_value,
            'exposureControlValue' => (string) $row->exposure_control_value,
            'kepSisa' => (string) $row->kep_sisa,
            'konseqSisa' => (string) $row->konseq_sisa,
            'targetRisk' => $row->target_risk,
            'tpLanjutan' => $row->tp_lanjutan,
            'ownerLanjutan' => $row->owner_lanjutan ?? '',
            'startDate' => $row->start_date?->format('Y-m-d') ?? '',
            'targetDate' => $row->target_date?->format('Y-m-d') ?? '',
            'status' => $row->status,
        ];
    }

    /**
     * @param  array<string, mixed>  $client
     * @return array<string, mixed>
     */
    private function mapClientToAttributes(array $client, string $company, int $periodYear, int $sortOrder): array
    {
        $start = trim((string) ($client['startDate'] ?? ''));
        $target = trim((string) ($client['targetDate'] ?? ''));

        return [
            'company' => $company,
            'period_year' => $periodYear,
            'improvement_plan' => trim((string) ($client['improvementPlan'] ?? 'New Improvement Plan')),
            'section' => trim((string) ($client['section'] ?? '')),
            'activity' => trim((string) ($client['activity'] ?? '')),
            'sub_activity' => trim((string) ($client['subActivity'] ?? '')),
            'sub_sub_activity' => trim((string) ($client['subSubActivity'] ?? '')),
            'rnr' => trim((string) ($client['rnr'] ?? 'R')),
            'site' => trim((string) ($client['site'] ?? 'HO')),
            'faktor' => trim((string) ($client['faktor'] ?? 'Men')),
            'hazard' => trim((string) ($client['hazard'] ?? '')),
            'event_potential' => trim((string) ($client['eventPotential'] ?? '')),
            'kep_awal' => $this->clampInt($client['kepAwal'] ?? 1, 0, 5),
            'konseq_awal' => $this->clampInt($client['konseqAwal'] ?? 1, 0, 5),
            'tp_awal' => trim((string) ($client['tpAwal'] ?? 'ADM')),
            'existing_control' => trim((string) ($client['existingControl'] ?? '')),
            'owner_existing' => trim((string) ($client['ownerExisting'] ?? '')),
            'control_level' => trim((string) ($client['controlLevel'] ?? 'L3 - Mendeteksi + Intervensi Manusia')),
            'exposure_type' => trim((string) ($client['exposureType'] ?? 'Unit')),
            'exposure_before_value' => $this->num($client['exposureBeforeValue'] ?? 0),
            'exposure_control_value' => $this->num($client['exposureControlValue'] ?? 0),
            'kep_sisa' => $this->clampInt($client['kepSisa'] ?? 1, 0, 5),
            'konseq_sisa' => $this->clampInt($client['konseqSisa'] ?? 1, 0, 5),
            'target_risk' => trim((string) ($client['targetRisk'] ?? 'Medium')),
            'tp_lanjutan' => trim((string) ($client['tpLanjutan'] ?? 'ADM')),
            'owner_lanjutan' => trim((string) ($client['ownerLanjutan'] ?? '')),
            'start_date' => $start !== '' ? $start : null,
            'target_date' => $target !== '' ? $target : null,
            'status' => trim((string) ($client['status'] ?? 'Not Started')),
            'sort_order' => $sortOrder,
        ];
    }

    /**
     * @return list<array<string, string>>
     */
    private function parseCsv(string $text): array
    {
        $lines = preg_split('/\r\n|\n|\r/', $text) ?: [];
        if ($lines === []) {
            return [];
        }

        $head = str_getcsv(array_shift($lines) ?: '');
        $rows = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }
            $cells = str_getcsv($line);
            $rows[] = array_combine($head, array_pad($cells, count($head), '')) ?: [];
        }

        return $rows;
    }

    private function num(mixed $value): float
    {
        $n = is_numeric($value) ? (float) $value : 0.0;

        return is_finite($n) ? $n : 0.0;
    }

    private function clampInt(mixed $value, int $min, int $max): int
    {
        $n = (int) round($this->num($value));

        return min($max, max($min, $n));
    }
}
