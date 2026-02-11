<x-app-layout>
    <x-slot name="header">
        <x-public-header
            title="{{ __('Calendar') }}"
            icon="📅"
        />
    </x-slot>

    <div class="container">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('Calendar') }}</h1>
        </div>

        <div id="calendar" class="hidden"
             data-feed-url="{{ route('events.json') }}"
             data-can-edit="0"
        ></div>
    </div>

    <!-- Calendar JS/CSS handled by Vite (resources/js/calendar.js) -->
</x-app-layout>
