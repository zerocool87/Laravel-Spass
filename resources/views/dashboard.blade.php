<x-app-layout>
    <x-slot name="header">
        <x-public-header
            title="{{ __('Dashboard') }}"
            icon="🏠"
        />
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-6">
                <div class="text-gray-900">
                    {{-- Welcome message can be placed here --}}
                </div>

                {{-- Read-only calendar for all users on the dashboard --}}
                <div class="mt-6">
                    {{-- <h3 class="text-lg font-semibold mb-3">{{ __('Calendar') }}</h3> --}}
                    <div id="dashboard-calendar" data-feed-url="/events/json" data-mode="month" data-can-edit="false" aria-label="Events Calendar"></div>

                    @can('admin')
                        <div class="mt-3 flex justify-end">
                            <x-primary-button href="{{ route('admin.events.create') }}">{{ __('Create Event') }}</x-primary-button>
                        </div>
                    @endcan

                    <style>
                    /* Light-themed overrides for the dashboard calendar */
                    #dashboard-calendar .fc {
                        color: #374151;
                        font-size: 0.95rem;
                    }
                    #dashboard-calendar .fc .fc-list-day-cushion {
                        background: transparent;
                        padding: 0.25rem 0.5rem;
                        border-bottom: 1px solid #e5e7eb;
                    }
                    #dashboard-calendar .fc .fc-list-item {
                        padding: 0.15rem 0.5rem;
                        background: transparent;
                    }
                    #dashboard-calendar .fc .fc-list-event {
                        padding: 0.35rem 0.6rem;
                        margin: 0.15rem 0;
                        border-radius: 0.375rem;
                        background: #f3f4f6;
                        border: 1px solid #d1d5db;
                    }
                    #dashboard-calendar .fc .fc-list-item-title,
                    #dashboard-calendar .fc .fc-list-item-time {
                        color: #374151;
                    }
                    #dashboard-calendar .fc .fc-list-item-time { min-width: 72px; }
                    #dashboard-calendar .fc .fc-list-day-text {
                        opacity: 0.75;
                        font-size: 0.85rem;
                    }
                    #dashboard-calendar .fc .fc-toolbar h2,
                    #dashboard-calendar .fc .fc-toolbar .fc-toolbar-title {
                        color: #1f2937;
                        font-size: 1.35rem;
                        font-weight: 500;
                    }
                    /* Limit height for compact mode */
                    #dashboard-calendar[data-mode="mini"] { max-height: 320px; overflow-y: auto; }
                    /* Expand for month/week modes */
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
                    #dashboard-calendar .fc .fc-scroller { overflow: visible !important; height: auto !important; }
                    </style>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
