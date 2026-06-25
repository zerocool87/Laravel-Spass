<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Nouvelle thématique') }}"
            subtitle="{{ __('Ajouter une thématique au forum') }}"
            icon="🏷️"
            :backRoute="route('admin.thematiques.index')"
            :backLabel="__('Retour aux thématiques')"
            activeSection="admin"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Administration'), 'url' => route('elus.admin.index')], ['label' => __('Thématiques'), 'url' => route('admin.thematiques.index')], ['label' => __('Nouvelle')]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-6 sm:p-8">
                <form action="{{ route('admin.thematiques.store') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label for="name" class="text-base font-semibold text-gray-700">
                            {{ __('Nom de la thématique') }}
                        </label>
                        <input id="name" name="name" type="text" class="w-full input-orange text-base" value="{{ old('name') }}" required maxlength="255" />
                        <x-input-error :messages="$errors->get('name')" />
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-2">
                        <a href="{{ route('admin.thematiques.index') }}" class="px-5 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="btn-primary-orange text-base px-6 py-3">
                            {{ __('Créer la thématique') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
