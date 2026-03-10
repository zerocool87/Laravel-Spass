<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Gestion des actualités') }}"
            subtitle="{{ __('Publiez et gérez les actualités du SDEEG') }}"
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

            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900">{{ __('Actualités') }}</h2>
                        <p class="text-sm text-gray-500">{{ __('Gérez les actualités publiées aux élus') }}</p>
                    </div>
                    <a href="{{ route('admin.actualites.create') }}" class="inline-flex items-center justify-center px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('Nouvelle actualité') }}
                    </a>
                </div>

                <div class="mt-6 overflow-x-auto">
                    @if($actualites->isEmpty())
                        <p class="py-12 text-center text-gray-400 text-sm">{{ __('Aucune actualité pour le moment.') }}</p>
                    @else
                        <table class="min-w-full divide-y divide-gray-100 text-sm">
                            <thead>
                                <tr class="text-left text-xs font-semibold uppercase tracking-wider text-gray-500">
                                    <th class="pb-3 pr-4">{{ __('Titre') }}</th>
                                    <th class="pb-3 pr-4">{{ __('Statut') }}</th>
                                    <th class="pb-3 pr-4">{{ __('Publié le') }}</th>
                                    <th class="pb-3 pr-4">{{ __('Auteur') }}</th>
                                    <th class="pb-3"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($actualites as $actualite)
                                    <tr class="hover:bg-[#faa21b]/5 transition">
                                        <td class="py-3 pr-4 font-medium text-gray-900 max-w-xs truncate">
                                            {{ $actualite->title }}
                                        </td>
                                        <td class="py-3 pr-4">
                                            @if($actualite->is_published)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800">{{ __('Publié') }}</span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">{{ __('Brouillon') }}</span>
                                            @endif
                                        </td>
                                        <td class="py-3 pr-4 text-gray-500">
                                            {{ $actualite->published_at?->format('d/m/Y') ?? '—' }}
                                        </td>
                                        <td class="py-3 pr-4 text-gray-500">
                                            {{ $actualite->creator?->name ?? '—' }}
                                        </td>
                                        <td class="py-3 flex items-center gap-3 justify-end">
                                            <a href="{{ route('admin.actualites.edit', $actualite) }}" class="text-[#faa21b] hover:underline font-medium">{{ __('Modifier') }}</a>
                                            <form action="{{ route('admin.actualites.destroy', $actualite) }}" method="POST" onsubmit="return confirm('{{ __('Supprimer cette actualité ?') }}')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:underline font-medium">{{ __('Supprimer') }}</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="mt-4">
                            {{ $actualites->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
