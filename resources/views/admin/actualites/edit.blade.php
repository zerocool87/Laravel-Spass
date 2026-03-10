<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Modifier l\'actualité') }}"
            subtitle="{{ $actualite->title }}"
            icon="📰"
            :backRoute="route('admin.actualites.index')"
            :backLabel="__('Retour aux actualités')"
            activeSection="admin"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">

            {{-- Statut actuel --}}
            <div class="flex items-center gap-3 text-sm">
                @if($actualite->is_published)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-green-100 text-green-800 font-medium">
                        <span class="w-2 h-2 rounded-full bg-green-500 inline-block"></span>
                        {{ __('Publiée') }}
                        @if($actualite->published_at)
                            · {{ $actualite->published_at->format('d/m/Y à H:i') }}
                        @endif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-gray-100 text-gray-600 font-medium">
                        <span class="w-2 h-2 rounded-full bg-gray-400 inline-block"></span>
                        {{ __('Brouillon') }}
                    </span>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8"
                 x-data="{ charCount: {{ strlen(old('content', $actualite->content)) }}, titleCount: {{ strlen(old('title', $actualite->title)) }} }">

                <form action="{{ route('admin.actualites.update', $actualite) }}" method="POST" class="space-y-6">
                    @csrf
                    @method('PATCH')

                    @if($errors->any())
                        <div class="rounded-xl border border-red-200 bg-red-50 p-4">
                            <div class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-red-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <div>
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

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="title" class="block text-sm font-medium text-gray-700">
                                {{ __('Titre') }} <span class="text-red-500">*</span>
                            </label>
                            <span class="text-xs text-gray-400" x-text="titleCount + '/255'"></span>
                        </div>
                        <input type="text" name="title" id="title"
                            value="{{ old('title', $actualite->title) }}" required maxlength="255"
                            x-on:input="titleCount = $el.value.length"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('title') border-red-300 @enderror" />
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label for="content" class="block text-sm font-medium text-gray-700">
                                {{ __('Contenu') }} <span class="text-red-500">*</span>
                            </label>
                            <span class="text-xs text-gray-400" x-text="charCount + ' ' + '{{ __('caractères') }}'"></span>
                        </div>
                        <textarea name="content" id="content" rows="12" required
                            x-on:input="charCount = $el.value.length"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('content') border-red-300 @enderror">{{ old('content', $actualite->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="rounded-xl border border-[#faa21b]/20 bg-[#faa21b]/5 p-4">
                        <div class="flex items-center gap-3">
                            <input type="hidden" name="is_published" value="0" />
                            <input type="checkbox" name="is_published" id="is_published" value="1"
                                class="rounded border-gray-300 text-[#faa21b] focus:ring-[#faa21b] w-4 h-4"
                                {{ old('is_published', $actualite->is_published) ? 'checked' : '' }} />
                            <div>
                                <label for="is_published" class="text-sm font-semibold text-gray-800 cursor-pointer">
                                    {{ __('Publié') }}
                                </label>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    @if($actualite->published_at)
                                        {{ __('Première publication le') }} {{ $actualite->published_at->format('d/m/Y') }}
                                    @else
                                        {{ __('Cocher pour rendre visible aux élus.') }}
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-wrap gap-3 pt-2 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            {{ __('Mettre à jour') }}
                        </button>
                        <a href="{{ route('admin.actualites.index') }}"
                           class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                            {{ __('Annuler') }}
                        </a>
                        @if($actualite->is_published)
                            <a href="{{ route('elus.actualites.show', $actualite) }}"
                               target="_blank"
                               class="ml-auto inline-flex items-center gap-2 px-4 py-2.5 bg-blue-50 text-blue-700 rounded-xl font-medium text-sm hover:bg-blue-100 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                {{ __('Aperçu élus') }}
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
