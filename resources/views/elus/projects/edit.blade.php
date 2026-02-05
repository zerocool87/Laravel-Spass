<x-app-layout>
    <x-slot name="header">
        <div class="bg-[#faa21b] -mx-8 -my-6 px-8 py-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('elus.dashboard') }}" class="text-white/80 hover:text-white transition" aria-label="{{ __('Retour au tableau de bord') }}">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <h2 class="font-bold text-2xl text-white mb-1">✏️ {{ __('Modifier projet') }}</h2>
                        <p class="text-white/90 text-sm">{{ __('Mise à jour de') }}: {{ $project->title }}</p>
                    </div>
                </div>
                <nav class="flex space-x-2 items-center">
                    <a href="{{ route('elus.instances.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Instances') }}</a>
                    <a href="{{ route('elus.projects.index') }}" class="px-4 py-2 text-sm bg-white text-[#faa21b] hover:bg-white/90 rounded-lg transition font-bold">{{ __('Projets') }}</a>
                    <a href="{{ route('elus.reunions.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Réunions') }}</a>
                    <a href="{{ route('elus.documents.index') }}" class="px-4 py-2 text-sm text-white hover:bg-white/20 rounded-lg transition font-medium">{{ __('Documents') }}</a>
                    <a href="{{ route('elus.dashboard') }}" class="px-4 py-2 text-sm bg-white text-[#faa21b] hover:bg-white/90 rounded-lg transition font-bold">{{ __('Tableau de bord') }}</a>
                </nav>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ route('elus.projects.update', $project) }}">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700">{{ __('Titre') }} *</label>
                            <input type="text" name="title" id="title" value="{{ old('title', $project->title) }}" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Type') }} *</label>
                                <select name="type" id="type" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach($types as $key => $label)
                                        <option value="{{ $key }}" {{ old('type', $project->type) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="status" class="block text-sm font-medium text-gray-700">{{ __('Statut') }} *</label>
                                <select name="status" id="status" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @foreach($statuses as $key => $label)
                                        <option value="{{ $key }}" {{ old('status', $project->status) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('status')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('description', $project->description) }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="budget" class="block text-sm font-medium text-gray-700">{{ __('Budget (€)') }}</label>
                            <input type="number" name="budget" id="budget" value="{{ old('budget', $project->budget) }}" step="0.01" min="0" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @error('budget')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700">{{ __('Date de début') }}</label>
                                <input type="date" name="start_date" id="start_date" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700">{{ __('Date de fin') }}</label>
                                <input type="date" name="end_date" id="end_date" value="{{ old('end_date', $project->end_date?->format('Y-m-d')) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Territoires') }}</label>
                            <div class="flex flex-wrap gap-2" id="territories-container">
                                {{-- Dynamic territory tags will be rendered here --}}
                            </div>
                            <div class="mt-2 flex gap-2">
                                <input type="text" id="new-territory" placeholder="{{ __('Ajouter un territoire') }}" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <button type="button" onclick="addTerritory()" class="px-3 py-2 bg-gray-200 rounded-md text-sm hover:bg-gray-300">+</button>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end space-x-3">
                        <a href="{{ route('elus.projects.show', $project) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50">
                            {{ __('Annuler') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">
                            {{ __('Enregistrer') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        let territories = @json($project->territories ?? []);

        function addTerritory() {
            const input = document.getElementById('new-territory');
            const value = input.value.trim();
            if (value && !territories.includes(value)) {
                territories.push(value);
                renderTerritories();
                input.value = '';
            }
        }

        function removeTerritory(index) {
            territories.splice(index, 1);
            renderTerritories();
        }

        function renderTerritories() {
            const container = document.getElementById('territories-container');
            container.innerHTML = territories.map((t, i) => `
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                    ${t}
                    <input type="hidden" name="territories[]" value="${t}">
                    <button type="button" onclick="removeTerritory(${i})" class="ml-2 text-blue-600 hover:text-blue-800">&times;</button>
                </span>
            `).join('');
        }

        document.getElementById('new-territory').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                addTerritory();
            }
        });

        // Initial render
        renderTerritories();
    </script>
</x-app-layout>
