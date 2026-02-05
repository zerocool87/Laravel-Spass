<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects.
     */
    public function index(Request $request): View
    {
        $query = Project::query();

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
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $projects = $query->orderBy('updated_at', 'desc')->paginate(12);
        $types = Project::TYPES;
        $statuses = Project::STATUSES;

        // Statistics
        $stats = [
            'total' => Project::count(),
            'active' => Project::active()->count(),
            'total_budget' => Project::active()->sum('budget'),
        ];

        return view('elus.projects.index', compact('projects', 'types', 'statuses', 'stats'));
    }

    /**
     * Show the form for creating a new project.
     */
    public function create(): View
    {
        abort_unless(request()->user()->isAdmin(), 403, __('Vous n\'avez pas l\'autorisation d\'effectuer cette action.'));

        $types = Project::TYPES;
        $statuses = Project::STATUSES;
        return view('elus.projects.create', compact('types', 'statuses'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        abort_unless(request()->user()->isAdmin(), 403, __('Vous n\'avez pas l\'autorisation d\'effectuer cette action.'));

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_keys(Project::TYPES)),
            'status' => 'required|string|in:' . implode(',', array_keys(Project::STATUSES)),
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
        return view('elus.projects.show', compact('project'));
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Project $project): View
    {
        abort_unless(request()->user()->isAdmin(), 403, __('Vous n\'avez pas l\'autorisation d\'effectuer cette action.'));

        $types = Project::TYPES;
        $statuses = Project::STATUSES;
        return view('elus.projects.edit', compact('project', 'types', 'statuses'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        abort_unless(request()->user()->isAdmin(), 403, __('Vous n\'avez pas l\'autorisation d\'effectuer cette action.'));

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_keys(Project::TYPES)),
            'status' => 'required|string|in:' . implode(',', array_keys(Project::STATUSES)),
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
        abort_unless(request()->user()->isAdmin(), 403, __('Vous n\'avez pas l\'autorisation d\'effectuer cette action.'));

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
        $projects = Project::active()
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
