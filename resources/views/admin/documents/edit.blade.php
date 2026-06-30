<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Modifier le document') }}"
            subtitle="{{ $document->title }}"
            icon="✏️"
            :backRoute="route('admin.documents.index')"
            :backLabel="__('Retour aux documents')"
            activeSection="admin"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Administration'), 'url' => route('elus.admin.index')], ['label' => __('Documents'), 'url' => route('admin.documents.index')], ['label' => $document->title]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                @include('partials._errors')

                <div class="flex flex-wrap items-center gap-x-5 gap-y-2 rounded-xl bg-[#faa21b]/5 border border-[#faa21b]/20 px-5 py-3 text-sm mb-6">
                    <svg class="w-4 h-4 text-[#faa21b] flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-gray-600"><strong class="text-gray-800">{{ __('Fichier :') }}</strong> {{ $document->original_name ?? __('Non défini') }}</span>
                    <span class="text-gray-300">·</span>
                    <span class="text-gray-600"><strong class="text-gray-800">{{ __('Auteur :') }}</strong> {{ $document->creator?->name ?? __('Non défini') }}</span>
                    <span class="text-gray-300">·</span>
                    <span class="text-gray-600"><strong class="text-gray-800">{{ __('Créé le :') }}</strong> {{ $document->created_at->format('d/m/Y') }}</span>
                </div>

                <form id="edit-form" action="{{ route('admin.documents.update', $document) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    @include('admin.documents.partials._form_fields', ['document' => $document])
                </form>

                <div class="flex items-center justify-between pt-6 mt-6 border-t border-gray-200">
                    <form method="POST" action="{{ route('admin.documents.destroy', $document) }}" @submit.prevent="if(confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce document ? Cette action est irréversible.') }}')) $el.submit()">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-1.5 px-4 py-2 text-sm font-medium text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            {{ __('Supprimer le document') }}
                        </button>
                    </form>
                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.documents.index') }}" class="px-5 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" form="edit-form" class="inline-flex items-center gap-2 px-5 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg hover:bg-[#e8941a] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Enregistrer les modifications') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
