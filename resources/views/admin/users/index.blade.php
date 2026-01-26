<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl neon-h1 leading-tight tracking-tight">{{ __('Users') }}</h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.users.create') }}" class="px-4 py-2 neon-btn">{{ __('Create User') }}</a>

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-800 text-green-100 rounded">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-gradient-to-bl from-gray-900 to-slate-900 accent-gradient shadow sm:rounded-lg p-4 glass">
                <div class="overflow-x-auto rounded-md">
                <table class="w-full text-gray-100 divide-y divide-gray-700 cyber-table">
                    <thead>
                        <tr>
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">{{ __('ID') }}</th>
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">{{ __('Name') }}</th>
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">{{ __('Email') }}</th>
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">{{ __('Admin') }}</th>
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody class="bg-transparent">
                        @foreach($users as $user)
                        <tr class="odd:bg-transparent even:bg-gray-800/20">
                            <td class="py-3 px-4">{{ $user->id }}</td>
                            <td class="py-3 px-4">{{ $user->name }}</td>
                            <td class="py-3 px-4">{{ $user->email }}</td>
                            <td class="py-3 px-4">{{ $user->is_admin ? __('Yes') : __('No') }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-2 py-1 bg-gradient-to-r from-indigo-600 to-cyan-500 hover:from-indigo-500 hover:to-cyan-400 text-white rounded text-sm">{{ __('Edit') }}</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block" onsubmit="return confirm('{{ __('Delete user?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-gradient-to-r from-red-600 to-rose-500 hover:from-red-500 hover:to-rose-400 text-white rounded text-sm ms-2">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>

                <div class="mt-4">
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
