<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProjectController extends Controller
{
    private ?array $communesList = null;

    private function communes(): array
    {
        if ($this->communesList !== null) {
            return $this->communesList;
        }

        $list = config('options.communes_haute_vienne', []);
        sort($list, SORT_STRING | SORT_FLAG_CASE);

        return $this->communesList = $list;
    }

    private function validationRules(): array
    {
        return [
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'type'        => 'required|string|in:'.implode(',', array_keys(Project::TYPES)),
            'status'      => 'required|string|in:'.implode(',', array_keys(Project::STATUSES)),
            'commune'     => ['nullable', 'string', 'max:255', Rule::in($this->communes())],
            'territories' => 'nullable|array',
            'budget'      => 'nullable|numeric|min:0',
            'start_date'  => 'nullable|date',
            'end_date'    => 'nullable|date|after_or_equal:start_date',
            'indicators'  => 'nullable|array',
        ];
    }

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

        // Filter by commune
        if ($request->filled('commune')) {
            $query->where('commune', $request->commune);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        $projects = $query->orderBy('updated_at', 'desc')->paginate(15)->withQueryString();
        $types = Project::TYPES;
        $statuses = Project::STATUSES;
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
        $types = Project::TYPES;
        $statuses = Project::STATUSES;
        $communes = $this->communes();

        return view('admin.projects.create', compact('types', 'statuses', 'communes'));
    }

    /**
     * Store a newly created project in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        Project::create($validated);

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
        $types = Project::TYPES;
        $statuses = Project::STATUSES;
        $communes = $this->communes();

        return view('admin.projects.edit', compact('project', 'types', 'statuses', 'communes'));
    }

    /**
     * Update the specified project in storage.
     */
    public function update(Request $request, Project $project): RedirectResponse
    {
        $validated = $request->validate($this->validationRules());

        $project->update($validated);

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
