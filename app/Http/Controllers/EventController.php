<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::with('creator')->where('start_at', '>=', now())->orderBy('start_at', 'asc')->paginate(20);

        return view('events.index', compact('events'));
    }

    public function show(Request $request, Event $event): Response|JsonResponse|View
    {
        // Progressive enhancement: if the request is an X-HR or explicitly asked for a partial, return the server-rendered partial HTML
        // This is checked first to prioritize HTML for modals over JSON.
        if (($request->ajax() || $request->has('partial')) && $request->accepts('text/html')) {
            return response()->view('events._detail', compact('event'));
        }

        // If client explicitly accepts JSON, return structured JSON for safe mapping
        if ($request->wantsJson()) {
            return response()->json([
                'id' => $event->id,
                'title' => $event->title,
                'description' => $event->description,
                'start_at' => $event->start_at ? $event->start_at->toIso8601String() : null,
                'end_at' => $event->end_at ? $event->end_at->toIso8601String() : null,
                'is_all_day' => (bool) $event->is_all_day,
                'location' => $event->location,
                'type' => $event->type,
                'organizer' => $event->creator ? ['id' => $event->creator->id, 'name' => $event->creator->name] : null,
                'attachments' => [],
            ]);
        }

        // Default full page render
        return view('events.show', compact('event'));
    }

    public function calendar(): View
    {
        return view('events.calendar');
    }

    public function json(Request $request): JsonResponse
    {
        $query = Event::with('creator');

        // If the calendar requests a range, filter events intersecting the range
        if ($request->has('start') && $request->has('end')) {
            try {
                $start = \Carbon\Carbon::parse($request->input('start'));
                $end = \Carbon\Carbon::parse($request->input('end'));

                $query->where(function ($q) use ($start, $end) {
                    $q->whereBetween('start_at', [$start, $end])
                        ->orWhereBetween('end_at', [$start, $end])
                        ->orWhere(function ($q2) use ($start, $end) {
                            $q2->where('start_at', '<=', $start)
                                ->where(function ($q3) use ($end) {
                                    $q3->whereNull('end_at')->orWhere('end_at', '>=', $end);
                                });
                        });
                });
            } catch (\Exception $e) {
                // ignore parse errors and fall back to upcoming
                $query->where('start_at', '>=', now());
            }
        } else {
            $query->where('start_at', '>=', now());
        }

        $events = $query->orderBy('start_at')->get();

        $payload = $events->map(function ($e) {
            return [
                'id' => $e->id,
                'title' => $e->title,
                'start' => $e->start_at ? $e->start_at->toIso8601String() : null,
                'end' => $e->end_at ? $e->end_at->toIso8601String() : null,
                'allDay' => (bool) $e->is_all_day,
                'type' => $e->type ?? 'autre',
                'url' => route('events.show', $e),
            ];
        });

        return response()->json($payload->values());
    }
}
