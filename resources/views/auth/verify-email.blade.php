<x-guest-layout>
    <div class="bg-[#faa21b] px-6 py-4 rounded-t-lg mb-4">
        <h2 class="text-lg font-bold text-white text-center">{{ __('Vérification d\'email') }}</h2>
    </div>

    <div class="mb-4 text-sm text-[#faa21b]">
        {{ __('Merci de vous être inscrit ! Avant de commencer, pourriez-vous vérifier votre adresse e-mail en cliquant sur le lien que nous venons de vous envoyer par e-mail ? Si vous n\'avez pas reçu l\'e-mail, nous vous enverrons volontiers un autre.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            {{ __('Un nouveau lien de vérification a été envoyé à l\'adresse e-mail que vous avez fournie lors de l\'inscription.') }}
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button class="bg-[#faa21b] hover:bg-[#e89315]">
                    {{ __('Renvoyer l\'email de vérification') }}
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-[#faa21b] hover:text-[#e89315] rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-[#faa21b]">
                {{ __('Se déconnecter') }}
            </button>
        </form>
    </div>
</x-guest-layout>
