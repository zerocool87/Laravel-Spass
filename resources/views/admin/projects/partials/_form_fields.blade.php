@props(['project' => null])

@php
    $titleValue = old('title', $project?->title ?? '');
    $typeValue = old('type', $project?->type ?? '');
    $statusValue = old('status', $project?->status ?? '');
    $communeValue = old('commune', $project?->commune ?? '');
    $descriptionValue = old('description', $project?->description ?? '');
    $budgetValue = old('budget', $project?->budget ?? '');
    $startDateValue = old('start_date', $project?->start_date?->format('Y-m-d') ?? '');
    $endDateValue = old('end_date', $project?->end_date?->format('Y-m-d') ?? '');
@endphp

<div class="space-y-5">
    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
        <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ __('Informations principales') }}
    </h3>

    <div>
        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
            {{ __('Titre du projet') }} <span class="text-red-500" aria-hidden="true">*</span>
        </label>
        <input type="text" name="title" id="title" value="{{ $titleValue }}" required autocomplete="off"
            placeholder="{{ __('Ex: Rénovation de la place centrale…') }}"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('title') border-red-300 @enderror" />
        @error('title')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Type de projet') }} <span class="text-red-500" aria-hidden="true">*</span>
            </label>
            <select name="type" id="type" required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('type') border-red-300 @enderror">
                <option value="">{{ __('Sélectionnez un type') }}</option>
                @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ $typeValue === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('type')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Statut') }} <span class="text-red-500" aria-hidden="true">*</span>
            </label>
            <select name="status" id="status" required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('status') border-red-300 @enderror">
                <option value="">{{ __('Sélectionnez un statut') }}</option>
                @foreach($statuses as $key => $label)
                    <option value="{{ $key }}" {{ $statusValue === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            @error('status')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="commune" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Commune') }}</label>
        <select name="commune" id="commune"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('commune') border-red-300 @enderror">
            <option value="">{{ __('Sélectionner une commune') }}</option>
            @foreach($communes as $commune)
                <option value="{{ $commune }}" {{ $communeValue === $commune ? 'selected' : '' }}>{{ $commune }}</option>
            @endforeach
        </select>
        @error('commune')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
        <textarea name="description" id="description" rows="4"
            placeholder="{{ __('Décrivez le projet, ses objectifs et son contexte…') }}"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('description') border-red-300 @enderror">{{ $descriptionValue }}</textarea>
        @error('description')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
    </div>
</div>

<div class="space-y-5">
    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
        <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
        </svg>
        {{ __('Planification & Budget') }}
    </h3>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <label for="budget" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Budget (€)') }}</label>
            <input type="number" name="budget" id="budget" value="{{ $budgetValue }}" min="0" step="0.01" placeholder="0.00"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('budget') border-red-300 @enderror" />
            @error('budget')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date de début') }}</label>
            <input type="date" name="start_date" id="start_date" value="{{ $startDateValue }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('start_date') border-red-300 @enderror" />
            @error('start_date')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date de fin') }}</label>
            <input type="date" name="end_date" id="end_date" value="{{ $endDateValue }}"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('end_date') border-red-300 @enderror" />
            @error('end_date')<p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
