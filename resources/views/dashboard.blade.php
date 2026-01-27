<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gradient-to-bl from-gray-900 to-slate-900 glass overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-100">
                    {{-- Message supprimé --}}
                </div>
            </div>

            {{-- Library removed from dashboard to reduce clutter. Documents are still accessible via the Library page. --}}

            {{-- Read-only calendar for all users on the dashboard --}}
            <div class="bg-slate-800 mt-6 p-4 rounded-lg shadow">
                <h3 class="text-lg font-semibold text-gray-100 mb-3">Calendrier</h3>
                <div id="dashboard-calendar" data-feed-url="/events/json" data-mode="mini" data-can-edit="false" class="w-full bg-transparent" aria-label="Calendrier des événements"></div>
            </div>

        </div>
    </div>
</x-app-layout>
