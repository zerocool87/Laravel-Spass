<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl leading-tight">{{ __('Create User') }}</h2>
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