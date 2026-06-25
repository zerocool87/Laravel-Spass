<x-app-layout>
    <x-slot name="header">
        <x-public-header
            title="{{ __('Calendar') }}"
            icon="📅"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('dashboard')], ['label' => __('Événements'), 'url' => route('events.index')], ['label' => __('Calendrier')]]" />
    </x-slot>

    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between mb-4">
            <h1 class="text-xl font-semibold text-gray-900">{{ __('Calendar') }}</h1>
        </div>

        <div id="calendar"
              data-feed-url="{{ route('events.json') }}"
              data-mode="full"
              data-can-edit="0"
              class="hidden h-[calc(100vh-150px)] w-full"
         ></div>
    </div>

    <!-- Calendar JS/CSS handled by Vite (resources/js/calendar.js) -->
</x-app-layout>
