<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Events</h2>
    </x-slot>

    <div class="container">
        <div class="d-flex justify-content-between mb-4">
            <h1>Events</h1>
            <a class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md" href="{{ route('admin.events.create') }}">Create Event</a>
        </div>

        @if(session('success'))
            <div class="bg-green-600 text-white p-2 rounded mb-4">{{ session('success') }}</div>
        @endif

        <table class="cyber-table w-full">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Start</th>
                    <th>End</th>
                    <th>Location</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($events as $event)
                    <tr>
                        <td>{{ $event->title }}</td>
                        <td>{{ $event->start_at }}</td>
                        <td>{{ $event->end_at }}</td>
                        <td>{{ $event->location }}</td>
                        <td>
                                <a href="{{ route('admin.events.edit', $event) }}" class="border border-gray-700 px-2 py-1 rounded-md">Edit</a>
                            <form action="{{ route('admin.events.destroy', $event) }}" method="POST" style="display:inline-block">@csrf @method('DELETE')<button class="bg-red-600 text-white px-2 py-1 rounded-md">Delete</button></form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $events->links() }}
    </div>
</x-app-layout>
