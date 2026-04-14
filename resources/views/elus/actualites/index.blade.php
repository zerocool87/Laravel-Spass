<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Actualités') }}"
            subtitle="{{ __('Revenez bientôt pour les dernières nouvelles du SEHV.') }}"
            icon="📰"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="actualites"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-5">

            {{-- Barre de recherche --}}
            <form method="GET" action="{{ route('elus.actualites.index') }}" class="flex gap-3">
                <div class="flex-1 relative">
                    <div class="absolute inset-y-0 left-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="search" name="search" value="{{ $search }}"
                        placeholder="{{ __('Rechercher une actualité…') }}"
                        class="w-full pl-10 rounded-xl border-[#faa21b]/30 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"/>
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
                    {{ $actualites->total() }} {{ trans_choice('résultat|résultats', $actualites->total()) }}
                    {{ __('pour') }} <span class="font-semibold text-gray-700">« {{ $search }} »</span>
                </p>
            @endif

            @forelse($actualites as $actualite)
                <a href="{{ route('elus.actualites.show', $actualite) }}"
                   class="block bg-white rounded-2xl shadow border border-[#faa21b]/15 p-6 hover:shadow-md hover:border-[#faa21b]/40 transition group">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-base font-semibold text-gray-900 group-hover:text-[#b36b00] transition truncate">
                                {{ $actualite->title }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                                {{ Str::limit(strip_tags($actualite->content), 160) }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-right">
                            <span class="text-xs text-gray-400 whitespace-nowrap">
                                {{ $actualite->published_at?->diffForHumans() }}
                            </span>
                            <div class="mt-1 flex justify-end">
                                <svg class="w-4 h-4 text-[#faa21b] opacity-0 group-hover:opacity-100 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </div>
                        </div>
                    </div>
                    @if($actualite->creator)
                        <div class="mt-3 flex items-center gap-2 text-xs text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            {{ $actualite->creator->name }}
                        </div>
                    @endif
                </a>
            @empty
                <div class="bg-white rounded-2xl shadow border border-[#faa21b]/15 p-12 text-center">
                    <p class="text-4xl mb-3">📰</p>
                    <p class="text-gray-500 font-medium">
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
