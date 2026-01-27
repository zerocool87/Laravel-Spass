<x-app-layout>
    <x-slot name="header">
        {{-- Title intentionally removed; calendar interactions replace the Create button --}}
    </x-slot>

    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            {{-- Header and create button removed; use calendar day-click to open create modal --}}
        </div>

        @if(session('success'))
            <div class="bg-green-600 text-white p-2 rounded mb-4">{{ session('success') }}</div>
        @endif

        <div class="flex justify-center">
            <div class="w-full max-w-4xl">
                <div class="overflow-x-auto">
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
                                        <x-secondary-button href="{{ route('admin.events.edit', $event) }}">Edit</x-secondary-button>
                                        <form action="{{ route('admin.events.destroy', $event) }}" method="POST" style="display:inline-block">@csrf @method('DELETE')<x-danger-button type="submit">Delete</x-danger-button></form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 flex justify-center">
                    {{ $events->links() }}
                </div>
            </div>
        </div>

        {{-- Debug flag and embedded calendar for admin users --}}
        <script>window.CALENDAR_DEBUG = true;</script>
        <div class="mt-8 mb-6 flex justify-center">
            <div id="admin-events-calendar"
                 data-feed-url="{{ route('events.json') }}"
                 data-mode="full"
                 data-can-edit="1"
                 data-create-url="{{ route('admin.events.create') }}"
                 data-edit-base="{{ route('admin.events.index') }}"
                 class="w-full max-w-4xl"></div>
        </div>

        @include('events._admin_create_modal')
    </div>
</x-app-layout>
