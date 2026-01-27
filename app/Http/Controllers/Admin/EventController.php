<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('can:admin')->except([]);
    }

    public function index(): View
    {
        $events = Event::with('creator')->orderBy('start_at', 'desc')->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.events.create', compact('users'));
    }

    public function store(EventRequest $request)
    {
        $data = $request->validated();
        $data['is_all_day'] = boolval($data['is_all_day'] ?? false);
        $data['created_by'] = $request->user()->id;

        $event = Event::create($data);

        // If the client expects JSON (AJAX calendar), return a JSON payload
        if ($request->wantsJson() || $request->ajax() || $request->header('Accept') === 'application/json') {
            return response()->json([
                'id' => $event->id,
                'title' => $event->title,
                'start' => $event->start_at ? $event->start_at->toIso8601String() : null,
                'end' => $event->end_at ? $event->end_at->toIso8601String() : null,
                'allDay' => (bool) $event->is_all_day,
                'url' => route('events.show', $event),
            ], 201);
        }

        return Redirect::route('admin.events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event): View
    {
        $users = User::orderBy('name')->get();

        return view('admin.events.edit', compact('event', 'users'));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();
        $data['is_all_day'] = boolval($data['is_all_day'] ?? false);

        $event->update($data);

        return Redirect::route('admin.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        // If the client expects JSON (AJAX calendar), return no content so the client can remove the event
        if (request()->wantsJson() || request()->ajax() || request()->header('Accept') === 'application/json') {
            return response()->noContent();
        }

        return Redirect::route('admin.events.index')->with('success', 'Event deleted.');
    }
}
