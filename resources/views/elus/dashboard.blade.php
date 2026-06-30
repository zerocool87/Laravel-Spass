<x-app-layout>
    <header>
        <x-elus-header
            :title="$greeting"
            subtitle="{{ __('Gouvernance et projets territoriaux') }}"
            icon="🏛️"
            activeSection="dashboard"
            :weather="$weather ?? null"
        />
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- 2×2 Widget Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Prochaines réunions --}}
            <x-dashboard-widget :title="__('Prochaines réunions')" :link-url="route('elus.reunions.calendar')" :link-text="__('Calendrier')">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </x-slot>

                @forelse($upcomingReunions as $reunion)
                    <a href="{{ route('elus.reunions.show', $reunion) }}" class="block px-4 py-3 hover:bg-orange-50/50 transition group">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-[#faa21b] truncate transition">{{ $reunion->title }}</p>
                                <p class="text-xs text-gray-500 truncate mt-0.5">
                                    @if($reunion->instance)<span class="mr-1">{{ $reunion->instance->icon }}</span>@endif{{ $reunion->instance->name ?? '-' }}
                                </p>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs font-bold text-[#faa21b]">{{ $reunion->start_time?->format('d/m') }}</p>
                                <p class="text-[11px] text-gray-400">{{ $reunion->start_time?->format('H:i') }}</p>
                            </div>
                        </div>
                        @if($reunion->location)
                            <div class="flex items-center gap-1 mt-1">
                                <svg class="w-3 h-3 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a2 2 0 01-2.828 0l-4.243-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-xs text-gray-400 truncate">{{ $reunion->location }}</p>
                            </div>
                        @endif
                    </a>
                @empty
                    <x-empty-state :message="__('Aucune réunion à venir')">
                        <x-slot name="icon">
                            <svg class="w-10 h-10 mb-2 text-[#faa21b]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </x-slot>
                    </x-empty-state>
                @endforelse
            </x-dashboard-widget>

            {{-- Actualités --}}
            <x-dashboard-widget :title="__('Actualités')" :link-url="route('elus.actualites.index')" :link-text="__('Tout voir')">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v4h4"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13h8"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h4"/>
                    </svg>
                </x-slot>

                @forelse($latestActualites as $actualite)
                    @php $isNew = $actualite->published_at?->greaterThanOrEqualTo(now()->subWeek()); @endphp
                    <a href="{{ route('elus.actualites.show', $actualite) }}" class="block px-4 py-3 hover:bg-orange-50/50 transition group">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                    @if($isNew)
                                        <svg class="w-4 h-4 shrink-0 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.362 5.214A8.252 8.252 0 0112 21 8.25 8.25 0 016.038 7.048 8.287 8.287 0 009 9.6a8.983 8.983 0 013.361-6.867 8.21 8.21 0 003 2.48z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18a3.75 3.75 0 00.495-7.467 5.99 5.99 0 00-1.925 3.546 5.176 5.176 0 00-2.48 1.868A3.75 3.75 0 0012 18z"/>
                                        </svg>
                                    @else
                                        <svg class="w-4 h-4 shrink-0 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v4h4"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13h8"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h4"/>
                                        </svg>
                                    @endif
                                    <p class="text-sm font-semibold text-gray-900 group-hover:text-[#faa21b] truncate transition">{{ $actualite->title }}</p>
                                </div>
                                @if($actualite->content)
                                    <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit(strip_tags($actualite->content), 100) }}</p>
                                @endif
                            </div>
                            <div class="shrink-0 text-right flex flex-col items-end gap-1">
                                @if($isNew)
                                    <span class="inline-flex items-center rounded-full bg-orange-100 px-2 py-0.5 text-[10px] font-bold uppercase text-orange-700">{{ __('Nouveau') }}</span>
                                @endif
                                <p class="text-[11px] text-gray-400 whitespace-nowrap">{{ $actualite->published_at?->diffForHumans() }}</p>
                            </div>
                        </div>
                    </a>
                @empty
                    <x-empty-state :message="__('Aucune actualité')">
                        <x-slot name="icon">
                            <svg class="w-10 h-10 mb-2 text-[#faa21b]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v4h4"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13h8"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h4"/>
                            </svg>
                        </x-slot>
                    </x-empty-state>
                @endforelse
            </x-dashboard-widget>

            {{-- Instances --}}
            <x-dashboard-widget :title="__('Instances')" :link-url="route('elus.instances.index')" :link-text="__('Tout voir')">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </x-slot>

                @forelse($instances as $instance)
                    <a href="{{ route('elus.instances.show', $instance) }}" class="block px-4 py-3 hover:bg-orange-50/50 transition group">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <span class="text-xl shrink-0">{{ $instance->icon }}</span>
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-[#faa21b] truncate transition">{{ $instance->name }}</p>
                            </div>
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-orange-100 text-orange-700 shrink-0">
                                {{ $instance->reunions_count }} {{ __('réunions') }}
                            </span>
                        </div>
                    </a>
                @empty
                    <x-empty-state :message="__('Aucune instance')">
                        <x-slot name="icon">
                            <svg class="w-10 h-10 mb-2 text-[#faa21b]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </x-slot>
                    </x-empty-state>
                @endforelse
            </x-dashboard-widget>

            {{-- Documents récents --}}
            <x-dashboard-widget :title="__('Documents récents')" :link-url="route('elus.documents.index')" :link-text="__('Bibliothèque')">
                <x-slot name="icon">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </x-slot>

                @forelse($latestDocuments as $document)
                    <button type="button"
                        @click="window.openDocument({
                            embed: {{ json_encode(route('documents.embed', $document)) }},
                            info: {{ json_encode(route('documents.info', $document)) }},
                            download: {{ json_encode(route('documents.download', $document)) }},
                            title: {{ json_encode($document->title) }},
                            category: {{ json_encode($document->category) }}
                        })"
                        class="w-full text-left flex items-center px-4 py-3 hover:bg-orange-50/50 transition group gap-3"
                    >
                        <x-category-icon :document="$document" size="w-6 h-6" />
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-[#faa21b] truncate transition">{{ $document->title }}</p>
                                @if($document->created_at?->greaterThanOrEqualTo(now()->subWeek()))
                                    <span class="inline-flex items-center rounded-full bg-orange-100 px-1.5 py-0.5 text-[10px] font-bold uppercase text-orange-700 shrink-0">new</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $document->created_at->format('d/m/Y') }}</p>
                        </div>
                    </button>
                @empty
                    <x-empty-state :message="__('Aucun document récent')">
                        <x-slot name="icon">
                            <svg class="w-10 h-10 mb-2 text-[#faa21b]/30" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </x-slot>
                    </x-empty-state>
                @endforelse
            </x-dashboard-widget>
        </div>

        {{-- Projets en cours --}}
        <div class="bg-white rounded-xl shadow-md border-2 border-[#faa21b]/15 overflow-hidden hover:shadow-lg transition-shadow mt-6">
            <div class="px-4 py-3 bg-[#faa21b]/10 border-b-[1.5px] border-[#faa21b]/20 flex items-center justify-between">
                <h3 class="text-sm font-bold text-[#faa21b] flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
                    </svg>
                    {{ __('Projets en cours') }}
                </h3>
                <a href="{{ route('elus.projects.index') }}" class="text-xs font-semibold text-[#faa21b] hover:text-[#e89315] flex items-center gap-1 transition">
                    {{ __('Tous les projets') }}
                    <x-icon.chevron-right class="w-3 h-3" />
                </a>
            </div>
            <div class="overflow-x-auto overflow-y-auto max-h-48">
                <table class="min-w-full divide-y divide-orange-50">
                    <thead class="bg-[#faa21b]/5">
                        <tr>
                            <th class="px-4 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Projet') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Type') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Statut') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Budget') }}</th>
                            <th class="px-4 py-2 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Échéance') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-orange-50">
                        @forelse($activeProjects as $project)
                            <tr class="hover:bg-orange-50/50 cursor-pointer transition" @click="window.location='{{ route('elus.projects.show', $project) }}'">
                                <td class="px-4 py-2">
                                    <div class="text-sm font-semibold text-gray-900">{{ Str::limit($project->title, 40) }}</div>
                                    @if($project->territories)
                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-0.5">
                                            <svg class="w-3 h-3 text-orange-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 12.414a8 8 0 1 0-1.414 1.414l4.243 4.243a2 2 0 0 0 2.828 0l.586-.586a2 2 0 0 0 0-2.828z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                            </svg>
                                            {{ implode(', ', $project->territories) }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-600">{{ $project->type_label }}</td>
                                <td class="px-4 py-2">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $project->status_color }}">{{ $project->status_label }}</span>
                                </td>
                                <td class="px-4 py-2 text-sm text-gray-900 font-medium">{{ $project->formatted_budget }}</td>
                                <td class="px-4 py-2 text-sm text-gray-500">{{ $project->end_date?->format('d/m/Y') ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-gray-500 text-sm">{{ __('Aucun projet en cours') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Document preview modal --}}
    @include('documents._preview_modal')

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
