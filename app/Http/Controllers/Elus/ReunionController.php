<?php

namespace App\Http\Controllers\Elus;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\RequiresAdmin;
use App\Models\Reunion;
use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class ReunionController extends Controller
{
    use RequiresAdmin;
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
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        $reunions = $query->orderBy('date', 'desc')->paginate(12);
        $instances = Instance::orderBy('name')->get();
        $statuses = Reunion::STATUSES;

        return view('elus.reunions.index', compact('reunions', 'instances', 'statuses'));
    }

    /**
     * Show the form for creating a new reunion.
     */
    public function create(Request $request): View
    {
        $this->requireAdmin();

        $instances = Instance::orderBy('name')->get();
        $statuses = Reunion::STATUSES;
        $selectedInstance = $request->instance_id;

        return view('elus.reunions.create', compact('instances', 'statuses', 'selectedInstance'));
    }

    /**
     * Store a newly created reunion in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'instance_id' => 'required|exists:instances,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'participants' => 'nullable|array',
            'status' => 'required|string|in:' . implode(',', array_keys(Reunion::STATUSES)),
            'ordre_du_jour' => 'nullable|string',
        ]);

        Reunion::create($validated);

        return redirect()
            ->route('elus.reunions.index')
            ->with('success', __('Réunion créée avec succès.'));
    }

    /**
     * Display the specified reunion.
     */
    public function show(Reunion $reunion): View
    {
        $reunion->load('instance');
        return view('elus.reunions.show', compact('reunion'));
    }

    /**
     * Show the form for editing the specified reunion.
     */
    public function edit(Reunion $reunion): View
    {
        $this->requireAdmin();

        $instances = Instance::orderBy('name')->get();
        $statuses = Reunion::STATUSES;
        return view('elus.reunions.edit', compact('reunion', 'instances', 'statuses'));
    }

    /**
     * Update the specified reunion in storage.
     */
    public function update(Request $request, Reunion $reunion): RedirectResponse
    {
        $this->requireAdmin();

        $validated = $request->validate([
            'instance_id' => 'required|exists:instances,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'date' => 'required|date',
            'location' => 'nullable|string|max:255',
            'participants' => 'nullable|array',
            'status' => 'required|string|in:' . implode(',', array_keys(Reunion::STATUSES)),
            'ordre_du_jour' => 'nullable|string',
            'compte_rendu' => 'nullable|string',
        ]);

        $reunion->update($validated);

        return redirect()
            ->route('elus.reunions.show', $reunion)
            ->with('success', __('Réunion mise à jour avec succès.'));
    }

    /**
     * Remove the specified reunion from storage.
     */
    public function destroy(Reunion $reunion): RedirectResponse
    {
        $this->requireAdmin();

        $reunion->delete();

        return redirect()
            ->route('elus.reunions.index')
            ->with('success', __('Réunion supprimée avec succès.'));
    }

    /**
     * Get reunions as JSON for calendar display.
     */
    public function json(Request $request): JsonResponse
    {
        $query = Reunion::with('instance');

        // Filter by date range for calendar
        if ($request->filled('start')) {
            $query->where('date', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->where('date', '<=', $request->end);
        }

        $reunions = $query->get();

        $events = $reunions->map(function ($reunion) {
            return [
                'id' => $reunion->id,
                'title' => $reunion->title,
                'start' => $reunion->date->toIso8601String(),
                'url' => route('elus.reunions.show', $reunion),
                'backgroundColor' => $this->getStatusColor($reunion->status),
                'borderColor' => $this->getStatusColor($reunion->status),
                'extendedProps' => [
                    'instance' => $reunion->instance->name ?? '',
                    'location' => $reunion->location,
                    'status' => $reunion->status_label,
                ],
            ];
        });

        return response()->json($events);
    }

    /**
     * Get the color for a status.
     */
    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'planifiee' => '#3b82f6',
            'confirmee' => '#22c55e',
            'terminee' => '#6b7280',
            'annulee' => '#ef4444',
            default => '#6b7280',
        };
    }

    /**
     * Display the calendar view.
     */
    public function calendar(): View
    {
        $instances = Instance::orderBy('name')->get();
        return view('elus.reunions.calendar', compact('instances'));
    }

    /**
     * Toggle calendar visibility.
     */
    public function toggleCalendar(Request $request): RedirectResponse
    {
        $showCalendar = $request->session()->get('show_calendar', false);
        $request->session()->put('show_calendar', !$showCalendar);

        return back();
    }
}
