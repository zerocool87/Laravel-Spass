<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ $actualite->title }}"
            subtitle="{{ $actualite->published_at?->format('d/m/Y') }}"
            icon="📰"
            :backRoute="route('elus.actualites.index')"
            :backLabel="__('Retour aux actualités')"
            activeSection="actualites"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow border border-[#faa21b]/15 overflow-hidden">

                {{-- Bandeau supérieur --}}
                <div class="border-b border-[#faa21b]/10 px-8 py-5 bg-[#faa21b]/5 flex flex-wrap items-center gap-4 text-sm text-gray-600">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>{{ $actualite->published_at?->isoFormat('D MMMM YYYY [à] H:mm') }}</span>
                    </div>
                    @if($actualite->creator)
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            <span>{{ $actualite->creator->name }}</span>
                        </div>
                    @endif
                </div>

                {{-- Contenu --}}
                <div class="px-8 py-8">
                    <div class="text-gray-800 leading-relaxed whitespace-pre-line text-base">
                        {{ $actualite->content }}
                    </div>
                </div>

                {{-- Pied de page --}}
                <div class="px-8 py-5 border-t border-gray-100 flex items-center justify-between">
                    <a href="{{ route('elus.actualites.index') }}"
                       class="inline-flex items-center gap-2 text-sm font-medium text-[#b36b00] hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        {{ __('Retour aux actualités') }}
                    </a>
                    <span class="text-xs text-gray-400">
                        {{ __('Publié') }} {{ $actualite->published_at?->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
