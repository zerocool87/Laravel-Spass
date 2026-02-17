<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InstanceController extends Controller
{
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
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $instances = $query->orderBy('name')->paginate(15);
        $types = Instance::TYPES;

        // Get unique territories for filter
        $territories = Instance::whereNotNull('territory')
            ->distinct()
            ->pluck('territory')
            ->sort();

        return view('admin.instances.index', compact('instances', 'types', 'territories'));
    }

    /**
     * Show the form for creating a new instance.
     */
    public function create(): View
    {
        $types = Instance::TYPES;
        $users = User::where('is_elu', true)
            ->orderBy('name')
            ->get();

        return view('admin.instances.create', compact('types', 'users'));
    }

    /**
     * Store a newly created instance in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Instance::TYPES)),
            'description' => 'nullable|string',
            'territory' => 'nullable|string|max:255',
            'members' => 'nullable|array',
            'members.*' => 'string',
        ]);

        Instance::create($validated);

        return redirect()
            ->route('admin.instances.index')
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
        $pastReunions = $instance->reunions()
            ->where('date', '<', now())
            ->orderBy('date', 'desc')
            ->take(10)
            ->get();

        return view('admin.instances.show', compact('instance', 'upcomingReunions', 'pastReunions'));
    }

    /**
     * Show the form for editing the specified instance.
     */
    public function edit(Instance $instance): View
    {
        $types = Instance::TYPES;
        $users = User::where('is_elu', true)
            ->orderBy('name')
            ->get();

        return view('admin.instances.edit', compact('instance', 'types', 'users'));
    }

    /**
     * Update the specified instance in storage.
     */
    public function update(Request $request, Instance $instance): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:' . implode(',', array_keys(Instance::TYPES)),
            'description' => 'nullable|string',
            'territory' => 'nullable|string|max:255',
            'members' => 'nullable|array',
            'members.*' => 'string',
        ]);

        $instance->update($validated);

        return redirect()
            ->route('admin.instances.index')
            ->with('success', __('Instance mise à jour avec succès.'));
    }

    /**
     * Remove the specified instance from storage.
     */
    public function destroy(Instance $instance): RedirectResponse
    {
        // Check if instance has reunions
        if ($instance->reunions()->count() > 0) {
            return redirect()
                ->route('admin.instances.index')
                ->with('error', __('Impossible de supprimer cette instance car elle contient des réunions.'));
        }

        $instance->delete();

        return redirect()
            ->route('admin.instances.index')
            ->with('success', __('Instance supprimée avec succès.'));
    }

    /**
     * Force delete the specified instance from storage.
     */
    public function forceDestroy(Instance $instance): RedirectResponse
    {
        // Delete all related reunions first
        $instance->reunions()->delete();

        $instance->delete();

        return redirect()
            ->route('admin.instances.index')
            ->with('success', __('Instance et ses réunions supprimées avec succès.'));
    }
}
