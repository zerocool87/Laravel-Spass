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

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Profil')]]" />
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @include('profile.partials.update-profile-information-form')

            @include('profile.partials.update-password-form')
        </div>
    </div>
</x-app-layout>
