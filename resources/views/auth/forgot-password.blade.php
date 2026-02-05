<x-guest-layout>
    <div class="bg-[#faa21b] px-6 py-4 rounded-t-lg mb-4">
        <h2 class="text-lg font-bold text-white text-center">{{ __('Mot de passe oublié') }}</h2>
    </div>

    <div class="mb-4 text-sm text-[#faa21b]">
        {{ __('Vous avez oublié votre mot de passe ? Aucun problème. Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation de mot de passe qui vous permettra d\'en choisir un nouveau.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[#faa21b] font-semibold" />
            <x-text-input id="email" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]" type="email" name="email" :value="old('email')" required autofocus />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="bg-[#faa21b] hover:bg-[#e89315]">
                {{ __('Envoyer le lien de réinitialisation') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
