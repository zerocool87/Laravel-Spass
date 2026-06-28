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

        $conflictResponse = $this->checkForConflict($validated);
        if ($conflictResponse !== null) {
            return $conflictResponse;
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

        $conflictResponse = $this->checkForConflict($validated, $reunion->id);
        if ($conflictResponse !== null) {
            return $conflictResponse;
        }

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

    /**
     * Check for scheduling conflicts and return the error response if any.
     *
     * @param  array<string, mixed>  $validated
     */
    private function checkForConflict(array $validated, ?int $excludeId = null): ?RedirectResponse
    {
        $conflicts = Reunion::conflicting(
            (int) $validated['instance_id'],
            Carbon::parse($validated['start_time']),
            Carbon::parse($validated['end_time']),
            $excludeId,
        );

        if ($conflicts->isEmpty()) {
            return null;
        }

        $alternatives = Reunion::suggestSlots(
            (int) $validated['instance_id'],
            Carbon::parse($validated['start_time']),
            Carbon::parse($validated['end_time']),
        );

        return back()->withInput()
            ->withErrors([
                'conflict' => __('Conflit d\'horaire détecté avec :count autre(s) réunion(s)', ['count' => $conflicts->count()]),
            ])
            ->with('alternative_slots', $alternatives);
    }
}
