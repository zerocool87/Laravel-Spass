<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Modifier un utilisateur') }}"
            subtitle="{{ __('Administration des élus') }}"
            icon="✏️"
            :backRoute="route('elus.admin.users')"
            :backLabel="__('Retour à la liste des utilisateurs')"
            activeSection="admin"
            :showNav="false"
        >
        </x-elus-header>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-xl shadow-lg border border-[#faa21b]/20 p-6">
                <form method="POST" action="{{ route('elus.admin.users.update', $user) }}">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-6">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('Modifier l\'utilisateur') }}</h2>
                        </div>

                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom complet') }} *</label>
                                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                @error('name')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} *</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="fonction" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Fonction') }}</label>
                                <input type="text" name="fonction" id="fonction" value="{{ old('fonction', $user->fonction) }}" placeholder="{{ __('Ex: Maire, Conseiller municipal...') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                @error('fonction')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="commune" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Commune') }}</label>
                                <select name="commune" id="commune" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                    <option value="">{{ __('Sélectionner une commune') }}</option>
                                    @foreach($communes as $commune)
                                        <option value="{{ $commune }}" {{ old('commune', $user->commune) === $commune ? 'selected' : '' }}>{{ $commune }}</option>
                                    @endforeach
                                </select>
                                @error('commune')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nouveau mot de passe') }}</label>
                                <input type="password" name="password" id="password" placeholder="{{ __('Laisser vide pour garder le mot de passe actuel') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                @error('password')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirmer le mot de passe') }}</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" placeholder="{{ __('Confirmer le nouveau mot de passe') }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                                @error('password_confirmation')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <p class="mt-2 text-sm text-gray-500">{{ __('Laisser vide pour ne pas modifier le mot de passe') }}</p>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_elu" id="is_elu" value="1" {{ old('is_elu', $user->is_elu) ? 'checked' : '' }} class="h-4 w-4 text-[#faa21b] border-gray-300 rounded focus:ring-[#faa21b]">
                                <label for="is_elu" class="ml-2 block text-sm text-gray-700">{{ __('Élu') }}</label>
                            </div>

                            <div class="flex items-center">
                                <input type="checkbox" name="is_admin" id="is_admin" value="1" {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} class="h-4 w-4 text-[#faa21b] border-gray-300 rounded focus:ring-[#faa21b]">
                                <label for="is_admin" class="ml-2 block text-sm text-gray-700">{{ __('Administrateur') }}</label>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-4 pt-4">
                            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition">
                                {{ __('Enregistrer les modifications') }}
                            </button>
                            <a href="{{ route('elus.admin.users') }}" class="inline-flex items-center justify-center px-6 py-2.5 bg-gray-200 text-gray-700 rounded-xl font-semibold text-sm hover:bg-gray-300 transition">
                                {{ __('Annuler') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
