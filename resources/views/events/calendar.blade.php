<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Calendar</h2>
    </x-slot>

    <div class="container">
        <div id="calendar" data-feed-url="{{ route('events.json') }}"></div>
    </div>

    <!-- Calendar JS/CSS handled by Vite (resources/js/calendar.js) -->
</x-app-layout>
