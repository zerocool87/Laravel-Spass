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

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                <form action="{{ route('admin.reunions.store') }}" method="POST" class="space-y-6">
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

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                        <!-- Left column: Informations + Planification -->
                        <div class="space-y-6">
                            <!-- Section: Informations principales -->
                            <div class="space-y-5">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
                                    <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ __('Informations principales') }}
                                </h3>

                                <!-- Titre -->
                                <div>
                                    <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Titre de la réunion') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('title') border-red-300 @enderror"
                                        placeholder="{{ __('Ex: Réunion du Conseil Municipal') }}" />
                                    @error('title')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Instance et Statut -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="instance_id" class="block text-sm font-medium text-gray-700 mb-1">
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
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
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
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!-- Section: Planification -->
                            <div class="space-y-5">
                                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
                                    <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ __('Planification') }}
                                </h3>

                                <!-- Date et Lieu -->
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="date" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ __('Date et heure') }} <span class="text-red-500">*</span>
                                        </label>
                                        <input type="datetime-local" name="date" id="date" value="{{ old('date') }}" required
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('date') border-red-300 @enderror" />
                                        @error('date')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div>
                                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">
                                            {{ __('Lieu') }}
                                        </label>
                                        <input type="text" name="location" id="location" value="{{ old('location') }}"
                                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('location') border-red-300 @enderror"
                                            placeholder="{{ __('Ex: Salle du Conseil') }}" />
                                        @error('location')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Description -->
                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                        {{ __('Description') }}
                                    </label>
                                    <textarea name="description" id="description" rows="3"
                                        class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('description') border-red-300 @enderror"
                                        placeholder="{{ __('Objet de la réunion, contexte...') }}"
                                    >{{ old('description') }}</textarea>
                                    @error('description')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Right column: Contenu de la réunion -->
                        <div class="space-y-5">
                            <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
                                <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                {{ __('Contenu de la réunion') }}
                            </h3>

                            <!-- Ordre du jour -->
                            <div>
                                <label for="ordre_du_jour" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Ordre du jour') }}
                                </label>
                                <textarea name="ordre_du_jour" id="ordre_du_jour" rows="5"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('ordre_du_jour') border-red-300 @enderror"
                                    placeholder="{{ __('1. Approbation du procès-verbal') }}&#10;{{ __('2. Questions diverses') }}"
                                >{{ old('ordre_du_jour') }}</textarea>
                                @error('ordre_du_jour')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Compte rendu -->
                            <div>
                                <label for="compte_rendu" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Compte rendu') }}
                                </label>
                                <textarea name="compte_rendu" id="compte_rendu" rows="5"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('compte_rendu') border-red-300 @enderror"
                                    placeholder="{{ __('Rédigez le compte rendu de la réunion...') }}"
                                >{{ old('compte_rendu') }}</textarea>
                                @error('compte_rendu')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Participants -->
                            <div>
                                <label for="participants" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Participants') }}
                                </label>
                                <textarea name="participants_text" id="participants" rows="3"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('participants_text') border-red-300 @enderror"
                                    placeholder="{{ __('Un participant par ligne') }}"
                                >{{ old('participants_text') }}</textarea>
                                <p class="mt-1 text-xs text-gray-500">{{ __('Un nom par ligne') }}</p>
                                @error('participants_text')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
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
