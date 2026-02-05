<x-app-layout>
    <x-slot name="header">
        <x-admin-header
            title="{{ __('Users') }}"
            icon="👥"
        >
            <x-slot name="actions">
                <x-primary-button href="{{ route('admin.users.create') }}">{{ __('Create User') }}</x-primary-button>
            </x-slot>
        </x-admin-header>
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
                                <th>{{ __('Rôles') }}</th>
                                <th>{{ __('Fonction') }}</th>
                                <th>{{ __('Commune') }}</th>
                                <th>{{ __('Territoire') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->is_admin)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">Admin</span>
                                    @endif
                                    @if($user->is_elu)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Élu</span>
                                    @endif
                                    @if(!$user->is_admin && !$user->is_elu)
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td>{{ $user->fonction }}</td>
                                <td>{{ $user->commune }}</td>
                                <td>{{ $user->territory ?? '-' }}</td>
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
