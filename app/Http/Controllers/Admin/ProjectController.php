<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->filtered($request->only(['type', 'status', 'search']))
            ->when($request->filled('commune'), fn ($q) => $q->where('commune', $request->commune))
            ->orderBy('updated_at', 'desc')
            ->paginate(15)
            ->withQueryString();

        $types = ProjectType::labels();
        $statuses = ProjectStatus::labels();
        $communes = $this->communes();

        // Statistics are intentionally global (not scoped to the current filter)
        // to give admins a consistent overview regardless of which filter is active.
        $stats = cache()->remember('admin.projects.stats', 60, function () {
            return [
                'total' => Project::count(),
                'active' => Project::active()->count(),
                'total_budget' => Project::active()->sum('budget'),
            ];
        });

        return view('admin.projects.index', compact('projects', 'types', 'statuses', 'communes', 'stats'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): View
    {
        $types = ProjectType::labels();
        $statuses = ProjectStatus::labels();
        $communes = $this->communes();

        return view('admin.projects.create', compact('types', 'statuses', 'communes'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(ProjectRequest $request): RedirectResponse
    {
        Project::create($request->validated());

        return redirect()
            ->route('admin.projects.index')
            ->with('success', __('Projet créé avec succès.'));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): View
    {
        return view('admin.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project): View
    {
        $types = ProjectType::labels();
        $statuses = ProjectStatus::labels();
        $communes = $this->communes();

        return view('admin.projects.edit', compact('project', 'types', 'statuses', 'communes'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()
            ->route('admin.projects.show', $project)
            ->with('success', __('Projet mis à jour avec succès.'));
    }

    /**
     * Remove the specified project from storage (soft delete).
     */
    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()
            ->route('admin.projects.index')
            ->with('success', __('Projet supprimé avec succès.'));
    }
}
