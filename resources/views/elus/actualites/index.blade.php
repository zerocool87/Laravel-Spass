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

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Actualités')]]" />
    </x-slot>

    @php
        $allArticles = $actualites->getCollection();
        $hasLead = !$search && $allArticles->isNotEmpty();
    @endphp

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8"
             x-data="{
                 items: {{ Js::from($allArticles->values()->map(fn ($a) => [
                     'id' => $a->id,
                     'title' => $a->title,
                      'content' => nl2br(e($a->content)),
                     'creator' => $a->creator?->name,
                     'published_at' => $a->published_at?->isoFormat('dddd D MMMM YYYY [à] H:mm'),
                     'published_at_raw' => $a->published_at?->diffForHumans(),
                     'is_current_month' => $a->published_at?->isCurrentMonth(),
                     'year' => $a->published_at?->format('Y'),
                 ])->values()) }},
                 isOpen: false,
                 selected: {},
                 openModal(index) {
                     this.selected = this.items[index];
                     this.isOpen = true;
                     document.body.classList.add('overflow-hidden');
                 },
                 closeModal() {
                     this.isOpen = false;
                     document.body.classList.remove('overflow-hidden');
                 },
             }">

            {{-- Masthead --}}
            <div class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 p-6 sm:p-8 mb-8 text-center select-none">
                <h1 class="font-sans font-black text-3xl sm:text-4xl lg:text-5xl tracking-tight text-[#faa21b] leading-none">
                    Le Journal du SEHV
                </h1>
                <p class="text-sm text-gray-500 mt-2 italic">
                    Le Quotidien des Élus du Territoire
                </p>
                <div class="flex items-center gap-4 justify-center mt-4 text-xs uppercase tracking-widest text-[#b36b00] font-bold">
                    <span class="h-px flex-1 max-w-24 bg-[#faa21b]/30"></span>
                    <span>{{ now()->isoFormat('dddd D MMMM YYYY') }}</span>
                    <span class="h-px flex-1 max-w-24 bg-[#faa21b]/30"></span>
                </div>
                <div class="mt-3 border-t-2 border-[#faa21b]/20 pt-2 flex items-center justify-between text-[11px] uppercase tracking-wider text-gray-500 font-semibold">
                    @if($actualites->hasPages())
                        <nav class="inline-flex items-center gap-3 select-none">
                            @if($actualites->onFirstPage())
                                <span class="text-gray-300 cursor-not-allowed">{{ __('Articles précédents') }}</span>
                            @else
                                <a href="{{ $actualites->previousPageUrl() }}" rel="prev" class="text-[#b36b00] hover:text-[#faa21b] transition">{{ __('Articles précédents') }}</a>
                            @endif
                            <span class="text-gray-300 mx-1">|</span>
                            @if($actualites->hasMorePages())
                                <a href="{{ $actualites->nextPageUrl() }}" rel="next" class="text-[#b36b00] hover:text-[#faa21b] transition">{{ __('Articles suivants') }}</a>
                            @else
                                <span class="text-gray-300 cursor-not-allowed">{{ __('Articles suivants') }}</span>
                            @endif
                        </nav>
                    @endif
                    <span>{{ $actualites->total() }} {{ trans_choice('article|articles', $actualites->total()) }}</span>
                </div>
            </div>

            {{-- Articles --}}
            @forelse($actualites as $index => $actualite)
                @php
                    $isLead = $index === 0 && $hasLead;
                @endphp

                @if($isLead)
                {{-- À la une --}}
                <article @click="openModal({{ $index }})"
                     @keydown.enter="openModal({{ $index }})"
                     @keydown.space.prevent="openModal({{ $index }})"
                     role="button"
                     tabindex="0"
                     class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 p-6 sm:p-8 mb-8 group cursor-pointer transition hover:shadow-xl">

                    <div class="text-xs uppercase tracking-widest font-bold text-[#faa21b] mb-3 flex items-center gap-2">
                        <span class="w-5 h-px bg-[#faa21b]/40"></span>
                        À la une
                    </div>

                    <h2 class="font-sans font-black text-3xl sm:text-4xl text-[#faa21b] group-hover:text-[#b36b00] transition leading-tight">
                        {{ $actualite->title }}
                    </h2>

                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-2 text-sm text-gray-500">
                        @if($actualite->creator)
                            <span class="font-semibold text-gray-700">{{ $actualite->creator->name }}</span>
                            <span class="text-gray-300">·</span>
                        @endif
                        <span>{{ $actualite->published_at?->isoFormat('D MMMM YYYY') }}</span>
                        @if($actualite->published_at?->isCurrentMonth())
                            <span class="inline-flex items-center rounded-full bg-[#faa21b]/15 px-2 py-0.5 text-[10px] font-bold uppercase text-[#b36b00]">Nouveau</span>
                        @endif
                    </div>

                    <p class="mt-3 text-base text-gray-600 leading-relaxed line-clamp-4">
                        {{ Str::limit(strip_tags($actualite->content), 350) }}
                    </p>

                    <div class="mt-4 flex items-center gap-1.5 text-sm font-semibold text-[#faa21b] group-hover:text-[#e89315] transition">
                        {{ __('Lire l\'article') }}
                        <span class="text-lg leading-none">&rarr;</span>
                    </div>
                </article>

                @elseif($isLead && $loop->remaining === 0)
                    {{-- Only lead article exists, nothing more --}}
                @elseif($hasLead && $index === 1)
                {{-- Ouvrir la grille --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <article @click="openModal({{ $index }})"
                         @keydown.enter="openModal({{ $index }})"
                         @keydown.space.prevent="openModal({{ $index }})"
                         role="button"
                         tabindex="0"
                         class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 p-5 sm:p-6 group cursor-pointer transition hover:shadow-xl">
                        <h3 class="font-sans font-bold text-xl sm:text-2xl text-[#faa21b] group-hover:text-[#b36b00] transition leading-tight">
                            {{ $actualite->title }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1.5 text-xs text-gray-500">
                            @if($actualite->creator)
                                <span class="font-semibold text-gray-700">{{ $actualite->creator->name }}</span>
                                <span class="text-gray-300">·</span>
                            @endif
                            <span>{{ $actualite->published_at?->isoFormat('D MMM YYYY') }}</span>
                            @if($actualite->published_at?->isCurrentMonth())
                                <span class="inline-flex items-center rounded-full bg-[#faa21b]/15 px-1.5 py-0.5 text-[10px] font-bold uppercase text-[#b36b00]">Nouveau</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-600 leading-relaxed line-clamp-3">
                            {{ Str::limit(strip_tags($actualite->content), 200) }}
                        </p>
                    </article>

                @elseif($hasLead && $index > 1)
                    <article @click="openModal({{ $index }})"
                         @keydown.enter="openModal({{ $index }})"
                         @keydown.space.prevent="openModal({{ $index }})"
                         role="button"
                         tabindex="0"
                         class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 p-5 sm:p-6 group cursor-pointer transition hover:shadow-xl">
                        <h3 class="font-sans font-bold text-xl sm:text-2xl text-[#faa21b] group-hover:text-[#b36b00] transition leading-tight">
                            {{ $actualite->title }}
                        </h3>
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1.5 text-xs text-gray-500">
                            @if($actualite->creator)
                                <span class="font-semibold text-gray-700">{{ $actualite->creator->name }}</span>
                                <span class="text-gray-300">·</span>
                            @endif
                            <span>{{ $actualite->published_at?->isoFormat('D MMM YYYY') }}</span>
                            @if($actualite->published_at?->isCurrentMonth())
                                <span class="inline-flex items-center rounded-full bg-[#faa21b]/15 px-1.5 py-0.5 text-[10px] font-bold uppercase text-[#b36b00]">Nouveau</span>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-600 leading-relaxed line-clamp-3">
                            {{ Str::limit(strip_tags($actualite->content), 200) }}
                        </p>
                    </article>

                @elseif(!$hasLead)
                {{-- Mode recherche --}}
                <article @click="openModal({{ $index }})"
                     @keydown.enter="openModal({{ $index }})"
                     @keydown.space.prevent="openModal({{ $index }})"
                     role="button"
                     tabindex="0"
                     class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/20 p-5 sm:p-6 group cursor-pointer transition hover:shadow-xl {{ $loop->first ? '' : '' }}">
                    <h3 class="font-sans font-bold text-xl sm:text-2xl text-[#faa21b] group-hover:text-[#b36b00] transition leading-tight">
                        {{ $actualite->title }}
                    </h3>
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1 mt-1.5 text-xs text-gray-500">
                        @if($actualite->creator)
                            <span class="font-semibold text-gray-700">{{ $actualite->creator->name }}</span>
                            <span class="text-gray-300">·</span>
                        @endif
                        <span>{{ $actualite->published_at?->isoFormat('D MMM YYYY') }}</span>
                        @if($actualite->published_at?->isCurrentMonth())
                            <span class="inline-flex items-center rounded-full bg-[#faa21b]/15 px-1.5 py-0.5 text-[10px] font-bold uppercase text-[#b36b00]">Nouveau</span>
                        @endif
                    </div>
                    <p class="mt-2 text-sm text-gray-600 leading-relaxed line-clamp-3">
                        {{ Str::limit(strip_tags($actualite->content), 200) }}
                    </p>
                </article>
                @endif

                @if($hasLead && $loop->last && $index >= 1)
                </div>
                @endif

            @empty
                <div class="bg-white rounded-xl shadow-lg border-2 border-dashed border-[#faa21b]/30 p-14 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-[#faa21b]/10 text-3xl mb-4">📰</div>
                    <p class="font-sans font-bold text-gray-900 text-lg">
                        @if($search)
                            {{ __('Aucune actualité ne correspond à votre recherche.') }}
                        @else
                            {{ __('Aucune actualité pour le moment.') }}
                        @endif
                    </p>
                    <p class="text-sm text-gray-400 mt-1">{{ __('Revenez bientôt pour les dernières nouvelles du SEHV.') }}</p>
                </div>
            @endforelse

            {{-- Modal article complet --}}
            <div x-show="isOpen"
                 x-cloak
                 x-transition.opacity.duration.200
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
                 @click.self="closeModal"
                 @keydown.escape.window="closeModal">

                <div class="fixed inset-0 bg-black/60"></div>

                <div x-show="isOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="relative bg-white w-full max-w-4xl mx-auto z-10 shadow-2xl border-2 border-[#faa21b]/20 rounded-xl">

                    <div class="absolute top-4 right-4 z-20">
                        <button @click="closeModal" type="button"
                                class="w-8 h-8 bg-white hover:bg-[#faa21b]/10 border border-[#faa21b]/30 rounded-lg flex items-center justify-center transition"
                                aria-label="{{ __('Fermer') }}">
                            <svg class="w-4 h-4 text-[#b36b00]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="p-8 sm:p-10">
                        <h2 class="font-sans font-black text-3xl sm:text-4xl text-[#faa21b] leading-tight"
                            x-text="selected.title"></h2>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-3 text-sm text-gray-500 border-b-2 border-[#faa21b]/20 pb-4 mb-6">
                            <template x-if="selected.creator">
                                <span class="font-semibold text-gray-700" x-text="selected.creator"></span>
                            </template>
                            <span class="text-gray-300">|</span>
                            <span x-text="selected.published_at"></span>
                            <span x-show="selected.is_current_month"
                                  class="inline-flex items-center rounded-full bg-[#faa21b]/15 px-2 py-0.5 text-[10px] font-bold uppercase text-[#b36b00]">Nouveau</span>
                        </div>

                        <div class="text-base text-gray-800 leading-relaxed"
                             x-html="selected.content"></div>

                        <div class="mt-8 pt-4 border-t-2 border-[#faa21b]/20 flex items-center justify-between text-xs text-gray-400">
                            <span class="font-semibold text-[#b36b00]">SEHV — Le Journal des Élus</span>
                            <span x-text="selected.year"></span>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
