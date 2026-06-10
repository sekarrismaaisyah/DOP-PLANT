<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hira;

use App\Http\Controllers\Controller;
use App\Services\Hira\HiraImprovementDetailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HiraImprovementDetailApiController extends Controller
{
    public function index(Request $request, HiraImprovementDetailService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);

        return response()->json([
            'rows' => $service->listForScope($company, $year),
            'options' => $this->optionsPayload(),
        ]);
    }

    public function overview(Request $request, HiraImprovementDetailService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);

        return response()->json($service->overviewForScope($company, $year));
    }

    public function sync(Request $request, HiraImprovementDetailService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $validated = $request->validate([
            'rows' => 'required|array',
        ]);

        $rows = $service->syncRows($company, $year, $validated['rows']);

        return response()->json(['rows' => $rows, 'message' => 'Data tersimpan.']);
    }

    public function store(Request $request, HiraImprovementDetailService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $validated = $request->validate([
            'row' => 'required|array',
            'sort_order' => 'nullable|integer|min:0',
        ]);

        $row = $service->createRow(
            $company,
            $year,
            $validated['row'],
            (int) ($validated['sort_order'] ?? 0),
        );

        return response()->json(['row' => $row], 201);
    }

    public function destroy(Request $request, int $id, HiraImprovementDetailService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $deleted = $service->deleteRow($company, $year, $id);

        return response()->json(['deleted' => $deleted]);
    }

    public function reset(Request $request, HiraImprovementDetailService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $service->resetToSample($company, $year);

        return response()->json([
            'rows' => $service->listForScope($company, $year),
            'message' => 'Data direset ke contoh.',
        ]);
    }

    public function import(Request $request, HiraImprovementDetailService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $file = $request->file('file');
        $text = (string) file_get_contents($file->getRealPath() ?: '');

        try {
            $rows = $service->importFromText($company, $year, $text);
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        return response()->json([
            'rows' => $rows,
            'message' => 'Impor berhasil.',
        ]);
    }

    public function exportCsv(Request $request, HiraImprovementDetailService $service): StreamedResponse
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
        }, 'tabel_detail_hira.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportExcel(Request $request, HiraImprovementDetailService $service): StreamedResponse
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
            'tabel_detail_hira.xls',
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

    /**
     * @return array<string, list<string>>
     */
    private function optionsPayload(): array
    {
        return [
            'sites' => HiraImprovementDetailService::OPT_SITES,
            'perusahaan' => HiraImprovementDetailService::OPT_PERUSAHAAN,
            'kategori' => HiraImprovementDetailService::OPT_KATEGORI,
            'rnr' => HiraImprovementDetailService::OPT_RNR,
            'faktor' => HiraImprovementDetailService::OPT_FAKTOR,
            'status' => HiraImprovementDetailService::OPT_STATUS,
            'tp' => HiraImprovementDetailService::OPT_TP,
            'exposure' => HiraImprovementDetailService::OPT_EXPOSURE,
            'control' => HiraImprovementDetailService::OPT_CONTROL,
            'target' => HiraImprovementDetailService::OPT_TARGET,
        ];
    }
}
