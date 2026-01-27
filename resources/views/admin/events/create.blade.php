<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Create Event</h2>
    </x-slot>

    <div class="container">
        <h1>Create Event</h1>

        @if($errors->any())
            <div class="bg-red-600 text-white p-2 rounded mb-4">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.events.store') }}">
            @csrf

            <div class="mb-3">
                <label>Title</label>
                <input name="title" class="form-control" value="{{ old('title') }}">
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
            </div>

            <div class="mb-3">
                <label>Start</label>
                @php
                    $startPrefill = old('start_at');
                    if (!$startPrefill && request('start')) {
                        // if date only (YYYY-MM-DD), convert to datetime-local format
                        $r = request('start');
                        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $r)) {
                            $startPrefill = $r . 'T00:00';
                        } else {
                            $startPrefill = $r;
                        }
                    }
                @endphp
                <input name="start_at" type="datetime-local" class="form-control" value="{{ $startPrefill }}">
            </div>

            <div class="mb-3">
                <label>End</label>
                <input name="end_at" type="datetime-local" class="form-control" value="{{ old('end_at') }}">
            </div>

            <div class="mb-3 form-check">
                <input name="is_all_day" type="checkbox" value="1" class="form-check-input" {{ old('is_all_day') ? 'checked' : '' }}>
                <label class="form-check-label">All day</label>
            </div>

            <div class="mb-3">
                <label>Location</label>
                <input name="location" class="form-control" value="{{ old('location') }}">
            </div>

            <x-primary-button>Create</x-primary-button>
        </form>
    </div>
</x-app-layout>
