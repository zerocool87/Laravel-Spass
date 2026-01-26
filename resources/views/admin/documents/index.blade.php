<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl neon-h1">{{ __('Documents') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-4">
                <a href="{{ route('admin.documents.create') }}" class="neon-btn">{{ __('Upload Document') }}</a>

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-800 text-green-100 rounded">
                {{ session('success') }}
            </div>
            @endif

            <div class="glass p-4">
                <table class="w-full cyber-table">
                    <thead>
                        <tr>
                            <th class="py-2 text-left">{{ __('Title') }}</th>
                            <th class="py-2 text-left">{{ __('Uploaded by') }}</th>
                            <th class="py-2 text-left">{{ __('Visible to all') }}</th>
                            <th class="py-2 text-left">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                        <tr class="border-t border-gray-700">
                            <td class="py-2">{{ $doc->title }}</td>
                            <td class="py-2">{{ $doc->creator?->name }}</td>
                            <td class="py-2">{{ $doc->visible_to_all ? __('Yes') : __('Restricted') }}</td>
                            <td class="py-2">
                                <a href="{{ route('admin.documents.edit', $doc) }}" class="inline-flex px-2 py-1 bg-indigo-600 text-white rounded">{{ __('Edit') }}</a>
                                <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('{{ __('Delete document?') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex px-2 py-1 bg-red-600 text-white rounded ms-2">{{ __('Delete') }}</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="mt-4">
                    {{ $documents->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
