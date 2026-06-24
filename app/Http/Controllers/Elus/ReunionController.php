<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Enums\ReunionStatus;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Elus\Concerns\RequiresAdmin;
use App\Http\Requests\StoreReunionRequest;
use App\Http\Requests\UpdateReunionRequest;
use App\Models\Instance;
use App\Models\Reunion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReunionController extends Controller
{
    use RequiresAdmin;

    /**
     * Apply titre access scope for non-admin élus.
     */
    private function scopeByTitres($query): void
    {
        $user = request()->user();

        if (! $user || $user->isAdmin()) {
            return;
        }

        $query->where(function ($q) use ($user) {
            $q->where('visible_to_all', true);
            if ($user->fonction) {
                $q->orWhereJsonContains('titres', $user->fonction);
            }
        });
    }

    /**
     * Display a listing of the reunions.
     */
    public function index(Request $request): View
    {
        $query = Reunion::with('instance');

        // Default: show only upcoming reunions unless a specific filter is applied
        $hasFilters = $request->filled('instance_id')
            || $request->filled('status')
            || $request->filled('from_date')
            || $request->filled('to_date')
            || $request->filled('search');

        if (! $hasFilters) {
            $query->upcoming();
        } else {
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
                $query->where('start_time', '>=', $request->from_date);
            }
            if ($request->filled('to_date')) {
                $query->where('end_time', '<=', $request->to_date);
            }

            // Search
            if ($request->filled('search')) {
                $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', '%'.$request->search.'%')
                        ->orWhere('description', 'like', '%'.$request->search.'%');
                });
            }

            $query->orderBy('start_time', 'desc');
        }

        $this->scopeByTitres($query);

        $reunions = $query->paginate(12);
        $instances = Instance::orderBy('name')->get();
        $statuses = ReunionStatus::labels();

        return view('elus.reunions.index', compact('reunions', 'instances', 'statuses'));
    }

    /**
     * Show the form for creating a new reunion.
     */
    public function create(Request $request): View
    {
        $this->requireAdmin();

        $instances = Instance::orderBy('name')->get();
        $statuses = ReunionStatus::labels();
        $selectedInstance = $request->instance_id;

        return view('elus.reunions.create', compact('instances', 'statuses', 'selectedInstance'));
    }

    /**
     * Store a newly created reunion in storage.
     */
    public function store(StoreReunionRequest $request): RedirectResponse
    {
        $this->requireAdmin();

        $validated = $request->validated();

        // Combine date with time
        $validated['start_time'] = $validated['date'].' '.$validated['start_time'];
        $validated['end_time'] = $validated['date'].' '.$validated['end_time'];

        unset($validated['date']);

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
        $user = request()->user();

        if (! $user?->isAdmin() && ! $reunion->visible_to_all) {
            if (! $user->fonction || ! in_array($user->fonction, $reunion->titres ?? [], true)) {
                abort(403, __('Vous n\'avez pas accès à cette réunion.'));
            }
        }

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
        $statuses = ReunionStatus::labels();

        return view('elus.reunions.edit', compact('reunion', 'instances', 'statuses'));
    }

    /**
     * Update the specified reunion in storage.
     */
    public function update(UpdateReunionRequest $request, Reunion $reunion): RedirectResponse
    {
        $this->requireAdmin();

        $validated = $request->validated();

        // Combine date with time
        $validated['start_time'] = $validated['date'].' '.$validated['start_time'];
        $validated['end_time'] = $validated['date'].' '.$validated['end_time'];

        unset($validated['date']);

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

        $this->scopeByTitres($query);

        // Filter by date range for calendar
        if ($request->filled('start')) {
            $query->where('start_time', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->where('end_time', '<=', $request->end);
        }

        $reunions = $query->get();

        $events = $reunions->map(function ($reunion) {
            return [
                'id' => $reunion->id,
                'title' => $reunion->title,
                'start' => $reunion->start_time ? $reunion->start_time->toIso8601String() : null,
                'end' => $reunion->end_time ? $reunion->end_time->toIso8601String() : null,
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
     * Get the hex color for a status.
     */
    private function getStatusColor(string $status): string
    {
        return ReunionStatus::tryFrom($status)?->hexColor() ?? '#6b7280';
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
    public function toggleCalendar(Request $request): JsonResponse|RedirectResponse
    {
        $showCalendar = $request->session()->get('show_calendar', false);
        $request->session()->put('show_calendar', ! $showCalendar);

        if ($request->wantsJson()) {
            return response()->json(['show' => ! $showCalendar]);
        }

        return back();
    }
}
