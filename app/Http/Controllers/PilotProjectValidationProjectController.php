<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PilotProjectValidation\PilotProjectValidationProjectStoreRequest;
use App\Http\Requests\PilotProjectValidation\PilotProjectValidationProjectUpdateRequest;
use App\Models\PilotProjectValidationProject;
use App\Services\PilotProjectValidation\PilotProjectValidationPortfolioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PilotProjectValidationProjectController extends Controller
{
    public function __construct(
        private readonly PilotProjectValidationPortfolioService $portfolioService
    ) {}

    public function index(): View
    {
        $projects = PilotProjectValidationProject::query()
            ->orderBy('project_name')
            ->get(['id', 'project_name', 'progress', 'current_period', 'updated_at']);

        return view('pilot-project-validation.projects.index', compact('projects'));
    }

    public function create(): View
    {
        return view('pilot-project-validation.projects.create');
    }

    public function store(PilotProjectValidationProjectStoreRequest $request): RedirectResponse
    {
        $this->portfolioService->createProjectWithDefaults($request->validated());

        return redirect()
            ->route('pilot-project-validation.projects.index')
            ->with('success', 'Proyek pilot berhasil dibuat dengan struktur default (gate, metrik, timeline).');
    }

    public function edit(PilotProjectValidationProject $project): View
    {
        return view('pilot-project-validation.projects.edit', compact('project'));
    }

    public function update(PilotProjectValidationProjectUpdateRequest $request, PilotProjectValidationProject $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()
            ->route('pilot-project-validation.projects.index')
            ->with('success', 'Data master proyek berhasil diperbarui.');
    }

    public function destroy(PilotProjectValidationProject $project): RedirectResponse
    {
        $project->delete();

        return redirect()
            ->route('pilot-project-validation.projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }
}
