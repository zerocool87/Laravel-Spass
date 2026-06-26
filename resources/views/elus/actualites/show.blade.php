<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ $actualite->title }}"
            subtitle="{{ $actualite->published_at?->isoFormat('D MMMM YYYY') }}"
            icon='<svg class="w-6 h-6 inline-block align-text-bottom" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 4v4h4"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 13h8"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 17h4"/></svg>'
            :backRoute="route('elus.actualites.index')"
            :backLabel="__('Retour aux actualités')"
            activeSection="actualites"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Actualités'), 'url' => route('elus.actualites.index')], ['label' => $actualite->title]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">

            <article class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 overflow-hidden">

                {{-- Masthead intérieur --}}
                <div class="text-center py-5 px-8 border-b-2 border-[#faa21b]/20 bg-[#faa21b]/5">
                    <div class="text-xs uppercase tracking-widest font-bold text-[#faa21b]">Le Journal du SEHV</div>
                    <div class="mt-1 text-[11px] text-gray-500 italic">
                        {{ $actualite->published_at?->isoFormat('dddd D MMMM YYYY') }}
                    </div>
                </div>

                {{-- Contenu --}}
                <div class="p-8 sm:p-10">
                    <h1 class="font-sans font-black text-3xl sm:text-4xl lg:text-5xl text-[#faa21b] leading-tight">
                        {{ $actualite->title }}
                    </h1>

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-4 text-sm text-gray-500 border-b-2 border-[#faa21b]/20 pb-4 mb-6">
                        @if($actualite->creator)
                            <span class="font-semibold text-gray-700">{{ $actualite->creator->name }}</span>
                            <span class="text-gray-300">|</span>
                        @endif
                        <span>{{ $actualite->published_at?->isoFormat('dddd D MMMM YYYY [à] H:mm') }}</span>
                        @if($actualite->published_at?->isCurrentMonth())
                            <span class="inline-flex items-center rounded-full bg-[#faa21b]/15 px-2 py-0.5 text-[10px] font-bold uppercase text-[#b36b00]">Nouveau</span>
                        @endif
                    </div>

                    <div class="text-base sm:text-[1.0625rem] text-gray-800 leading-relaxed [&::first-letter]:float-left [&::first-letter]:text-5xl sm:[&::first-letter]:text-6xl [&::first-letter]:font-sans [&::first-letter]:font-black [&::first-letter]:text-[#b36b00] [&::first-letter]:mr-3 [&::first-letter]:mt-1 [&::first-letter]:leading-none [&::first-letter]:select-none">
                        {!! nl2br(e($actualite->content)) !!}
                    </div>
                </div>

                {{-- Footer --}}
                <div class="border-t-2 border-[#faa21b]/20 px-8 sm:px-10 py-4 bg-[#faa21b]/5 flex items-center justify-between text-xs text-gray-400">
                    <a href="{{ route('elus.actualites.index') }}"
                       class="inline-flex items-center gap-1.5 font-bold text-[#faa21b] hover:text-[#e89315] transition uppercase tracking-wider text-[11px]">
                        <span class="text-base leading-none">&larr;</span>
                        {{ __('Retour aux actualités') }}
                    </a>
                    <span class="font-semibold text-[#b36b00]">SEHV · {{ $actualite->published_at?->format('Y') }}</span>
                </div>
            </article>

        </div>
    </div>
</x-app-layout>
