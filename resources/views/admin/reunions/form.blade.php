@php
    $isEdit = isset($reunion);
@endphp

<div class="space-y-6">
    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
            <p class="text-sm font-semibold text-red-900">{{ __('Veuillez corriger les erreurs suivantes :') }}</p>
            <ul class="mt-2 list-disc pl-5 text-sm text-red-800 space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('alternative_slots') && $errors->has('conflict'))
        <div class="rounded-xl border border-yellow-200 bg-yellow-50 p-4 mt-4">
            <p class="text-sm font-semibold text-yellow-900">{{ __('Créneaux alternatifs suggérés :') }}</p>
            <ul class="mt-2 list-disc pl-5 text-sm text-yellow-800 space-y-1">
                @foreach(session('alternative_slots') as $slot)
                    <li>{{ $slot['start'] }} - {{ $slot['end'] }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label for="instance_id" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Instance') }} <span class="text-red-500">*</span>
            </label>
            <select
                name="instance_id"
                id="instance_id"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('instance_id') border-red-300 @enderror"
            >
                <option value="">{{ __('Sélectionner une instance') }}</option>
                @foreach($instances as $instance)
                    <option value="{{ $instance->id }}" {{ old('instance_id', $isEdit ? $reunion->instance_id : $selectedInstance) == $instance->id ? 'selected' : '' }}>
                        {{ $instance->name }}
                    </option>
                @endforeach
            </select>
            @error('instance_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="status" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Statut') }} <span class="text-red-500">*</span>
            </label>
            <select
                name="status"
                id="status"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('status') border-red-300 @enderror"
            >
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ old('status', $isEdit ? $reunion->status : null) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
            @error('status')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="title" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Titre') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="text"
                name="title"
                id="title"
                value="{{ old('title', $isEdit ? $reunion->title : '') }}"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('title') border-red-300 @enderror"
                placeholder="{{ __('Ex: Réunion préparatoire du conseil') }}"
            />
            @error('title')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="description" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Description') }}
            </label>
            <textarea
                name="description"
                id="description"
                rows="3"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('description') border-red-300 @enderror"
                placeholder="{{ __('Contexte et objectif de la réunion...') }}"
            >{{ old('description', $isEdit ? $reunion->description : '') }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="date" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Date') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="date"
                name="date"
                id="date"
                value="{{ old('date', $isEdit && $reunion->start_time ? $reunion->start_time->format('Y-m-d') : '') }}"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('date') border-red-300 @enderror"
            />
            @error('date')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="start_time" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Heure de début (HH:MM)') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="time"
                name="start_time"
                id="start_time"
                value="{{ old('start_time', $isEdit && $reunion->start_time ? $reunion->start_time->format('H:i') : '') }}"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('start_time') border-red-300 @enderror"
            />
            @error('start_time')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="end_time" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Heure de fin (HH:MM)') }} <span class="text-red-500">*</span>
            </label>
            <input
                type="time"
                name="end_time"
                id="end_time"
                value="{{ old('end_time', $isEdit && $reunion->end_time ? $reunion->end_time->format('H:i') : '') }}"
                required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('end_time') border-red-300 @enderror"
            />
            @error('end_time')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="location" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Lieu') }}
            </label>
            <input
                type="text"
                name="location"
                id="location"
                value="{{ old('location', $isEdit ? $reunion->location : '') }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('location') border-red-300 @enderror"
                placeholder="{{ __('Ex: Salle du conseil') }}"
            />
            @error('location')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="ordre_du_jour" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Ordre du jour') }}
            </label>
            <textarea
                name="ordre_du_jour"
                id="ordre_du_jour"
                rows="4"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('ordre_du_jour') border-red-300 @enderror"
                placeholder="{{ __('Points à aborder pendant la réunion...') }}"
            >{{ old('ordre_du_jour', $isEdit ? $reunion->ordre_du_jour : '') }}</textarea>
            @error('ordre_du_jour')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="compte_rendu" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Compte rendu') }}
            </label>
            <textarea
                name="compte_rendu"
                id="compte_rendu"
                rows="4"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('compte_rendu') border-red-300 @enderror"
                placeholder="{{ __('Décisions prises et actions à suivre...') }}"
            >{{ old('compte_rendu', $isEdit ? $reunion->compte_rendu : '') }}</textarea>
            @error('compte_rendu')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="md:col-span-2">
            <label for="participants" class="block text-sm font-semibold text-gray-900 mb-2">
                {{ __('Participants (un par ligne)') }}
            </label>
            <textarea
                name="participants_text"
                id="participants"
                rows="4"
                placeholder="Nom1&#10;Nom2&#10;Nom3"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]"
            >{{ old('participants_text', $isEdit && is_array($reunion->participants) ? implode("\n", $reunion->participants) : '') }}</textarea>
            <p class="mt-1 text-xs text-gray-500">{{ __('Entrez les noms des participants, un par ligne.') }}</p>
        </div>
    </div>

    <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
        <a
            href="{{ route('admin.reunions.index') }}"
            class="px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition"
        >
            {{ __('Annuler') }}
        </a>
        <button
            type="submit"
            class="px-6 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition"
        >
            {{ $isEdit ? __('Enregistrer les modifications') : __('Créer la réunion') }}
        </button>
    </div>
</div>
