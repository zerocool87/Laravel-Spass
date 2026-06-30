<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Enums\ProjectStatus;
use App\Enums\ProjectType;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectRequest;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $baseQuery = Project::query()->visibleToUser($request->user());

        $projects = (clone $baseQuery)
            ->filtered($request->only(['type', 'status', 'search']))
            ->when($request->filled('territory'), fn ($q) => $q->whereJsonContains('territories', $request->territory))
            ->orderBy('updated_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        $types = ProjectType::labels();
        $statuses = ProjectStatus::labels();

        // Stats are scoped to the user's visible projects (no caching: visibility varies by commune).
        $stats = Project::statsFor($request->user());

        return view('elus.projects.index', compact('projects', 'types', 'statuses', 'stats'));
    }

    public function create(): View
    {
        return view('elus.projects.create', $this->formData());
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        Project::create($request->validated());

        return redirect()
            ->route('elus.projects.index')
            ->with('success', __('Projet créé avec succès.'));
    }

    public function show(Request $request, Project $project): View
    {
        abort_unless(
            Project::query()->visibleToUser($request->user())->whereKey($project->getKey())->exists(),
            403,
            __('Vous n\'avez pas accès à ce projet.')
        );

        return view('elus.projects.show', compact('project'));
    }

    public function edit(Project $project): View
    {
        return view('elus.projects.edit', ['project' => $project] + $this->formData());
    }

    /** @return array<string, mixed> */
    private function formData(): array
    {
        return [
            'types' => ProjectType::labels(),
            'statuses' => ProjectStatus::labels(),
            'communes' => $this->communes(),
        ];
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        $project->update($request->validated());

        return redirect()
            ->route('elus.projects.show', $project)
            ->with('success', __('Projet mis à jour avec succès.'));
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()
            ->route('elus.projects.index')
            ->with('success', __('Projet supprimé avec succès.'));
    }

    /**
     * Get projects as GeoJSON for map display.
     */
    public function geojson(Request $request): JsonResponse
    {
        $projects = Project::query()
            ->visibleToUser($request->user())
            ->active()
            ->whereNotNull('geodata')
            ->get();

        $features = $projects->map(function ($project) {
            return [
                'type' => 'Feature',
                'properties' => [
                    'id' => $project->id,
                    'title' => $project->title,
                    'type' => $project->type,
                    'status' => $project->status,
                    'budget' => $project->formatted_budget,
                ],
                'geometry' => $project->geodata,
            ];
        });

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}
