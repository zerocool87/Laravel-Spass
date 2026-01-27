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

            {{-- Embedded full calendar centered on the dashboard --}}
            <div class="mt-6 flex justify-center">
                <div id="dashboard-calendar"
                     data-feed-url="{{ route('events.json') }}"
                     data-mode="full"
                     data-can-edit="{{ auth()->check() && auth()->user()->can('admin') ? '1' : '0' }}"
                     data-create-url="{{ auth()->check() && auth()->user()->can('admin') ? route('admin.events.create') : '' }}"
                     data-edit-base="{{ auth()->check() && auth()->user()->can('admin') ? route('admin.events.index') : '' }}"
                     class="w-full max-w-4xl"></div>
            </div>

            @include('events._admin_create_modal')

            @can('admin')
                <button type="button" aria-label="{{ __('Create Event') }}" title="{{ __('Create Event') }}" onclick="window.openEventCreateModal(new Date().toISOString().slice(0,10))" class="fixed bottom-8 right-8 z-50 bg-cyan-600 hover:bg-cyan-500 text-white rounded-full w-auto h-14 flex items-center justify-center shadow-lg transition-colors px-4 space-x-3 animate-pulse">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    <span class="font-medium">{{ __('Create Event') }}</span>
                </button>
            @endcan

        </div>
    </div>
</x-app-layout>
