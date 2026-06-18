<?php

declare(strict_types=1);

namespace App\Http\Controllers\Hira;

use App\Http\Controllers\Controller;
use App\Services\Hira\HiraImprovementExcelTemplateWriter;
use App\Services\Hira\HiraImprovementRekayasaMergedExportService;
use App\Services\Hira\HiraImprovementRekayasaReplikasiService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class HiraImprovementRekayasaReplikasiApiController extends Controller
{
    public function index(Request $request, HiraImprovementRekayasaReplikasiService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);

        return response()->json([
            'rows' => $service->listForScope($company, $year),
            'headers' => HiraImprovementRekayasaMergedExportService::MERGED_EXPORT_HEADERS,
        ]);
    }

    public function sync(Request $request, HiraImprovementRekayasaReplikasiService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $validated = $request->validate([
            'rows' => 'required|array',
        ]);

        $rows = $service->syncRows($company, $year, $validated['rows']);

        return response()->json([
            'rows' => $rows,
            'message' => 'Data rekayasa & replikasi tersimpan.',
        ]);
    }

    public function reset(Request $request, HiraImprovementRekayasaReplikasiService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $service->resetToSample($company, $year);

        return response()->json([
            'rows' => $service->listForScope($company, $year),
            'message' => 'Data direset ke contoh.',
        ]);
    }

    public function destroy(Request $request, int $id, HiraImprovementRekayasaReplikasiService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);

        return response()->json(['deleted' => $service->deleteRow($company, $year, $id)]);
    }

    public function import(Request $request, HiraImprovementRekayasaReplikasiService $service): JsonResponse
    {
        [$company, $year] = $this->scope($request);
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ], [
            'file.mimes' => 'Format file harus Excel (.xlsx atau .xls).',
        ]);

        try {
            $rows = $service->importFromExcelFile(
                $company,
                $year,
                (string) $request->file('file')?->getRealPath(),
            );
        } catch (\InvalidArgumentException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        } catch (\Throwable $e) {
            report($e);

            return response()->json(['message' => 'Gagal membaca file Excel. Pastikan format sesuai template.'], 422);
        }

        return response()->json([
            'rows' => $rows,
            'message' => 'Impor Excel berhasil.',
        ]);
    }

    public function exportExcel(Request $request, HiraImprovementRekayasaReplikasiService $service): StreamedResponse
    {
        [$company, $year] = $this->scope($request);
        $data = $service->exportRows($company, $year);
        $spreadsheet = $service->buildSpreadsheet($data);
        $writer = new Xlsx($spreadsheet);

        return response()->streamDownload(function () use ($writer): void {
            $writer->save('php://output');
        }, 'tabel_rekayasa_replikasi.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function exportTemplate(Request $request, HiraImprovementRekayasaReplikasiService $service)
    {
        [$company, $year] = $this->scope($request);
        $spreadsheet = $service->buildSpreadsheet($service->templateRows($company, $year), true);

        return HiraImprovementExcelTemplateWriter::download($spreadsheet, 'template_rekayasa_replikasi.xlsx');
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
