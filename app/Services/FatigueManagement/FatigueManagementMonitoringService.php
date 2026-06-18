<?php

declare(strict_types=1);

namespace App\Services\FatigueManagement;

use App\Enums\FatigueManagementEvaluationStatus;
use App\Enums\FatigueManagementEvidenceStatus;
use App\Models\FatigueManagementProgramMonitoring;
use App\Support\FatigueManagement\FatigueManagementFrequencyPlan;
use App\Support\FatigueManagement\FatigueManagementFrequencyChecker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Monitoring operasional: upload evidence & proses evaluasi per program × mitra × periode.
 */
final class FatigueManagementMonitoringService
{
    public function __construct(
        private readonly FatigueManagementProgramCatalogService $catalogService,
        private readonly FatigueManagementSiteMatrixService $siteMatrixService,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function buildDashboard(
        ?int $year = null,
        ?string $isoWeek = null,
        ?string $partnerKey = null,
        ?string $programKey = null,
        ?string $programType = null,
        ?string $checklistStatus = null,
        ?string $evidenceStatus = null,
        ?string $evaluationStatus = null,
    ): array {
        $catalog = $this->catalogService->buildCatalog();
        $year = $year ?? (int) date('Y');
        $isoWeek = $isoWeek ?? ('W' . str_pad((string) date('W'), 2, '0', STR_PAD_LEFT));

        $gmoPartnerKeys = collect($this->siteMatrixService->partnerKeysForSites())
            ->map(static fn (string $key): string => strtoupper($key))
            ->flip()
            ->all();

        $programs = $catalog['programs'] ?? [];
        $partners = array_values(array_filter(
            $catalog['partners'] ?? [],
            static fn (array $partner): bool => isset($gmoPartnerKeys[strtoupper((string) ($partner['key'] ?? ''))]),
        ));
        $programs = array_values(array_filter(
            $programs,
            static fn (array $program): bool => isset($gmoPartnerKeys[strtoupper((string) ($program['partner_key'] ?? ''))]),
        ));
        $partnerMap = collect($partners)->keyBy('key');

        $records = FatigueManagementProgramMonitoring::query()
            ->where('year', $year)
            ->where('iso_week', $isoWeek)
            ->get()
            ->groupBy(static fn (FatigueManagementProgramMonitoring $r): string => $r->program_key . '|' . $r->partner_key);

        $rows = [];
        foreach ($programs as $program) {
            $pKey = (string) ($program['partner_key'] ?? '');
            $progKey = (string) ($program['key'] ?? '');

            if (! isset($gmoPartnerKeys[strtoupper($pKey)])) {
                continue;
            }

            if ($partnerKey !== null && $partnerKey !== '' && strtoupper($partnerKey) !== strtoupper($pKey)) {
                continue;
            }
            if ($programKey !== null && $programKey !== '' && $programKey !== $progKey) {
                continue;
            }
            if ($programType !== null && $programType !== '' && ! $this->programTypeMatches($programType, (string) ($program['program_type'] ?? ''))) {
                continue;
            }

            $partner = $partnerMap->get($pKey, ['key' => $pKey, 'name' => $pKey]);
            $slotRecords = $records->get($progKey . '|' . $pKey, collect());
            $row = $this->composeRow($program, $partner, $slotRecords, $year, $isoWeek);

            if ($checklistStatus !== null && $checklistStatus !== '' && ($row['checklist_status'] ?? '') !== $checklistStatus) {
                continue;
            }
            if ($evidenceStatus !== null && $evidenceStatus !== '' && ($row['evidence_status'] ?? '') !== $evidenceStatus) {
                continue;
            }
            if ($evaluationStatus !== null && $evaluationStatus !== '' && ($row['evaluation_status'] ?? '') !== $evaluationStatus) {
                continue;
            }

            $rows[] = $row;
        }

        usort($rows, static function (array $a, array $b): int {
            $freqOrder = ['shift' => 0, 'daily' => 1, 'weekly' => 2];
            $fa = $freqOrder[$a['frequency_category'] ?? 'weekly'] ?? 9;
            $fb = $freqOrder[$b['frequency_category'] ?? 'weekly'] ?? 9;
            if ($fa !== $fb) {
                return $fa <=> $fb;
            }

            $typeOrder = ['mandatory' => 0, 'upgrade' => 1, 'mitra' => 2];
            $ta = $typeOrder[$a['program_type'] ?? 'mitra'] ?? 9;
            $tb = $typeOrder[$b['program_type'] ?? 'mitra'] ?? 9;
            if ($ta !== $tb) {
                return $ta <=> $tb;
            }

            $cmp = ($a['program_no'] ?? 0) <=> ($b['program_no'] ?? 0);
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcmp((string) ($a['partner_key'] ?? ''), (string) ($b['partner_key'] ?? ''));
        });

        return [
            'document' => $catalog['document'] ?? [],
            'filters' => compact('year', 'isoWeek', 'partnerKey', 'programKey', 'programType', 'checklistStatus', 'evidenceStatus', 'evaluationStatus'),
            'filter_options' => $this->filterOptions($catalog, $programs, $partners),
            'summary' => $this->buildSummary($rows),
            'programs' => $programs,
            'partners' => $partners,
            'rows' => $rows,
            'upload_frequency_groups' => $this->buildFrequencyGroups($rows),
            'frequency_groups' => $this->buildFrequencyGroups($rows),
            'company_groups' => $this->buildCompanyGroups($rows),
            'site_matrix' => $this->siteMatrixService->build($rows),
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function buildCompanyGroups(array $rows): array
    {
        $groups = [];

        foreach ($rows as $row) {
            $key = (string) ($row['partner_key'] ?? '');
            if ($key === '') {
                continue;
            }

            if (! isset($groups[$key])) {
                $groups[$key] = [
                    'partner_key' => $key,
                    'partner_name' => $row['partner_name'] ?? $key,
                    'partner_classification' => $row['partner_classification'] ?? '',
                    'total' => 0,
                    'checklist_ok' => 0,
                    'belum_checklist' => 0,
                    'submitted_count' => 0,
                    'belum_submit' => 0,
                    'verified_count' => 0,
                    'uploaded_count' => 0,
                    'eval_approved' => 0,
                    'shift_total' => 0,
                    'shift_ok' => 0,
                    'daily_total' => 0,
                    'daily_ok' => 0,
                    'weekly_total' => 0,
                    'weekly_ok' => 0,
                    'mandatory_total' => 0,
                    'mandatory_ok' => 0,
                    'upgrade_total' => 0,
                    'upgrade_ok' => 0,
                    'mitra_total' => 0,
                    'mitra_ok' => 0,
                    'programs' => [],
                ];
            }

            $groups[$key]['total']++;
            $met = (bool) ($row['checklist_met'] ?? false);
            $evidenceStatus = (string) ($row['evidence_status'] ?? 'belum_upload');
            $evaluationStatus = (string) ($row['evaluation_status'] ?? 'menunggu_evidence');

            if ($met) {
                $groups[$key]['checklist_ok']++;
            } else {
                $groups[$key]['belum_checklist']++;
            }

            if ($evidenceStatus === 'belum_upload') {
                $groups[$key]['belum_submit']++;
            } else {
                $groups[$key]['submitted_count']++;
            }

            if ($evidenceStatus === 'terverifikasi') {
                $groups[$key]['verified_count']++;
            }

            if (in_array($evidenceStatus, ['sudah_upload', 'perlu_lengkap'], true)) {
                $groups[$key]['uploaded_count']++;
            }

            if ($evaluationStatus === 'disetujui') {
                $groups[$key]['eval_approved']++;
            }

            $freq = (string) ($row['frequency_category'] ?? 'weekly');
            if ($freq === 'shift') {
                $groups[$key]['shift_total']++;
                if ($met) {
                    $groups[$key]['shift_ok']++;
                }
            } elseif ($freq === 'daily') {
                $groups[$key]['daily_total']++;
                if ($met) {
                    $groups[$key]['daily_ok']++;
                }
            } else {
                $groups[$key]['weekly_total']++;
                if ($met) {
                    $groups[$key]['weekly_ok']++;
                }
            }

            $type = $row['program_type'] ?? 'mitra';
            if ($type === 'mandatory') {
                $groups[$key]['mandatory_total']++;
                if ($met) {
                    $groups[$key]['mandatory_ok']++;
                }
            } elseif ($type === 'upgrade') {
                $groups[$key]['upgrade_total']++;
                if ($met) {
                    $groups[$key]['upgrade_ok']++;
                }
            } else {
                $groups[$key]['mitra_total']++;
                if ($met) {
                    $groups[$key]['mitra_ok']++;
                }
            }

            $groups[$key]['programs'][] = $row;
        }

        foreach ($groups as &$group) {
            $total = (int) ($group['total'] ?? 0);
            $checklistOk = (int) ($group['checklist_ok'] ?? 0);
            $submitted = (int) ($group['submitted_count'] ?? 0);

            $group['pct_checklist'] = $total > 0
                ? round(100 * $checklistOk / $total, 1)
                : 0.0;
            $group['pct_submitted'] = $total > 0
                ? round(100 * $submitted / $total, 1)
                : 0.0;
            $group['pct_verified'] = $total > 0
                ? round(100 * (int) ($group['verified_count'] ?? 0) / $total, 1)
                : 0.0;

            $pct = (float) $group['pct_checklist'];
            $group['status_tier'] = match (true) {
                $pct >= 100 => 'complete',
                $pct >= 75 => 'good',
                $pct >= 40 => 'warning',
                default => 'critical',
            };
            $group['status_label'] = match ($group['status_tier']) {
                'complete' => 'Lengkap',
                'good' => 'Baik',
                'warning' => 'Dalam Proses',
                default => 'Perlu Perhatian',
            };

            $group['shift_pct'] = ($group['shift_total'] ?? 0) > 0
                ? round(100 * (int) $group['shift_ok'] / (int) $group['shift_total'], 0)
                : 0;
            $group['daily_pct'] = ($group['daily_total'] ?? 0) > 0
                ? round(100 * (int) $group['daily_ok'] / (int) $group['daily_total'], 0)
                : 0;
            $group['weekly_pct'] = ($group['weekly_total'] ?? 0) > 0
                ? round(100 * (int) $group['weekly_ok'] / (int) $group['weekly_total'], 0)
                : 0;
            $group['mandatory_pct'] = ($group['mandatory_total'] ?? 0) > 0
                ? round(100 * (int) $group['mandatory_ok'] / (int) $group['mandatory_total'], 0)
                : 0;
            $group['upgrade_pct'] = ($group['upgrade_total'] ?? 0) > 0
                ? round(100 * (int) $group['upgrade_ok'] / (int) $group['upgrade_total'], 0)
                : 0;
            $group['mitra_pct'] = ($group['mitra_total'] ?? 0) > 0
                ? round(100 * (int) $group['mitra_ok'] / (int) $group['mitra_total'], 0)
                : 0;

            usort($group['programs'], static function (array $a, array $b): int {
                $freqOrder = ['shift' => 0, 'daily' => 1, 'weekly' => 2];
                $fa = $freqOrder[$a['frequency_category'] ?? 'weekly'] ?? 9;
                $fb = $freqOrder[$b['frequency_category'] ?? 'weekly'] ?? 9;
                if ($fa !== $fb) {
                    return $fa <=> $fb;
                }

                $typeOrder = ['mandatory' => 0, 'upgrade' => 1, 'mitra' => 2];
                $ta = $typeOrder[$a['program_type'] ?? 'mitra'] ?? 9;
                $tb = $typeOrder[$b['program_type'] ?? 'mitra'] ?? 9;
                if ($ta !== $tb) {
                    return $ta <=> $tb;
                }

                return ($a['program_no'] ?? 0) <=> ($b['program_no'] ?? 0);
            });

            $group['frequency_sections'] = $this->buildFrequencyGroups($group['programs']);
        }
        unset($group);

        $result = array_values($groups);
        usort($result, static fn (array $a, array $b): int => strcmp((string) ($a['partner_key'] ?? ''), (string) ($b['partner_key'] ?? '')));

        return $result;
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function buildFrequencyGroups(array $rows): array
    {
        $bucketed = ['shift' => [], 'daily' => [], 'weekly' => []];
        foreach ($rows as $row) {
            $category = (string) ($row['frequency_category'] ?? 'weekly');
            if (! isset($bucketed[$category])) {
                $category = 'weekly';
            }
            $bucketed[$category][] = $row;
        }

        $groups = [];
        foreach (FatigueManagementFrequencyPlan::uploadGroups() as $def) {
            $key = $def['key'];
            if ($bucketed[$key] === []) {
                continue;
            }

            $items = $bucketed[$key];
            $total = count($items);
            $checklistOk = count(array_filter($items, static fn (array $r): bool => (bool) ($r['checklist_met'] ?? false)));

            $groups[] = array_merge($def, [
                'rows' => $items,
                'total' => $total,
                'checklist_ok' => $checklistOk,
                'pct_checklist' => $total > 0 ? round(100 * $checklistOk / $total, 1) : 0.0,
            ]);
        }

        return $groups;
    }

    /**
     * @deprecated Use buildFrequencyGroups()
     *
     * @param  list<array<string, mixed>>  $rows
     * @return list<array<string, mixed>>
     */
    public function buildUploadFrequencyGroups(array $rows): array
    {
        return $this->buildFrequencyGroups($rows);
    }

    /**
     * @return array<string, mixed>
     */
    public function storeEvidence(
        string $programKey,
        string $partnerKey,
        int $year,
        string $isoWeek,
        string $frequencySlot,
        UploadedFile $file,
        ?string $notes = null,
        ?string $picName = null,
    ): array {
        $program = $this->findProgram($programKey, strtoupper($partnerKey));
        $frequencyRaw = (string) ($program['frequency_raw'] ?? $program['frequency'] ?? 'Weekly');

        $existing = FatigueManagementProgramMonitoring::query()
            ->where('program_key', $programKey)
            ->where('partner_key', strtoupper($partnerKey))
            ->where('year', $year)
            ->where('iso_week', $isoWeek)
            ->where('frequency_slot', $frequencySlot)
            ->first();

        if ($existing === null) {
            FatigueManagementFrequencyPlan::assertSlotUploadable(
                $frequencyRaw,
                $frequencySlot,
                $year,
                $isoWeek,
            );
        } elseif (FatigueManagementFrequencyPlan::weekRelation($year, $isoWeek) === 'future') {
            throw ValidationException::withMessages([
                'frequency_slot' => ['Tidak dapat upload untuk minggu yang belum dimulai.'],
            ]);
        }

        $path = $file->storeAs(
            'fatigue-management/evidence/' . $year . '/' . $isoWeek . '/' . strtoupper($partnerKey),
            $programKey . '_' . $frequencySlot . '_' . time() . '.' . $file->getClientOriginalExtension(),
        );

        if ($existing?->evidence_file_path && Storage::exists($existing->evidence_file_path)) {
            Storage::delete($existing->evidence_file_path);
        }

        $record = FatigueManagementProgramMonitoring::query()->updateOrCreate(
            [
                'program_key' => $programKey,
                'partner_key' => strtoupper($partnerKey),
                'year' => $year,
                'iso_week' => $isoWeek,
                'frequency_slot' => $frequencySlot,
            ],
            [
                'evidence_status' => FatigueManagementEvidenceStatus::SudahUpload->value,
                'evidence_file_path' => $path,
                'evidence_original_name' => $file->getClientOriginalName(),
                'evidence_notes' => $notes,
                'evidence_uploaded_at' => now(),
                'evaluation_status' => FatigueManagementEvaluationStatus::MenungguReview->value,
                'pic_name' => $picName,
            ],
        );

        $slotRecords = FatigueManagementProgramMonitoring::query()
            ->where('program_key', $programKey)
            ->where('partner_key', strtoupper($partnerKey))
            ->where('year', $year)
            ->where('iso_week', $isoWeek)
            ->get();

        return $this->composeRow(
            $program,
            $this->findPartner($partnerKey),
            $slotRecords,
            $year,
            $isoWeek,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function storeEvaluation(
        int $monitoringId,
        string $evaluationStatus,
        ?int $score = null,
        ?string $notes = null,
        ?string $evaluatedBy = null,
    ): array {
        $record = FatigueManagementProgramMonitoring::query()->findOrFail($monitoringId);

        $evalEnum = FatigueManagementEvaluationStatus::from($evaluationStatus);
        $evidenceEnum = match ($evalEnum) {
            FatigueManagementEvaluationStatus::Disetujui => FatigueManagementEvidenceStatus::Terverifikasi,
            FatigueManagementEvaluationStatus::PerluPerbaikan => FatigueManagementEvidenceStatus::PerluLengkap,
            FatigueManagementEvaluationStatus::Ditolak => FatigueManagementEvidenceStatus::PerluLengkap,
            default => $record->evidence_status instanceof FatigueManagementEvidenceStatus
                ? $record->evidence_status
                : FatigueManagementEvidenceStatus::from((string) $record->evidence_status),
        };

        $record->update([
            'evaluation_status' => $evalEnum->value,
            'evaluation_score' => $score,
            'evaluation_notes' => $notes,
            'evaluated_by' => $evaluatedBy,
            'evaluated_at' => now(),
            'evidence_status' => $evidenceEnum->value,
        ]);

        return $this->composeRow(
            $this->findProgram($record->program_key, $record->partner_key),
            $this->findPartner($record->partner_key),
            FatigueManagementProgramMonitoring::query()
                ->where('program_key', $record->program_key)
                ->where('partner_key', $record->partner_key)
                ->where('year', $record->year)
                ->where('iso_week', $record->iso_week)
                ->get(),
            (int) $record->year,
            (string) $record->iso_week,
        );
    }

    /**
     * @param  \Illuminate\Support\Collection<int, FatigueManagementProgramMonitoring>|iterable<int, FatigueManagementProgramMonitoring>  $slotRecords
     * @param  array<string, mixed>  $program
     * @param  array<string, mixed>  $partner
     * @return array<string, mixed>
     */
    private function composeRow(
        array $program,
        array $partner,
        iterable $slotRecords,
        int $year,
        string $isoWeek,
    ): array {
        $frequencyRaw = (string) ($program['frequency_raw'] ?? $program['frequency'] ?? 'Weekly');
        $frequencyPlan = FatigueManagementFrequencyPlan::resolve($frequencyRaw);
        $slotDefs = FatigueManagementFrequencyPlan::slotsForWeek($frequencyRaw, $year, $isoWeek);

        $recordsBySlot = [];
        foreach ($slotRecords as $record) {
            $recordsBySlot[(string) $record->frequency_slot] = $record;
        }

        $slotStates = [];
        $firstSlotKey = $slotDefs[0]['key'] ?? 'wk-1';

        foreach ($slotDefs as $slotDef) {
            $slotKey = $slotDef['key'];
            $record = $recordsBySlot[$slotKey] ?? null;

            if ($record === null && isset($recordsBySlot['default']) && $slotKey === $firstSlotKey) {
                $record = $recordsBySlot['default'];
            }

            $evidenceStatus = $record?->evidence_status ?? FatigueManagementEvidenceStatus::BelumUpload;
            if (! $evidenceStatus instanceof FatigueManagementEvidenceStatus) {
                $evidenceStatus = FatigueManagementEvidenceStatus::from((string) $evidenceStatus);
            }

            $hasFile = $evidenceStatus !== FatigueManagementEvidenceStatus::BelumUpload;
            $fileUrl = null;
            if ($record?->evidence_file_path && Storage::exists($record->evidence_file_path)) {
                $fileUrl = route('fatigue-management.monitoring.evidence.download', ['id' => $record->id]);
            }

            $uploadCtx = FatigueManagementFrequencyPlan::slotUploadContext(
                $slotDef,
                $frequencyPlan,
                $year,
                $isoWeek,
                $hasFile,
            );

            $slotStates[] = [
                'key' => $slotKey,
                'label' => $slotDef['label'],
                'date_label' => $slotDef['date_label'] ?? null,
                'time_window' => $uploadCtx['time_window'],
                'done' => $hasFile,
                'status' => $evidenceStatus->value,
                'status_label' => $evidenceStatus->label(),
                'status_color' => $evidenceStatus->color(),
                'record_id' => $record?->id,
                'evidence_uploaded_at' => $record?->evidence_uploaded_at?->format('d M Y H:i'),
                'evidence_original_name' => $record?->evidence_original_name,
                'evidence_file_url' => $fileUrl,
                'pic_name' => $record?->pic_name,
                'is_visible' => $uploadCtx['visible'],
                'is_uploadable' => $uploadCtx['uploadable'],
                'is_active' => $uploadCtx['is_active'],
                'hint' => $uploadCtx['hint'],
            ];
        }

        $visibleSlotStates = array_values(array_filter(
            $slotStates,
            static fn (array $s): bool => (bool) ($s['is_visible'] ?? false),
        ));

        $checklist = FatigueManagementFrequencyChecker::evaluateFromSlots($frequencyRaw, $slotStates);
        $slotsDone = count(array_filter($slotStates, static fn (array $s): bool => $s['done']));
        $slotsTotal = count($slotStates);

        $primaryRecord = $recordsBySlot[$firstSlotKey] ?? $recordsBySlot['default'] ?? null;
        $evaluationStatus = $primaryRecord?->evaluation_status ?? FatigueManagementEvaluationStatus::MenungguEvidence;
        if (! $evaluationStatus instanceof FatigueManagementEvaluationStatus) {
            $evaluationStatus = FatigueManagementEvaluationStatus::from((string) $evaluationStatus);
        }

        $aggregateEvidenceStatus = $slotsDone === 0
            ? FatigueManagementEvidenceStatus::BelumUpload
            : ($slotsDone < $slotsTotal
                ? FatigueManagementEvidenceStatus::SudahUpload
                : ($checklist['met'] && $checklist['status'] === 'sesuai'
                    ? FatigueManagementEvidenceStatus::Terverifikasi
                    : FatigueManagementEvidenceStatus::SudahUpload));

        if (count(array_filter($slotStates, static fn (array $s): bool => ($s['status'] ?? '') === 'perlu_lengkap')) > 0) {
            $aggregateEvidenceStatus = FatigueManagementEvidenceStatus::PerluLengkap;
        }

        $latestUpload = collect($slotStates)
            ->pluck('evidence_uploaded_at')
            ->filter()
            ->sort()
            ->last();

        return [
            'id' => $primaryRecord?->id,
            'program_key' => $program['key'] ?? '',
            'program_no' => $program['no'] ?? 0,
            'program_title' => $program['title'] ?? '',
            'program_type' => $program['program_type'] ?? 'mitra',
            'program_type_label' => $program['program_type_label'] ?? 'Program Mitra',
            'ho_badge' => $program['ho_badge'] ?? '',
            'program_status' => $program['status'] ?? '',
            'program_pillar' => $program['pillar'] ?? '',
            'sites' => $program['sites'] ?? [],
            'frequency' => (string) ($program['frequency'] ?? 'Weekly'),
            'frequency_raw' => $frequencyRaw,
            'frequency_category' => $frequencyPlan['category'],
            'frequency_category_label' => $frequencyPlan['category_label'],
            'frequency_plan' => $frequencyPlan,
            'implementation_indicator' => $program['implementation_indicator'] ?? '',
            'partner_key' => $partner['key'] ?? '',
            'partner_name' => $partner['name'] ?? '',
            'partner_classification' => $partner['classification'] ?? '',
            'year' => $year,
            'iso_week' => $isoWeek,
            'checklist_status' => $checklist['status'],
            'checklist_label' => $checklist['label'],
            'checklist_color' => $checklist['color'],
            'checklist_met' => $checklist['met'],
            'slots_done' => $slotsDone,
            'slots_total' => $slotsTotal,
            'slots_progress_label' => $slotsDone . '/' . $slotsTotal,
            'frequency_slots' => FatigueManagementFrequencyChecker::frequencySlotsFromStates($slotStates),
            'slot_states' => $slotStates,
            'upload_slot_states' => $visibleSlotStates,
            'evidence_status' => $aggregateEvidenceStatus->value,
            'evidence_status_label' => $aggregateEvidenceStatus->label(),
            'evidence_status_color' => $aggregateEvidenceStatus->color(),
            'evidence_uploaded_at' => $latestUpload,
            'evidence_original_name' => $primaryRecord?->evidence_original_name,
            'evidence_notes' => $primaryRecord?->evidence_notes,
            'evidence_file_url' => $slotStates[0]['evidence_file_url'] ?? null,
            'evaluation_status' => $evaluationStatus->value,
            'evaluation_status_label' => $evaluationStatus->label(),
            'evaluation_status_color' => $evaluationStatus->color(),
            'evaluation_score' => $primaryRecord?->evaluation_score,
            'evaluation_notes' => $primaryRecord?->evaluation_notes,
            'evaluated_by' => $primaryRecord?->evaluated_by,
            'evaluated_at' => $primaryRecord?->evaluated_at?->format('d M Y H:i'),
            'pic_name' => $primaryRecord?->pic_name,
            'needs_evidence' => ! $checklist['met'],
            'can_evaluate' => $slotsDone > 0,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, int|float>
     */
    private function buildSummary(array $rows): array
    {
        $total = count($rows);
        $checklistOk = 0;
        $belumChecklist = 0;
        $mandatoryTotal = 0;
        $mandatoryOk = 0;
        $upgradeTotal = 0;
        $upgradeOk = 0;
        $mitraTotal = 0;
        $mitraOk = 0;

        foreach ($rows as $row) {
            if ($row['checklist_met'] ?? false) {
                $checklistOk++;
            } else {
                $belumChecklist++;
            }

            $type = $row['program_type'] ?? 'mitra';
            $met = (bool) ($row['checklist_met'] ?? false);

            if ($type === 'mandatory') {
                $mandatoryTotal++;
                if ($met) {
                    $mandatoryOk++;
                }
            } elseif ($type === 'upgrade') {
                $upgradeTotal++;
                if ($met) {
                    $upgradeOk++;
                }
            } else {
                $mitraTotal++;
                if ($met) {
                    $mitraOk++;
                }
            }
        }

        return [
            'total_items' => $total,
            'checklist_ok' => $checklistOk,
            'belum_checklist' => $belumChecklist,
            'pct_checklist' => $total > 0 ? round(100 * $checklistOk / $total, 1) : 0.0,
            'mandatory_total' => $mandatoryTotal,
            'mandatory_ok' => $mandatoryOk,
            'upgrade_total' => $upgradeTotal,
            'upgrade_ok' => $upgradeOk,
            'mitra_total' => $mitraTotal,
            'mitra_ok' => $mitraOk,
        ];
    }

    /**
     * @param  array<string, mixed>  $catalog
     * @param  list<array<string, mixed>>  $programs
     * @return array<string, mixed>
     */
    private function filterOptions(array $catalog, array $programs, array $partners): array
    {
        $weeks = [];
        for ($w = 1; $w <= 53; $w++) {
            $weeks[] = 'W' . str_pad((string) $w, 2, '0', STR_PAD_LEFT);
        }

        $uniquePrograms = [];
        $seen = [];
        foreach ($programs as $p) {
            $key = (string) ($p['key'] ?? '');
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $uniquePrograms[] = $p;
        }

        return [
            'years' => [(int) date('Y') - 1, (int) date('Y'), (int) date('Y') + 1],
            'weeks' => $weeks,
            'partners' => array_map(static fn (array $p): array => [
                'value' => (string) ($p['key'] ?? ''),
                'label' => (string) ($p['name'] ?? ''),
            ], $partners),
            'programs' => array_map(static fn (array $p): array => [
                'value' => (string) ($p['key'] ?? ''),
                'label' => ($p['program_type_label'] ?? '') . ' — ' . Str::limit((string) ($p['title'] ?? ''), 48),
            ], $uniquePrograms),
            'program_types' => [
                ['value' => 'mandatory', 'label' => 'Mandatory (M)'],
                ['value' => 'upgrade', 'label' => 'Upgrade (U)'],
                ['value' => 'mitra', 'label' => 'Program Mitra (Tentatif)'],
            ],
            'checklist_statuses' => [
                ['value' => 'sesuai', 'label' => 'Sudah Checklist'],
                ['value' => 'uploaded', 'label' => 'Sudah Upload'],
                ['value' => 'belum', 'label' => 'Belum Checklist'],
                ['value' => 'terlambat', 'label' => 'Belum Periode Ini'],
                ['value' => 'perlu_perbaikan', 'label' => 'Perlu Dilengkapi'],
            ],
            'evidence_statuses' => array_map(static fn (FatigueManagementEvidenceStatus $s): array => [
                'value' => $s->value,
                'label' => $s->label(),
            ], FatigueManagementEvidenceStatus::cases()),
            'evaluation_statuses' => array_map(static fn (FatigueManagementEvaluationStatus $s): array => [
                'value' => $s->value,
                'label' => $s->label(),
            ], FatigueManagementEvaluationStatus::cases()),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function findProgram(string $programKey, string $partnerKey): array
    {
        foreach ($this->catalogService->buildCatalog()['programs'] ?? [] as $program) {
            if (($program['key'] ?? '') === $programKey && strtoupper((string) ($program['partner_key'] ?? '')) === strtoupper($partnerKey)) {
                return $program;
            }
        }

        return [
            'key' => $programKey,
            'no' => 0,
            'title' => $programKey,
            'program_type' => 'mitra',
            'frequency' => 'Weekly',
            'status' => 'tentatif',
            'pillar' => '',
            'partner_key' => $partnerKey,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function findPartner(string $partnerKey): array
    {
        $key = strtoupper($partnerKey);
        foreach ($this->catalogService->buildCatalog()['partners'] ?? [] as $partner) {
            if (strtoupper((string) ($partner['key'] ?? '')) === $key) {
                return $partner;
            }
        }

        return ['key' => $key, 'name' => $key, 'classification' => 'medium'];
    }

    private function programTypeMatches(string $filter, string $actual): bool
    {
        $aliases = [
            'wajib' => 'mandatory',
            'mandatori' => 'upgrade',
        ];

        $normalizedFilter = $aliases[$filter] ?? $filter;
        $normalizedActual = $aliases[$actual] ?? $actual;

        return $normalizedFilter === $normalizedActual;
    }
}
