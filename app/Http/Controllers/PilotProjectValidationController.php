<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PilotProjectValidation\PilotProjectValidationPortfolioSaveRequest;
use App\Services\PilotProjectValidation\PilotProjectValidationExcelImportService;
use App\Services\PilotProjectValidation\PilotProjectValidationExcelTemplateService;
use App\Services\PilotProjectValidation\PilotProjectValidationPortfolioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class PilotProjectValidationController extends Controller
{
    public function __construct(
        private readonly PilotProjectValidationPortfolioService $portfolioService,
        private readonly PilotProjectValidationExcelImportService $excelImportService,
        private readonly PilotProjectValidationExcelTemplateService $excelTemplateService
    ) {}

    public function index(): View
    {
        return view('PilotProjectValidation.index');
    }

    public function portfolio(): JsonResponse
    {
        try {
            $data = $this->portfolioService->portfolioToFrontendArray();

            return response()->json($data);
        } catch (Throwable $e) {
            Log::error('pilot-project-validation.portfolio.show', ['e' => $e->getMessage()]);

            return response()->json([
                'projects' => [],
                'historySnapshots' => [],
                'error' => 'Gagal memuat data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storePortfolio(PilotProjectValidationPortfolioSaveRequest $request): JsonResponse
    {
        try {
            $this->portfolioService->syncPortfolioFromRequest([
                'projects' => $request->input('projects', []),
                'historySnapshots' => $request->input('historySnapshots', []),
            ]);

            return response()->json([
                'ok' => true,
                'message' => 'Portfolio berhasil disimpan.',
            ]);
        } catch (Throwable $e) {
            Log::error('pilot-project-validation.portfolio.store', ['e' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'ok' => false,
                'message' => 'Gagal menyimpan: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Impor file Excel di server lalu simpan langsung ke database (tanpa payload JSON besar).
     */
    public function importExcel(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:51200'],
        ]);

        $file = $request->file('file');
        if ($file === null) {
            return response()->json(['ok' => false, 'message' => 'File tidak ditemukan.'], 422);
        }

        $path = $file->getRealPath();
        if ($path === false) {
            return response()->json(['ok' => false, 'message' => 'Tidak dapat membaca file upload.'], 422);
        }

        try {
            $payload = $this->excelImportService->buildPortfolioPayloadFromPath($path);
            $this->portfolioService->syncPortfolioFromRequest($payload);
            $data = $this->portfolioService->portfolioToFrontendArray();

            return response()->json([
                'ok' => true,
                'message' => 'File Excel diimpor dan disimpan ke database.',
                'projects' => $data['projects'],
                'historySnapshots' => $data['historySnapshots'],
            ]);
        } catch (Throwable $e) {
            Log::error('pilot-project-validation.import-excel', ['e' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);

            return response()->json([
                'ok' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Unduh file Excel contoh (satu workbook, sheet PROJECTS / TIMELINE / GATES / METRICS / HISTORY).
     */
    public function downloadTemplate(): StreamedResponse
    {
        $spreadsheet = $this->excelTemplateService->createSpreadsheet();
        $filename = 'template-pilot-project-validation.xlsx';

        return response()->streamDownload(function () use ($spreadsheet): void {
            (new Xlsx($spreadsheet))->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    public function projectPdf(string $key): BinaryFileResponse
    {
        $map = [
            'arcas' => 'ARCAS.pdf',
            'mea' => 'MEA.pdf',
            'mgc' => 'MGC.pdf',
        ];
        if (! array_key_exists($key, $map)) {
            abort(404);
        }

        $file = $map[$key];
        $disk = Storage::disk('local');
        if (! $disk->exists($file)) {
            $fallbackCandidates = [
                strtolower($file),
                strtoupper($file),
                ucfirst(strtolower($file)),
            ];
            $resolved = null;
            foreach ($fallbackCandidates as $candidate) {
                if ($disk->exists($candidate)) {
                    $resolved = $candidate;
                    break;
                }
            }
            if ($resolved === null) {
                abort(404, 'File PDF tidak ditemukan.');
            }
            $file = $resolved;
        }

        return response()->file($disk->path($file), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file . '"',
        ]);
    }
}
