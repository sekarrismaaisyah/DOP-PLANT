<?php

declare(strict_types=1);

namespace App\Services\Hira;

use App\Models\HiraImprovementDetailRow;
use App\Models\HiraImprovementScurveTask;
use Illuminate\Support\Facades\DB;

final class HiraImprovementScurveTaskService
{
    public const OPT_STATUS = ['Open', 'Progress', 'Done', 'Closed'];

    /**
     * @return list<array<string, mixed>>
     */
    public function listForScope(string $company, int $periodYear): array
    {
        if (! $this->hasTasks($company, $periodYear)) {
            $this->seedFromDetailRows($company, $periodYear);
        }

        return HiraImprovementScurveTask::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('improvement_plan')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (HiraImprovementScurveTask $task) => $this->toClientTask($task))
            ->values()
            ->all();
    }

    /**
     * @return list<string>
     */
    public function improvementPlans(string $company, int $periodYear): array
    {
        return HiraImprovementDetailRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('improvement_plan')
            ->distinct()
            ->pluck('improvement_plan')
            ->filter()
            ->values()
            ->all();
    }

    public function hasTasks(string $company, int $periodYear): bool
    {
        return HiraImprovementScurveTask::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->exists();
    }

    public function seedFromDetailRows(string $company, int $periodYear): void
    {
        $detailRows = HiraImprovementDetailRow::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        if ($detailRows->isEmpty()) {
            app(HiraImprovementDetailService::class)->listForScope($company, $periodYear);
            $detailRows = HiraImprovementDetailRow::query()
                ->where('company', $company)
                ->where('period_year', $periodYear)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
        }

        $grouped = $detailRows->groupBy('improvement_plan');

        DB::transaction(function () use ($company, $periodYear, $grouped) {
            HiraImprovementScurveTask::query()
                ->where('company', $company)
                ->where('period_year', $periodYear)
                ->delete();

            foreach ($grouped as $plan => $rows) {
                $starts = $rows->map(fn ($r) => $r->start_date?->format('Y-m-d'))->filter()->sort()->values();
                $ends = $rows->map(fn ($r) => $r->target_date?->format('Y-m-d'))->filter()->sort()->values();
                $start = $starts->first();
                $end = $ends->last() ?? $start;
                $mid = $this->midDate($start, $end);

                $defaults = [
                    ['task_name' => 'Planning & preparation', 'planned_date' => $start, 'status' => 'Progress', 'note' => 'Input manual task progress'],
                    ['task_name' => 'Implementation', 'planned_date' => $mid, 'status' => 'Open', 'note' => 'Isi actual date saat task berjalan/selesai'],
                    ['task_name' => 'Verification & closure', 'planned_date' => $end, 'status' => 'Open', 'note' => 'Validasi efektivitas improvement'],
                ];

                foreach ($defaults as $i => $def) {
                    HiraImprovementScurveTask::query()->create([
                        'company' => $company,
                        'period_year' => $periodYear,
                        'improvement_plan' => (string) $plan,
                        'task_name' => $def['task_name'],
                        'planned_date' => $def['planned_date'],
                        'actual_date' => null,
                        'status' => $def['status'],
                        'note' => $def['note'],
                        'sort_order' => $i,
                    ]);
                }
            }
        });
    }

    /**
     * @param  list<array<string, mixed>>  $clientTasks
     * @return list<array<string, mixed>>
     */
    public function syncTasks(string $company, int $periodYear, array $clientTasks): array
    {
        return DB::transaction(function () use ($company, $periodYear, $clientTasks) {
            $keptIds = [];

            foreach ($clientTasks as $index => $client) {
                $id = isset($client['id']) ? (int) $client['id'] : 0;
                $attrs = $this->mapClientToAttributes($client, $company, $periodYear, $index);

                if ($id > 0) {
                    $model = HiraImprovementScurveTask::query()
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

                $model = HiraImprovementScurveTask::query()->create($attrs);
                $keptIds[] = $model->id;
            }

            HiraImprovementScurveTask::query()
                ->where('company', $company)
                ->where('period_year', $periodYear)
                ->when($keptIds !== [], fn ($q) => $q->whereNotIn('id', $keptIds))
                ->when($keptIds === [], fn ($q) => $q)
                ->delete();

            return $this->listForScope($company, $periodYear);
        });
    }

    public function deleteTask(string $company, int $periodYear, int $id): bool
    {
        return (bool) HiraImprovementScurveTask::query()
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

        return $this->syncTasks($company, $periodYear, $parsed);
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
            $lines = preg_split('/\r\n|\n|\r/', $text) ?: [];
            if ($lines !== []) {
                $head = str_getcsv(array_shift($lines) ?: '');
                foreach ($lines as $line) {
                    if (trim($line) === '') {
                        continue;
                    }
                    $cells = str_getcsv($line);
                    $raw[] = array_combine($head, array_pad($cells, count($head), '')) ?: [];
                }
            }
        }

        $parsed = [];
        foreach ($raw as $i => $r) {
            $plan = trim((string) ($r['Improvement Plan'] ?? ''));
            $taskName = trim((string) ($r['Child Task'] ?? $r['Task'] ?? ''));

            if ($plan === '' && $taskName === '') {
                continue;
            }

            $parsed[] = [
                'improvementPlan' => $plan !== '' ? $plan : 'Imported Plan ' . ($i + 1),
                'taskName' => $taskName !== '' ? $taskName : 'Task baru',
                'plannedDate' => trim((string) ($r['Plan Date'] ?? '')),
                'actualDate' => trim((string) ($r['Actual Date'] ?? '')),
                'status' => trim((string) ($r['Status'] ?? 'Open')),
                'note' => trim((string) ($r['Keterangan'] ?? $r['Note'] ?? '')),
            ];
        }

        return $parsed;
    }

    /**
     * @return list<array<string, string>>
     */
    public function exportRows(string $company, int $periodYear): array
    {
        return HiraImprovementScurveTask::query()
            ->where('company', $company)
            ->where('period_year', $periodYear)
            ->orderBy('improvement_plan')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(function (HiraImprovementScurveTask $task, int $i) {
                $client = $this->toClientTask($task);

                return [
                    'No' => (string) ($i + 1),
                    'Improvement Plan' => (string) $client['improvementPlan'],
                    'Child Task' => (string) $client['taskName'],
                    'Plan Date' => (string) $client['plannedDate'],
                    'Actual Date' => (string) $client['actualDate'],
                    'Status' => (string) $client['status'],
                    'Keterangan' => (string) $client['note'],
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public function toClientTask(HiraImprovementScurveTask $task): array
    {
        return [
            'id' => $task->id,
            'improvementPlan' => $task->improvement_plan,
            'taskName' => $task->task_name,
            'plannedDate' => $task->planned_date?->format('Y-m-d') ?? '',
            'actualDate' => $task->actual_date?->format('Y-m-d') ?? '',
            'status' => $task->status,
            'note' => $task->note ?? '',
        ];
    }

    /**
     * @param  array<string, mixed>  $client
     * @return array<string, mixed>
     */
    private function mapClientToAttributes(array $client, string $company, int $periodYear, int $sortOrder): array
    {
        $planned = trim((string) ($client['plannedDate'] ?? ''));
        $actual = trim((string) ($client['actualDate'] ?? ''));

        return [
            'company' => $company,
            'period_year' => $periodYear,
            'improvement_plan' => trim((string) ($client['improvementPlan'] ?? 'Improvement Plan')),
            'task_name' => trim((string) ($client['taskName'] ?? 'Task baru')),
            'planned_date' => $planned !== '' ? $planned : null,
            'actual_date' => $actual !== '' ? $actual : null,
            'status' => trim((string) ($client['status'] ?? 'Open')),
            'note' => trim((string) ($client['note'] ?? '')),
            'sort_order' => $sortOrder,
        ];
    }

    private function midDate(?string $start, ?string $end): ?string
    {
        if (! $start || ! $end) {
            return $end ?? $start;
        }

        $s = strtotime($start);
        $e = strtotime($end);
        if (! $s || ! $e) {
            return $end;
        }

        return date('Y-m-d', (int) (($s + $e) / 2));
    }
}
