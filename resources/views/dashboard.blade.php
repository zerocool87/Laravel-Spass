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

                <style>
                /* Compact, dark-themed overrides for the mini dashboard calendar */
                #dashboard-calendar .fc {
                    color: #e6eef8;
                    font-size: 0.95rem;
                }
                #dashboard-calendar .fc .fc-list-day-cushion {
                    background: transparent;
                    padding: 0.25rem 0.5rem;
                    border-bottom: 1px solid rgba(255,255,255,0.04);
                }
                #dashboard-calendar .fc .fc-list-item {
                    padding: 0.15rem 0.5rem;
                    background: transparent;
                }
                #dashboard-calendar .fc .fc-list-event {
                    padding: 0.35rem 0.6rem;
                    margin: 0.15rem 0;
                    border-radius: 0.375rem;
                }
                #dashboard-calendar .fc .fc-list-item-title,
                #dashboard-calendar .fc .fc-list-item-time {
                    color: #e6eef8;
                }
                #dashboard-calendar .fc .fc-list-item-time { min-width: 72px; }
                #dashboard-calendar .fc .fc-list-day-text {
                    opacity: 0.75;
                    font-size: 0.85rem;
                }
                /* make event pill colors a bit inset to show card background */
                #dashboard-calendar .fc .fc-list-event .fc-event-main {
                    background-clip: padding-box;
                }
                </style>
            </div>

        </div>
    </div>
</x-app-layout>
