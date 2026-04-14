<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ $actualite->title }}"
            subtitle="{{ $actualite->published_at?->isoFormat('D MMMM YYYY') }}"
            icon="📰"
            :backRoute="route('elus.actualites.index')"
            :backLabel="__('Retour aux actualités')"
            activeSection="actualites"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Carte principale --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

                {{-- Bandeau hero coloré --}}
                <div class="bg-gradient-to-r from-[#faa21b] to-amber-400 px-8 py-6">
                    <div class="flex flex-wrap items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-white/25 flex items-center justify-center text-xl flex-shrink-0">
                            📰
                        </div>
                        <div class="flex-1 min-w-0">
                            <h1 class="text-lg font-bold text-white leading-snug">
                                {{ $actualite->title }}
                            </h1>
                            <p class="text-sm text-white/80 mt-0.5">
                                {{ $actualite->published_at?->isoFormat('dddd D MMMM YYYY [à] H:mm') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Méta-infos --}}
                <div class="border-b border-gray-100 px-8 py-3 bg-gray-50/60 flex flex-wrap items-center gap-4 text-xs text-gray-500">
                    @if($actualite->creator)
                        <div class="flex items-center gap-1.5">
                            <div class="w-5 h-5 rounded-full bg-[#faa21b]/20 flex items-center justify-center">
                                <svg class="w-3 h-3 text-[#b36b00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                            </div>
                            <span class="font-medium text-gray-700">{{ $actualite->creator->name }}</span>
                        </div>
                        <span class="text-gray-200">|</span>
                    @endif
                    <div class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>{{ __('Publié') }} {{ $actualite->published_at?->diffForHumans() }}</span>
                    </div>
                    @if($actualite->published_at?->isCurrentMonth())
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">
                            ✓ {{ __('Récent') }}
                        </span>
                    @endif
                </div>

                {{-- Contenu de l'article --}}
                <div class="px-8 py-8">
                    <div class="prose prose-gray max-w-none text-gray-800 leading-relaxed text-[0.9375rem] whitespace-pre-line">
                        {{ $actualite->content }}
                    </div>
                </div>

                {{-- Séparateur décoratif --}}
                <div class="mx-8 border-t border-dashed border-[#faa21b]/20"></div>

                {{-- Pied de page --}}
                <div class="px-8 py-4 flex items-center justify-between bg-[#faa21b]/3">
                    <a href="{{ route('elus.actualites.index') }}"
                       class="inline-flex items-center gap-2 text-sm font-semibold text-[#b36b00] hover:text-[#faa21b] transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        {{ __('Retour aux actualités') }}
                    </a>
                    <span class="text-xs text-gray-400">
                        SEHV · {{ $actualite->published_at?->format('Y') }}
                    </span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
