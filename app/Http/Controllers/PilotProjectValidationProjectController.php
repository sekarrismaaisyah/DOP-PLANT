<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PilotProjectValidation\PilotProjectValidationProjectStoreRequest;
use App\Http\Requests\PilotProjectValidation\PilotProjectValidationProjectUpdateRequest;
use App\Models\PilotProjectValidationProject;
use App\Services\PilotProjectValidation\PilotProjectValidationPortfolioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PilotProjectValidationProjectController extends Controller
{
    public function __construct(
        private readonly PilotProjectValidationPortfolioService $portfolioService
    ) {}

    public function index(Request $request): View
    {
        $q = trim((string) $request->query('q', ''));

        $projects = PilotProjectValidationProject::query()
            ->when($q !== '', function ($query) use ($q): void {
                $query->where(function ($inner) use ($q): void {
                    $inner
                        ->where('project_name', 'like', "%{$q}%")
                        ->orWhere('subtitle', 'like', "%{$q}%")
                        ->orWhere('pilot_area', 'like', "%{$q}%")
                        ->orWhere('current_phase', 'like', "%{$q}%")
                        ->orWhere('current_period', 'like', "%{$q}%")
                        ->orWhere('next_milestone', 'like', "%{$q}%")
                        ->orWhere('need_support_pic', 'like', "%{$q}%");
                });
            })
            ->orderBy('project_name')
            ->paginate(12, ['id', 'project_name', 'subtitle', 'pilot_area', 'support', 'current_phase', 'progress', 'current_period', 'next_milestone', 'need_support_pic', 'updated_at'])
            ->withQueryString();

        return view('pilot-project-validation.projects.index', compact('projects', 'q'));
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
