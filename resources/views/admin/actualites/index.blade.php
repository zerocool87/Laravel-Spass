<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Gestion des actualités') }}"
            subtitle="{{ __('Publiez et gérez les actualités du SEHV') }}"
            icon="📰"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="admin"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-sm flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Header + Nouvelle actualité --}}
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('Actualités') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('Gérez les actualités publiées aux élus') }}</p>
                    </div>
                    <a href="{{ route('admin.actualites.create') }}"
                       class="inline-flex items-center justify-center px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('Nouvelle actualité') }}
                    </a>
                </div>

                {{-- Recherche + filtres --}}
                <form method="GET" action="{{ route('admin.actualites.index') }}"
                      class="mt-6 border-t border-[#faa21b]/10 pt-6"
                      x-data="{}" x-ref="filterForm">
                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-12 lg:items-end">
                        <div class="lg:col-span-7">
                            <input type="search" name="q" value="{{ $search }}"
                                placeholder="{{ __('Rechercher par titre…') }}"
                                class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"/>
                        </div>
                        <div class="lg:col-span-3">
                            <select name="status"
                                class="w-full rounded-lg border-gray-200 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"
                                x-on:change="$refs.filterForm.submit()">
                                <option value="">{{ __('Tous les statuts') }}</option>
                                <option value="published" {{ $status === 'published' ? 'selected' : '' }}>{{ __('Publiés') }}</option>
                                <option value="draft"     {{ $status === 'draft'     ? 'selected' : '' }}>{{ __('Brouillons') }}</option>
                            </select>
                        </div>
                        <div class="lg:col-span-2 flex gap-2">
                            <button type="submit"
                                class="inline-flex flex-1 items-center justify-center px-4 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow hover:bg-[#f39b14] transition">
                                {{ __('Filtrer') }}
                            </button>
                            @if($search || $status)
                                <a href="{{ route('admin.actualites.index') }}"
                                   class="inline-flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition"
                                   title="{{ __('Réinitialiser') }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($search || $status)
                        <div class="mt-3 flex flex-wrap items-center gap-2 text-xs text-gray-600">
                            <span class="font-semibold text-gray-500">{{ __('Filtres actifs') }} :</span>
                            @if($search)
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-gray-700">
                                    {{ __('Recherche') }}: {{ $search }}
                                </span>
                            @endif
                            @if($status)
                                <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-1 text-gray-700">
                                    {{ $status === 'published' ? __('Publiés') : __('Brouillons') }}
                                </span>
                            @endif
                        </div>
                    @endif
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 overflow-hidden">
                <div class="px-6 py-4 border-b border-[#faa21b]/10 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ __('Liste des actualités') }}</h3>
                        <p class="text-sm text-gray-500" aria-live="polite">{{ __('Total') }} : {{ $actualites->total() }}</p>
                    </div>
                    <div class="text-xs text-gray-400">{{ now()->format('d/m/Y H:i') }}</div>
                </div>

                @if($actualites->isEmpty())
                    <div class="px-6 py-12 text-center text-gray-500">
                        <div class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-[#faa21b]/10 text-[#b36b00]">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l6 6v10a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <p class="font-semibold text-gray-700">{{ __('Aucune actualité trouvée') }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            @if($search || $status)
                                {{ __('Essayez de modifier les filtres.') }}
                            @else
                                {{ __('Créez votre première actualité.') }}
                            @endif
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[#faa21b]/10 text-sm">
                            <thead class="bg-[#faa21b]/5">
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    <th class="px-6 py-3">{{ __('Titre') }}</th>
                                    <th class="px-4 py-3">{{ __('Statut') }}</th>
                                    <th class="px-4 py-3">{{ __('Publié le') }}</th>
                                    <th class="px-4 py-3">{{ __('Auteur') }}</th>
                                    <th class="px-4 py-3 text-right">{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50 bg-white">
                                @foreach($actualites as $actualite)
                                    <tr class="hover:bg-[#faa21b]/5 transition">
                                        <td class="px-6 py-4 font-medium text-gray-900 max-w-xs">
                                            <div class="truncate" title="{{ $actualite->title }}">
                                                {{ $actualite->title }}
                                            </div>
                                            <div class="text-xs text-gray-400 mt-0.5 truncate">
                                                {{ Str::limit(strip_tags($actualite->content), 80) }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-4">
                                            @if($actualite->is_published)
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 inline-block"></span>
                                                    {{ __('Publié') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                                    {{ __('Brouillon') }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-gray-500 whitespace-nowrap">
                                            {{ $actualite->published_at?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-4 text-gray-500">
                                            {{ $actualite->creator?->name ?? '—' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex items-center justify-end gap-2">
                                                @if($actualite->is_published)
                                                    <a href="{{ route('elus.actualites.show', $actualite) }}"
                                                       target="_blank"
                                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 hover:bg-blue-100 transition"
                                                       title="{{ __('Aperçu côté élus') }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                        </svg>
                                                        {{ __('Voir') }}
                                                    </a>
                                                @endif
                                                <a href="{{ route('admin.actualites.edit', $actualite) }}"
                                                   class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-[#faa21b]/10 text-[#b36b00] hover:bg-[#faa21b]/20 transition">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536M9 11l6.536-6.536a2 2 0 012.828 0l.172.172a2 2 0 010 2.828L12 14H9v-3z"/>
                                                    </svg>
                                                    {{ __('Modifier') }}
                                                </a>
                                                <form action="{{ route('admin.actualites.destroy', $actualite) }}" method="POST"
                                                      x-data
                                                      x-on:submit.prevent="if(confirm('{{ __('Supprimer cette actualité ?') }}')) $el.submit()">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg text-xs font-medium bg-red-50 text-red-600 hover:bg-red-100 transition">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4h6v3M4 7h16"/>
                                                        </svg>
                                                        {{ __('Supprimer') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if($actualites->hasPages())
                        <div class="px-6 py-4 border-t border-[#faa21b]/10">
                            {{ $actualites->links() }}
                        </div>
                    @endif
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
