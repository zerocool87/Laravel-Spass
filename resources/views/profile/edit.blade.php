<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Modifier le profil') }}"
            subtitle="{{ __('Gestion du compte') }}"
            icon="✏️"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="profile"
            :showNav="true"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
        </div>
    </div>
</x-app-layout>
