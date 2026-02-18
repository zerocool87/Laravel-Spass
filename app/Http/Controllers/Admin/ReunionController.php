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

        $reunions = $query->orderBy('start_time', 'desc')->paginate(15);
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
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
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

        // Combine date with time
        $startDateTime = $validated['date'].' '.$validated['start_time'];
        $endDateTime = $validated['date'].' '.$validated['end_time'];

        $validated['start_time'] = $startDateTime;
        $validated['end_time'] = $endDateTime;

        unset($validated['date']);

        // Check for scheduling conflicts
        $conflicts = $this->checkForConflicts(
            $validated['instance_id'],
            $startDateTime,
            $endDateTime
        );

        if ($conflicts->isNotEmpty()) {
            // Generate alternative time slots
            $alternatives = $this->suggestAlternativeTimeSlots(
                $validated['instance_id'],
                $startDateTime,
                $endDateTime
            );

            return back()->withInput()
                ->withErrors([
                    'conflict' => __('Conflit d\'horaire détecté avec :count autre(s) réunion(s)', ['count' => $conflicts->count()]),
                ])
                ->with('alternative_slots', $alternatives);
        }

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
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
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

        // Combine date with time
        $startDateTime = $validated['date'].' '.$validated['start_time'];
        $endDateTime = $validated['date'].' '.$validated['end_time'];

        $validated['start_time'] = $startDateTime;
        $validated['end_time'] = $endDateTime;

        unset($validated['date']);

        // Check for scheduling conflicts (excluding current reunion)
        $conflicts = $this->checkForConflicts(
            $validated['instance_id'],
            $startDateTime,
            $endDateTime,
            $reunion->id
        );

        if ($conflicts->isNotEmpty()) {
            // Generate alternative time slots
            $alternatives = $this->suggestAlternativeTimeSlots(
                $validated['instance_id'],
                $startDateTime,
                $endDateTime
            );

            return back()->withInput()
                ->withErrors([
                    'conflict' => __('Conflit d\'horaire détecté avec :count autre(s) réunion(s)', ['count' => $conflicts->count()]),
                ])
                ->with('alternative_slots', $alternatives);
        }

        $reunion->update($validated);

        return redirect()
            ->route('admin.reunions.index')
            ->with('success', __('Réunion mise à jour avec succès.'));
    }

    /**
     * Check for scheduling conflicts.
     */
    private function checkForConflicts(int $instanceId, string $startTime, string $endTime, ?int $excludeId = null): \Illuminate\Database\Eloquent\Collection
    {
        $start = \Carbon\Carbon::parse($startTime)->setTimezone('UTC');
        $end = \Carbon\Carbon::parse($endTime)->setTimezone('UTC');

        $query = Reunion::where('instance_id', $instanceId)
            ->where(function ($q) use ($start, $end) {
                // Check for overlapping time ranges
                $q->where(function ($q2) use ($start, $end) {
                    $q2->where('start_time', '<', $end)
                        ->where('end_time', '>', $start);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->whereIn('status', ['planifiee', 'confirmee'])->get();
    }

    /**
     * Suggest alternative time slots.
     */
    private function suggestAlternativeTimeSlots(int $instanceId, string $startTime, string $endTime): array
    {
        $start = \Carbon\Carbon::parse($startTime);
        $end = \Carbon\Carbon::parse($endTime);
        $duration = $end->diffInMinutes($start);

        $alternatives = [];
        $current = $start->copy();

        // Try to find 3 alternative slots
        for ($i = 0; $i < 10; $i++) {
            $current->addHours(2); // Try 2 hours later
            $proposedEnd = $current->copy()->addMinutes($duration);

            // Check if this slot is available
            $conflicts = $this->checkForConflicts(
                $instanceId,
                $current->toDateTimeString(),
                $proposedEnd->toDateTimeString()
            );

            if ($conflicts->isEmpty()) {
                $alternatives[] = [
                    'start' => $current->format('H:i'),
                    'end' => $proposedEnd->format('H:i'),
                ];

                if (count($alternatives) >= 3) {
                    break;
                }
            }
        }

        return $alternatives;
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
