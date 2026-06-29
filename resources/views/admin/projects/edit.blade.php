<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Modifier le projet') }}"
            subtitle="{{ $project->title }}"
            icon="📋"
            :backRoute="route('admin.projects.show', $project)"
            :backLabel="__('Retour au projet')"
            activeSection="admin"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Administration'), 'url' => route('elus.admin.index')], ['label' => __('Projets'), 'url' => route('admin.projects.index')], ['label' => $project->title]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                @include('partials._errors')

                <form id="update-form" method="POST" action="{{ route('admin.projects.update', $project) }}" class="space-y-6">
                    @csrf
                    @method('PATCH')
                    @include('admin.projects.partials._form_fields', ['project' => $project])
                </form>

                <div class="flex items-center justify-between pt-5 border-t border-gray-200 mt-5">
                    <button type="button" class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition"
                        x-on:click="$dispatch('open-modal', 'confirm-delete-project-edit')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        {{ __('Supprimer le projet') }}
                    </button>

                    <x-modal name="confirm-delete-project-edit" :show="false">
                        <div class="p-6">
                            <div class="flex flex-col items-center text-center">
                                <svg class="w-10 h-10 text-red-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Supprimer le projet') }}</h3>
                                <p class="text-sm text-gray-600 mb-4">
                                    {{ __('Êtes-vous sûr de vouloir supprimer ce projet ?') }}<br>
                                    {{ __('Cette action est irréversible.') }}
                                </p>
                                <div class="flex items-center justify-center gap-3">
                                    <button type="button" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition" x-on:click="$dispatch('close-modal', 'confirm-delete-project-edit')">
                                        {{ __('Annuler') }}
                                    </button>
                                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-sm shadow hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-600 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            {{ __('Supprimer') }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </x-modal>

                    <div class="flex items-center gap-3">
                        <a href="{{ route('admin.projects.show', $project) }}" class="px-5 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" form="update-form" class="inline-flex items-center gap-2 px-5 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg hover:bg-[#e8941a] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
