<div class="bg-white rounded-xl shadow-lg border border-[#faa21b]/20 p-4">
    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
        @csrf
        @method('patch')

        <div>
            <h2 class="text-base font-semibold text-gray-900">{{ __('Modifier les informations du profil') }}</h2>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label for="prenom" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Prénom') }}</label>
                <input type="text" name="prenom" id="prenom" value="{{ old('prenom', $user->prenom) }}" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
            </div>
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom') }} *</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Email') }} *</label>
            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required class="w-full rounded-lg border-gray-300 shadow-sm focus:border-[#faa21b] focus:ring-[#faa21b] px-3 py-2">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <p class="text-sm mt-2 text-gray-500">
                    {{ __("Votre adresse e-mail n'est pas vérifiée") }}.
                    <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand">
                        {{ __("Cliquez ici pour renvoyer l'e-mail de vérification") }}.
                    </button>
                </p>
                @if (session('status') === 'verification-link-sent')
                    <p class="mt-1 font-medium text-sm text-green-600">
                        {{ __('Un nouveau lien de vérification a été envoyé à votre adresse e-mail') }}.
                    </p>
                @endif
            @endif
        </div>

        <div class="flex pt-1">
            <button type="submit" class="inline-flex items-center justify-center px-4 py-2 bg-[#faa21b] text-white rounded-lg font-semibold text-sm shadow hover:bg-[#f39b14] transition">
                {{ __('Enregistrer') }}
            </button>
        </div>
    </form>
</div>
