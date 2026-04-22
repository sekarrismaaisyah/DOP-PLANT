<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PilotProjectValidationHistorySnapshot;
use App\Models\PilotProjectValidationProject;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PilotProjectValidationHistorySnapshotController extends Controller
{
    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));
        $rows = PilotProjectValidationHistorySnapshot::query()
            ->with('project:id,project_name')
            ->when($q !== '', fn ($query) => $query->where('snapshot_date', 'like', "%{$q}%"))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        return view('pilot-project-validation.history-snapshots.index', compact('rows', 'q'));
    }

    public function create(): View
    {
        $projects = PilotProjectValidationProject::query()->orderBy('project_name')->get(['id', 'project_name']);

        return view('pilot-project-validation.history-snapshots.create', compact('projects'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:pilot_project_validation_projects,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'snapshot_date' => ['required', 'string', 'max:128'],
            'progress' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'decision_score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        PilotProjectValidationHistorySnapshot::query()->create($data);

        return redirect()->route('pilot-project-validation.history-snapshots.index')->with('success', 'History snapshot berhasil dibuat.');
    }

    public function edit(PilotProjectValidationHistorySnapshot $historySnapshot): View
    {
        $projects = PilotProjectValidationProject::query()->orderBy('project_name')->get(['id', 'project_name']);

        return view('pilot-project-validation.history-snapshots.edit', ['row' => $historySnapshot, 'projects' => $projects]);
    }

    public function update(Request $request, PilotProjectValidationHistorySnapshot $historySnapshot): RedirectResponse
    {
        $data = $request->validate([
            'project_id' => ['required', 'integer', 'exists:pilot_project_validation_projects,id'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'snapshot_date' => ['required', 'string', 'max:128'],
            'progress' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'decision_score' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $historySnapshot->update($data);

        return redirect()->route('pilot-project-validation.history-snapshots.index')->with('success', 'History snapshot berhasil diperbarui.');
    }

    public function destroy(PilotProjectValidationHistorySnapshot $historySnapshot): RedirectResponse
    {
        $historySnapshot->delete();

        return redirect()->route('pilot-project-validation.history-snapshots.index')->with('success', 'History snapshot berhasil dihapus.');
    }
}

