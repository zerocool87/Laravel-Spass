<x-app-layout>
    <x-slot name="header">
        <x-admin-header
            title="{{ __('Edit User') }}"
            icon="✏️"
            :backRoute="route('admin.users.index')"
            :backLabel="__('Retour aux utilisateurs')"
        />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-4">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @method('PATCH')
                    @include('admin.users.form', ['user' => $user])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
