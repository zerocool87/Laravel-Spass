<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\EventRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        $events = Event::with('creator')->orderBy('start_at','desc')->paginate(20);
        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        return view('admin.events.create', compact('users'));
    }

    public function store(EventRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_all_day'] = boolval($data['is_all_day'] ?? false);
        $data['created_by'] = $request->user()->id;

        Event::create($data);

        return Redirect::route('admin.events.index')->with('success', 'Event created.');
    }

    public function edit(Event $event): View
    {
        $users = User::orderBy('name')->get();
        return view('admin.events.edit', compact('event','users'));
    }

    public function update(EventRequest $request, Event $event): RedirectResponse
    {
        $data = $request->validated();
        $data['is_all_day'] = boolval($data['is_all_day'] ?? false);

        $event->update($data);

        return Redirect::route('admin.events.index')->with('success', 'Event updated.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();
        return Redirect::route('admin.events.index')->with('success', 'Event deleted.');
    }
}
