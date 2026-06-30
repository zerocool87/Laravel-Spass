@php
    $categoryIcons = [
        'Convocations' => '📧',
        'Ordres du jour' => '📋',
        'Comptes rendus' => '📝',
        'Rapports' => '📊',
        'Délibérations' => '⚖️',
        'Guides' => '📖',
    ];
    $categoryColors = config('documents.category_colors', []);
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

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Documents')]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($documentsByCategory->isEmpty())
                <div class="bg-white rounded-xl shadow border-2 border-[#faa21b]/20 p-8 sm:p-12 text-center">
                    <div class="text-gray-300 mb-4">
                        <svg class="w-20 h-20 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ __('Aucun document disponible') }}</h3>
                    <p class="text-gray-500 text-sm">{{ __('Aucun document ne correspond à vos critères de recherche.') }}</p>
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

                        @foreach($categories as $cat)
                            @php $icon = $categoryIcons[$cat] ?? '📄'; @endphp
                            <button
                                @click="activeTab = '{{ $cat }}'"
                                :class="activeTab === '{{ $cat }}' ? 'bg-[#faa21b] text-white border-[#faa21b] shadow-md' : 'bg-white text-gray-600 border-gray-200 hover:border-[#faa21b]/30 hover:text-[#faa21b]'"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg border-2 font-bold text-sm transition-all"
                            >
                                <span>{{ $icon }}</span>
                                <span>{{ $cat }}</span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Panneaux --}}
                    <div class="bg-white rounded-xl shadow border border-gray-200/80 p-4 sm:p-6">
                        <div class="space-y-6">
                            @foreach($documentsByCategory as $category => $docs)
                                @php $icon = $categoryIcons[$category] ?? '📄'; @endphp

                                <div
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
                </div>

                <div class="mt-8">
                    {{ $documents->links() }}
                </div>
            @endif

            @include('documents._preview_modal')
        </div>
    </div>
</x-app-layout>
