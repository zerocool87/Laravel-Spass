<section class="space-y-6 glass">
    <header>
        <h2 class="text-lg font-semibold text-gray-900">
            {{ __('Supprimer le compte') }}
        </h2>
        <p class="mt-1 text-sm text-gray-500">
            {{ __('Une fois supprimé, toutes vos données seront définitivement effacées. Téléchargez ce que vous souhaitez conserver avant de supprimer votre compte.') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __('Delete Account') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
            @csrf
            @method('delete')

            <h2 class="text-lg font-semibold text-gray-900">
                {{ __('Êtes-vous sûr de vouloir supprimer votre compte ?') }}
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                {{ __('Toutes vos données seront définitivement effacées. Veuillez entrer votre mot de passe pour confirmer la suppression.') }}
            </p>

            <div class="mt-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="sr-only" />

                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    class="mt-1 block w-3/4"
                    placeholder="{{ __('Password') }}"
                />

                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="mt-6 flex justify-end gap-3">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __('Annuler') }}
                </x-secondary-button>
                <x-danger-button class="ms-3">
                    {{ __('Supprimer le compte') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
