<x-guest-layout>
    <div class="bg-[#faa21b] px-6 py-4 rounded-t-lg mb-4">
        <h2 class="text-lg font-bold text-white text-center">{{ __('Inscription') }}</h2>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nom')" class="text-[#faa21b] font-semibold" />
            <x-text-input id="name" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" class="text-[#faa21b] font-semibold" />
            <x-text-input id="email" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" class="text-[#faa21b] font-semibold" />

            <x-text-input id="password" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" class="text-[#faa21b] font-semibold" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-[#faa21b] hover:text-[#e89315] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#faa21b]" href="{{ route('login') }}">
                {{ __('Déjà inscrit ?') }}
            </a>

            <x-primary-button class="ms-4 bg-[#faa21b] hover:bg-[#e89315]">
                {{ __('S\'inscrire') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
