<?php

declare(strict_types=1);

namespace App\Services\PilotProjectValidation;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * Template workbook: header baris 1 mengikuti nama kolom tabel (snake_case) agar selaras DB.
 *
 * - PROJECTS → pilot_project_validation_projects
 * - TIMELINE_PERIODS → pilot_project_validation_roadmap_periods (+ project_name untuk FK logis)
 * - TIMELINE_TASKS → pilot_project_validation_timeline_tasks (+ project_name, period, phase untuk tautan periode)
 * - TIMELINE → format lama (satu baris = periode + tugas)
 * - GATES → pilot_project_validation_gates
 * - METRICS → pilot_project_validation_metrics
 * - HISTORY → pilot_project_validation_history_snapshots (+ project_name)
 */
class PilotProjectValidationExcelTemplateService
{
    public function createSpreadsheet(): Spreadsheet
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getDefaultStyle()->getFont()->setName('Calibri')->setSize(11);

        $this->fillProjectsSheet($spreadsheet->getActiveSheet());
        $this->fillTimelinePeriodsSheet($spreadsheet->createSheet());
        $this->fillTimelineTasksSheet($spreadsheet->createSheet());
        $this->fillTimelineLegacySheet($spreadsheet->createSheet());
        $this->fillGatesSheet($spreadsheet->createSheet());
        $this->fillMetricsSheet($spreadsheet->createSheet());
        $this->fillHistorySheet($spreadsheet->createSheet());

        $spreadsheet->setActiveSheetIndex(0);

