<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\RequiresAdmin;
use App\Models\Instance;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InstanceController extends Controller
{
    use RequiresAdmin;

    private function communes(): array
    {
        $list = config('options.communes_haute_vienne', []);
        sort($list, SORT_STRING | SORT_FLAG_CASE);

        return $list;
    }

    /**
     * Display a listing of the instances.
     */
    public function index(Request $request): View
    {
        $query = Instance::withCount('reunions');

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by territory
        if ($request->filled('territory')) {
            $query->where('territory', $request->territory);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        $instances = $query->orderBy('name')->paginate(12);
        $types = Instance::TYPES;

        return view('elus.instances.index', compact('instances', 'types'));
    }

    /**
     * Show the form for creating a new instance.
     */
    public function create(): View
    {
        $this->requireAdmin();

        $types = Instance::TYPES;

        return view('elus.instances.create', compact('types'));
    }

    /**
     * Store a newly created instance in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:'.implode(',', array_keys(Instance::TYPES)),
            'description' => 'nullable|string',
            'territory' => ['nullable', 'string', 'max:255', Rule::in($this->communes())],
            'members' => 'nullable|array',
        ]);

        Instance::create($validated);

        return redirect()
            ->route('elus.instances.index')
            ->with('success', __('Instance créée avec succès.'));
    }

    /**
     * Display the specified instance.
     */
    public function show(Instance $instance): View
    {
        $instance->load(['reunions' => function ($query) {
            $query->orderBy('date', 'desc');
        }]);

        $upcomingReunions = $instance->upcomingReunions()->take(5)->get();
        $pastReunions = $instance->reunions()->where('date', '<', now())->orderBy('date', 'desc')->take(10)->get();

        return view('elus.instances.show', compact('instance', 'upcomingReunions', 'pastReunions'));
    }

    /**
     * Show the form for editing the specified instance.
     */
    public function edit(Instance $instance): View
    {
        $this->requireAdmin();

        $types = Instance::TYPES;
        $communes = $this->communes();

        return view('elus.instances.edit', compact('instance', 'types', 'communes'));
    }

    /**
     * Update the specified instance in storage.
     */
    public function update(Request $request, Instance $instance): RedirectResponse
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:'.implode(',', array_keys(Instance::TYPES)),
            'description' => 'nullable|string',
            'territory' => ['nullable', 'string', 'max:255', Rule::in($this->communes())],
            'members' => 'nullable|array',
        ]);

        $instance->update($validated);

        return redirect()
            ->route('elus.instances.show', $instance)
            ->with('success', __('Instance mise à jour avec succès.'));
    }

    /**
     * Remove the specified instance from storage.
     */
    public function destroy(Instance $instance): RedirectResponse
    {
        $this->requireAdmin();

        $instance->delete();

        return redirect()
            ->route('elus.instances.index')
            ->with('success', __('Instance supprimée avec succès.'));
    }
}
