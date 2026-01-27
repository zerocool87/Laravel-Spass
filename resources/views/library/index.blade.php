<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900">{{ __('Bibliothèque') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-4">
                {{-- Category Filter --}}
                <div class="mb-6 flex flex-wrap gap-2">
                </div>
                <script>
                var resetUrl = '{{ route('library.index') }}';
                var currentCategory = '{{ request('category') }}';
                function toggleDocsTable(cat) {
                    var el = document.getElementById('docs-table-' + cat);
                    var icon = document.getElementById('toggle-icon-' + cat);
                    if (!el) return;
                    if (el.style.display === '' || el.style.display === 'block') {
                        el.style.display = 'none';
                        if (icon) icon.textContent = '🙈';
                    } else {
                        el.style.display = '';
                        if (icon) icon.textContent = '👁️';
                    }
                }
                function toggleAllCategories() {
                    // Reset filters by redirecting to base URL
                    window.location.href = resetUrl;
                }
                </script>
                    <button type="button" id="toggle-all-btn" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-cyan-200 bg-white text-cyan-700 font-semibold shadow-sm transition hover:bg-cyan-50" onclick="toggleAllCategories()">
                        <span id="toggle-all-icon">👁️</span>
                    </button>
                    @foreach($categoryCounts ?? [] as $cat => $count)
                        <a href="{{ route('library.index', ['category' => $cat]) }}" onclick="if (currentCategory === '{{ $cat }}') { window.location.href = resetUrl; return false; }" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg border border-cyan-200 bg-white text-cyan-700 font-semibold shadow-sm transition {{ request('category') === $cat ? 'ring-2 ring-cyan-400' : 'hover:bg-cyan-50' }}" aria-label="{{ __($cat) }} ({{ $count }})" title="{{ __($cat) }} ({{ $count }})">
                            <x-category-badge :category="$cat === 'Uncategorized' ? null : $cat" />
                            <span class="sr-only">{{ __($cat) }}</span>
                            <span class="text-xs opacity-75">({{ $count }})</span>
                        </a>
                    @endforeach
                </div>

                {{-- Documents List groupés par type --}}
                @php
                    $showAll = !request('category') || request('category') === 'all';
                @endphp
                @if($showAll)
                    @if($documentsByCategory->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <p>{{ __('Aucun document disponible.') }}</p>
                        </div>
                    @else
                        <div class="mb-2">
                            <table class="cyber-table w-full">
                                <colgroup>
                                    <col style="width:28%">
                                    <col style="width:42%">
                                    <col style="width:15%">
                                    <col style="width:15%">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="text-left align-middle">{{ __('Titre') }}</th>
                                        <th class="text-left align-middle">{{ __('Description') }}</th>
                                        <th class="text-right align-middle" colspan="2">
                                            <span class="block w-full text-right pr-2">{{ __('Actions') }}</span>
                                        </th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                        <div class="space-y-8" id="categories-container">
                            @foreach($documentsByCategory as $cat => $docs)
                                <div>
                                    <div class="flex items-center gap-3 mb-3 mt-4">
                                        <x-category-badge :category="$cat === 'Uncategorized' ? null : $cat" />
                                        <button type="button" class="ml-2 px-2 py-1 text-xs rounded bg-gray-100 border border-gray-300 hover:bg-gray-200 transition" onclick="toggleDocsTable('{{ Str::slug($cat) }}')">
                                            <span id="toggle-icon-{{ Str::slug($cat) }}">🙈</span> {{ __('Masquer/Voir') }}
                                        </button>
                                    </div>
                                    <div class="overflow-x-auto">
                                        <div id="docs-table-{{ Str::slug($cat) }}" style="display: none;">
                                            <table class="cyber-table w-full">
                                                <colgroup>
                                                    <col style="width:28%">
                                                    <col style="width:42%">
                                                    <col style="width:15%">
                                                    <col style="width:15%">
                                                </colgroup>
                                                <tbody>
                                                    @foreach($docs as $doc)
                                                        <tr>
                                                            <td class="font-semibold text-gray-900 align-middle">{{ $doc->title }}</td>
                                                            <td class="text-sm text-gray-500 align-middle">{{ $doc->description }}</td>
                                                            <td class="text-right align-middle" colspan="2">
                                                                <div class="flex items-center gap-3 justify-end">
                                                                    <span class="inline-flex items-center min-w-[70px] justify-start gap-3" style="margin-left:0;">
                                                                        @if($doc->visible_to_all)
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200" style="margin-left:0;">
                                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                                                </svg>
                                                                                {{ __('Public') }}
                                                                            </span>
                                                                        @else
                                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200" style="margin-left:0;">
                                                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                                                </svg>
                                                                                {{ __('Privé') }}
                                                                            </span>
                                                                        @endif
                                                                    </span>
                                                                    <x-secondary-button 
                                                                        type="button" 
                                                                        onclick="window.openDocument({embed: {{ json_encode(route('documents.embed', $doc)) }}, info: {{ json_encode(route('documents.info', $doc)) }}, download: {{ json_encode(route('documents.download', $doc)) }}, title: {{ json_encode($doc->title) }}})">
                                                                        {{ __('Voir') }}
                                                                    </x-secondary-button>
                                                                    <x-primary-button href="{{ route('documents.download', $doc) }}" target="_blank" rel="noopener">
                                                                        {{ __('Télécharger') }}
                                                                    </x-primary-button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    {{-- Filtres actifs : on garde la liste paginée classique --}}
                    @if($documents->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <p>{{ __('Aucun document disponible.') }}</p>
                        </div>
                    @else
                        <div class="space-y-2">
                            @foreach($documents as $doc)
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
                                        @if($doc->category && (request('category') && request('category') !== 'all'))
                                            <div class="mt-2">
                                                <x-category-badge :category="$doc->category === 'Uncategorized' ? null : $doc->category" />
                                            </div>
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
                        <div class="mt-6">
                            {{ $documents->links() }}
                        </div>
                    @endif
                @endif
                @include('documents._preview_modal')
            </div>
        </div>
    </div>
</x-app-layout>