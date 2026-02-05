<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('elus.reunions.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl leading-tight">{{ __('Calendrier des réunions') }}</h2>
            </div>
            @can('admin')
            <a href="{{ route('elus.reunions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                + {{ __('Nouvelle réunion') }}
            </a>
            @endcan
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div id="reunions-calendar" data-feed-url="{{ route('elus.reunions.json') }}" data-mode="full"></div>
            </div>

            {{-- Legend --}}
            <div class="mt-6 bg-white rounded-lg shadow p-4">
                <h4 class="text-sm font-medium text-gray-700 mb-3">{{ __('Légende') }}</h4>
                <div class="flex flex-wrap gap-4">
                    <div class="flex items-center">
                        <span class="w-4 h-4 rounded bg-blue-500 mr-2"></span>
                        <span class="text-sm text-gray-600">{{ __('Planifiée') }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-4 h-4 rounded bg-green-500 mr-2"></span>
                        <span class="text-sm text-gray-600">{{ __('Confirmée') }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-4 h-4 rounded bg-gray-500 mr-2"></span>
                        <span class="text-sm text-gray-600">{{ __('Terminée') }}</span>
                    </div>
                    <div class="flex items-center">
                        <span class="w-4 h-4 rounded bg-red-500 mr-2"></span>
                        <span class="text-sm text-gray-600">{{ __('Annulée') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
