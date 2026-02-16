<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Gestion des documents') }}"
            subtitle="{{ __('Administration de la bibliotheque') }}"
            icon="📄"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="admin"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-sm flex items-start gap-3">
                    <div class="mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('Documents') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('Gerez, editez et supprimez les documents') }}</p>
                    </div>
                    <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                        {{ __('Nouveau document') }}
                    </a>
                </div>

                <div class="mt-6 grid grid-cols-1 gap-3 sm:grid-cols-3">
                    <a href="{{ route('admin.documents.index', request()->except('visibility', 'page')) }}"
                       class="group rounded-xl border {{ $visibility === null ? 'border-[#faa21b] bg-[#faa21b]/10' : 'border-gray-200 bg-white' }} px-4 py-3 shadow-sm hover:border-[#faa21b]/60 hover:bg-[#faa21b]/5 transition"
                       @if($visibility === null) aria-current="page" @endif>
                        <p class="text-xs font-semibold text-gray-500">{{ __('Vue') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ __('Tous les documents') }}</p>
                    </a>
                    <a href="{{ route('admin.documents.index', array_merge(request()->except('page'), ['visibility' => 'public'])) }}"
                       class="group rounded-xl border {{ $visibility === 'public' ? 'border-[#faa21b] bg-[#faa21b]/10' : 'border-gray-200 bg-white' }} px-4 py-3 shadow-sm hover:border-[#faa21b]/60 hover:bg-[#faa21b]/5 transition"
                       @if($visibility === 'public') aria-current="page" @endif>
                        <p class="text-xs font-semibold text-gray-500">{{ __('Acces') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ __('Public') }}</p>
                    </a>
                    <a href="{{ route('admin.documents.index', array_merge(request()->except('page'), ['visibility' => 'private'])) }}"
                       class="group rounded-xl border {{ $visibility === 'private' ? 'border-[#faa21b] bg-[#faa21b]/10' : 'border-gray-200 bg-white' }} px-4 py-3 shadow-sm hover:border-[#faa21b]/60 hover:bg-[#faa21b]/5 transition"
                       @if($visibility === 'private') aria-current="page" @endif>
                        <p class="text-xs font-semibold text-gray-500">{{ __('Acces') }}</p>
                        <p class="text-sm font-semibold text-gray-900">{{ __('Prive') }}</p>
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.documents.index') }}" class="mt-6 border-t border-[#faa21b]/10 pt-6" x-data="{}" x-ref="filterForm">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-end">
                        <div class="lg:col-span-5">
                            <label for="search" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Recherche') }}</label>
                            <input
                                id="search"
                                name="q"
                                type="search"
                                value="{{ $search }}"
                                placeholder="{{ __('Titre, description, fichier...') }}"
                                class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"
                            />
                        </div>
                        <div class="lg:col-span-3">
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Categorie') }}</label>
                            <select id="category" name="category" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]" x-on:change="$refs.filterForm.submit()">
                                <option value="">{{ __('Toutes les categories') }}</option>
                                @foreach(config('documents.categories', []) as $cat)
                                    <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="lg:col-span-3">
                            <label for="visibility" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Acces') }}</label>
                            <select id="visibility" name="visibility" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]" x-on:change="$refs.filterForm.submit()">
                                <option value="">{{ __('Tous') }}</option>
                                <option value="public" {{ $visibility === 'public' ? 'selected' : '' }}>{{ __('Public') }}</option>
                                <option value="private" {{ $visibility === 'private' ? 'selected' : '' }}>{{ __('Prive') }}</option>
                            </select>
                        </div>
                        <div class="lg:col-span-1 flex gap-2">
                            <button type="submit" class="inline-flex w-full items-center justify-center px-4 py-2 bg-[#faa21b] border border-transparent rounded-lg font-semibold text-sm text-white shadow hover:bg-[#f39b14] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                                {{ __('Filtrer') }}
                            </button>
                        </div>
                    </div>

                    @if($search || $category || $visibility)
                        <div class="mt-4 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                            <span class="font-semibold text-gray-500">{{ __('Filtres actifs') }}:</span>
                            @if($search)
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-gray-700">{{ __('Recherche') }}: {{ $search }}</span>
                            @endif
                            @if($category)
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-gray-700">{{ __('Categorie') }}: {{ $category }}</span>
                            @endif
                            @if($visibility)
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-gray-700">{{ __('Acces') }}: {{ $visibility === 'public' ? __('Public') : __('Prive') }}</span>
                            @endif
                            <a href="{{ route('admin.documents.index') }}" class="inline-flex items-center gap-1 rounded-full border border-gray-200 px-2.5 py-1 text-gray-700 hover:border-[#faa21b]/40 hover:text-[#b36b00] transition">
                                {{ __('Reinitialiser') }}
                            </a>
                        </div>
                    @endif
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Liste des documents') }}</h3>
                        <p class="text-sm text-gray-500" aria-live="polite">{{ __('Total') }}: {{ $documents->total() }}</p>
                    </div>
                    <div class="text-xs text-gray-500">
                        {{ __('Derniere mise a jour') }}: {{ now()->format('d/m/Y H:i') }}
                    </div>
                </div>

                @if($documents->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[#faa21b]/10 text-[#b36b00]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-700">{{ __('Aucun document trouve') }}</p>
                        <p class="text-sm text-gray-500">{{ __('Essayez de modifier les filtres ou creez un document.') }}</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($documents as $doc)
                            <div class="group px-6 py-5 lg:py-6 hover:bg-[#fff7ea]/40 transition-colors">
                                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                    <div class="min-w-0">
                                        <div class="flex flex-wrap items-center gap-3">
                                            <x-category-icon :document="$doc" size="w-10 h-10" />
                                            <div class="min-w-0">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <h4 class="text-lg font-semibold text-gray-900 break-words group-hover:text-[#b36b00]">{{ $doc->title }}</h4>
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $doc->visible_to_all ? 'bg-emerald-100 text-emerald-800' : 'bg-orange-100 text-orange-800' }}">
                                                        {{ $doc->visible_to_all ? __('Public') : __('Prive') }}
                                                    </span>
                                                </div>
                                                <div class="mt-2 text-xs text-gray-500 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                                                    <span class="inline-flex items-center gap-2">
                                                        <span class="font-semibold text-gray-600">{{ __('Categorie') }}:</span>
                                                        {{ $doc->category ?? __('Aucune') }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-2">
                                                        <span class="font-semibold text-gray-600">{{ __('Cree le') }}:</span>
                                                        {{ $doc->created_at?->format('d/m/Y') }}
                                                    </span>
                                                    <span class="inline-flex items-center gap-2">
                                                        <span class="font-semibold text-gray-600">{{ __('Auteur') }}:</span>
                                                        {{ $doc->creator?->name ?? '-' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap items-center gap-2">
                                        <a href="{{ route('documents.download', $doc) }}" class="inline-flex items-center gap-2 px-3 py-2 border border-[#faa21b]/50 rounded-lg text-sm font-semibold text-[#b36b00] hover:bg-[#faa21b]/10 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v10m0 0l3-3m-3 3l-3-3m6 7H9a2 2 0 01-2-2v-1m10 3a2 2 0 002-2v-1"></path>
                                            </svg>
                                            {{ __('Telecharger') }}
                                        </a>
                                        <a href="{{ route('admin.documents.edit', $doc) }}" class="inline-flex items-center gap-2 px-3 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:border-gray-300 hover:bg-gray-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5h6m-6 4h6m-6 4h6m-6 4h6M6 5h.01M6 9h.01M6 13h.01M6 17h.01"></path>
                                            </svg>
                                            {{ __('Modifier') }}
                                        </a>
                                        <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" onsubmit="return confirm('{{ __('Supprimer ce document ?') }}')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center gap-2 px-3 py-2 border border-red-200 rounded-lg text-sm font-semibold text-red-700 hover:bg-red-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-300 transition">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                {{ __('Supprimer') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-6">
                @if($documents->hasPages())
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 px-4 py-3">
                        {{ $documents->onEachSide(1)->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
