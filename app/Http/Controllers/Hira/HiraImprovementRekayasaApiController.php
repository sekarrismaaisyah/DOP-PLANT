<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hira;

use App\Http\Controllers\Controller;
use App\Services\Hira\HiraImprovementRekayasaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HiraImprovementRekayasaApiController extends Controller
{
    public function index(Request $request, HiraImprovementRekayasaService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);

        return response()->json([
            'rows' => $service->listForScope($company, $year),
            'headers' => HiraImprovementRekayasaService::EXPORT_HEADERS,
        ]);
    }

    public function sync(Request $request, HiraImprovementRekayasaService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $validated = $request->validate([
            'rows' => 'required|array',
        ]);

        $rows = $service->syncRows($company, $year, $validated['rows']);

        return response()->json(['rows' => $rows, 'message' => 'Data pengendalian rekayasa tersimpan.']);
    }

    public function reset(Request $request, HiraImprovementRekayasaService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $service->resetToSample($company, $year);

        return response()->json([
            'rows' => $service->listForScope($company, $year),
            'message' => 'Data direset ke contoh.',
        ]);
    }

    public function destroy(Request $request, int $id, HiraImprovementRekayasaService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);

        return response()->json(['deleted' => $service->deleteRow($company, $year, $id)]);
    }

    public function import(Request $request, HiraImprovementRekayasaService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $request->validate([
            'file' => 'required|file|max:10240',
        ]);

        $text = (string) file_get_contents($request->file('file')->getRealPath() ?: '');

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

    public function exportCsv(Request $request, HiraImprovementRekayasaService $service): StreamedResponse
    {
        [$company, $year] = $this->scope($request);
        $data = $service->exportRows($company, $year);
        $keys = HiraImprovementRekayasaService::EXPORT_HEADERS;

        return response()->streamDownload(function () use ($data, $keys) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $keys);
            foreach ($data as $row) {
                fputcsv($out, array_map(fn ($k) => $row[$k] ?? '', $keys));
            }
            fclose($out);
        }, 'tabel_pengendalian_rekayasa.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    public function exportExcel(Request $request, HiraImprovementRekayasaService $service): StreamedResponse
    {
        [$company, $year] = $this->scope($request);
        $data = $service->exportRows($company, $year);

        return $this->streamExcelDownload($data, 'tabel_pengendalian_rekayasa.xls');
    }

    public function exportTemplate(HiraImprovementRekayasaService $service): StreamedResponse
    {
        return $this->streamCsvDownload($service->templateRows(), 'template_pengendalian_rekayasa.csv');
    }

    public function exportTemplateExcel(HiraImprovementRekayasaService $service): StreamedResponse
    {
        return $this->streamExcelDownload($service->templateRows(), 'template_pengendalian_rekayasa.xls');
    }

    /**
     * @param  list<array<string, string>>  $data
     */
    private function streamCsvDownload(array $data, string $filename): StreamedResponse
    {
        $keys = HiraImprovementRekayasaService::EXPORT_HEADERS;

        return response()->streamDownload(function () use ($data, $keys) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, $keys);
            foreach ($data as $row) {
                fputcsv($out, array_map(fn ($k) => $row[$k] ?? '', $keys));
            }
            fclose($out);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * @param  list<array<string, string>>  $data
     */
    private function streamExcelDownload(array $data, string $filename): StreamedResponse
    {
        $keys = HiraImprovementRekayasaService::EXPORT_HEADERS;
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
            $filename,
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
