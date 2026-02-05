<x-app-layout>
    <x-slot name="header">
        <div class="bg-[#FFA500] -mx-8 -my-6 px-8 py-6 shadow-lg">
            <div class="flex items-center space-x-4">
                <a href="{{ route('elus.reunions.index') }}" class="text-white/80 hover:text-white transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <h2 class="font-semibold text-xl text-white leading-tight">{{ __('Nouvelle réunion') }}</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ route('elus.reunions.store') }}">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <label for="instance_id" class="block text-sm font-medium text-gray-700">{{ __('Instance') }} *</label>
                            <select name="instance_id" id="instance_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <option value="">{{ __('Sélectionner une instance') }}</option>
                                @foreach($instances as $instance)
                                    <option value="{{ $instance->id }}" {{ old('instance_id', $selectedInstance) == $instance->id ? 'selected' : '' }}>
                                        {{ $instance->name }} ({{ $instance->type_label }})
                                    </option>
                                @endforeach
                            </select>
                            @error('instance_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Titre') }} *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="date" class="block text-sm font-medium text-gray-700">{{ __('Date et heure') }} *</label>
                                <input type="datetime-local" name="date" id="date" value="{{ old('date') }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Statut') }} *</label>
                                <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach($statuses as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', 'planifiee') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="location" class="block text-sm font-medium text-gray-700">{{ __('Lieu') }}</label>
                            <input type="text" name="location" id="location" value="{{ old('location') }}" placeholder="{{ __('Ex: Salle du conseil, Mairie...') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('location')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                            <textarea name="description" id="description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="ordre_du_jour" class="block text-sm font-medium text-gray-700">{{ __('Ordre du jour') }}</label>
                            <textarea name="ordre_du_jour" id="ordre_du_jour" rows="6" placeholder="{{ __('1. Approbation du compte rendu précédent&#10;2. Point sur les projets en cours&#10;3. Questions diverses') }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('ordre_du_jour') }}</textarea>
                            @error('ordre_du_jour')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('elus.reunions.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
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
