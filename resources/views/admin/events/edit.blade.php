<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Event</h2>
    </x-slot>

    <div class="container">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-2xl font-semibold">Edit Event</h1>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.events.index') }}" class="text-sm text-cyan-200 hover:underline">&larr; Back to events</a>

                <form method="POST" action="{{ route('admin.events.destroy', $event) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this event?') }}');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 text-sm bg-red-600 text-white rounded px-3 py-1">Delete</button>
                </form>
            </div>
        </div>

        @if($errors->any())
            <div class="bg-red-600 text-white p-2 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.events.update', $event) }}" class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @csrf
            @method('PATCH')

            <div class="md:col-span-2 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-200">Title</label>
                    <input name="title" class="form-control w-full" value="{{ old('title', $event->title) }}" placeholder="Event title">
                    @error('title') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-200">Description</label>
                    <textarea name="description" rows="4" class="form-control w-full" placeholder="Short description...">{{ old('description', $event->description) }}</textarea>
                    @error('description') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-200">Start</label>
                        <input id="start_at" name="start_at" type="datetime-local" class="form-control w-full" value="{{ old('start_at', optional($event->start_at)->format('Y-m-d\TH:i')) }}">
                        <p class="text-xs text-gray-400 mt-1">Timezone: {{ now()->getTimezone()->getName() }}</p>
                        @error('start_at') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-200">End</label>
                        <input id="end_at" name="end_at" type="datetime-local" class="form-control w-full" value="{{ old('end_at', optional($event->end_at)->format('Y-m-d\TH:i')) }}">
                        @error('end_at') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <label class="inline-flex items-center gap-2">
                        <input id="is_all_day" name="is_all_day" type="checkbox" value="1" class="form-check-input" {{ old('is_all_day', $event->is_all_day) ? 'checked' : '' }}>
                        <span class="text-sm">All day</span>
                    </label>

                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-200">Location</label>
                        <input name="location" class="form-control w-full" value="{{ old('location', $event->location) }}" placeholder="e.g. Main Hall">
                        @error('location') <p class="text-red-400 text-sm mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="pt-4">
                    <x-primary-button>Save</x-primary-button>
                    <a href="{{ url('/events/'.$event->id) }}" class="ml-3 text-sm text-gray-200 hover:underline">View</a>
                </div>
            </div>

            <aside class="md:col-span-1 bg-gray-900 rounded p-4">
                <h4 class="text-sm font-semibold text-gray-100 mb-2">Details</h4>
                <dl class="text-sm text-gray-300">
                    <dt class="font-medium">Created by</dt>
                    <dd class="mb-2">{{ $event->creator?->name ?? '—' }}</dd>

                    <dt class="font-medium">Created at</dt>
                    <dd class="mb-2">{{ $event->created_at?->toDayDateTimeString() ?? '—' }}</dd>

                    <dt class="font-medium">Status</dt>
                    <dd class="mb-2">{{ $event->trashed() ? 'Deleted' : 'Active' }}</dd>
                </dl>
            </aside>
        </form>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const start = document.getElementById('start_at');
        const end = document.getElementById('end_at');

        if (!start || !end) return;

        start.addEventListener('change', function(){
            if (!end.value) {
                try {
                    const d = new Date(start.value);
                    d.setHours(d.getHours() + 1);
                    end.value = d.toISOString().slice(0,16);
                } catch (err) {
                    // ignore
                }
            }
        });
    });
    </script>
</x-app-layout>
