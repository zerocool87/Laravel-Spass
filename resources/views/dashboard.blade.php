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
                <div id="dashboard-calendar" data-feed-url="/events/json" data-mode="month" data-can-edit="false" class="w-full bg-transparent" aria-label="Calendrier des événements"></div>

                @can('admin')
                    <div class="mt-3 flex justify-end">
                        <x-primary-button href="{{ route('admin.events.create') }}">{{ __('Create Event') }}</x-primary-button>
                    </div>
                @endcan

                <style>
                /* Mini-mode dark-themed overrides for the dashboard calendar */
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

                /* List view specific: make time box and title align correctly */
                #dashboard-calendar .fc .fc-list-event-main {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 0.35rem 0.6rem;
                    border-radius: 0.375rem;
                }
                #dashboard-calendar .fc .fc-list-item-time {
                    background: rgba(255,255,255,0.06);
                    padding: 0.2rem 0.5rem;
                    border-radius: 0.25rem;
                    font-weight: 700;
                    color: #e6eef8;
                    min-width: 84px;
                    text-align: left;
                }
                #dashboard-calendar .fc .fc-list-item-title { flex: 1; }

                /* Header styling */
                #dashboard-calendar .fc .fc-toolbar h2,
                #dashboard-calendar .fc .fc-toolbar .fc-toolbar-title {
                    color: #dff3ff;
                    font-size: 1.35rem;
                    font-weight: 500;
                }

                /* Limit height only for compact (mini) mode to avoid layout break */
                #dashboard-calendar[data-mode="mini"] { max-height: 320px; overflow-y: auto; }

                /* For month/week modes let calendar expand to avoid internal scrollbars */
                #dashboard-calendar[data-mode="month"],
                #dashboard-calendar[data-mode="week"] {
                    overflow: visible;
                    max-height: none;
                }

                #dashboard-calendar[data-mode="month"] .fc,
                #dashboard-calendar[data-mode="week"] .fc {
                    min-height: auto;
                    height: auto !important;
                }

                /* Ensure the calendar's internal scroller does not constrain height */
                #dashboard-calendar .fc .fc-scroller { overflow: visible !important; height: auto !important; }
                </style>
            </div>

        </div>
    </div>
</x-app-layout>
