<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('elus.dashboard') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl leading-tight">{{ __('Réunions') }}</h2>
            </div>
            <div class="flex space-x-2">
                <a href="{{ route('elus.reunions.calendar') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                    📅 {{ __('Calendrier') }}
                </a>
                @can('admin')
                <a href="{{ route('elus.reunions.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 transition">
                    + {{ __('Nouvelle réunion') }}
                </a>
                @endcan
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="widget-container mb-6">
                <x-widget-header 
                    title="🔍 {{ __('Filtres') }}" 
                    :link="route('elus.reunions.index')"
                    linkText="{{ __('Réinitialiser') }}"
                    linkIcon="🔄"
                />
                <form method="GET" action="{{ route('elus.reunions.index') }}" class="flex flex-wrap gap-4 mt-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Rechercher...') }}" class="w-full input-orange">
                    </div>
                    <div>
                        <select name="instance_id" class="select-orange">
                            <option value="">{{ __('Toutes les instances') }}</option>
                            @foreach($instances as $instance)
                                <option value="{{ $instance->id }}" {{ request('instance_id') == $instance->id ? 'selected' : '' }}>{{ $instance->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="status" class="select-orange">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <input type="date" name="from_date" value="{{ request('from_date') }}" class="input-orange" placeholder="{{ __('Du') }}">
                    </div>
                    <div>
                        <input type="date" name="to_date" value="{{ request('to_date') }}" class="input-orange" placeholder="{{ __('Au') }}">
                    </div>
                    <div>
                        <button type="submit" class="btn-primary-orange">
                            <svg class="btn-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            {{ __('Filtrer') }}
                        </button>
                    </div>
                </form>
            </div>

            {{-- Reunions List --}}
            <div class="widget-container">
                <x-widget-header 
                    title="📅 {{ __('Liste des réunions') }}" 
                    :link="route('elus.reunions.calendar')"
                    linkText="{{ __('Voir calendrier') }}"
                    linkIcon="📅"
                />
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
