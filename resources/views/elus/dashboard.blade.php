<x-app-layout>
    <header>
        @php
            $hour = (int) now()->format('H');
            $timeGreeting = match (true) { $hour < 12 => '☀️', $hour < 18 => '🌤️', default => '🌙' };
            $displayName = $user->prenom ?? $user->name ?? __('cher élu');
        @endphp
        <x-elus-header
            :title="__('Bonjour') . ' ' . $displayName . ' ' . $timeGreeting"
            subtitle="{{ __('Gouvernance et projets territoriaux') }}"
            icon="🏛️"
            activeSection="dashboard"
            :weather="$weather ?? null"
        />
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col py-1 gap-3 mt-3">

            {{-- 2×2 Widget Grid — fills remaining vertical space --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-1 gap-y-3">
                {{-- Prochaines réunions --}}
                <div class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 flex flex-col min-h-0 overflow-hidden">
                    <div class="px-3 py-1 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between gap-2 rounded-t-xl shrink-0">
                        <h3 class="text-sm font-bold text-[#faa21b]">📅 {{ __('Prochaines réunions') }}</h3>
                        <a href="{{ route('elus.reunions.calendar') }}" class="text-xs text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                            {{ __('Calendrier') }} <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-orange-50 overflow-y-auto flex-1 min-h-0">
                        @forelse($upcomingReunions as $reunion)
                            <a href="{{ route('elus.reunions.show', $reunion) }}" class="block px-3 py-1 hover:bg-orange-50/50 transition group">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-orange-600 truncate">{{ $reunion->title }}</p>
                                        <p class="text-xs text-gray-600 truncate">{{ $reunion->instance->name ?? '-' }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <p class="text-xs font-bold text-orange-600">{{ $reunion->start_time?->format('d/m') }}</p>
                                        <p class="text-xs text-gray-500">{{ $reunion->start_time?->format('H:i') }}</p>
                                    </div>
                                </div>
                                @if($reunion->location)
                                    <p class="text-xs text-gray-500 truncate">📍 {{ $reunion->location }}</p>
                                @endif
                            </a>
                        @empty
                            <div class="flex items-center justify-center text-gray-400 text-sm p-4">📅 {{ __('Aucune réunion à venir') }}</div>
                        @endforelse
                    </div>
                </div>

                {{-- Actualités --}}
                <div class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 flex flex-col min-h-0 overflow-hidden">
                    <div class="px-3 py-1 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between gap-2 rounded-t-xl shrink-0">
                        <h3 class="text-sm font-bold text-[#faa21b]">📰 {{ __('Actualités') }}</h3>
                        <a href="{{ route('elus.actualites.index') }}" class="text-xs text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                            {{ __('Tout voir') }} <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-orange-50 overflow-y-auto flex-1 min-h-0">
                        @forelse($latestActualites as $actualite)
                            @php $isNew = $actualite->published_at?->greaterThanOrEqualTo(now()->subWeek()); @endphp
                            <a href="{{ route('elus.actualites.show', $actualite) }}" class="block px-3 py-1 hover:bg-orange-50/50 transition group">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <span class="shrink-0 {{ $isNew ? 'text-orange-500' : 'text-gray-300' }}">{{ $isNew ? '✨' : '📄' }}</span>
                                            <p class="text-xs font-semibold text-gray-900 group-hover:text-[#faa21b] truncate">{{ $actualite->title }}</p>
                                        </div>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        @if($isNew)
                                            <span class="inline-flex items-center rounded-full bg-[#faa21b]/15 px-1.5 py-0.5 text-[10px] font-bold uppercase text-[#b36b00]">{{ __('Nouveau') }}</span>
                                        @endif
                                        <p class="text-xs text-gray-400 whitespace-nowrap {{ $isNew ? 'mt-0.5' : '' }}">{{ $actualite->published_at?->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="flex items-center justify-center text-gray-400 text-sm p-4">📰 {{ __('Aucune actualité') }}</div>
                        @endforelse
                    </div>
                </div>

                {{-- Instances --}}
                <div class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 flex flex-col min-h-0 overflow-hidden">
                    <div class="px-3 py-1 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between gap-2 rounded-t-xl shrink-0">
                        <h3 class="text-sm font-bold text-[#faa21b]">🏛️ {{ __('Instances') }}</h3>
                        <a href="{{ route('elus.instances.index') }}" class="text-xs text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                            {{ __('Tout voir') }} <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-orange-50 overflow-y-auto flex-1 min-h-0">
                        @forelse($instances as $instance)
                            <a href="{{ route('elus.instances.show', $instance) }}" class="block px-3 py-1 hover:bg-orange-50/50 transition group">
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2 min-w-0">
                                        <span class="text-base shrink-0">{{ $instance->icon }}</span>
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-orange-600 truncate">{{ $instance->name }}</p>
                                    </div>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 shrink-0">
                                        {{ $instance->reunions_count }} {{ __('réunions') }}
                                    </span>
                                </div>
                            </a>
                        @empty
                            <div class="flex items-center justify-center text-gray-400 text-sm p-4">🏛️ {{ __('Aucune instance') }}</div>
                        @endforelse
                    </div>
                </div>

                {{-- Documents récents --}}
                <div class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 flex flex-col min-h-0 overflow-hidden">
                    <div class="px-3 py-1 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between gap-2 rounded-t-xl shrink-0">
                        <h3 class="text-sm font-bold text-[#faa21b]">📄 {{ __('Documents récents') }}</h3>
                        <a href="{{ route('elus.documents.index') }}" class="text-xs text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                            {{ __('Bibliothèque') }} <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                    <div class="divide-y divide-orange-50 overflow-y-auto flex-1 min-h-0">
                        @forelse($latestDocuments as $document)
                            <a href="{{ route('documents.download', $document) }}" class="flex items-center px-3 py-1 hover:bg-orange-50/50 transition group gap-2">
                                <x-category-icon :document="$document" size="w-5 h-5" />
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-[#faa21b]">{{ $document->title }}</p>
                                        @if($document->created_at?->greaterThanOrEqualTo(now()->subWeek()))
                                            <span class="inline-flex items-center rounded-full bg-[#faa21b]/15 px-1.5 py-0.5 text-[10px] font-bold uppercase text-[#b36b00] shrink-0">new</span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-gray-500">{{ $document->created_at->format('d/m/Y') }}</p>
                                </div>
                            </a>
                        @empty
                            <div class="flex items-center justify-center text-gray-400 text-sm p-4">📄 {{ __('Aucun document récent') }}</div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- Projets en cours --}}
            <div class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 overflow-hidden shrink-0">
                <div class="px-3 py-1 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-[#faa21b]">📋 {{ __('Projets en cours') }}</h3>
                    <a href="{{ route('elus.projects.index') }}" class="text-xs text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
                        {{ __('Tous les projets') }} <svg class="w-3 h-3 ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                    </a>
                </div>
                <div class="overflow-x-auto overflow-y-auto max-h-48">
                    <table class="min-w-full divide-y divide-[#faa21b]/20">
                        <thead class="bg-[#faa21b]/5">
                            <tr>
                                <th class="px-3 py-1 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Projet') }}</th>
                                <th class="px-3 py-1 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Type') }}</th>
                                <th class="px-3 py-1 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Statut') }}</th>
                                <th class="px-3 py-1 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Budget') }}</th>
                                <th class="px-3 py-1 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Échéance') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-orange-50">
                            @forelse($activeProjects as $project)
                                <tr class="hover:bg-orange-50/50 cursor-pointer transition" onclick="window.location='{{ route('elus.projects.show', $project) }}'">
                                    <td class="px-3 py-1">
                                        <div class="text-sm font-semibold text-gray-900">{{ Str::limit($project->title, 40) }}</div>
                                        @if($project->territories)
                                            <div class="text-xs text-gray-600 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a8 8 0 1 0-1.414 1.414l4.243 4.243a2 2 0 0 0 2.828 0l.586-.586a2 2 0 0 0 0-2.828z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"></path></svg>
                                                {{ implode(', ', $project->territories) }}
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-1 text-sm text-gray-600">{{ $project->type_label }}</td>
                                    <td class="px-3 py-1">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $project->status_color }}">{{ $project->status_label }}</span>
                                    </td>
                                    <td class="px-3 py-1 text-sm text-gray-900">{{ $project->formatted_budget }}</td>
                                    <td class="px-3 py-1 text-sm text-gray-500">{{ $project->end_date?->format('d/m/Y') ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-4 py-4 text-center text-gray-500 text-sm">{{ __('Aucun projet en cours') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
        </div>
    </div>

    {{-- Onboarding tour --}}
    <x-onboarding-tour :show="$showOnboarding" />

    @if($weather)
        <script>
            (function () {
                if (!navigator.geolocation) return;

                navigator.geolocation.getCurrentPosition(
                    async function (pos) {
                        const lat = pos.coords.latitude;
                        const lng = pos.coords.longitude;
                        try {
                            const resp = await fetch(`{{ route('elus.weather.by-coords') }}?lat=${lat}&lng=${lng}`);
                            if (!resp.ok) return;
                            const data = await resp.json();

                            const icon = document.getElementById('weather-icon');
                            const tempEl = document.getElementById('weather-temp');
                            const city = document.getElementById('weather-city');

                            if (icon && data.icon) icon.textContent = data.icon;
                            if (tempEl && data.temp) tempEl.textContent = data.temp;
                            if (city && data.city) city.textContent = data.city;
                        } catch (_) { /* fallback to server-side weather */ }
                    },
                    function () { /* permission denied, fallback to server-side weather */ },
                    { timeout: 5000, maximumAge: 600000 }
                );
            })();
        </script>
    @endif
</x-app-layout>
