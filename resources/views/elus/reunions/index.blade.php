<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Réunions') }}"
            subtitle="{{ __('Calendrier des réunions') }}"
            icon="📅"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="reunions"
        >
            <x-slot name="actions">
            </x-slot>
        </x-elus-header>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-3 lg:px-4">
            {{-- Create Reunion Button moved into widget header --}}

            {{-- Calendar Section (header always visible; body toggled by session) --}}
            <div class="widget-container mb-3">
                <x-widget-header
                    title="📅 {{ __('Calendrier') }}"
                >
                    <x-slot name="actions">
                        <form method="POST" action="{{ route('elus.reunions.toggle-calendar') }}" class="flex items-center">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-[#faa21b] hover:bg-[#e89315] transition shadow-sm">
                                @if(session('show_calendar', true))
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    {{ __('Masquer le calendrier') }}
                                @else
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    {{ __('Afficher le calendrier') }}
                                @endif
                            </button>
                        </form>
                    </x-slot>
                </x-widget-header>
                @if(session('show_calendar', true))
                    <div class="bg-white rounded-lg shadow-lg border-2 border-[#faa21b]/20 p-3">
                        <div id="reunions-calendar" data-feed-url="{{ route('elus.reunions.json') }}" data-mode="compact"></div>
                    </div>
                @else
                    <div class="bg-white rounded-lg shadow-lg border-2 border-[#faa21b]/20 p-3">
                        <div class="text-sm text-gray-500 p-4">{{ __('Le calendrier est masqué. Cliquez sur "Afficher le calendrier" pour le réafficher.') }}</div>
                    </div>
                @endif
            </div>

            {{-- Reunions List --}}
            <div class="widget-container mt-3">
                <x-widget-header
                    title="📅 {{ __('Les 2 prochaines réunions à venir') }}"
                >
                    <x-slot name="actions">
                        @can('admin')
                        <a href="{{ route('elus.reunions.create') }}" class="inline-flex items-center px-3 py-1 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest bg-[#faa21b] hover:bg-[#e89315] transition shadow-sm">
                            + {{ __('Nouvelle réunion') }}
                        </a>
                        @endcan
                    </x-slot>
                </x-widget-header>
                <div class="divide-y divide-[#faa21b]/20">
                    @forelse($reunions as $reunion)
                        <a href="{{ route('elus.reunions.show', $reunion) }}" class="block px-6 py-4 hover:bg-[#faa21b]/5 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $reunion->status_color }}">
                                            {{ $reunion->status_label }}
                                        </span>
                                        <h3 class="text-sm font-medium text-gray-900">{{ $reunion->title }}</h3>
                                    </div>
                                    <div class="mt-1 flex items-center space-x-4 text-sm text-gray-500">
                                        <span>{{ $reunion->instance->name ?? '-' }}</span>
                                        @if($reunion->location)
                                            <span>📍 {{ $reunion->location }}</span>
                                        @endif
                                        @if($reunion->compte_rendu)
                                            <span class="text-green-600">✓ {{ __('CR disponible') }}</span>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-gray-900">{{ $reunion->date->format('d/m/Y') }}</p>
                                    <p class="text-sm text-gray-500">{{ $reunion->date->format('H:i') }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="widget-empty">
                            <svg class="widget-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <h3 class="widget-empty-title">{{ __('Aucune réunion') }}</h3>
                            <p class="widget-empty-description">{{ __('Commencez par planifier une nouvelle réunion.') }}</p>
                            @can('admin')
                            <div class="mt-6">
                                <a href="{{ route('elus.reunions.create') }}" class="btn-primary-orange">
                                    + {{ __('Nouvelle réunion') }}
                                </a>
                            </div>
                            @endcan
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $reunions->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
