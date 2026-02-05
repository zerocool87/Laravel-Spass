<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="bg-[#faa21b] px-6 py-4 rounded-t-lg mb-4">
        <h2 class="text-lg font-bold text-white text-center">{{ __('Connexion') }}</h2>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[#faa21b] font-semibold" />
            <x-text-input id="email" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Mot de passe')" class="text-[#faa21b] font-semibold" />

            <x-text-input id="password" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]"
                            type="password"
                            name="password"
                            required autocomplete="current-password" />

            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="block mt-4">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded border-[#faa21b] text-[#faa21b] shadow-sm focus:ring-[#faa21b]" name="remember">
                <span class="ms-2 text-sm text-[#faa21b]">{{ __('Se souvenir de moi') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            @if (Route::has('password.request'))
                <a class="underline text-sm text-[#faa21b] hover:text-[#e89315] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#faa21b]" href="{{ route('password.request') }}">
                    {{ __('Mot de passe oublié ?') }}
                </a>
            @endif

            <x-primary-button class="ms-3 bg-[#faa21b] hover:bg-[#e89315]">
                {{ __('Se connecter') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
