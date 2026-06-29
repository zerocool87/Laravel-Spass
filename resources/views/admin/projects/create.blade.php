<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Créer un projet') }}"
            subtitle="{{ __('Ajouter un nouveau projet territorial') }}"
            icon="📋"
            :backRoute="route('admin.projects.index')"
            :backLabel="__('Retour aux projets')"
            activeSection="admin"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Administration'), 'url' => route('elus.admin.index')], ['label' => __('Projets'), 'url' => route('admin.projects.index')], ['label' => __('Nouveau')]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                <form method="POST" action="{{ route('admin.projects.store') }}" class="space-y-6">
                    @csrf
                    @include('partials._errors')
                    @include('admin.projects.partials._form_fields')

                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-200">
                        <a href="{{ route('admin.projects.index') }}" class="px-5 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg hover:bg-[#e8941a] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Créer le projet') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
