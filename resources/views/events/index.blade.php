<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Upcoming Events</h2>
    </x-slot>

    {{-- Enable calendar debug to surface initialization issues on this page --}}
    <script>window.CALENDAR_DEBUG = {{ config('app.debug') ? 'true' : 'false' }};</script>

    <div class="container">
        @can('admin')
            <div class="mb-4 flex justify-center">
                <button type="button" onclick="window.openEventCreateModal(new Date().toISOString().slice(0,10))" class="bg-cyan-600 hover:bg-cyan-500 text-white rounded px-4 py-2">{{ __('Create Event') }}</button>
            </div>
        @endcan

        <div class="mt-6">
                @php
                    $typeColors = [
                        'assemblee' => '#7c3aed',
                        'bureau' => '#dc2626',
                        'commissions' => '#059669',
                        'autre' => '#0369a1',
                    ];
                @endphp
                @foreach($events as $event)
                <div class="glass mb-3 p-3">
                        @php
                            $color = $typeColors[$event->type ?? 'autre'] ?? $typeColors['autre'];
                            $typeLabels = [
                                'assemblee' => 'Assemblée plénière',
                                'bureau' => 'Réunion bureau',
                                'commissions' => 'Commissions',
                                'autre' => 'Autre',
                            ];
                            $label = $typeLabels[$event->type ?? 'autre'] ?? $typeLabels['autre'];
                        @endphp
                        <h5 class="text-2xl font-semibold inline-block" style="background: {{ $color }}; color: #fff; padding: 0.25rem 0.5rem; border-radius: 0.375rem; border: 1px solid rgba(0,0,0,0.25); box-shadow: 0 2px 6px rgba(0,0,0,0.25);">
                            <a href="{{ route('events.show', $event) }}" style="color: inherit;">{{ $event->title }}</a>
                        </h5>
                        <div class="mt-2">
                            <span class="text-sm font-medium" style="color: {{ $color }};">{{ $label }}</span>
                        </div>
                    <p>{{ $event->start_at->format('Y-m-d H:i') }} @if($event->end_at) - {{ $event->end_at->format('Y-m-d H:i') }} @endif</p>
                    <p class="text-sm">{{ $event->location }}</p>
                </div>
            @endforeach

            {{ $events->links() }}
        </div>

        <div class="mt-8 mb-6 flex justify-center">
            <div id="events-calendar"
                 data-feed-url="{{ route('events.json') }}"
                 data-mode="full"
                 data-can-edit="{{ auth()->check() && auth()->user()->can('admin') ? '1' : '0' }}"
                 data-create-url="{{ auth()->check() && auth()->user()->can('admin') ? route('admin.events.create') : '' }}"
                 data-edit-base="{{ auth()->check() && auth()->user()->can('admin') ? route('admin.events.index') : '' }}"
                 class="w-full max-w-4xl"></div>
        </div>
    </div>

    @can('admin')
        @include('events._admin_create_modal')
    @endcan
</x-app-layout>
