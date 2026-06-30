@php
    $categoryColors = config('documents.category_colors', []);
    $categoryIcons = [
        'Convocations' => '📧',
        'Ordres du jour' => '📋',
        'Comptes rendus' => '📝',
        'Rapports' => '📊',
        'Délibérations' => '⚖️',
        'Guides' => '📖',
    ];
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-public-header
            title="{{ __('Bibliothèque') }}"
            icon="📚"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('dashboard')], ['label' => __('Bibliothèque')]]" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 p-4 sm:p-6">
                @if($documentsByCategory->isEmpty())
                    <div class="text-center py-12">
                        <div class="text-gray-300 mb-4">
                            <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="text-gray-600 font-medium">{{ __('Aucun document disponible.') }}</p>
                    </div>
                @else
                    <div x-data="{ activeTab: 'all' }">
                        {{-- Barre d'onglets --}}
                        <div class="mb-6 flex flex-wrap gap-1.5">
                            <button
                                @click="activeTab = 'all'"
                                :class="activeTab === 'all' ? 'bg-[#faa21b] text-white border-[#faa21b] shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-[#faa21b]/30 hover:text-[#faa21b]'"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border-2 font-bold text-sm transition-all"
                            >
                                <span>📁</span>
                                <span>{{ __('Toutes') }}</span>
                            </button>

                            @foreach($allCategories as $cat)
                                @php $icon = $categoryIcons[$cat] ?? '📄'; @endphp
                                <button
                                    @click="activeTab = '{{ $cat }}'"
                                    :class="activeTab === '{{ $cat }}' ? 'bg-[#faa21b] text-white border-[#faa21b] shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-[#faa21b]/30 hover:text-[#faa21b]'"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border-2 font-bold text-sm transition-all"
                                >
                                    <span>{{ $icon }}</span>
                                    <span>{{ $cat }}</span>
                                    <span class="text-xs opacity-60">({{ $documentsByCategory[$cat]->count() }})</span>
                                </button>
                            @endforeach
                        </div>

                        {{-- Panneaux --}}
                        <div class="space-y-6">
                            @foreach($documentsByCategory as $category => $docs)
                                @php $icon = $categoryIcons[$category] ?? '📄'; @endphp

                                <div
                                    id="category-{{ Str::slug($category) }}"
                                    x-show="activeTab === 'all' || activeTab === '{{ $category }}'"
                                    x-transition:enter="ease-out duration-200"
                                    x-transition:enter-start="opacity-0"
                                    x-transition:enter-end="opacity-100"
                                    x-cloak
                                >
                                    @if($documentsByCategory->count() > 1)
                                        <div class="flex items-center gap-2 mb-3 pb-2 border-b border-gray-100">
                                            <span class="text-base">{{ $icon }}</span>
                                            <h4 class="font-semibold text-gray-800 text-sm">{{ $category }}</h4>
                                            <span class="text-xs font-medium text-gray-400">({{ $docs->count() }})</span>
                                        </div>
                                    @endif

                                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                                        @foreach($docs as $doc)
                                            <x-document-pocket :document="$doc" />
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @include('documents._preview_modal')
            </div>
        </div>
    </div>
</x-app-layout>
