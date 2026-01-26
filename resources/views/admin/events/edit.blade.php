<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Event</h2>
    </x-slot>

    <div class="container">
        <h1>Edit Event</h1>

        @if($errors->any())
            <div class="bg-red-600 text-white p-2 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.events.update', $event) }}">
            @csrf
            @method('PATCH')

            <div class="mb-3">
                <label>Title</label>
                <input name="title" class="form-control" value="{{ old('title', $event->title) }}">
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description', $event->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label>Start</label>
                <input name="start_at" type="datetime-local" class="form-control" value="{{ old('start_at', optional($event->start_at)->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="mb-3">
                <label>End</label>
                <input name="end_at" type="datetime-local" class="form-control" value="{{ old('end_at', optional($event->end_at)->format('Y-m-d\TH:i')) }}">
            </div>

            <div class="mb-3 form-check">
                <input name="is_all_day" type="checkbox" value="1" class="form-check-input" {{ old('is_all_day', $event->is_all_day) ? 'checked' : '' }}>
                <label class="form-check-label">All day</label>
            </div>

            <div class="mb-3">
                <label>Location</label>
                <input name="location" class="form-control" value="{{ old('location', $event->location) }}">
            </div>

            <button class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">Save</button>
        </form>
    </div>
</x-app-layout>
