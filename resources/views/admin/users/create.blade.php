<x-app-layout>
    <x-slot name="header">
        <x-admin-header
            title="{{ __('Create User') }}"
            icon="➕"
            :backRoute="route('admin.users.index')"
            :backLabel="__('Retour aux utilisateurs')"
        />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-4">
                <form action="{{ route('admin.users.store') }}" method="POST">
                    @include('admin.users.form', ['user' => new \App\Models\User()])
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
