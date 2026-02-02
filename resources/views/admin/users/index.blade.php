<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl leading-tight">{{ __('Users') }}</h2>
            <x-primary-button href="{{ route('admin.users.create') }}">{{ __('Create User') }}</x-primary-button>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <div class="glass p-4">
                <div class="overflow-x-auto">
                    <table class="w-full cyber-table">
                        <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Admin') }}</th>
                                <th>{{ __('Fonction') }}</th>
                                <th>{{ __('Commune') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->is_admin ? __('Yes') : __('No') }}</td>
                                <td>{{ $user->fonction }}</td>
                                <td>{{ $user->commune }}</td>
                                <td class="flex items-center gap-2">
                                    <x-secondary-button href="{{ route('admin.users.edit', $user) }}">{{ __('Edit') }}</x-secondary-button>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('{{ __('Delete user?') }}');">
                                        @csrf
                                        @method('DELETE')
                                        <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($users->hasPages())
                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
