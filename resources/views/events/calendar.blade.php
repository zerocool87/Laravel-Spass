<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Calendar</h2>
    </x-slot>

    <div class="container">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold text-gray-100">{{ __('Calendar') }}</h1>
            @can('admin')
                <x-primary-button href="{{ route('admin.events.create') }}">{{ __('Create Event') }}</x-primary-button>
            @endcan
        </div>

        <div id="calendar"
             data-feed-url="{{ route('events.json') }}"
             data-can-edit="{{ auth()->check() && auth()->user()->can('admin') ? '1' : '0' }}"
             data-create-url="{{ auth()->check() && auth()->user()->can('admin') ? route('admin.events.create') : '' }}"
             data-edit-base="{{ auth()->check() && auth()->user()->can('admin') ? route('admin.events.index') : '' }}"
        ></div>

        @can('admin')
            {{-- Admin calendar styles moved to resources/css/app.css --}}
            @include('events._admin_create_modal')

            <button type="button" aria-label="{{ __('Create Event') }}" title="{{ __('Create Event') }}" onclick="window.openEventCreateModal(new Date().toISOString().slice(0,10))" class="fixed bottom-8 right-8 z-50 bg-cyan-600 hover:bg-cyan-500 text-white rounded-full w-auto h-14 flex items-center justify-center shadow-lg transition-colors px-4 space-x-3 animate-pulse">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                <span class="font-medium">{{ __('Create Event') }}</span>
            </button>
        @endcan
    </div>

    <!-- Calendar JS/CSS handled by Vite (resources/js/calendar.js) -->
</x-app-layout>
