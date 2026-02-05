<x-guest-layout>
    <div class="bg-[#faa21b] px-6 py-4 rounded-t-lg mb-4">
        <h2 class="text-lg font-bold text-white text-center">{{ __('Réinitialiser le mot de passe') }}</h2>
    </div>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" class="text-[#faa21b] font-semibold" />
            <x-text-input id="email" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Nouveau mot de passe')" class="text-[#faa21b] font-semibold" />
            <x-text-input id="password" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirmer le nouveau mot de passe')" class="text-[#faa21b] font-semibold" />

            <x-text-input id="password_confirmation" class="block mt-1 w-full border-[#faa21b]/30 focus:border-[#faa21b] focus:ring-[#faa21b]"
                                type="password"
                                name="password_confirmation" required autocomplete="new-password" />

            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="bg-[#faa21b] hover:bg-[#e89315]">
                {{ __('Réinitialiser le mot de passe') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
