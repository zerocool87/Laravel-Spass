<x-app-layout>
    <x-slot name="header">
        <div class="bg-[#FFA500] -mx-8 -my-6 px-8 py-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('elus.dashboard') }}" class="text-white/80 hover:text-white transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h2 class="font-bold text-2xl text-white mb-1">📅 {{ __('Calendrier') }}</h2>
                        <p class="text-white/90 text-sm">{{ __('Vue calendrier des réunions') }}</p>
                    </div>
                </div>
                <nav class="flex space-x-2 items-center">
                    <a href="{{ route('elus.instances.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Instances') }}</a>
                    <a href="{{ route('elus.projects.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Projets') }}</a>
                    <a href="{{ route('elus.reunions.index') }}" class="px-4 py-2 text-sm bg-white text-[#FFA500] hover:bg-white/90 rounded-lg transition font-bold">{{ __('Réunions') }}</a>
                    <a href="{{ route('elus.documents.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Documents') }}</a>
                    @can('admin')
                    <a href="{{ route('elus.reunions.create') }}" class="inline-flex items-center px-4 py-2 border border-white/30 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-white/10 transition">
                        + {{ __('Nouvelle réunion') }}
                    </a>
                    @endcan
                </nav>
            </div>
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
