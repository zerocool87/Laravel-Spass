<?php

declare(strict_types=1);

namespace App\Http\Controllers\Elus;

use App\Enums\ReunionStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ReunionRequest;
use App\Models\Instance;
use App\Models\Reunion;
use Illuminate\Http\JsonResponse;
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
        $hasFilters = $request->filled('instance_id')
            || $request->filled('status')
            || $request->filled('from_date')
            || $request->filled('to_date')
            || $request->filled('search');

        $query = Reunion::with('instance')->byTitres($request->user());

        if (! $hasFilters) {
            $query->upcoming();
        } else {
            $query->filtered($request->only(['instance_id', 'status', 'from_date', 'to_date', 'search']))
                ->orderBy('start_time', 'desc');
        }

        $reunions = $query->paginate(6)->withQueryString();
        $instances = Instance::orderBy('name')->get();
        $statuses = ReunionStatus::labels();

        return view('elus.reunions.index', compact('reunions', 'instances', 'statuses'));
    }

    public function create(Request $request): View
    {
        return view('elus.reunions.create', [
            'selectedInstance' => $request->instance_id,
        ] + $this->formData());
    }

    public function store(ReunionRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        Reunion::create($validated);

        return redirect()
            ->route('elus.reunions.index')
            ->with('success', __('Réunion créée avec succès.'));
    }

    public function show(Request $request, Reunion $reunion): View
    {
        abort_unless(
            Reunion::byTitres($request->user())->whereKey($reunion->id)->exists(),
            403,
            __('Vous n\'avez pas accès à cette réunion.')
        );

        $reunion->load('instance');

        return view('elus.reunions.show', compact('reunion'));
    }

    public function edit(Reunion $reunion): View
    {
        return view('elus.reunions.edit', ['reunion' => $reunion] + $this->formData());
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

        $reunion->update($validated);

        return redirect()
            ->route('elus.reunions.show', $reunion)
            ->with('success', __('Réunion mise à jour avec succès.'));
    }

    public function destroy(Reunion $reunion): RedirectResponse
    {
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
        $query = Reunion::with('instance')->byTitres($request->user());

        if ($request->filled('start')) {
            $query->where('start_time', '>=', $request->start);
        }
        if ($request->filled('end')) {
            $query->where('end_time', '<=', $request->end);
        }

        $reunions = $query->get();

        $events = $reunions->map(function ($reunion) {
            $color = ReunionStatus::tryFrom($reunion->status)?->hexColor() ?? '#6b7280';

            return [
                'id' => $reunion->id,
                'title' => $reunion->title,
                'start' => $reunion->start_time ? $reunion->start_time->toIso8601String() : null,
                'end' => $reunion->end_time ? $reunion->end_time->toIso8601String() : null,
                'url' => route('elus.reunions.show', $reunion),
                'backgroundColor' => $color,
                'borderColor' => $color,
                'extendedProps' => [
                    'instance' => $reunion->instance->name ?? '',
                    'location' => $reunion->location,
                    'status' => $reunion->status_label,
                ],
            ];
        });

        return response()->json($events);
    }

    public function calendar(): View
    {
        $instances = Instance::orderBy('name')->get();

        return view('elus.reunions.calendar', compact('instances'));
    }

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
