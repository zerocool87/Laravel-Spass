<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Models\Reunion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReunionController extends Controller
{
    /**
     * Display a listing of the reunions.
     */
    public function index(Request $request): View
    {
        $query = Reunion::with('instance');

        // Filter by instance
        if ($request->filled('instance_id')) {
            $query->where('instance_id', $request->instance_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from_date')) {
            $query->where('date', '>=', $request->from_date);
        }
        if ($request->filled('to_date')) {
            $query->where('date', '<=', $request->to_date);
        }

        // Search
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', '%'.$request->search.'%')
                    ->orWhere('description', 'like', '%'.$request->search.'%');
            });
        }

        $reunions = $query->orderBy('date', 'desc')->paginate(15);
        $instances = Instance::orderBy('name')->get();
        $statuses = Reunion::STATUSES;

        return view('admin.reunions.index', compact('reunions', 'instances', 'statuses'));
    }

    /**
     * Show the form for creating a new reunion.
     */
    public function create(Request $request): View
    {
        $instances = Instance::orderBy('name')->get();
        $statuses = Reunion::STATUSES;
        $selectedInstance = $request->instance_id;

        return view('admin.reunions.create', compact('instances', 'statuses', 'selectedInstance'));
    }

    /**
     * Store a newly created reunion in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'instance_id' => 'required|exists:instances,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string|in:'.implode(',', array_keys(Reunion::STATUSES)),
            'ordre_du_jour' => 'nullable|string',
            'compte_rendu' => 'nullable|string',
        ]);

        // Convert participants_text to array
        $participantsText = $request->input('participants_text', '');
        $participants = array_filter(
            array_map('trim', explode("\n", $participantsText)),
            fn ($p) => ! empty($p)
        );

        $validated['participants'] = array_values($participants);

        Reunion::create($validated);

        return redirect()
            ->route('admin.reunions.index')
            ->with('success', __('Réunion créée avec succès.'));
    }

    /**
     * Show the form for editing the specified reunion.
     */
    public function edit(Reunion $reunion): View
    {
        $instances = Instance::orderBy('name')->get();
        $statuses = Reunion::STATUSES;

        return view('admin.reunions.edit', compact('reunion', 'instances', 'statuses'));
    }

    /**
     * Update the specified reunion in storage.
     */
    public function update(Request $request, Reunion $reunion): RedirectResponse
    {
        $validated = $request->validate([
            'instance_id' => 'required|exists:instances,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string|in:'.implode(',', array_keys(Reunion::STATUSES)),
            'ordre_du_jour' => 'nullable|string',
            'compte_rendu' => 'nullable|string',
        ]);

        // Convert participants_text to array
        $participantsText = $request->input('participants_text', '');
        $participants = array_filter(
            array_map('trim', explode("\n", $participantsText)),
            fn ($p) => ! empty($p)
        );

        $validated['participants'] = array_values($participants);

        $reunion->update($validated);

        return redirect()
            ->route('admin.reunions.index')
            ->with('success', __('Réunion mise à jour avec succès.'));
    }

    /**
     * Remove the specified reunion from storage.
     */
    public function destroy(Reunion $reunion): RedirectResponse
    {
        $reunion->delete();

        return redirect()
            ->route('admin.reunions.index')
            ->with('success', __('Réunion supprimée avec succès.'));
    }
}
