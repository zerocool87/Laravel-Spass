<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Nouveau document') }}"
            subtitle="{{ __('Ajouter un document à la bibliothèque') }}"
            icon="📄"
            :backRoute="route('admin.documents.index')"
            :backLabel="__('Retour aux documents')"
            activeSection="admin"
        />
    </x-slot>

    <div class="py-4">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-6">
                <form action="{{ route('admin.documents.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
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

                    <!-- Section: Informations du document -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
                            <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ __('Informations du document') }}
                        </h3>

                        <!-- Titre + Catégorie -->
                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                            <div class="lg:col-span-2">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Titre') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="title" id="title" value="{{ old('title') }}" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('title') border-red-300 @enderror"
                                    placeholder="{{ __('Ex: Compte rendu du conseil municipal') }}" />
                                @error('title')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Catégorie') }}
                                </label>
                                <select name="category" id="category"
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('category') border-red-300 @enderror">
                                    <option value="">{{ __('-- Aucune --') }}</option>
                                    @foreach(config('documents.categories', []) as $cat)
                                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>{{ __($cat) }}</option>
                                    @endforeach
                                </select>
                                @error('category')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Description -->
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Description') }}
                            </label>
                            <textarea name="description" id="description" rows="2"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('description') border-red-300 @enderror"
                                placeholder="{{ __('Décrivez le contenu du document...') }}"
                            >{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Section: Fichier et accès -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wide flex items-center gap-2 pb-2 border-b border-[#faa21b]/20">
                            <svg class="w-4 h-4 text-[#faa21b]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            {{ __('Fichier et accès') }}
                        </h3>

                        <!-- Fichier + Visibilité -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 items-start">
                            <div>
                                <label for="file" class="block text-sm font-medium text-gray-700 mb-1">
                                    {{ __('Fichier') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="file" name="file" id="file" required
                                    class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-[#faa21b]/10 file:text-[#b36b00] hover:file:bg-[#faa21b]/20 @error('file') border-red-300 @enderror" />
                                <p class="mt-1 text-xs text-gray-500">{{ __('Formats acceptés : PDF, Word, Excel, images, texte') }}</p>
                                @error('file')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="pt-6">
                                <label class="flex items-center gap-3 cursor-pointer">
                                    <input type="checkbox" name="visible_to_all" value="1"
                                        class="rounded border-gray-300 text-[#faa21b] shadow-sm focus:ring-[#faa21b]"
                                        {{ old('visible_to_all', true) ? 'checked' : '' }} />
                                    <span class="text-sm font-medium text-gray-700">{{ __('Visible par tous les utilisateurs') }}</span>
                                </label>
                            </div>
                        </div>

                        <!-- Utilisateurs assignés -->
                        <div>
                            <label for="assigned_users" class="block text-sm font-medium text-gray-700 mb-1">
                                {{ __('Assigner à des utilisateurs spécifiques') }}
                            </label>
                            <select name="assigned_users[]" id="assigned_users" multiple size="3"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('assigned_users') border-red-300 @enderror">
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}" {{ in_array($u->id, old('assigned_users', [])) ? 'selected' : '' }}>
                                        {{ $u->name }} ({{ $u->email }})
                                    </option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Requis si le document n\'est pas visible par tous') }}</p>
                            @error('assigned_users')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-3 pt-5 border-t border-gray-200">
                        <a href="{{ route('admin.documents.index') }}" class="px-5 py-2 text-sm font-medium text-gray-600 hover:text-gray-800 hover:bg-gray-100 rounded-lg transition">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow-md hover:shadow-lg hover:bg-[#e8941a] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            {{ __('Créer le document') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
