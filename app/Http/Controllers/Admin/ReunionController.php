<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Enums\ReunionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReunionRequest;
use App\Models\Instance;
use App\Models\Reunion;
use App\Models\User;
use App\Services\ReunionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReunionController extends Controller
{
    public function __construct(
        private readonly ReunionService $reunionService,
    ) {}

    public function index(Request $request): View
    {
        $reunions = Reunion::with('instance')
            ->filtered($request->only(['instance_id', 'status', 'from_date', 'to_date', 'search']))
            ->orderBy('start_time', 'desc')
            ->paginate(15)
            ->withQueryString();

        return view('admin.reunions.index', [
            'reunions' => $reunions,
            'instances' => Instance::orderBy('name')->get(),
            'statuses' => ReunionStatus::labels(),
        ]);
    }

    public function create(Request $request): View
    {
        return view('admin.reunions.create', [
            'selectedInstance' => $request->instance_id,
            'titres' => User::titresElus(),
        ] + $this->formData());
    }

    public function store(ReunionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($this->reunionService->hasConflicts(
            (int) $validated['instance_id'],
            $validated['start_time'],
            $validated['end_time'],
        )) {
            return back()->withInput()
                ->withErrors(['conflict' => __('Conflit d\'horaire détecté.')])
                ->with('alternative_slots', $this->reunionService->suggestSlots(
                    (int) $validated['instance_id'],
                    $validated['start_time'],
                    $validated['end_time'],
                ));
        }

        Reunion::create($validated);

        return redirect()->route('admin.reunions.index')
            ->with('success', __('Réunion créée avec succès.'));
    }

    public function edit(Reunion $reunion): View
    {
        return view('admin.reunions.edit', [
            'reunion' => $reunion,
            'titres' => User::titresElus(),
        ] + $this->formData());
    }

    /** @return array<string, mixed> */
    private function formData(): array
    {
        return [
            'instances' => Instance::orderBy('name')->get(),
            'statuses' => ReunionStatus::labels(),
        ];
    }

    public function update(ReunionRequest $request, Reunion $reunion): RedirectResponse
    {
        $validated = $request->validated();

        if ($this->reunionService->hasConflicts(
            (int) $validated['instance_id'],
            $validated['start_time'],
            $validated['end_time'],
            $reunion->id,
        )) {
            return back()->withInput()
                ->withErrors(['conflict' => __('Conflit d\'horaire détecté.')])
                ->with('alternative_slots', $this->reunionService->suggestSlots(
                    (int) $validated['instance_id'],
                    $validated['start_time'],
                    $validated['end_time'],
                ));
        }

        $reunion->update($validated);

        return redirect()->route('admin.reunions.index')
            ->with('success', __('Réunion mise à jour avec succès.'));
    }

    public function destroy(Reunion $reunion): RedirectResponse
    {
        $reunion->delete();

        return redirect()->route('admin.reunions.index')
            ->with('success', __('Réunion supprimée avec succès.'));
    }
}
