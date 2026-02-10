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
                    /* Dashboard calendar – defer to app.css, only layout tweaks here */
                    #dashboard-calendar[data-mode="mini"] { max-height: 320px; overflow-y: auto; }
                    #dashboard-calendar[data-mode="month"],
                    #dashboard-calendar[data-mode="week"] { overflow: visible; max-height: none; }
                    #dashboard-calendar[data-mode="month"] .fc,
                    #dashboard-calendar[data-mode="week"] .fc { min-height: auto; height: auto !important; }
                    #dashboard-calendar .fc .fc-scroller { overflow: visible !important; height: auto !important; }
                    </style>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
