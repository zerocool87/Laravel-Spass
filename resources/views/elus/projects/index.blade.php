<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Projets') }}"
            subtitle="{{ __('Gestion des projets territoriaux') }}"
            icon="📋"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="projects"
        >
            <x-slot name="actions">
                @can('admin')
                <a href="{{ route('elus.projects.create') }}" class="inline-flex items-center px-4 py-2 border border-white/30 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-white/10 transition">
                    + {{ __('Nouveau projet') }}
                </a>
                @endcan
            </x-slot>
        </x-elus-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Statistics --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-[#faa21b] rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80 mb-1">{{ __('Total projets') }}</p>
                            <p class="text-3xl font-bold">{{ $stats['total'] }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-white/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-[#faa21b] rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80 mb-1">{{ __('Projets actifs') }}</p>
                            <p class="text-3xl font-bold">{{ $stats['active'] }}</p>
                        </div>
                        <div class="p-3 rounded-full bg-white/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
                <div class="bg-[#faa21b] rounded-xl shadow-lg p-6 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-white/80 mb-1">{{ __('Budget total') }}</p>
                            <p class="text-3xl font-bold">{{ number_format($stats['total_budget'], 0, ',', ' ') }} €</p>
                        </div>
                        <div class="p-3 rounded-full bg-white/20">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="widget-container mb-6">
                <x-widget-header
                    title="🔍 {{ __('Filtres') }}"
                    :link="route('elus.projects.index')"
                    linkText="{{ __('Réinitialiser') }}"
                    linkIcon="🔄"
                />
                <form method="GET" action="{{ route('elus.projects.index') }}" class="flex flex-wrap gap-4 mt-4">
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
                        <select name="status" class="select-orange">
                            <option value="">{{ __('Tous les statuts') }}</option>
                            @foreach($statuses as $key => $label)
                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $label }}</option>
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

            {{-- Projects Table --}}
            <div class="widget-container">
                <x-widget-header
                    title="📋 {{ __('Liste des projets') }}"
                    :link="route('elus.projects.create')"
                    linkText="{{ __('Nouveau projet') }}"
                    linkIcon="➕"
                />
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#faa21b]/20">
                        <thead class="bg-[#faa21b]/5">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Projet') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Type') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Statut') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Budget') }}</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Période') }}</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#faa21b]/20">
                            @forelse($projects as $project)
                                <tr class="hover:bg-[#faa21b]/5">
                                    <td class="px-6 py-4">
                                        <div>
                                            <a href="{{ route('elus.projects.show', $project) }}" class="text-sm font-medium text-gray-900 hover:text-[#faa21b]">{{ $project->title }}</a>
                                            @if($project->territories && count($project->territories) > 0)
                                                <p class="text-xs text-gray-500">{{ implode(', ', $project->territories) }}</p>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $project->type_label }}</td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $project->status_color }}">
                                            {{ $project->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">{{ $project->formatted_budget }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        @if($project->start_date && $project->end_date)
                                            {{ $project->start_date->format('m/Y') }} - {{ $project->end_date->format('m/Y') }}
                                        @elseif($project->start_date)
                                            {{ __('Début') }}: {{ $project->start_date->format('d/m/Y') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right text-sm font-medium">
                                        <a href="{{ route('elus.projects.show', $project) }}" class="text-[#faa21b] hover:text-[#e89315] mr-3">{{ __('Voir') }}</a>
                                        @can('admin')
                                        <a href="{{ route('elus.projects.edit', $project) }}" class="text-gray-600 hover:text-[#faa21b]">{{ __('Modifier') }}</a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                        {{ __('Aucun projet trouvé') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $projects->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
