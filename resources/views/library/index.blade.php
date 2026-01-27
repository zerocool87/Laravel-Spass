<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100">{{ __('Library') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-4">
                {{-- Category Filter --}}
                <div class="mb-6 flex flex-wrap gap-2">
                    <a href="{{ route('library.index') }}" 
                       class="inline-flex items-center gap-2 px-4 py-2 rounded transition
                              {{ !request('category') || request('category') === 'all' ? 'bg-cyan-600 text-white' : 'bg-gray-700 text-cyan-200 hover:bg-gray-600' }}"
                       aria-label="{{ __('All') }} ({{ isset($categoryCounts) ? array_sum($categoryCounts) : 0 }})"
                       title="{{ __('All') }} ({{ isset($categoryCounts) ? array_sum($categoryCounts) : 0 }})">
                        {{-- Icon for All (keeps compact filter) --}}
                        <svg class="w-4 h-4 text-cyan-100" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        @if(isset($categoryCounts))
                            <span class="text-xs opacity-75">({{ array_sum($categoryCounts) }})</span>
                        @endif
                    </a>
                    
                    @foreach($categoryCounts ?? [] as $cat => $count)
                        <a href="{{ route('library.index', ['category' => $cat]) }}" 
                           class="inline-flex items-center gap-2 px-4 py-2 rounded transition
                                  {{ request('category') === $cat ? 'bg-cyan-600 text-white' : 'bg-gray-700 text-cyan-200 hover:bg-gray-600' }}"
                           aria-label="{{ __($cat) }} ({{ $count }})"
                           title="{{ __($cat) }} ({{ $count }})">
                            <x-category-badge :category="$cat === 'Uncategorized' ? null : $cat" />
                            <span class="sr-only">{{ __($cat) }}</span>
                            <span class="text-xs opacity-75">({{ $count }})</span>
                        </a>
                    @endforeach
                </div>

                {{-- Documents List --}}
                @if($documents->isEmpty())
                    <div class="text-center py-8 text-cyan-300">
                        <p>{{ __('No documents available.') }}</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @php
                            $currentCategory = null;
                        @endphp
                        
                        @foreach($documents as $doc)
                            @if(!request('category') || request('category') === 'all')
                                @php
                                    $docCategory = $doc->category ?: 'Uncategorized';
                                @endphp
                                
                                @if($currentCategory !== $docCategory)
                                    @if($currentCategory !== null)
                                        <div class="border-t border-cyan-800 my-4"></div>
                                    @endif
                                    
                                    <div class="flex items-center gap-3 mb-3 mt-4">
                                        <x-category-badge :category="$doc->category" />
                                        <h3 class="text-cyan-100 font-semibold text-lg">{{ __($docCategory) }}</h3>
                                    </div>
                                    
                                    @php
                                        $currentCategory = $docCategory;
                                    @endphp
                                @endif
                            @endif
                            
                            <div class="flex items-center justify-between p-3 rounded bg-gray-800/50 hover:bg-gray-800 transition">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <div class="font-semibold text-cyan-100">{{ $doc->title }}</div>
                                        @if($doc->visible_to_all)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-600/20 text-green-400 border border-green-600/30">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ __('Public') }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-600/20 text-orange-400 border border-orange-600/30">
                                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                </svg>
                                                {{ __('Private') }}
                                            </span>
                                        @endif
                                    </div>
                                    @if($doc->description)
                                        <div class="text-sm text-cyan-300 mt-1">{{ $doc->description }}</div>
                                    @endif
                                    @if($doc->category && (request('category') && request('category') !== 'all'))
                                        <div class="mt-2">
                                            <x-category-badge :category="$doc->category" />
                                        </div>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 ml-4">
                                    <x-secondary-button 
                                        type="button" 
                                        onclick="window.openDocument({embed: {{ json_encode(route('documents.embed', $doc)) }}, info: {{ json_encode(route('documents.info', $doc)) }}, download: {{ json_encode(route('documents.download', $doc)) }}, title: {{ json_encode($doc->title) }}})">
                                        {{ __('View') }}
                                    </x-secondary-button>
                                    <x-primary-button href="{{ route('documents.download', $doc) }}" target="_blank" rel="noopener">
                                        {{ __('Download') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    <div class="mt-6">
                        {{ $documents->links() }}
                    </div>
                @endif

                @include('documents._preview_modal')
            </div>
        </div>
    </div>
</x-app-layout>