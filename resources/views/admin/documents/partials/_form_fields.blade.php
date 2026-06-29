@props(['document' => null])

@php
    $titleValue = old('title', $document?->title ?? '');
    $categoryValue = old('category', $document?->category ?? '');
    $descriptionValue = old('description', $document?->description ?? '');
    $visibleValue = old('visible_to_all', $document?->visible_to_all ?? true);
    $titresValue = old('titres', $document?->titres ?? []);
    $assignedValue = old('assigned_users', $assigned ?? []);
    $fileRequired = $document === null;
@endphp

<div class="space-y-5">
    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
        <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        {{ __('Informations du document') }}
    </h3>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <div class="lg:col-span-2">
            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                {{ __('Titre') }} <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" id="title" value="{{ $titleValue }}" required
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('title') border-red-300 @enderror"
                placeholder="{{ __('Ex: Compte rendu du conseil municipal') }}" />
            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div>
            <label for="category" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Catégorie') }}</label>
            <select name="category" id="category"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('category') border-red-300 @enderror">
                <option value="">{{ __('-- Aucune --') }}</option>
                @foreach(config('documents.categories', []) as $cat)
                    <option value="{{ $cat }}" {{ $categoryValue === $cat ? 'selected' : '' }}>{{ __($cat) }}</option>
                @endforeach
            </select>
            @error('category')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
        <textarea name="description" id="description" rows="2"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('description') border-red-300 @enderror"
            placeholder="{{ __('Décrivez le contenu du document...') }}">{{ $descriptionValue }}</textarea>
        @error('description')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>

<div class="space-y-5">
    <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
        <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
        </svg>
        {{ __('Fichier et accès') }}
    </h3>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
        <div>
            <label for="file" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Fichier') }} @if($fileRequired)<span class="text-red-500">*</span>@endif</label>
            <input type="file" name="file" id="file" @if($fileRequired)required @endif
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#faa21b]/10 file:text-[#b36b00] hover:file:bg-[#faa21b]/20 @error('file') border-red-300 @enderror" />
            <p class="mt-1 text-xs text-gray-500">{{ $document ? __('Laissez vide pour conserver le fichier actuel') : __('Formats acceptés : PDF, Word, Excel, images, texte') }}</p>
            @error('file')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
        </div>
        <div class="pt-6" x-data="{ visible: {{ $visibleValue ? 'true' : 'false' }} }">
            <label class="flex items-center gap-3 cursor-pointer">
                <input type="checkbox" name="visible_to_all" value="1" x-model="visible"
                    class="rounded border-gray-300 text-[#faa21b] shadow-sm focus:ring-[#faa21b]" />
                <span class="text-sm font-medium text-gray-700">{{ __('Visible par tous les élus') }}</span>
            </label>
            <div x-show="!visible" class="mt-3">
                <p class="text-xs font-medium text-gray-700 mb-2">{{ __('Restreindre aux titres suivants :') }}</p>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach($titres as $titre)
                        <label class="flex items-center gap-2 text-sm text-gray-600 cursor-pointer">
                            <input type="checkbox" name="titres[]" value="{{ $titre }}"
                                class="rounded border-gray-300 text-[#faa21b] shadow-sm focus:ring-[#faa21b]"
                                {{ in_array($titre, $titresValue) ? 'checked' : '' }} />
                            {{ $titre }}
                        </label>
                    @endforeach
                </div>
                @if(empty($titres))
                    <p class="text-xs text-gray-400">{{ __('Aucun titre disponible. Importez des élus d\'abord.') }}</p>
                @endif
            </div>
        </div>
    </div>

    <div>
        <label for="assigned_users" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Assigner à des utilisateurs spécifiques') }}</label>
        <select name="assigned_users[]" id="assigned_users" multiple size="3"
            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('assigned_users') border-red-300 @enderror">
            @foreach($users as $u)
                <option value="{{ $u->id }}" {{ in_array($u->id, $assignedValue) ? 'selected' : '' }}>
                    {{ $u->name }} ({{ $u->email }})
                </option>
            @endforeach
        </select>
        <p class="mt-1 text-xs text-gray-500">{{ __('Requis si le document n\'est pas visible par tous') }}</p>
        @error('assigned_users')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
    </div>
</div>
