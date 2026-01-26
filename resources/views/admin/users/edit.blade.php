<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Edit User') }}</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-gray-800 shadow sm:rounded-lg p-4">
                <form action="{{ route('admin.users.update', $user) }}" method="POST">
                    @method('PATCH')
                    @include('admin.users.form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
