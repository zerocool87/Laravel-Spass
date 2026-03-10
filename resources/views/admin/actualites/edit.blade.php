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
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
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
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Titre') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title', $actualite->title) }}" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('title') border-red-300 @enderror" />
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="content" class="block text-sm font-medium text-gray-700 mb-1">
                            {{ __('Contenu') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea name="content" id="content" rows="10" required
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] @error('content') border-red-300 @enderror">{{ old('content', $actualite->content) }}</textarea>
                        @error('content')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex items-center gap-3">
                        <input type="hidden" name="is_published" value="0" />
                        <input type="checkbox" name="is_published" id="is_published" value="1"
                            class="rounded border-gray-300 text-[#faa21b] focus:ring-[#faa21b]"
                            {{ old('is_published', $actualite->is_published) ? 'checked' : '' }} />
                        <label for="is_published" class="text-sm font-medium text-gray-700">
                            {{ __('Publié') }}
                        </label>
                        @if($actualite->published_at)
                            <span class="text-xs text-gray-400">({{ __('le') }} {{ $actualite->published_at->format('d/m/Y') }})</span>
                        @endif
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-[#faa21b] transition">
                            {{ __('Mettre à jour') }}
                        </button>
                        <a href="{{ route('admin.actualites.index') }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-100 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-200 transition">
                            {{ __('Annuler') }}
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
