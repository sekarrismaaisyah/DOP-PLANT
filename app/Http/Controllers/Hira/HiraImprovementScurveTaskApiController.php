<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hira;

use App\Http\Controllers\Controller;
use App\Models\HiraImprovementScurveTask;
use App\Services\Hira\HiraImprovementScurveTaskService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HiraImprovementScurveTaskApiController extends Controller
{
    public function index(Request $request, HiraImprovementScurveTaskService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);

        return response()->json([
            'tasks' => $service->listForScope($company, $year),
            'plans' => $service->improvementPlans($company, $year),
            'options' => ['status' => HiraImprovementScurveTaskService::OPT_STATUS],
        ]);
    }

    public function sync(Request $request, HiraImprovementScurveTaskService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $validated = $request->validate([
            'tasks' => 'required|array',
        ]);

        $tasks = $service->syncTasks($company, $year, $validated['tasks']);

        return response()->json(['tasks' => $tasks, 'message' => 'Task S-Curve tersimpan.']);
    }

    public function reseed(Request $request, HiraImprovementScurveTaskService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);

        HiraImprovementScurveTask::query()
            ->where('company', $company)
            ->where('period_year', $year)
            ->delete();

        $service->seedFromDetailRows($company, $year);

        return response()->json([
            'tasks' => $service->listForScope($company, $year),
            'message' => 'Task diregenerasi dari data HIRA detail.',
        ]);
    }

    public function destroy(Request $request, int $id, HiraImprovementScurveTaskService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);

        return response()->json(['deleted' => $service->deleteTask($company, $year, $id)]);
    }

    public function import(Request $request, HiraImprovementScurveTaskService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $text = (string) file_get_contents($request->file('file')->getRealPath() ?: '');

        try {
            $tasks = $service->importFromText($company, $year, $text);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'tasks' => $tasks,
            'message' => 'Impor task S-Curve berhasil.',
        ]);
    }

    public function exportCsv(Request $request, HiraImprovementScurveTaskService $service): StreamedResponse
    {
        [$company, $year] = $this->scope($request);
        $data = $service->exportRows($company, $year);
        $keys = array_keys($data[0] ?? ['Improvement Plan' => '']);

        return response()->streamDownload(function () use ($data, $keys) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $keys);
            foreach ($data as $row) {
                fputcsv($out, array_map(fn ($k) => $row[$k] ?? '', $keys));
            }
            fclose($out);
        }, 'tabel_scurve_improvement.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportExcel(Request $request, HiraImprovementScurveTaskService $service): StreamedResponse
    {
        [$company, $year] = $this->scope($request);
        $data = $service->exportRows($company, $year);
        $keys = array_keys($data[0] ?? ['Improvement Plan' => '']);
        $esc = static fn (string $s): string => htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
        $header = '<tr>' . implode('', array_map(fn ($k) => '<th>' . $esc($k) . '</th>', $keys)) . '</tr>';
        $body = '';
        foreach ($data as $row) {
            $body .= '<tr>' . implode('', array_map(fn ($k) => '<td>' . $esc((string) ($row[$k] ?? '')) . '</td>', $keys)) . '</tr>';
        }
        $html = '<!doctype html><html><head><meta charset="utf-8"></head><body><table>' . $header . $body . '</table></body></html>';

        return response()->streamDownload(
            static function () use ($html) {
                echo $html;
            },
            'tabel_scurve_improvement.xls',
            ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8'],
        );
    }

    /**
     * @return array{0: string, 1: int}
     */
    private function scope(Request $request): array
    {
        $company = trim((string) $request->input('company', 'Bukit Makmur'));
        $year = (int) $request->input('period_year', 2026);

        if ($company === '') {
            $company = 'Bukit Makmur';
        }
        if ($year < 2000 || $year > 2100) {
            $year = 2026;
        }

        return [$company, $year];
    }
}
