<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Nouvelle instance') }}"
            subtitle="{{ __('Créer une nouvelle commission ou comité') }}"
            icon="➕"
            :backRoute="route('elus.instances.index')"
            :backLabel="__('Retour aux instances')"
            activeSection="instances"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-lg shadow p-6">
                <form method="POST" action="{{ route('elus.instances.store') }}">
                    @csrf

                    <div class="space-y-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">{{ __('Nom') }} *</label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="type" class="block text-sm font-medium text-gray-700">{{ __('Type') }} *</label>
                            <select name="type" id="type" required class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
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
                            <input type="text" name="territory" id="territory" value="{{ old('territory') }}" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">
                            @error('territory')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700">{{ __('Description') }}</label>
                            <textarea name="description" id="description" rows="4" class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b]">{{ old('description') }}</textarea>
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
