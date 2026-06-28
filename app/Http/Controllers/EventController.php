<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class EventController extends Controller
{
    public function index(): View
    {
        $events = Event::with('creator')
            ->where('end_at', '>=', now())
            ->orderBy('start_at', 'asc')
            ->paginate(20)
            ->withQueryString();

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

        if ($request->has('start') && $request->has('end')) {
            try {
                $start = Carbon::parse($request->input('start'));
                $end = Carbon::parse($request->input('end'));

                $query->inRange($start, $end);
            } catch (\Exception $e) {
                report($e);
                $query->where('end_at', '>=', now());
            }
        } else {
            $query->where('start_at', '>=', now());
        }

        $events = $query->orderBy('start_at')->get();

        $payload = $events->map(fn (Event $e) => [
            'id' => $e->id,
            'title' => $e->title,
            'start' => $e->start_at?->toIso8601String(),
            'end' => $e->end_at?->toIso8601String(),
            'allDay' => (bool) $e->is_all_day,
            'type' => $e->type ?? 'autre',
            'url' => route('events.show', $e),
        ]);

        return response()->json($payload->values());
    }
}
