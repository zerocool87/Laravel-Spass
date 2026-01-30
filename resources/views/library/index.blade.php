<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900">{{ __('Bibliothèque') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-4">
                @php
                    $documents = \App\Models\Document::latest()->get();
                    $documentsByCategory = $documents->groupBy(function ($d) { return $d->category ?: 'Uncategorized'; });
                    $allCategories = array_keys($documentsByCategory->toArray());
                @endphp

                <div class="mb-6 flex flex-wrap gap-2">
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            document.querySelectorAll('.category-filter-btn').forEach(function(btn) {
                                btn.addEventListener('click', function() {
                                    var slug = this.getAttribute('data-slug');
                                    document.querySelectorAll('[id^="category-"]').forEach(function(el) {
                                        el.style.display = (el.id === 'category-' + slug) ? '' : 'none';
                                    });
                                    document.querySelectorAll('.category-filter-btn').forEach(function(b) {
                                        b.classList.add('opacity-50');
                                    });
                                    this.classList.remove('opacity-50');
                                });
                            });
                            var allBtn = document.getElementById('show-all-categories');
                            if (allBtn) {
                                allBtn.addEventListener('click', function() {
                                    document.querySelectorAll('[id^="category-"]').forEach(function(el) {
                                        el.style.display = '';
                                    });
                                    document.querySelectorAll('.category-filter-btn').forEach(function(b) {
                                        b.classList.remove('opacity-50');
                                    });
                                });
                            }
                        });
                    </script>

                    <button id="show-all-categories" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-cyan-200 bg-white text-cyan-700 font-semibold shadow-sm transition hover:bg-cyan-50">
                        <span>🔁</span>
                        <span class="text-sm">{{ __('Tous') }}</span>
                    </button>

                        @foreach($allCategories as $cat)
                        <button type="button" data-slug="{{ Str::slug($cat) }}" class="category-filter-btn inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-cyan-200 bg-white text-cyan-700 font-semibold shadow-sm transition hover:bg-cyan-50">
                            <x-category-badge :category="$cat === 'Uncategorized' ? null : $cat" />
                            <span class="sr-only">{{ $cat }}</span>
                            <span class="text-xs opacity-75">({{ $documentsByCategory[$cat]->count() }})</span>
                        </button>
                    @endforeach
                </div>

                @if($documentsByCategory->isEmpty())
                    <div class="text-center py-8 text-gray-400">
                        <p>{{ __('Aucun document disponible.') }}</p>
                    </div>
                @else
                    <div class="space-y-8">
                        @foreach($documentsByCategory as $cat => $docs)
                            <div id="category-{{ Str::slug($cat) }}">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="w-full flex flex-col">
                                        @php
                                            $categoryColors = [
                                                'Convocations' => '#d97706',
                                                'Ordres du jour' => '#f59e42',
                                                'Comptes rendus' => '#059669',
                                                'Rapports' => '#0891b2',
                                                'Délibérations' => '#e11d48',
                                                'Guides' => '#0284c7',
                                            ];
                                            $barColor = $categoryColors[$cat] ?? '#9CA3AF';
                                        @endphp
                                        <div class="w-full h-8 mb-1 flex items-center justify-center" style="background: {{ $barColor }};">
                                            <div class="text-sm font-semibold text-white uppercase">{{ $cat }} <span class="text-xs text-white/80">({{ $docs->count() }})</span></div>
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-2">
                                    @foreach($docs as $doc)
                                        <div class="flex items-center justify-between p-3 rounded-lg bg-white border border-gray-200 shadow-sm hover:bg-gray-50 transition">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-2">
                                                    <div class="font-semibold text-gray-900">{{ $doc->title }}</div>
                                                    @if($doc->visible_to_all)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            {{ __('Public') }}
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">
                                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                            </svg>
                                                            {{ __('Privé') }}
                                                        </span>
                                                    @endif
                                                </div>

                                                @if($doc->description)
                                                    <div class="text-sm text-gray-500 mt-1">{{ $doc->description }}</div>
                                                @endif

                                            </div>

                                            <div class="flex items-center gap-2 ml-4">
                                                <x-secondary-button
                                                    type="button"
                                                    onclick="window.openDocument({embed: {{ json_encode(route('documents.embed', $doc)) }}, info: {{ json_encode(route('documents.info', $doc)) }}, download: {{ json_encode(route('documents.download', $doc)) }}, title: {{ json_encode($doc->title) }}})">
                                                    {{ __('Voir') }}
                                                </x-secondary-button>
                                                <x-primary-button href="{{ route('documents.download', $doc) }}" target="_blank" rel="noopener">
                                                    {{ __('Télécharger') }}
                                                </x-primary-button>
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
    </div>
</x-app-layout>
