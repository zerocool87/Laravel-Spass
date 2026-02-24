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

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                <form id="update-form" method="POST" action="{{ route('admin.projects.update', $project) }}" class="space-y-6">
                    @csrf
                    @method('PUT')

                    @if($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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

                    {{-- Section: Informations principales --}}
                    <div class="space-y-5">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
                            <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Informations principales') }}
                        </h3>

                        {{-- Titre --}}
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Titre du projet') }} <span class="text-red-500" aria-hidden="true">*</span>
                            </label>
                            <input
                                type="text"
                                name="title"
                                id="title"
                                value="{{ old('title', $project->title) }}"
                                required
                                autocomplete="off"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('title') border-red-300 @enderror"
                            />
                            @error('title')
                                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Type & Statut --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Type de projet') }} <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <select
                                    name="type"
                                    id="type"
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('type') border-red-300 @enderror"
                                >
                                    <option value="">{{ __('Sélectionnez un type') }}</option>
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ old('type', $project->type) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Statut') }} <span class="text-red-500" aria-hidden="true">*</span>
                                </label>
                                <select
                                    name="status"
                                    id="status"
                                    required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('status') border-red-300 @enderror"
                                >
                                    <option value="">{{ __('Sélectionnez un statut') }}</option>
                                    @foreach($statuses as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', $project->status) === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Commune --}}
                        <div>
                            <label for="commune" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Commune') }}</label>
                            <select
                                name="commune"
                                id="commune"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('commune') border-red-300 @enderror"
                            >
                                <option value="">{{ __('Sélectionner une commune') }}</option>
                                @foreach($communes as $commune)
                                    <option value="{{ $commune }}" {{ old('commune', $project->commune) === $commune ? 'selected' : '' }}>{{ $commune }}</option>
                                @endforeach
                            </select>
                            @error('commune')
                                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Description') }}</label>
                            <textarea
                                name="description"
                                id="description"
                                rows="4"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('description') border-red-300 @enderror"
                            >{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Section: Planification --}}
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
                                <input
                                    type="number"
                                    name="budget"
                                    id="budget"
                                    value="{{ old('budget', $project->budget) }}"
                                    min="0"
                                    step="0.01"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('budget') border-red-300 @enderror"
                                />
                                @error('budget')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date de début') }}</label>
                                <input
                                    type="date"
                                    name="start_date"
                                    id="start_date"
                                    value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('start_date') border-red-300 @enderror"
                                />
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Date de fin') }}</label>
                                <input
                                    type="date"
                                    name="end_date"
                                    id="end_date"
                                    value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('end_date') border-red-300 @enderror"
                                />
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600" role="alert">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                </form>{{-- end update form --}}

                {{-- Actions bar — outside the update form so the delete form is not nested --}}
                <div class="flex items-center justify-between pt-5 border-t border-gray-200 mt-5">
                     <button
                         type="button"
                         class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 rounded-lg transition"
                         x-on:click="$dispatch('open-modal', 'confirm-delete-project-edit')"
                     >
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
                        <button
                            type="submit"
                            form="update-form"
                            class="inline-flex items-center gap-2 px-5 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg hover:bg-[#e8941a] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition"
                        >
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
