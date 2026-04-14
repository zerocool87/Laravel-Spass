<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Actualités') }}"
            subtitle="{{ __('Dernières nouvelles du SEHV') }}"
            icon="📰"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="actualites"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Barre de recherche --}}
            <form method="GET" action="{{ route('elus.actualites.index') }}" class="flex gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="search" name="search" value="{{ $search }}"
                        placeholder="{{ __('Rechercher une actualité…') }}"
                        class="w-full pl-10 rounded-xl border-[#faa21b]/30 bg-white shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] text-sm"/>
                </div>
                <button type="submit"
                    class="px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow hover:bg-[#f39b14] transition">
                    {{ __('Rechercher') }}
                </button>
                @if($search)
                    <a href="{{ route('elus.actualites.index') }}"
                       class="px-4 py-2.5 bg-gray-100 text-gray-600 rounded-xl text-sm font-medium hover:bg-gray-200 transition flex items-center">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </form>

            @if($search)
                <p class="text-sm text-gray-500">
                    <span class="font-semibold text-[#b36b00]">{{ $actualites->total() }}</span>
                    {{ trans_choice('résultat|résultats', $actualites->total()) }}
                    {{ __('pour') }} <span class="font-semibold text-gray-700">« {{ $search }} »</span>
                </p>
            @endif

            {{-- Liste des actualités --}}
            @forelse($actualites as $index => $actualite)
                @php
                    $accentColors = [
                        ['border' => 'border-l-[#faa21b]', 'dot' => 'bg-[#faa21b]', 'badge' => 'bg-[#faa21b]/10 text-[#b36b00]'],
                        ['border' => 'border-l-amber-400',  'dot' => 'bg-amber-400',  'badge' => 'bg-amber-50 text-amber-700'],
                        ['border' => 'border-l-orange-400', 'dot' => 'bg-orange-400', 'badge' => 'bg-orange-50 text-orange-700'],
                        ['border' => 'border-l-yellow-500', 'dot' => 'bg-yellow-500', 'badge' => 'bg-yellow-50 text-yellow-700'],
                    ];
                    $accent = $accentColors[$index % count($accentColors)];
                @endphp
                <a href="{{ route('elus.actualites.show', $actualite) }}"
                   class="block bg-white rounded-2xl shadow-sm border border-gray-100 border-l-4 {{ $accent['border'] }} p-6 hover:shadow-md hover:border-l-4 transition-all group">

                    {{-- En-tête carte --}}
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            {{-- Badge date --}}
                            <div class="flex items-center gap-2 mb-2">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $accent['badge'] }}">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    {{ $actualite->published_at?->isoFormat('D MMM YYYY') ?? __('Non daté') }}
                                </span>
                                @if($actualite->published_at?->isCurrentMonth())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                                        {{ __('Nouveau') }}
                                    </span>
                                @endif
                            </div>

                            <h2 class="text-base font-bold text-gray-900 group-hover:text-[#b36b00] transition leading-snug">
                                {{ $actualite->title }}
                            </h2>
                            <p class="mt-2 text-sm text-gray-500 line-clamp-2 leading-relaxed">
                                {{ Str::limit(strip_tags($actualite->content), 180) }}
                            </p>
                        </div>

                        {{-- Flèche --}}
                        <div class="flex-shrink-0 mt-1">
                            <div class="w-8 h-8 rounded-full bg-gray-50 group-hover:bg-[#faa21b]/10 flex items-center justify-center transition">
                                <svg class="w-4 h-4 text-gray-300 group-hover:text-[#faa21b] transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- Pied de carte --}}
                    @if($actualite->creator)
                        <div class="mt-4 pt-3 border-t border-gray-50 flex items-center gap-2 text-xs text-gray-400">
                            <div class="w-5 h-5 rounded-full {{ $accent['dot'] }}/20 flex items-center justify-center">
                                <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <span>{{ $actualite->creator->name }}</span>
                            <span class="text-gray-200">·</span>
                            <span>{{ $actualite->published_at?->diffForHumans() }}</span>
                        </div>
                    @else
                        <div class="mt-3 text-xs text-gray-400 text-right">
                            {{ $actualite->published_at?->diffForHumans() }}
                        </div>
                    @endif
                </a>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-dashed border-[#faa21b]/30 p-14 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#faa21b]/10 text-3xl mb-4">
                        📰
                    </div>
                    <p class="text-gray-700 font-semibold text-base">
                        @if($search)
                            {{ __('Aucune actualité ne correspond à votre recherche.') }}
                        @else
                            {{ __('Aucune actualité pour le moment.') }}
                        @endif
                    </p>
                    <p class="text-sm text-gray-400 mt-1">{{ __('Revenez bientôt pour les dernières nouvelles du SEHV.') }}</p>
                </div>
            @endforelse

            @if($actualites->hasPages())
                <div class="mt-4">
                    {{ $actualites->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
