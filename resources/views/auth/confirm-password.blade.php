<x-guest-layout>
    <div class="bg-[#faa21b] px-6 py-4 rounded-t-lg mb-4">
        <h2 class="text-lg font-bold text-white text-center">{{ __('Confirmation de mot de passe') }}</h2>
    </div>

    <div class="mb-4 text-sm text-[#faa21b]">
        {{ __('Ceci est une zone sécurisée de l\'application. Veuillez confirmer votre mot de passe avant de continuer.') }}
    </div>

    <form method="POST" action="{{ route('password.confirm') }}">
        @csrf

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" class="text-[#faa21b] font-semibold" />

            <x-text-input id="password" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex justify-end mt-4">
            <x-primary-button class="bg-[#faa21b] hover:bg-[#e89315]">
                {{ __('Confirmer') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
