<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PilotProjectValidationGate;
use App\Models\PilotProjectValidationMetric;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PilotProjectValidationMetricController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $rows = PilotProjectValidationMetric::query()
            ->with('gate.project:id,project_name')
            ->when($q !== '', fn ($query) => $query->where('metric_name', 'like', "%{$q}%"))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pilot-project-validation.metrics.index', compact('rows', 'q'));
    }

    public function create(): View
    {
        $gates = PilotProjectValidationGate::query()
            ->with('project:id,project_name')
            ->orderByDesc('id')
            ->get(['id', 'project_id', 'gate_label', 'gate_title']);

        return view('pilot-project-validation.metrics.create', compact('gates'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:pilot_project_validation_gates,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'metric_name' => ['required', 'string', 'max:255'],
            'metric_type' => ['nullable', 'string', 'max:32'],
            'metric_desc' => ['nullable', 'string'],
            'direction' => ['nullable', 'string', 'max:16'],
            'unit' => ['nullable', 'string', 'max:64'],
            'critical' => ['nullable', 'boolean'],
            'metric_value' => ['nullable', 'string', 'max:64'],
            'min_value' => ['nullable', 'numeric'],
            'max_value' => ['nullable', 'numeric'],
            'step_value' => ['nullable', 'numeric'],
            'pass_threshold' => ['nullable', 'numeric'],
            'conditional_threshold' => ['nullable', 'numeric'],
            'pic_current_finding' => ['nullable', 'string'],
            'pic_evidence_source' => ['nullable', 'string'],
            'pic_comment' => ['nullable', 'string'],
            'metric_status' => ['nullable', 'string', 'max:64'],
        ]);
        $data['critical'] = (bool) ($data['critical'] ?? false);

        PilotProjectValidationMetric::query()->create($data);

        return redirect()->route('pilot-project-validation.metrics.index')->with('success', 'Metric berhasil dibuat.');
    }

    public function edit(PilotProjectValidationMetric $metric): View
    {
        $gates = PilotProjectValidationGate::query()
            ->with('project:id,project_name')
            ->orderByDesc('id')
            ->get(['id', 'project_id', 'gate_label', 'gate_title']);

        return view('pilot-project-validation.metrics.edit', ['row' => $metric, 'gates' => $gates]);
    }

    public function update(Request $request, PilotProjectValidationMetric $metric): RedirectResponse
    {
        $data = $request->validate([
            'gate_id' => ['required', 'integer', 'exists:pilot_project_validation_gates,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'metric_name' => ['required', 'string', 'max:255'],
            'metric_type' => ['nullable', 'string', 'max:32'],
            'metric_desc' => ['nullable', 'string'],
            'direction' => ['nullable', 'string', 'max:16'],
            'unit' => ['nullable', 'string', 'max:64'],
            'critical' => ['nullable', 'boolean'],
            'metric_value' => ['nullable', 'string', 'max:64'],
            'min_value' => ['nullable', 'numeric'],
            'max_value' => ['nullable', 'numeric'],
            'step_value' => ['nullable', 'numeric'],
            'pass_threshold' => ['nullable', 'numeric'],
            'conditional_threshold' => ['nullable', 'numeric'],
            'pic_current_finding' => ['nullable', 'string'],
            'pic_evidence_source' => ['nullable', 'string'],
            'pic_comment' => ['nullable', 'string'],
            'metric_status' => ['nullable', 'string', 'max:64'],
        ]);
        $data['critical'] = (bool) ($data['critical'] ?? false);

        $metric->update($data);

        return redirect()->route('pilot-project-validation.metrics.index')->with('success', 'Metric berhasil diperbarui.');
    }

    public function destroy(PilotProjectValidationMetric $metric): RedirectResponse
    {
        $metric->delete();

        return redirect()->route('pilot-project-validation.metrics.index')->with('success', 'Metric berhasil dihapus.');
    }
}

