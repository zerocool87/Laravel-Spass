<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-2xl text-gray-100 leading-tight tracking-tight">{{ __('Users') }}</h2>
            <a href="{{ route('admin.users.create') }}" class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-semibold rounded-lg shadow">Create User</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.users.create') }}" class="px-4 py-2 bg-gradient-to-r from-indigo-600 to-indigo-500 hover:from-indigo-500 hover:to-indigo-400 text-white font-semibold rounded-lg shadow">Create User</a>
            </div>

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-800 text-green-100 rounded">
                {{ session('success') }}
            </div>
            @endif

            <div class="bg-gray-800 shadow sm:rounded-lg p-4">
                <div class="overflow-x-auto rounded-md">
                <table class="w-full text-gray-100 divide-y divide-gray-700">
                    <thead>
                        <tr class="bg-gray-900">
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">ID</th>
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">Name</th>
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">Email</th>
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">Admin</th>
                            <th class="text-left py-3 px-4 text-sm uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-gray-800">
                        @foreach($users as $user)
                        <tr class="odd:bg-gray-800 even:bg-gray-700">
                            <td class="py-3 px-4">{{ $user->id }}</td>
                            <td class="py-3 px-4">{{ $user->name }}</td>
                            <td class="py-3 px-4">{{ $user->email }}</td>
                            <td class="py-3 px-4">{{ $user->is_admin ? 'Yes' : 'No' }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('admin.users.edit', $user) }}" class="inline-flex items-center px-2 py-1 bg-indigo-600 hover:bg-indigo-500 text-white rounded text-sm">Edit</a>
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Delete user?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-1 bg-red-600 hover:bg-red-500 text-white rounded text-sm ms-2">Delete</button>
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
