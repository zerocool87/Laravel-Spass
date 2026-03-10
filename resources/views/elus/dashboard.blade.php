<x-app-layout>
    <header>
        <x-elus-header
            :title="__('Espace Élus')"
            :subtitle="__('Gouvernance et projets territoriaux')"
            icon="🏛️"
            activeSection="dashboard"
        />
    </header>
    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Statistics Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <div class="bg-[#faa21b] rounded-xl shadow-lg p-4 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-white/80 mb-0.5">{{ __('Instances') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['total_instances'] }}</p>
                        </div>
                        <div class="p-2 rounded-full bg-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-[#faa21b] rounded-xl shadow-lg p-4 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-white/80 mb-0.5">{{ __('Projets actifs') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['active_projects'] }}</p>
                        </div>
                        <div class="p-2 rounded-full bg-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-[#faa21b] rounded-xl shadow-lg p-4 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-white/80 mb-0.5">{{ __('Réunions à venir') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['upcoming_reunions'] }}</p>
                        </div>
                        <div class="p-2 rounded-full bg-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-[#faa21b] rounded-xl shadow-lg p-4 text-white transform hover:scale-105 transition">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-xs font-medium text-white/80 mb-0.5">{{ __('Documents') }}</p>
                            <p class="text-2xl font-bold">{{ $stats['total_documents'] }}</p>
                        </div>
                        <div class="p-2 rounded-full bg-white/20">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main Content Grid: 2×2 direct grid children so CSS Grid controls row height --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                {{-- Upcoming Reunions --}}
                <div class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 flex flex-col overflow-hidden upcoming-reunions-widget">
                    <div class="px-5 py-2.5 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between rounded-t-xl">
                        <h3 class="text-base font-bold text-[#faa21b]">📅 {{ __('Prochaines réunions') }}</h3>
                        <a href="{{ route('elus.reunions.calendar') }}" class="text-sm text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                            {{ __('Voir le calendrier') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="divide-y divide-orange-50 overflow-y-auto">
                        @forelse($upcomingReunions->take(3) as $reunion)
                            <a href="{{ route('elus.reunions.show', $reunion) }}" class="block px-5 py-3 hover:bg-orange-50/50 transition group">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-orange-600 transition">{{ Str::limit($reunion->title, 20) }}</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ Str::limit($reunion->instance->name ?? '-', 20) }}</p>
                                    </div>
                                    <div class="text-right ml-4">
                                        <p class="text-sm font-bold text-orange-600">{{ $reunion->start_time ? $reunion->start_time->format('d/m/Y') : '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $reunion->start_time ? $reunion->start_time->format('H:i') : '-' }}</p>
                                    </div>
                                </div>
                                @if($reunion->location)
                                    <p class="mt-1 text-xs text-gray-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1 text-orange-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a8 8 0 1 0-1.414 1.414l4.243 4.243a2 2 0 0 0 2.828 0l.586-.586a2 2 0 0 0 0-2.828z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                                        </svg>
                                        {{ $reunion->location }}
                                    </p>
                                @endif
                            </a>
                        @empty
                            <div class="flex items-center justify-center text-center text-gray-400 text-sm p-6">
                                📅 {{ __('Aucune réunion à venir') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Latest Actualités --}}
                <div class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 flex flex-col overflow-hidden">
                    <div class="px-5 py-2.5 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between rounded-t-xl">
                        <h3 class="text-base font-bold text-[#faa21b]">📰 {{ __('Actualités') }}</h3>
                        <a href="{{ route('elus.actualites.index') }}" class="text-sm text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                            {{ __('Toutes les actualités') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="divide-y divide-orange-50 overflow-y-auto">
                        @forelse($latestActualites->take(3) as $actualite)
                            <a href="{{ route('elus.actualites.show', $actualite) }}" class="block px-5 py-3 hover:bg-orange-50/50 transition group">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-[#faa21b] transition truncate">{{ $actualite->title }}</p>
                                        <p class="mt-0.5 text-xs text-gray-500 line-clamp-2">{{ Str::limit(strip_tags($actualite->content), 100) }}</p>
                                    </div>
                                    <div class="flex-shrink-0 text-right">
                                        <p class="text-xs text-gray-400 whitespace-nowrap">{{ $actualite->published_at?->diffForHumans() }}</p>
                                        @if($actualite->creator)
                                            <p class="text-xs text-gray-400 mt-0.5">{{ $actualite->creator->name }}</p>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="flex items-center justify-center text-center text-gray-400 text-sm p-6">
                                📰 {{ __('Aucune actualité pour le moment.') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Instances --}}
                <div class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 flex flex-col overflow-hidden instances-widget">
                    <div class="px-5 py-2.5 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between rounded-t-xl">
                        <h3 class="text-base font-bold text-[#faa21b]">🏛️ {{ __('Instances') }}</h3>
                        <a href="{{ route('elus.instances.index') }}" class="text-sm text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                            {{ __('Tout voir') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="divide-y divide-orange-50 overflow-y-auto">
                        @forelse($instances as $instance)
                            <a href="{{ route('elus.instances.show', $instance) }}" class="block px-5 py-3 hover:bg-orange-50/50 transition group">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-orange-600 transition">{{ Str::limit($instance->name, 20) }}</p>
                                        <p class="text-xs text-gray-600 mt-0.5">{{ $instance->type_label }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700 ml-2 flex-shrink-0">
                                        {{ $instance->reunions_count }} {{ __('réunions') }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="flex items-center justify-center text-center text-gray-400 text-sm p-6">
                                🏛️ {{ __('Aucune instance') }}
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Recent Documents Widget --}}
                <div class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 flex flex-col overflow-hidden recent-documents-widget">
                    <div class="px-5 py-2.5 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between rounded-t-xl">
                        <h3 class="text-base font-bold text-[#faa21b]">📄 {{ __('Documents récents') }}</h3>
                        <a href="{{ route('elus.documents.index') }}" class="text-sm text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                            {{ __('Bibliothèque') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="divide-y divide-orange-50 overflow-y-auto">
                        @forelse($latestDocuments as $document)
                            <a href="{{ route('documents.download', $document) }}" class="flex items-center px-5 py-3 hover:bg-orange-50/50 transition group">
                                <div class="flex-shrink-0 p-2 rounded-lg transition">
                                    <x-category-icon :document="$document" size="w-5 h-5" />
                                </div>
                                <div class="ml-3 overflow-hidden flex-1">
                                    <div class="flex items-center gap-2">
                                        <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-[#faa21b] transition">{{ Str::limit($document->title, 20) }}</p>
                                        @if($document->created_at && $document->created_at->greaterThanOrEqualTo(now()->subDays(7)))
                                            <span class="inline-flex items-center rounded-full bg-[#faa21b]/15 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-[#b36b00]">
                                                new
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $document->created_at->format('d/m/Y') }}</p>
                                </div>
                            </a>
                            @empty
                                <div class="flex items-center justify-center text-center text-gray-400 text-sm p-6">
                                    📄 {{ __('Aucun document récent') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
            </div>

            {{-- Active Projects --}}
            <div class="mt-4">
                <div class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 overflow-hidden active-projects-widget">
                        <div class="px-5 py-2.5 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between">
                            <h3 class="text-base font-bold text-[#faa21b]">📋 {{ __('Projets en cours') }}</h3>
                        <a href="{{ route('elus.projects.index') }}" class="text-sm text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                            {{ __('Tous les projets') }}
                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#faa21b]/20">
                            <thead class="bg-[#faa21b]/5">
                                <tr>
                                    <th class="px-5 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Projet') }}</th>
                                    <th class="px-5 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Type') }}</th>
                                    <th class="px-5 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Statut') }}</th>
                                    <th class="px-5 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Budget') }}</th>
                                    <th class="px-5 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Échéance') }}</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-orange-50">
                                @forelse($activeProjects as $project)
                                    <tr class="hover:bg-orange-50/50 cursor-pointer transition" onclick="window.location='{{ route('elus.projects.show', $project) }}'">
                                        <td class="px-5 py-2">
                                            <div class="text-sm font-semibold text-gray-900">{{ Str::limit($project->title, 20) }}</div>
                                            @if($project->territories)
                                                <div class="text-xs text-gray-600 mt-1 flex items-center">
                                                    <svg class="w-3 h-3 mr-1 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a8 8 0 1 0-1.414 1.414l4.243 4.243a2 2 0 0 0 2.828 0l.586-.586a2 2 0 0 0 0-2.828z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path>
                                                    </svg>
                                                    {{ implode(', ', $project->territories) }}
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-5 py-2 text-sm text-gray-600">{{ $project->type_label }}</td>
                                        <td class="px-5 py-2">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $project->status_color }}">
                                                {{ $project->status_label }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-2 text-sm text-gray-900">{{ $project->formatted_budget }}</td>
                                        <td class="px-5 py-2 text-sm text-gray-500">{{ $project->end_date?->format('d/m/Y') ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                            {{ __('Aucun projet en cours') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
