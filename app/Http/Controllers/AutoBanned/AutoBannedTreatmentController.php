<?php

declare(strict_types=1);

namespace App\Http\Controllers\AutoBanned;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AutoBanned\Concerns\ProvidesAutoBannedLayout;
use App\Http\Requests\AutoBanned\AutoBannedStoreTreatmentEvidenceRequest;
use App\Models\AutoBannedUnbanRequest;
use App\Services\AutoBanned\AutoBannedOverviewService;
use App\Services\AutoBanned\AutoBannedTreatmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AutoBannedTreatmentController extends Controller
{
    use ProvidesAutoBannedLayout;

    public function __construct(
        private readonly AutoBannedOverviewService $overviewService,
        private readonly AutoBannedTreatmentService $treatmentService,
    ) {}

    public function lookupSid(Request $request): JsonResponse
    {
        $sid = strtoupper(trim((string) $request->query('sid', '')));
        $period = $this->overviewService->resolvePeriod([
            'week' => (string) $request->query('week', ''),
            'year' => (string) $request->query('year', ''),
        ]);

        if ($sid === '') {
            return response()->json(['found' => false, 'message' => 'SID wajib diisi.']);
        }

        $context = $this->treatmentService->resolveSidContext($sid, $period['week'], $period['year']);

        if ($context === null) {
            return response()->json([
                'found' => false,
                'message' => 'SID tidak ditemukan pada periode '.$period['week'].' '.$period['year'].'.',
            ]);
        }

        return response()->json([
            'found' => true,
            'data' => $context,
        ]);
    }

    public function store(AutoBannedStoreTreatmentEvidenceRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->treatmentService->storeTreatmentEvidence(
            sid: (string) $validated['sid'],
            week: (string) $validated['week'],
            year: (string) $validated['year'],
            alasanPengajuan: (string) $validated['alasan_pengajuan'],
            file: $request->file('evidence_file'),
            user: $request->user(),
        );

        return redirect()
            ->route('auto-banned.inputasi.index', [
                'week' => $validated['week'],
                'year' => $validated['year'],
                'open_inputasi' => 'treatment',
            ])
            ->with('success', 'Evidence treatment untuk SID '.strtoupper($validated['sid']).' berhasil diupload.');
    }

    public function downloadEvidence(AutoBannedUnbanRequest $unbanRequest): StreamedResponse
    {
        return $this->treatmentService->downloadEvidence($unbanRequest);
    }
}
