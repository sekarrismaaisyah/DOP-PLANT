<?php

declare(strict_types=1);

namespace App\Services\FatigueManagement;

use App\Enums\FatigueManagementEvaluationStatus;
use App\Enums\FatigueManagementEvidenceStatus;
use App\Models\FatigueManagementProgramMonitoring;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Monitoring operasional: upload evidence & proses evaluasi per program × mitra × periode.
 */
final class FatigueManagementMonitoringService
{
    /**
     * @return array<string, mixed>
     */
    public function buildDashboard(
        ?int $year = null,
        ?string $isoWeek = null,
        ?string $partnerKey = null,
        ?string $programKey = null,
        ?string $evidenceStatus = null,
        ?string $evaluationStatus = null,
    ): array {
        $framework = $this->loadFramework();
        $year = $year ?? (int) date('Y');
        $isoWeek = $isoWeek ?? ('W' . str_pad((string) date('W'), 2, '0', STR_PAD_LEFT));

        $programs = $this->programCatalog($framework);
        $partners = $framework['partners'] ?? [];
        $evidenceMap = $this->evidenceRequirementMap($framework);

        $records = FatigueManagementProgramMonitoring::query()
            ->where('year', $year)
            ->where('iso_week', $isoWeek)
            ->get()
            ->keyBy(static fn (FatigueManagementProgramMonitoring $r): string => $r->program_key . '|' . $r->partner_key);

        $rows = [];
        foreach ($programs as $program) {
            foreach ($partners as $partner) {
                $pKey = (string) ($partner['key'] ?? '');
                $progKey = (string) ($program['key'] ?? '');

                if ($partnerKey !== null && $partnerKey !== '' && strtoupper($partnerKey) !== strtoupper($pKey)) {
                    continue;
                }
                if ($programKey !== null && $programKey !== '' && $programKey !== $progKey) {
                    continue;
                }

                $record = $records->get($progKey . '|' . $pKey);
                $row = $this->composeRow($program, $partner, $record, $evidenceMap, $year, $isoWeek);

                if ($evidenceStatus !== null && $evidenceStatus !== '' && ($row['evidence_status'] ?? '') !== $evidenceStatus) {
                    continue;
                }
                if ($evaluationStatus !== null && $evaluationStatus !== '' && ($row['evaluation_status'] ?? '') !== $evaluationStatus) {
                    continue;
                }

                $rows[] = $row;
            }
        }

        usort($rows, static function (array $a, array $b): int {
            $cmp = ($a['program_no'] ?? 0) <=> ($b['program_no'] ?? 0);
            if ($cmp !== 0) {
                return $cmp;
            }

            return strcmp((string) ($a['partner_key'] ?? ''), (string) ($b['partner_key'] ?? ''));
        });

        return [
            'document' => $framework['document'] ?? [],
            'filters' => compact('year', 'isoWeek', 'partnerKey', 'programKey', 'evidenceStatus', 'evaluationStatus'),
            'filter_options' => $this->filterOptions($framework, $programs),
            'summary' => $this->buildSummary($rows),
            'programs' => $programs,
            'partners' => $partners,
            'rows' => $rows,
            'chart' => $this->chartPayload($rows, $partners),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function storeEvidence(
        string $programKey,
        string $partnerKey,
        int $year,
        string $isoWeek,
        UploadedFile $file,
        ?string $notes = null,
        ?string $picName = null,
    ): array {
        $path = $file->storeAs(
            'fatigue-management/evidence/' . $year . '/' . $isoWeek . '/' . strtoupper($partnerKey),
            $programKey . '_' . time() . '.' . $file->getClientOriginalExtension(),
        );

        $existing = FatigueManagementProgramMonitoring::query()
            ->where('program_key', $programKey)
            ->where('partner_key', strtoupper($partnerKey))
            ->where('year', $year)
            ->where('iso_week', $isoWeek)
            ->first();

        if ($existing?->evidence_file_path && Storage::exists($existing->evidence_file_path)) {
            Storage::delete($existing->evidence_file_path);
        }

        $record = FatigueManagementProgramMonitoring::query()->updateOrCreate(
            [
                'program_key' => $programKey,
                'partner_key' => strtoupper($partnerKey),
                'year' => $year,
                'iso_week' => $isoWeek,
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

        return $this->composeRow(
            $this->findProgram($programKey),
            $this->findPartner($partnerKey),
            $record->fresh(),
            [],
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
            $this->findProgram($record->program_key),
            $this->findPartner($record->partner_key),
            $record->fresh(),
            [],
            (int) $record->year,
            (string) $record->iso_week,
        );
    }

    /**
     * @param  array<string, mixed>  $framework
     * @return list<array<string, mixed>>
     */
    private function programCatalog(array $framework): array
    {
        $programs = [];
        foreach ($framework['site_standards'] ?? [] as $std) {
            $no = (int) ($std['no'] ?? 0);
            $programs[] = [
                'key' => 'site-' . str_pad((string) $no, 2, '0', STR_PAD_LEFT),
                'no' => $no,
                'title' => (string) ($std['standard'] ?? ''),
                'status' => (string) ($std['status'] ?? 'wajib'),
                'pillar' => $this->pillarForStandard($no),
            ];
        }

        return $programs;
    }

    /**
     * @param  array<string, mixed>  $framework
     * @return array<string, string>
     */
    private function evidenceRequirementMap(array $framework): array
    {
        $map = [];
        foreach ($framework['evidence_standards'] ?? [] as $ev) {
            $program = mb_strtolower((string) ($ev['program'] ?? ''));
            $map[$program] = (string) ($ev['evidence'] ?? '');
        }

        return $map;
    }

    /**
     * @param  array<string, mixed>  $program
     * @param  array<string, mixed>  $partner
     * @param  array<string, string>  $evidenceMap
     * @return array<string, mixed>
     */
    private function composeRow(
        array $program,
        array $partner,
        ?FatigueManagementProgramMonitoring $record,
        array $evidenceMap,
        int $year,
        string $isoWeek,
    ): array {
        $evidenceStatus = $record?->evidence_status ?? FatigueManagementEvidenceStatus::BelumUpload;
        $evaluationStatus = $record?->evaluation_status ?? FatigueManagementEvaluationStatus::MenungguEvidence;

        if (! $evidenceStatus instanceof FatigueManagementEvidenceStatus) {
            $evidenceStatus = FatigueManagementEvidenceStatus::from((string) $evidenceStatus);
        }
        if (! $evaluationStatus instanceof FatigueManagementEvaluationStatus) {
            $evaluationStatus = FatigueManagementEvaluationStatus::from((string) $evaluationStatus);
        }

        $evidenceHint = $this->matchEvidenceHint((string) ($program['title'] ?? ''), $evidenceMap);
        $fileUrl = null;
        if ($record?->evidence_file_path && Storage::exists($record->evidence_file_path)) {
            $fileUrl = route('fatigue-management.monitoring.evidence.download', ['id' => $record->id]);
        }

        return [
            'id' => $record?->id,
            'program_key' => $program['key'] ?? '',
            'program_no' => $program['no'] ?? 0,
            'program_title' => $program['title'] ?? '',
            'program_status' => $program['status'] ?? '',
            'program_pillar' => $program['pillar'] ?? '',
            'partner_key' => $partner['key'] ?? '',
            'partner_name' => $partner['name'] ?? '',
            'partner_classification' => $partner['classification'] ?? '',
            'year' => $year,
            'iso_week' => $isoWeek,
            'evidence_status' => $evidenceStatus->value,
            'evidence_status_label' => $evidenceStatus->label(),
            'evidence_status_color' => $evidenceStatus->color(),
            'evidence_uploaded_at' => $record?->evidence_uploaded_at?->format('d M Y H:i'),
            'evidence_original_name' => $record?->evidence_original_name,
            'evidence_notes' => $record?->evidence_notes,
            'evidence_requirement' => $evidenceHint,
            'evidence_file_url' => $fileUrl,
            'evaluation_status' => $evaluationStatus->value,
            'evaluation_status_label' => $evaluationStatus->label(),
            'evaluation_status_color' => $evaluationStatus->color(),
            'evaluation_score' => $record?->evaluation_score,
            'evaluation_notes' => $record?->evaluation_notes,
            'evaluated_by' => $record?->evaluated_by,
            'evaluated_at' => $record?->evaluated_at?->format('d M Y H:i'),
            'pic_name' => $record?->pic_name,
            'needs_evidence' => $evidenceStatus === FatigueManagementEvidenceStatus::BelumUpload
                || $evidenceStatus === FatigueManagementEvidenceStatus::PerluLengkap,
            'can_evaluate' => in_array($evaluationStatus, [
                FatigueManagementEvaluationStatus::MenungguReview,
                FatigueManagementEvaluationStatus::DalamEvaluasi,
                FatigueManagementEvaluationStatus::PerluPerbaikan,
            ], true) || $evidenceStatus !== FatigueManagementEvidenceStatus::BelumUpload,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @return array<string, int|float>
     */
    private function buildSummary(array $rows): array
    {
        $total = count($rows);
        $uploaded = 0;
        $verified = 0;
        $belumUpload = 0;
        $menungguReview = 0;
        $disetujui = 0;
        $perluPerbaikan = 0;

        foreach ($rows as $row) {
            $ev = $row['evidence_status'] ?? '';
            $eval = $row['evaluation_status'] ?? '';

            if (in_array($ev, ['sudah_upload', 'terverifikasi', 'perlu_lengkap'], true)) {
                $uploaded++;
            }
            if ($ev === 'terverifikasi') {
                $verified++;
            }
            if ($ev === 'belum_upload') {
                $belumUpload++;
            }
            if (in_array($eval, ['menunggu_review', 'dalam_evaluasi'], true)) {
                $menungguReview++;
            }
            if ($eval === 'disetujui') {
                $disetujui++;
            }
            if (in_array($eval, ['perlu_perbaikan', 'ditolak'], true)) {
                $perluPerbaikan++;
            }
        }

        return [
            'total_items' => $total,
            'evidence_uploaded' => $uploaded,
            'evidence_belum' => $belumUpload,
            'evidence_verified' => $verified,
            'pct_uploaded' => $total > 0 ? round(100 * $uploaded / $total, 1) : 0.0,
            'pct_verified' => $total > 0 ? round(100 * $verified / $total, 1) : 0.0,
            'menunggu_review' => $menungguReview,
            'disetujui' => $disetujui,
            'perlu_perbaikan' => $perluPerbaikan,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $rows
     * @param  list<array<string, mixed>>  $partners
     * @return array<string, mixed>
     */
    private function chartPayload(array $rows, array $partners): array
    {
        $byPartner = [];
        foreach ($partners as $p) {
            $key = (string) ($p['key'] ?? '');
            $byPartner[$key] = ['uploaded' => 0, 'total' => 0, 'approved' => 0];
        }

        foreach ($rows as $row) {
            $key = (string) ($row['partner_key'] ?? '');
            if (! isset($byPartner[$key])) {
                $byPartner[$key] = ['uploaded' => 0, 'total' => 0, 'approved' => 0];
            }
            $byPartner[$key]['total']++;
            if (in_array($row['evidence_status'] ?? '', ['sudah_upload', 'terverifikasi', 'perlu_lengkap'], true)) {
                $byPartner[$key]['uploaded']++;
            }
            if (($row['evaluation_status'] ?? '') === 'disetujui') {
                $byPartner[$key]['approved']++;
            }
        }

        $labels = [];
        $uploadPct = [];
        $approved = [];
        foreach ($byPartner as $key => $data) {
            $labels[] = $key;
            $uploadPct[] = $data['total'] > 0 ? round(100 * $data['uploaded'] / $data['total'], 1) : 0;
            $approved[] = $data['approved'];
        }

        $evidenceCounts = ['belum_upload' => 0, 'sudah_upload' => 0, 'perlu_lengkap' => 0, 'terverifikasi' => 0];
        $evalCounts = [];
        foreach ($rows as $row) {
            $evidenceCounts[$row['evidence_status'] ?? 'belum_upload'] = ($evidenceCounts[$row['evidence_status'] ?? 'belum_upload'] ?? 0) + 1;
            $ek = $row['evaluation_status'] ?? 'menunggu_evidence';
            $evalCounts[$ek] = ($evalCounts[$ek] ?? 0) + 1;
        }

        return [
            'partner_labels' => $labels,
            'partner_upload_pct' => $uploadPct,
            'partner_approved' => $approved,
            'evidence_counts' => $evidenceCounts,
            'evaluation_counts' => $evalCounts,
        ];
    }

    /**
     * @param  array<string, mixed>  $framework
     * @param  list<array<string, mixed>>  $programs
     * @return array<string, mixed>
     */
    private function filterOptions(array $framework, array $programs): array
    {
        $weeks = [];
        for ($w = 1; $w <= 53; $w++) {
            $weeks[] = 'W' . str_pad((string) $w, 2, '0', STR_PAD_LEFT);
        }

        return [
            'years' => [(int) date('Y') - 1, (int) date('Y'), (int) date('Y') + 1],
            'weeks' => $weeks,
            'partners' => array_map(static fn (array $p): array => [
                'value' => (string) ($p['key'] ?? ''),
                'label' => (string) ($p['name'] ?? ''),
            ], $framework['partners'] ?? []),
            'programs' => array_map(static fn (array $p): array => [
                'value' => (string) ($p['key'] ?? ''),
                'label' => 'Std ' . ($p['no'] ?? '') . ' — ' . ($p['title'] ?? ''),
            ], $programs),
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

    private function pillarForStandard(int $no): string
    {
        return match (true) {
            $no <= 4 => 'Prevention & Awareness',
            $no <= 9 => 'Pre-Shift Control',
            $no <= 13 => 'In-Shift Monitoring & Recovery',
            default => 'Assurance & Governance',
        };
    }

    /**
     * @param  array<string, string>  $evidenceMap
     */
    private function matchEvidenceHint(string $programTitle, array $evidenceMap): string
    {
        $title = mb_strtolower($programTitle);
        foreach ($evidenceMap as $key => $evidence) {
            if (str_contains($title, $key) || str_contains($key, mb_substr($title, 0, 12))) {
                return $evidence;
            }
        }

        if (str_contains($title, 'campaign')) {
            return $evidenceMap['campaign / sosialisasi'] ?? 'Foto, materi campaign, daftar hadir';
        }
        if (str_contains($title, 'fatigue check') && str_contains($title, 'awal')) {
            return $evidenceMap['fatigue check awal shift'] ?? 'Form pemeriksaan, rekap pekerja diperiksa';
        }
        if (str_contains($title, 'speak up')) {
            return $evidenceMap['speak up / tegur sapa'] ?? 'Log check point, tindak lanjut pengawas';
        }
        if (str_contains($title, 'reporting')) {
            return $evidenceMap['reporting program'] ?? 'Daily report, weekly summary, action closure';
        }

        return 'Evidence sesuai standar GMO — lihat matriks evidence FMP-STD-001';
    }

    /**
     * @return array<string, mixed>
     */
    private function findProgram(string $programKey): array
    {
        foreach ($this->programCatalog($this->loadFramework()) as $program) {
            if (($program['key'] ?? '') === $programKey) {
                return $program;
            }
        }

        return ['key' => $programKey, 'no' => 0, 'title' => $programKey, 'status' => 'wajib', 'pillar' => ''];
    }

    /**
     * @return array<string, mixed>
     */
    private function findPartner(string $partnerKey): array
    {
        $key = strtoupper($partnerKey);
        foreach ($this->loadFramework()['partners'] ?? [] as $partner) {
            if (strtoupper((string) ($partner['key'] ?? '')) === $key) {
                return $partner;
            }
        }

        return ['key' => $key, 'name' => $key, 'classification' => 'medium'];
    }

    /**
     * @return array<string, mixed>
     */
    private function loadFramework(): array
    {
        $path = resource_path('data/fatigue_management_gmo_program.json');
        if (! is_file($path)) {
            return [];
        }

        $payload = json_decode((string) file_get_contents($path), true);

        return is_array($payload) ? $payload : [];
    }
}
