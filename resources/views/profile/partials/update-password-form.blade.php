<div class="bg-white rounded-xl shadow-lg border border-[#faa21b]/20 p-6 mt-6">
    <form method="post" action="{{ route('password.update') }}" class="space-y-6">
        @csrf
        @method('put')

        <div>
            <h2 class="text-xl font-semibold text-gray-900 mb-6">{{ __('Changer le mot de passe') }}</h2>
        </div>

        <div class="grid grid-cols-1 gap-6">
            <div>
                <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Mot de passe actuel') }} *</label>
                <input type="password" name="current_password" id="update_password_current_password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nouveau mot de passe') }} *</label>
                <input type="password" name="password" id="update_password_password" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Confirmer le mot de passe') }} *</label>
                <input type="password" name="password_confirmation" id="update_password_password_confirmation" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                @error('password_confirmation')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 pt-4">
            <button type="submit" class="inline-flex items-center justify-center px-6 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl transition">
                {{ __('Enregistrer les modifications') }}
            </button>
        </div>
    </form>
</div>
