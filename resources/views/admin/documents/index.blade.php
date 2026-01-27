<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100">{{ __('Documents') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-800 text-green-100 rounded">
                {{ session('success') }}
            </div>
            @endif

            <div class="glass p-4">
                <div class="mb-4 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <x-primary-button href="{{ route('admin.documents.create') }}">{{ __('Upload Document') }}</x-primary-button>

                        <form method="GET" action="{{ route('admin.documents.index') }}" class="flex items-center gap-2">
                            <select name="category" class="block bg-gray-800 text-gray-100 p-2 rounded">
                                <option value="">-- {{ __('All categories') }} --</option>
                                @foreach(config('documents.categories', []) as $cat)
                                    <option value="{{ $cat }}" {{ isset($category) && $category === $cat ? 'selected' : '' }}>{{ __($cat) }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="inline-flex items-center px-3 py-1 bg-gray-700 text-white rounded">{{ __('Filter') }}</button>
                            @if(!empty($category))
                                <a href="{{ route('admin.documents.index') }}" class="inline-flex items-center px-3 py-1 bg-gray-600 text-white rounded">{{ __('Clear') }}</a>
                            @endif
                        </form>
                    </div>
                    <div class="text-sm text-gray-300">{{ $documents->total() }} {{ __('documents') }}</div>
                </div>

                <table class="w-full cyber-table">
                    <thead>
                        <tr>
                            <th class="py-2 text-left">{{ __('Title') }}</th>
                            <th class="py-2 text-left">{{ __('Category') }}</th>
                            <th class="py-2 text-left">{{ __('Uploaded by') }}</th>
                            <th class="py-2 text-left">{{ __('Visible to all') }}</th>
                            <th class="py-2 text-left">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                        <tr class="border-t border-gray-700">
                            <td class="py-2">{{ $doc->title }}</td>
                            <td class="py-2"><x-category-badge :category="$doc->category" /></td>
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
