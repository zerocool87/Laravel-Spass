<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Upcoming Events</h2>
    </x-slot>

    <div class="container">
        <div class="mb-4">
            <a class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150" href="{{ route('events.calendar') }}">Open Calendar</a>
        </div>

        @foreach($events as $event)
            <div class="glass mb-3 p-3">
                <h5 class="text-2xl font-semibold text-gray-100"><a href="{{ route('events.show', $event) }}">{{ $event->title }}</a></h5>
                <p>{{ $event->start_at->format('Y-m-d H:i') }} @if($event->end_at) - {{ $event->end_at->format('Y-m-d H:i') }} @endif</p>
                <p class="text-sm">{{ $event->location }}</p>
            </div>
        @endforeach

        {{ $events->links() }}
    </div>
</x-app-layout>
