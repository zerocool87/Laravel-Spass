<x-app-layout>
    <x-slot name="header">
        <div class="bg-[#FFA500] -mx-8 -my-6 px-8 py-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('elus.dashboard') }}" class="text-white/80 hover:text-white transition" aria-label="{{ __('Retour au tableau de bord') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h2 class="font-bold text-2xl text-white mb-1">🏛️ {{ __('Instances') }}</h2>
                        <p class="text-white/90 text-sm">{{ __('Comités et commissions') }}</p>
                    </div>
                </div>
                <nav class="flex space-x-2 items-center">
                    <a href="{{ route('elus.dashboard') }}" class="px-4 py-2 text-sm bg-white text-[#faa21b] hover:bg-white/90 rounded-lg transition font-bold">{{ __('Tableau de bord') }}</a>
                    <a href="{{ route('elus.instances.index') }}" class="px-4 py-2 text-sm bg-white text-[#FFA500] hover:bg-white/90 rounded-lg transition font-bold">{{ __('Instances') }}</a>
                    <a href="{{ route('elus.projects.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Projets') }}</a>
                    <a href="{{ route('elus.reunions.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Réunions') }}</a>
                    <a href="{{ route('elus.documents.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Documents') }}</a>
                    @can('admin')
                    <a href="{{ route('elus.instances.create') }}" class="inline-flex items-center px-4 py-2 border border-white/30 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-white/10 transition">
                        + {{ __('Nouvelle instance') }}
                    </a>
                    @endcan
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Filters --}}
            <div class="widget-container mb-6">
                <x-widget-header
                    title="🔍 {{ __('Filtres') }}"
                    :link="route('elus.instances.index')"
                    linkText="{{ __('Réinitialiser') }}"
                    linkIcon="🔄"
                />
                <form method="GET" action="{{ route('elus.instances.index') }}" class="flex flex-wrap gap-4 mt-4">
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Rechercher...') }}" class="w-full input-orange">
                    </div>
                    <div>
                        <select name="type" class="select-orange">
                            <option value="">{{ __('Tous les types') }}</option>
                            @foreach($types as $key => $label)
                                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
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

            {{-- Instances Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($instances as $instance)
                    <div class="widget-container">
                        <div class="widget-content">
                            <div class="flex items-start justify-between">
                                <div>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium type-badge-orange">
                                        {{ $instance->type_label }}
                                    </span>
                                    <h3 class="mt-2 text-lg font-medium text-gray-900">{{ $instance->name }}</h3>
                                </div>
                                @can('admin')
                                <div class="flex space-x-2">
                                    <a href="{{ route('elus.instances.edit', $instance) }}" class="text-gray-400 hover:text-[#faa21b]">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                </div>
                                @endcan
                            </div>
                            @if($instance->description)
                                <p class="mt-2 text-sm text-gray-500 line-clamp-2">{{ $instance->description }}</p>
                            @endif
                            @if($instance->territory)
                                <p class="mt-2 text-xs text-gray-500">📍 {{ $instance->territory }}</p>
                            @endif
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm text-gray-500">{{ $instance->reunions_count }} {{ __('réunion(s)') }}</span>
                                <a href="{{ route('elus.instances.show', $instance) }}" class="text-sm text-[#faa21b] hover:text-[#e89315] font-semibold">{{ __('Voir détails') }} →</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 widget-container">
                        <div class="widget-empty">
                            <svg class="widget-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <h3 class="widget-empty-title">{{ __('Aucune instance') }}</h3>
                            <p class="widget-empty-description">{{ __('Commencez par créer une nouvelle instance.') }}</p>
                            @can('admin')
                            <div class="mt-6">
                                <a href="{{ route('elus.instances.create') }}" class="btn-primary-orange">
                                    + {{ __('Nouvelle instance') }}
                                </a>
                            </div>
                            @endcan
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $instances->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
