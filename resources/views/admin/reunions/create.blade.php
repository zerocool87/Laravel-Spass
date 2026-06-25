<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Créer une réunion') }}"
            subtitle="{{ __('Planifier une nouvelle réunion') }}"
            icon="📅"
            :backRoute="route('admin.reunions.index')"
            :backLabel="__('Retour aux réunions')"
            activeSection="admin"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Administration'), 'url' => route('elus.admin.index')], ['label' => __('Réunions'), 'url' => route('admin.reunions.index')], ['label' => __('Nouvelle')]]" />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-6">
                <form action="{{ route('admin.reunions.store') }}" method="POST" class="space-y-5">
                    @csrf

                    @if($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 p-3">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-red-900">{{ __('Veuillez corriger les erreurs suivantes :') }}</p>
                                    <ul class="mt-1.5 list-disc pl-5 text-sm text-red-800 space-y-0.5">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">
                        <section class="rounded-xl border border-[#faa21b]/20 p-4 space-y-4">
                            <div class="flex items-center gap-2 rounded-lg bg-[#faa21b]/10 px-3 py-2 border border-[#faa21b]/20">
                                <span class="h-4 w-1.5 rounded-full bg-[#faa21b]"></span>
                                <h3 class="text-sm font-bold text-[#b36b00] uppercase tracking-wide">{{ __('Informations principales') }}</h3>
                            </div>

                            <div>
                                <label for="title" class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('Titre de la réunion') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('title') border-red-300 @enderror"
                                    placeholder="{{ __('Ex: Réunion du Conseil Municipal') }}" />
                                @error('title')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="instance_id" class="block text-xs font-semibold text-gray-700 mb-1">
                                        {{ __('Instance') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="instance_id" id="instance_id" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('instance_id') border-red-300 @enderror">
                                        <option value="">{{ __('Sélectionner') }}</option>
                                        @foreach($instances as $instance)
                                            <option value="{{ $instance->id }}" {{ old('instance_id', $selectedInstance) == $instance->id ? 'selected' : '' }}>
                                                {{ $instance->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('instance_id')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="status" class="block text-xs font-semibold text-gray-700 mb-1">
                                        {{ __('Statut') }} <span class="text-red-500">*</span>
                                    </label>
                                    <select name="status" id="status" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('status') border-red-300 @enderror">
                                        @foreach($statuses as $key => $label)
                                            <option value="{{ $key }}" {{ old('status') == $key ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="location" class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('Lieu') }}
                                </label>
                                <input type="text" name="location" id="location" value="{{ old('location') }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('location') border-red-300 @enderror"
                                    placeholder="{{ __('Ex: Salle du Conseil') }}" />
                                @error('location')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </section>

                        <section class="rounded-xl border border-[#faa21b]/20 p-4 space-y-4">
                            <div class="flex items-center gap-2 rounded-lg bg-[#faa21b]/10 px-3 py-2 border border-[#faa21b]/20">
                                <span class="h-4 w-1.5 rounded-full bg-[#faa21b]"></span>
                                <h3 class="text-sm font-bold text-[#b36b00] uppercase tracking-wide">{{ __('Planification') }}</h3>
                            </div>

                            <div>
                                <label for="date" class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('Date') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="date" id="date" value="{{ old('date') }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('date') border-red-300 @enderror" />
                                @error('date')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="start_time" class="block text-xs font-semibold text-gray-700 mb-1">
                                        {{ __('Heure de début') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" name="start_time" id="start_time" value="{{ old('start_time', '09:00') }}" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('start_time') border-red-300 @enderror" />
                                    @error('start_time')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="end_time" class="block text-xs font-semibold text-gray-700 mb-1">
                                        {{ __('Heure de fin') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="time" name="end_time" id="end_time" value="{{ old('end_time', '11:00') }}" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('end_time') border-red-300 @enderror" />
                                    @error('end_time')
                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div>
                                <label for="participants" class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('Participants') }}
                                </label>
                                <textarea name="participants_text" id="participants" rows="2"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"
                                    placeholder="{{ __('Un participant par ligne') }}"
                                >{{ old('participants_text') }}</textarea>
                            </div>
                        </section>

                        <section class="rounded-xl border border-[#faa21b]/20 p-4 space-y-4">
                            <div class="flex items-center gap-2 rounded-lg bg-[#faa21b]/10 px-3 py-2 border border-[#faa21b]/20">
                                <span class="h-4 w-1.5 rounded-full bg-[#faa21b]"></span>
                                <h3 class="text-sm font-bold text-[#b36b00] uppercase tracking-wide">{{ __('Contenu de la réunion') }}</h3>
                            </div>

                            <div>
                                <label for="description" class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('Description') }}
                                </label>
                                <textarea name="description" id="description" rows="4"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('description') border-red-300 @enderror"
                                    placeholder="{{ __('Objet de la réunion, contexte...') }}"
                                >{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="ordre_du_jour" class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('Ordre du jour') }}
                                </label>
                                <textarea name="ordre_du_jour" id="ordre_du_jour" rows="5"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('ordre_du_jour') border-red-300 @enderror"
                                    placeholder="{{ __('1. Approbation du procès-verbal') }}&#10;{{ __('2. Questions diverses') }}"
                                >{{ old('ordre_du_jour') }}</textarea>
                                @error('ordre_du_jour')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="compte_rendu" class="block text-xs font-semibold text-gray-700 mb-1">
                                    {{ __('Compte rendu') }}
                                </label>
                                <textarea name="compte_rendu" id="compte_rendu" rows="5"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('compte_rendu') border-red-300 @enderror"
                                    placeholder="{{ __('Rédigez le compte rendu de la réunion...') }}"
                                >{{ old('compte_rendu') }}</textarea>
                                @error('compte_rendu')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </section>
                    </div>

                    <div class="rounded-xl border border-[#faa21b]/20 p-4 space-y-3" x-data="{ visible: false }">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2 rounded-lg bg-[#faa21b]/10 px-3 py-2 border border-[#faa21b]/20">
                                <span class="h-4 w-1.5 rounded-full bg-[#faa21b]"></span>
                                <h3 class="text-sm font-bold text-[#b36b00] uppercase tracking-wide">{{ __('Accès élus') }}</h3>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="visible_to_all" value="1" x-model="visible"
                                    class="rounded border-gray-300 text-[#faa21b] shadow-sm focus:ring-[#faa21b]" />
                                <span class="text-sm font-medium text-gray-700">{{ __('Visible par tous les élus') }}</span>
                            </label>
                        </div>
                        <div x-show="!visible">
                            @if(!empty($titres))
                            <p class="text-xs font-medium text-gray-700 mb-2">{{ __('Restreindre aux titres suivants :') }}</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                                @foreach($titres as $titre)
                                    <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                                        <input type="checkbox" name="titres[]" value="{{ $titre }}"
                                            class="rounded border-gray-300 text-[#faa21b] shadow-sm focus:ring-[#faa21b]"
                                            {{ in_array($titre, old('titres', [])) ? 'checked' : '' }} />
                                        {{ $titre }}
                                    </label>
                                @endforeach
                            </div>
                            @else
                            <p class="text-xs text-gray-400">{{ __('Aucun titre disponible. Importez des élus d\'abord.') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-200">
                        <a href="{{ route('admin.reunions.index') }}" class="px-5 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg hover:bg-[#e8941a] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Créer la réunion') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
