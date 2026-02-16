@php
    $categoryIcons = [
        'Convocations' => '📧',
        'Ordres du jour' => '📋',
        'Comptes rendus' => '📝',
        'Rapports' => '📊',
        'Délibérations' => '⚖️',
        'Guides' => '📖',
    ];
    $filterActiveClass = 'border-[#faa21b] bg-[#faa21b] text-white';
    $filterInactiveClass = 'border-[#faa21b]/30 bg-white text-[#faa21b]';
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Bibliothèque') }}"
            subtitle="{{ __('Accédez à tous les documents officiels') }}"
            icon="📚"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="documents"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Quick Category Filters --}}
            <div class="mb-6 flex flex-wrap gap-2">
                <a href="{{ route('elus.documents.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 {{ !request('category') ? $filterActiveClass : $filterInactiveClass }} font-semibold shadow-sm transition hover:bg-[#faa21b] hover:text-white hover:border-[#faa21b]">
                    <span>🔁</span>
                    <span class="text-sm">{{ __('Tous') }}</span>
                </a>
                @foreach($categories as $cat)
                    @php
                        $icon = $categoryIcons[$cat] ?? '📄';
                    @endphp
                    <a href="{{ route('elus.documents.index', ['category' => $cat]) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border-2 {{ request('category') === $cat ? $filterActiveClass : $filterInactiveClass }} font-semibold shadow-sm transition hover:bg-[#faa21b] hover:text-white hover:border-[#faa21b]">
                        <span>{{ $icon }}</span>
                        <span class="text-sm">{{ $cat }}</span>
                    </a>
                @endforeach
            </div>

            {{-- Search & Filters --}}
            <div class="bg-white rounded-xl shadow-lg mb-6 p-6 border-2 border-[#faa21b]/20">
                <form method="GET" action="{{ route('elus.documents.index') }}" class="flex flex-wrap gap-4 items-end">
                    <div class="flex-1 min-w-[250px]">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Rechercher') }}</label>
                        <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="{{ __('Titre, description...') }}" class="w-full rounded-lg border-[#faa21b]/30 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                    </div>
                    <div class="min-w-[200px]">
                        <label for="category" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Catégorie') }}</label>
                        <select id="category" name="category" class="w-full rounded-lg border-[#faa21b]/30 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                            <option value="">{{ __('Toutes les catégories') }}</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                            <option value="uncategorized" {{ request('category') === 'uncategorized' ? 'selected' : '' }}>{{ __('Non catégorisé') }}</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="px-6 py-2.5 bg-[#faa21b] text-white rounded-lg font-medium hover:bg-[#e89315] transition-colors shadow-md flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            {{ __('Rechercher') }}
                        </button>
                        @if(request()->hasAny(['search', 'category']))
                            <a href="{{ route('elus.documents.index') }}" class="px-6 py-2.5 bg-gray-200 text-gray-700 rounded-lg font-medium hover:bg-gray-300 transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                {{ __('Réinitialiser') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            @if($documentsByCategory->isEmpty())
                <div class="bg-white rounded-xl shadow-lg p-12 text-center border-2 border-[#faa21b]/20">
                    <div class="text-gray-400 mb-4">
                        <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">{{ __('Aucun document disponible') }}</h3>
                    <p class="text-gray-500">{{ __('Aucun document ne correspond à vos critères de recherche.') }}</p>
                </div>
            @else
                <div class="space-y-8">
                    @foreach($documentsByCategory as $category => $docs)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden border-2 border-[#faa21b]/20">
                            {{-- Category Header --}}
                            @php
                                $categoryColors = config('documents.category_colors', []);
                                $colorClass = $categoryColors[$category] ?? 'bg-[#faa21b]';
                                $icon = $categoryIcons[$category] ?? '📄';
                            @endphp
                            <div class="{{ $colorClass }} px-6 py-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-lg font-bold text-white flex items-center">
                                        <span class="mr-2">{{ $icon }}</span>
                                        {{ $category }}
                                    </h3>
                                    <span class="text-white/80 text-sm font-medium">{{ $docs->count() }} {{ $docs->count() > 1 ? __('documents') : __('document') }}</span>
                                </div>
                            </div>

                            {{-- Documents List --}}
                            <div class="divide-y divide-gray-200">
                                @foreach($docs as $doc)
                                    <div class="p-6 hover:bg-[#faa21b]/5 transition-colors">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-3 mb-2">
                                                    <h4 class="text-lg font-semibold text-gray-900">{{ $doc->title }}</h4>
                                                    @if($doc->visible_to_all)
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            {{ __('Public') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            {{ __('Privé') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                @if($doc->description)
                                                    <p class="text-sm text-gray-600 mb-3">{{ $doc->description }}</p>
                                                @endif

                                                <div class="flex items-center gap-4 text-xs text-gray-500">
                                                    @if($doc->category)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium" style="background-color: rgba(var(--tw-bg-opacity), 0.1); color: var(--tw-text-opacity)">
                                                            <x-category-icon :document="$doc" size="w-4 h-4" />
                                                            <span class="ml-1">{{ $doc->category }}</span>
                                                        </span>
                                                    @endif
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span class="ml-1">{{ $doc->created_at->format('d/m/Y') }}</span>
                                                    </span>
                                                    @if($doc->creator)
                                                        <span class="flex items-center">
                                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                            </svg>
                                                            {{ $doc->creator->name }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 flex-shrink-0">
                                                <button
                                                    type="button"
                                                    onclick="window.openDocument({embed: '{{ route('documents.embed', $doc) }}', info: '{{ route('documents.info', $doc) }}', download: '{{ route('documents.download', $doc) }}', title: '{{ addslashes($doc->title) }}'})"
                                                    class="inline-flex items-center px-4 py-2 border border-[#faa21b] rounded-lg text-sm font-medium text-[#faa21b] hover:bg-[#faa21b]/10 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                    </svg>
                                                    {{ __('Voir') }}
                                                </button>
                                                <a
                                                    href="{{ route('documents.download', $doc) }}"
                                                    target="_blank"
                                                    rel="noopener"
                                                    class="inline-flex items-center px-4 py-2 bg-[#faa21b] border border-transparent rounded-lg text-sm font-medium text-white hover:bg-[#e89315] transition-colors shadow-sm">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                                    </svg>
                                                    {{ __('Télécharger') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            @include('documents._preview_modal')
        </div>
    </div>
</x-app-layout>
