<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PilotProjectValidation\PilotProjectValidationPortfolioSaveRequest;
use App\Models\PilotProjectValidationProject;
use App\Services\PilotProjectValidation\PilotProjectValidationExcelImportService;
use App\Services\PilotProjectValidation\PilotProjectValidationExcelTemplateService;
use App\Services\PilotProjectValidation\PilotProjectValidationPortfolioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
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
        $needSupportProjects = PilotProjectValidationProject::query()
            ->orderBy('project_name')
            ->get(['project_name', 'support']);

        try {
            $portfolio = $this->portfolioService->portfolioToFrontendArray();
        } catch (Throwable $e) {
            Log::error('pilot-project-validation.index.portfolio', ['e' => $e->getMessage()]);
            $portfolio = ['projects' => [], 'historySnapshots' => []];
        }

        return view('PilotProjectValidation.index', compact('needSupportProjects', 'portfolio'));
    }

    public function portfolio(): JsonResponse
    {
        try {
            $data = $this->portfolioService->portfolioToFrontendArray();

            return response()
                ->json($data)
                ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache');
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
        $keywordMap = [
            'arcas' => ['arcas'],
            'mea' => ['mea', 'mining eyes'],
            'mgc' => ['mgc', 'mgd'],
            'dozer' => ['dozer', 'remote dozer'],
            'minepump' => ['minepump', 'mine pump', 'remote pump'],
        ];
        $preferredNames = [
            'arcas' => ['ARCAS.pdf', 'arcas.pdf'],
            'mea' => ['MEA.pdf', 'mea.pdf'],
            'mgc' => ['MGC.pdf', 'mgc.pdf', 'MGD.pdf', 'mgd.pdf'],
            'dozer' => ['Dozer.pdf', 'dozer.pdf'],
            'minepump' => ['MinePump.pdf', 'minepump.pdf', 'mine-pump.pdf'],
        ];
        if (! array_key_exists($key, $keywordMap)) {
            abort(404);
        }

        $disk = Storage::disk('local');
        $allFiles = File::allFiles($disk->path(''));
        $resolvedPath = null;

        // 1) Coba cocokkan nama file yang diharapkan terlebih dahulu.
        foreach ($allFiles as $splFile) {
            $filename = $splFile->getFilename();
            foreach ($preferredNames[$key] as $preferred) {
                if (strcasecmp($filename, $preferred) === 0) {
                    $resolvedPath = $splFile->getPathname();
                    break 2;
                }
            }
        }

        // 2) Fallback: cocokkan keyword di nama file.
        if ($resolvedPath === null) {
            foreach ($allFiles as $splFile) {
                $filename = strtolower($splFile->getFilename());
                if (! str_ends_with($filename, '.pdf')) {
                    continue;
                }
                foreach ($keywordMap[$key] as $keyword) {
                    if (str_contains($filename, strtolower($keyword))) {
                        $resolvedPath = $splFile->getPathname();
                        break 2;
                    }
                }
            }
        }

        if ($resolvedPath === null) {
            $available = [];
            foreach ($allFiles as $splFile) {
                if (str_ends_with(strtolower($splFile->getFilename()), '.pdf')) {
                    $available[] = $splFile->getFilename();
                }
            }
            $hint = $available === [] ? 'Tidak ada file PDF di storage/app.' : ('PDF tersedia: ' . implode(', ', $available));
            abort(404, 'File PDF untuk key "' . $key . '" tidak ditemukan. ' . $hint);
        }

        return response()->file($resolvedPath, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline',
        ]);
    }
}
