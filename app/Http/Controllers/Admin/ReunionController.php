<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ReunionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReunionRequest;
use App\Models\Instance;
use App\Models\Reunion;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
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
        $reunions = Reunion::with('instance')
            ->filtered($request->only(['instance_id', 'status', 'from_date', 'to_date', 'search']))
            ->orderBy('start_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        $instances = Instance::orderBy('name')->get();
        $statuses = ReunionStatus::labels();

        return view('admin.reunions.index', compact('reunions', 'instances', 'statuses'));
    }

    /**
     * Show the form for creating a new reunion.
     */
    public function create(Request $request): View
    {
        $instances = Instance::orderBy('name')->get();
        $statuses = ReunionStatus::labels();
        $selectedInstance = $request->instance_id;
        $titres = User::titresElus();

        return view('admin.reunions.create', compact('instances', 'statuses', 'selectedInstance', 'titres'));
    }

    /**
     * Store a newly created reunion in storage.
     */
    public function store(ReunionRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['date']);

        // Check for scheduling conflicts
        $conflicts = $this->checkForConflicts(
            (int) $validated['instance_id'],
            $validated['start_time'],
            $validated['end_time']
        );

        if ($conflicts->isNotEmpty()) {
            $alternatives = $this->suggestAlternativeTimeSlots(
                (int) $validated['instance_id'],
                $validated['start_time'],
                $validated['end_time']
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
        $statuses = ReunionStatus::labels();
        $titres = User::titresElus();

        return view('admin.reunions.edit', compact('reunion', 'instances', 'statuses', 'titres'));
    }

    /**
     * Update the specified reunion in storage.
     */
    public function update(ReunionRequest $request, Reunion $reunion): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['date']);

        // Check for scheduling conflicts (excluding current reunion)
        $conflicts = $this->checkForConflicts(
            (int) $validated['instance_id'],
            $validated['start_time'],
            $validated['end_time'],
            $reunion->id
        );

        if ($conflicts->isNotEmpty()) {
            $alternatives = $this->suggestAlternativeTimeSlots(
                (int) $validated['instance_id'],
                $validated['start_time'],
                $validated['end_time']
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
    private function checkForConflicts(string|int $instanceId, string $startTime, string $endTime, ?int $excludeId = null): Collection
    {
        $instanceId = (int) $instanceId;
        $start = Carbon::parse($startTime)->setTimezone('UTC');
        $end = Carbon::parse($endTime)->setTimezone('UTC');

        $query = Reunion::where('instance_id', $instanceId)
            ->where(function ($q) use ($start, $end) {
                $q->where(function ($q2) use ($start, $end) {
                    $q2->where('start_time', '<', $end)
                        ->where('end_time', '>', $start);
                });
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->whereIn('status', [ReunionStatus::Planifiee->value, ReunionStatus::Confirmee->value])->get();
    }

    /**
     * Suggest alternative time slots.
     */
    private function suggestAlternativeTimeSlots(string|int $instanceId, string $startTime, string $endTime): array
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        $duration = $end->diffInMinutes($start);

        $alternatives = [];
        $current = $start->copy();

        for ($i = 0; $i < 10; $i++) {
            $current->addHours(2);
            $proposedEnd = $current->copy()->addMinutes($duration);

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
