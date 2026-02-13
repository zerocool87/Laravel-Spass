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
                <div class="bg-green-50 border-2 border-green-200 text-green-800 px-6 py-4 rounded-xl shadow">
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <div class="bg-white rounded-xl shadow-lg border border-[#faa21b]/20 p-6">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">{{ __('Documents') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('Gerez, editez et supprimez les documents') }}</p>
                    </div>
                    <a href="{{ route('admin.documents.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition">
                        {{ __('Nouveau document') }}
                    </a>
                </div>

                <form method="GET" action="{{ route('admin.documents.index') }}" class="mt-6 border-t border-[#faa21b]/10 pt-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-[1fr_auto] items-end">
                        <div>
                            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Categorie') }}</label>
                            <select id="category" name="category" class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                                <option value="">{{ __('Toutes les categories') }}</option>
                                @foreach(config('documents.categories', []) as $cat)
                                    <option value="{{ $cat }}" {{ $category === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-[#faa21b] border border-transparent rounded-lg font-semibold text-sm text-white shadow hover:bg-[#f39b14] transition">
                            {{ __('Filtrer') }}
                        </button>
                    </div>
                </form>
            </div>

            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Liste des documents') }}</h3>
                        <p class="text-sm text-gray-500">{{ __('Total') }}: {{ $documents->total() }}</p>
                    </div>
                </div>

                @if($documents->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">
                        {{ __('Aucun document trouve') }}
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach($documents as $doc)
                            <div class="px-6 py-4 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="text-lg font-semibold text-gray-900 break-words">{{ $doc->title }}</h4>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $doc->visible_to_all ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                            {{ $doc->visible_to_all ? __('Public') : __('Prive') }}
                                        </span>
                                    </div>
                                    <div class="mt-2 text-sm text-gray-500 flex flex-wrap gap-4">
                                        <span>{{ __('Categorie') }}: {{ $doc->category ?? __('Aucune') }}</span>
                                        <span>{{ __('Cree le') }}: {{ $doc->created_at?->format('d/m/Y') }}</span>
                                        <span>{{ __('Auteur') }}: {{ $doc->creator?->name ?? '-' }}</span>
                                    </div>
                                </div>
                                <div class="flex flex-wrap items-center gap-2">
                                    <a href="{{ route('documents.download', $doc) }}" class="inline-flex items-center px-3 py-2 border border-[#faa21b] rounded-lg text-sm font-medium text-[#faa21b] hover:bg-[#faa21b]/10 transition-colors">
                                        {{ __('Telecharger') }}
                                    </a>
                                    <a href="{{ route('admin.documents.edit', $doc) }}" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                                        {{ __('Modifier') }}
                                    </a>
                                    <form method="POST" action="{{ route('admin.documents.destroy', $doc) }}" onsubmit="return confirm('{{ __('Supprimer ce document ?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center px-3 py-2 border border-red-200 rounded-lg text-sm font-medium text-red-700 hover:bg-red-50">
                                            {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="mt-6">
                {{ $documents->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
