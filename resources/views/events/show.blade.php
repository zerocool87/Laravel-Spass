<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Event Details') }}</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-6 rounded-xl shadow-md">
                <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $event->title }}</h1>
                <div class="flex flex-wrap items-center gap-4 text-gray-700 mb-4">
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-cyan-100 text-cyan-800 text-sm font-medium">
                        <svg class="w-4 h-4 mr-1 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        {{ $event->start_at->format('d/m/Y H:i') }}
                        @if($event->end_at)
                            <span class="mx-2">&rarr;</span> {{ $event->end_at->format('d/m/Y H:i') }}
                        @endif
                    </span>
                    <span class="inline-flex items-center px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-sm font-medium">
                        <svg class="w-4 h-4 mr-1 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a4 4 0 10-1.414 1.414l4.243 4.243a1 1 0 001.414-1.414z"/></svg>
                        {{ $event->location }}
                    </span>
                    @if($event->type)
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-purple-100 text-purple-800 text-sm font-medium">
                            <svg class="w-4 h-4 mr-1 text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 1.343-3 3s1.343 3 3 3 3-1.343 3-3-1.343-3-3-3zm0 10c-4.418 0-8-1.79-8-4V6a2 2 0 012-2h12a2 2 0 012 2v8c0 2.21-3.582 4-8 4z"/></svg>
                            {{ $event->type }}
                        </span>
                    @endif
                </div>
                <div class="prose max-w-none text-gray-800 bg-white/80 rounded-lg p-4">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
