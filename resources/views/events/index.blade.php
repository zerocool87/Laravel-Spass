<x-app-layout>
    <x-slot name="header">
        <x-public-header
            title="{{ __('Upcoming Events') }}"
            icon="📅"
        />
    </x-slot>

    {{-- Enable calendar debug to surface initialization issues on this page --}}
    <script>window.CALENDAR_DEBUG = {{ config('app.debug') ? 'true' : 'false' }};</script>

    <div class="container">

        <div class="mt-6">
                @foreach($events as $event)
                    @php
                        $typeLabels = [
                            'assemblee' => 'Assemblée plénière',
                            'bureau' => 'Réunion bureau',
                            'commissions' => 'Commissions',
                            'autre' => 'Autre',
                        ];
                        $label = $typeLabels[$event->type ?? 'autre'] ?? $typeLabels['autre'];
                    @endphp
                    <div class="widget-container mb-4">
                        <div class="p-5">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <a href="{{ route('events.show', $event) }}" class="text-xl font-bold text-gray-900 hover:text-[#faa21b] transition">
                                        {{ $event->title }}
                                    </a>
                                    <div class="mt-2 flex flex-wrap gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#faa21b]/15 text-[#b36b00]">
                                            {{ $label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="mt-3 flex flex-wrap items-center gap-4 text-sm text-gray-600">
                                <span class="inline-flex items-center gap-1">
                                    <svg class="w-4 h-4 text-[#faa21b]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ $event->start_at->format('d/m/Y H:i') }}
                                    @if($event->end_at) → {{ $event->end_at->format('d/m/Y H:i') }} @endif
                                </span>
                                @if($event->location)
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4 text-[#faa21b]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        {{ $event->location }}
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

            {{ $events->links() }}
        </div>

        <div class="mt-8 mb-6 flex justify-center">
            <div id="events-calendar"
                 data-feed-url="{{ route('events.json') }}"
                 data-mode="full"
                 data-can-edit="0"
                 class="w-full max-w-4xl"></div>
        </div>
    </div>
</x-app-layout>
