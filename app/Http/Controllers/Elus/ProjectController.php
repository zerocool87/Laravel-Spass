<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\RequiresAdmin;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    use RequiresAdmin;

    private function communes(): array
    {
        $list = config('options.communes_haute_vienne', []);
        sort($list, SORT_STRING | SORT_FLAG_CASE);

        return $list;
    }

    /**
     * Display a listing of the projects.
     */
    public function index(Request $request): View
    {
        $baseQuery = Project::query()->visibleToUser($request->user());
        $query = clone $baseQuery;

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by territory
        if ($request->filled('territory')) {
            $query->whereJsonContains('territories', $request->territory);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        $projects = $query->orderBy('updated_at', 'desc')->paginate(12);
        $types = Project::TYPES;
        $statuses = Project::STATUSES;

        // Statistics
        $stats = [
            'total' => (clone $baseQuery)->count(),
            'active' => (clone $baseQuery)->active()->count(),
            'total_budget' => (clone $baseQuery)->active()->sum('budget'),
        ];

        return view('elus.projects.index', compact('projects', 'types', 'statuses', 'stats'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): View
    {
        $this->requireAdmin();

        $types = Project::TYPES;
        $statuses = Project::STATUSES;
        $communes = $this->communes();

        return view('elus.projects.create', compact('types', 'statuses', 'communes'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:'.implode(',', array_keys(Project::TYPES)),
            'status' => 'required|string|in:'.implode(',', array_keys(Project::STATUSES)),
            'commune' => ['nullable', 'string', 'max:255', Rule::in($this->communes())],
            'territories' => 'nullable|array',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'indicators' => 'nullable|array',
        ]);

        Project::create($validated);

        return redirect()
            ->route('elus.projects.index')
            ->with('success', __('Projet créé avec succès.'));
    }

    /**
     * Display the specified project.
     */
    public function show(Project $project): View
    {
        abort_unless(
            Project::query()->visibleToUser(request()->user())->whereKey($project->getKey())->exists(),
            403,
            __('Vous n\'avez pas accès à ce projet.')
        );

        return view('elus.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project): View
    {
        $this->requireAdmin();

        $types = Project::TYPES;
        $statuses = Project::STATUSES;
        $communes = $this->communes();

        return view('elus.projects.edit', compact('project', 'types', 'statuses', 'communes'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:'.implode(',', array_keys(Project::TYPES)),
            'status' => 'required|string|in:'.implode(',', array_keys(Project::STATUSES)),
            'commune' => ['nullable', 'string', 'max:255', Rule::in($this->communes())],
            'territories' => 'nullable|array',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'indicators' => 'nullable|array',
        ]);

        $project->update($validated);

        return redirect()
            ->route('elus.projects.show', $project)
            ->with('success', __('Projet mis à jour avec succès.'));
    }

    /**
     * Remove the specified project from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        $this->requireAdmin();

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