        return $spreadsheet;
    }

    private function fillProjectsSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('PROJECTS');
        // pilot_project_validation_projects (tanpa id, timestamps)
        $headers = [
            'project_name',
            'subtitle',
            'pilot_area',
            'support',
            'current_phase',
            'progress',
            'current_period',
            'next_milestone',
            'need_support_pic',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            [
                'Arcas HD',
                'Heavy automation use case with high dependency on',
                'PAMA BMO2, MTN SMO',
                'Infrastructure Network (5G), ROC / Monitoring, autonomous zone readiness',
                'Pilot proving & controlled expansion',
                62.5,
                'Apr–Jun 2026',
                'Safety drill closeout & scale recommendation',
                '',
            ],
        ], null, 'A2');
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setWidth(14);
        }
        $sheet->getColumnDimension('B')->setWidth(40);
        $sheet->getColumnDimension('D')->setWidth(48);
        $sheet->getColumnDimension('H')->setWidth(36);
    }

    private function fillTimelinePeriodsSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('TIMELINE_PERIODS');
        // roadmap_periods: gunakan project_name untuk mengaitkan proyek; urutan baris ≈ sort_order
        $headers = [
            'project_name',
            'display_current_period',
            'period',
            'phase',
            'status',
            'period_explanation',
            'planned_objective_outcome',
            'pic_update_summary',
            'pic_risks_dependencies',
            'pic_owner',
            'target_date',
            'reviewer_status',
            'period_progress_percent',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            [
                'Arcas HD',
                'Apr - Jun 2026',
                'Jan - Mar 2026',
                'Infrastructure & technical proving',
                'done',
                'Fase Infrastructure & technical proving pada periode Jan-Mar 2026 untuk Arcas HD.',
                'Validate network backbone and ROC connection stability; Complete initial end-to-end system integration test',
                '',
                '',
                '',
                '',
                '',
                100.0,
            ],
        ], null, 'A2');
        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(32);
        $sheet->getColumnDimension('F')->setWidth(48);
        $sheet->getColumnDimension('G')->setWidth(48);
    }

    private function fillTimelineTasksSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('TIMELINE_TASKS');
        // timeline_tasks: period + phase harus cocok dengan baris TIMELINE_PERIODS (kolom period, phase)
        $headers = [
            'project_name',
            'period',
            'phase',
            'task_text',
            'task_owner',
            'task_status',
            'original_owner',
            'original_status',
            'pic_actual_owner',
            'pic_start_date',
            'pic_actual_percent',
            'pic_progress_note',
            'evidence_link',
            'target_date',
            'dependency_blocker',
            'task_progress_percent_normalized',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            [
                'Arcas HD',
                'Jan - Mar 2026',
                'Infrastructure & technical proving',
                'Validate network backbone and ROC connection stability',
                'IT / Automation',
                'done',
                'IT / Automation',
                'done',
                '',
                '',
                1.0,
                '',
                '',
                '',
                'Belum ada infra',
                100.0,
            ],
        ], null, 'A2');
        $sheet->getColumnDimension('D')->setWidth(44);
        $sheet->getColumnDimension('L')->setWidth(20);
    }

    private function fillTimelineLegacySheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('TIMELINE');
        // Satu baris: kolom periode + tugas (alias umum tetap didukung impor)
        $headers = [
            'project_name',
            'display_current_period',
            'period',
            'phase',
            'status',
            'period_explanation',
            'planned_objective_outcome',
            'task_text',
            'task_owner',
            'task_status',
            'original_owner',
            'original_status',
            'pic_actual_owner',
            'pic_start_date',
            'pic_actual_percent',
            'target_date',
            'reviewer_status',
            'period_progress_percent',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            [
                'Arcas HD',
                '',
                'Apr–Jun 2026',
                'Pilot operation',
                'progress',
                '',
                '',
                'Run supervised multi-shift pilot scenario',
                'Ops',
                'progress',
                'Ops',
                'progress',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
        ], null, 'A2');
        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('H')->setWidth(42);
    }

    private function fillGatesSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('GATES');
        // pilot_project_validation_gates
        $headers = [
            'project_name',
            'gate_label',
            'gate_title',
            'gate_caption',
            'hard_gate',
            'gate_definition',
            'project_specific_explanation',
            'what_gate_confirms',
            'what_pic_needs_to_fill',
            'pic_status',
            'pic_notes_key_findings',
            'evidence_link_folder',
            'pic_owner',
            'target_close_date',
            'reviewer_status',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray([
            [
                'Arcas HD',
                'Gate 1',
                'Technical Feasibility',
                '',
                'yes',
                'Menilai kelayakan teknis dasar solusi sebelum pilot dilanjutkan atau diperluas.',
                '',
                'Kesiapan infrastruktur, integrasi sistem, kualitas data/sinyal, stabilitas teknis, uptime, latency, dan reliability.',
                'Lengkapi evidence hasil uji teknis, gap teknis terbuka, mitigasi, dan rekomendasi readiness.',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            ['Arcas HD', 'Gate 2', 'Performance / Effectiveness', '', 'no', '', '', '', '', '', '', '', '', '', ''],
            ['Arcas HD', 'Gate 3', 'Safety Case & Procedure', '', 'yes', '', '', '', '', '', '', '', '', '', ''],
            ['Arcas HD', 'Gate 4', 'Business / Assurance', '', 'no', '', '', '', '', '', '', '', '', '', ''],
        ], null, 'A2');
        $sheet->getColumnDimension('A')->setWidth(12);
        $sheet->getColumnDimension('B')->setWidth(12);
        $sheet->getColumnDimension('C')->setWidth(26);
        $sheet->getColumnDimension('F')->setWidth(40);
        $sheet->getColumnDimension('H')->setWidth(44);
        $sheet->getColumnDimension('I')->setWidth(44);
    }

    private function fillMetricsSheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('METRICS');
        // pilot_project_validation_metrics — nilai numerik / select di metric_value
        $headers = [
            'project_name',
            'gate_label',
            'metric_name',
            'metric_type',
            'metric_desc',
            'direction',
            'critical',
            'unit',
            'metric_value',
            'pass_threshold',
            'conditional_threshold',
            'min_value',
            'max_value',
            'step_value',
            'pic_current_finding',
            'pic_evidence_source',
            'pic_comment',
            'metric_status',
        ];
        $sheet->fromArray($headers, null, 'A1');
        $rows = [
            ['Arcas HD', 'Gate 1', 'Network uptime', 'range', 'Live network support for remote operation.', 'high', 'no', '%', 90.1, 98, 96, 90, 100, 0.1, '', '', '', ''],
            ['Arcas HD', 'Gate 1', 'Latency', 'range', 'Round-trip delay', 'low', 'yes', ' ms', 165, 200, 250, 50, 400, 5, '', '', '', ''],
            ['Arcas HD', 'Gate 1', 'Integration', 'select', 'ROC end-to-end status', 'high', 'yes', '', 'pass', '', '', '', '', '', '', '', '', ''],
            ['Arcas HD', 'Gate 3', 'SOP readiness', 'select', 'Procedure readiness', '', 'yes', '', 'pass', '', '', '', '', '', '', '', '', ''],
        ];
        $sheet->fromArray($rows, null, 'A2');
        foreach (range('A', 'R') as $col) {
            $sheet->getColumnDimension($col)->setWidth(11);
        }
        $sheet->getColumnDimension('E')->setWidth(30);
    }

    private function fillHistorySheet(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): void
    {
        $sheet->setTitle('HISTORY');
        // history_snapshots: snapshot_date, progress, decision_score + project_name
        $sheet->fromArray([
            ['project_name', 'snapshot_date', 'progress', 'decision_score', 'decision_status'],
            ['Arcas HD', '2026-04-01', 52, '', 'conditional go'],
            ['Arcas HD', '2026-05-01', 68.25, '', 'go'],
            ['Arcas HD', '2026-06-01', 72, 85, ''],
        ], null, 'A1');
        $sheet->getColumnDimension('A')->setWidth(14);
        $sheet->getColumnDimension('B')->setWidth(16);
    }
}
