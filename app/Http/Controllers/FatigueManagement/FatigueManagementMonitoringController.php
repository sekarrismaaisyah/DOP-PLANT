<?php

declare(strict_types=1);

namespace App\Http\Controllers\FatigueManagement;

use App\Http\Controllers\Controller;
use App\Http\Requests\FatigueManagement\FatigueManagementStoreEvaluationRequest;
use App\Http\Requests\FatigueManagement\FatigueManagementStoreEvidenceRequest;
use App\Models\FatigueManagementProgramMonitoring;
use App\Services\FatigueManagement\FatigueManagementMonitoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FatigueManagementMonitoringController extends Controller
{
    public function storeEvidence(
        FatigueManagementStoreEvidenceRequest $request,
        FatigueManagementMonitoringService $monitoringService,
    ): JsonResponse|RedirectResponse {
        $validated = $request->validated();

        $row = $monitoringService->storeEvidence(
            (string) $validated['program_key'],
            (string) $validated['partner_key'],
            (int) $validated['year'],
            (string) $validated['iso_week'],
            $request->file('evidence_file'),
            $validated['evidence_notes'] ?? null,
            $validated['pic_name'] ?? null,
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'row' => $row]);
        }

        return redirect()
            ->route('fatigue-management.dashboard', $request->only(['year', 'iso_week', 'partner', 'program']))
            ->with('success', 'Evidence berhasil diupload.');
    }

    public function storeEvaluation(
        int $id,
        FatigueManagementStoreEvaluationRequest $request,
        FatigueManagementMonitoringService $monitoringService,
    ): JsonResponse|RedirectResponse {
        $validated = $request->validated();

        $row = $monitoringService->storeEvaluation(
            $id,
            (string) $validated['evaluation_status'],
            isset($validated['evaluation_score']) ? (int) $validated['evaluation_score'] : null,
            $validated['evaluation_notes'] ?? null,
            $validated['evaluated_by'] ?? null,
        );

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'row' => $row]);
        }

        return redirect()
            ->route('fatigue-management.dashboard', $request->only(['year', 'iso_week', 'partner', 'program']))
            ->with('success', 'Evaluasi berhasil disimpan.');
    }

    public function downloadEvidence(int $id): StreamedResponse
    {
        $record = FatigueManagementProgramMonitoring::query()->findOrFail($id);

        if (! $record->evidence_file_path || ! Storage::exists($record->evidence_file_path)) {
            abort(404, 'File evidence tidak ditemukan.');
        }

        $name = $record->evidence_original_name ?: basename($record->evidence_file_path);

        return Storage::download($record->evidence_file_path, $name);
    }
}
