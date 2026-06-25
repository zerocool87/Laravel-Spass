<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Nouveau sujet') }}"
            subtitle="{{ __('Lancer une discussion') }}"
            icon="💬"
            :backRoute="route('elus.forum.index')"
            :backLabel="__('Retour au forum')"
            activeSection="forum"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Forum'), 'url' => route('elus.forum.index')], ['label' => __('Nouveau sujet')]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="widget-container">
                <form method="POST" action="{{ route('elus.forum.store') }}" class="p-6 sm:p-8 flex flex-col gap-5">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label for="instance_id" class="text-base font-semibold text-gray-700">
                            {{ __('Instance') }}
                        </label>
                        <select id="instance_id" name="instance_id" class="w-full select-orange text-base" required>
                            <option value="">{{ __('Choisir une instance') }}</option>
                            @foreach($instances as $instance)
                                <option value="{{ $instance->id }}" {{ old('instance_id') == $instance->id ? 'selected' : '' }}>
                                    {{ $instance->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('instance_id')" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="title" class="text-base font-semibold text-gray-700">
                            {{ __('Titre du sujet') }}
                        </label>
                        <input id="title" name="title" type="text" class="w-full input-orange text-base" value="{{ old('title') }}" required maxlength="255" />
                        <x-input-error :messages="$errors->get('title')" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="body" class="text-base font-semibold text-gray-700">
                            {{ __('Premier message') }}
                        </label>
                        <textarea id="body" name="body" rows="6" class="w-full input-orange text-base" required maxlength="5000">{{ old('body') }}</textarea>
                        <x-input-error :messages="$errors->get('body')" />
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-2">
                        <a href="{{ route('elus.forum.index') }}" class="text-base text-gray-500 hover:text-gray-700">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="btn-primary-orange text-base px-6 py-3">
                            {{ __('Créer le sujet') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
