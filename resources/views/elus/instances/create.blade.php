<x-app-layout>
    <x-slot name="header">
        <div class="bg-[#FFA500] -mx-8 -my-6 px-8 py-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('elus.dashboard') }}" class="text-white/80 hover:text-white transition" aria-label="{{ __('Retour au tableau de bord') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h2 class="font-bold text-2xl text-white mb-1">➕ {{ __('Nouvelle instance') }}</h2>
                        <p class="text-white/90 text-sm">{{ __('Créer une nouvelle commission ou comité') }}</p>
                    </div>
                </div>
                <nav class="flex space-x-2 items-center">
                    <a href="{{ route('elus.instances.index') }}" class="px-4 py-2 text-sm bg-white text-[#FFA500] hover:bg-white/90 rounded-lg transition font-bold">{{ __('Instances') }}</a>
                    <a href="{{ route('elus.projects.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Projets') }}</a>
                    <a href="{{ route('elus.reunions.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Réunions') }}</a>
                    <a href="{{ route('elus.documents.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Documents') }}</a>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ route('elus.instances.store') }}">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nom') }} *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Type') }} *</label>
                            <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('Sélectionner un type') }}</option>
                                @foreach($types as $key => $label)
                                    <option value="{{ $key }}" {{ old('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="territory" class="block text-sm font-medium text-gray-700">{{ __('Territoire') }}</label>
                            <input type="text" name="territory" id="territory" value="{{ old('territory') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('territory')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('elus.instances.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            {{ __('Créer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
