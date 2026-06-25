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

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="widget-container">
                <form method="POST" action="{{ route('elus.forum.store') }}" class="p-6 flex flex-col gap-4">
                    @csrf

                    <div class="flex flex-col gap-2">
                        <label for="instance_id" class="text-sm font-semibold text-gray-700">
                            {{ __('Instance') }}
                        </label>
                        <select id="instance_id" name="instance_id" class="w-full select-orange" required>
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
                        <label for="title" class="text-sm font-semibold text-gray-700">
                            {{ __('Titre du sujet') }}
                        </label>
                        <input id="title" name="title" type="text" class="w-full input-orange" value="{{ old('title') }}" required maxlength="255" />
                        <x-input-error :messages="$errors->get('title')" />
                    </div>

                    <div class="flex flex-col gap-2">
                        <label for="body" class="text-sm font-semibold text-gray-700">
                            {{ __('Premier message') }}
                        </label>
                        <textarea id="body" name="body" rows="6" class="w-full input-orange" required maxlength="5000">{{ old('body') }}</textarea>
                        <x-input-error :messages="$errors->get('body')" />
                    </div>

                    <div class="flex items-center justify-between gap-4 pt-2">
                        <a href="{{ route('elus.forum.index') }}" class="text-sm text-gray-500 hover:text-gray-700">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="btn-primary-orange">
                            {{ __('Créer le sujet') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
