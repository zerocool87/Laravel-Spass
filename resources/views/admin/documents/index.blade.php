<x-app-layout>
    <x-slot name="header">
        <x-admin-header
            title="{{ __('Documents') }}"
            icon="📄"
        />
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
            @endif

            <div class="glass p-4">
                <div class="mb-4 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-4">
                        <x-primary-button href="{{ route('admin.documents.create') }}">{{ __('Upload Document') }}</x-primary-button>
                    </div>
                    <form method="GET" action="{{ route('admin.documents.index') }}" class="flex items-center gap-2">
                        <select name="category" class="mt-1">
                            <option value="">-- {{ __('All categories') }} --</option>
                            @foreach(config('documents.categories', []) as $cat)
                                <option value="{{ $cat }}" {{ isset($category) && $category === $cat ? 'selected' : '' }}>{{ __($cat) }}</option>
                            @endforeach
                        </select>
                        <x-secondary-button type="submit">{{ __('Filter') }}</x-secondary-button>
                        @if(!empty($category))
                            <x-secondary-button href="{{ route('admin.documents.index') }}">{{ __('Clear') }}</x-secondary-button>
                        @endif
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full cyber-table">
                        <thead>
                            <tr>
                                <th>{{ __('Title') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Uploaded by') }}</th>
                                <th>{{ __('Visible to all') }}</th>
                                <th>{{ __('Assigned users') }}</th>
                                <th class="w-48">{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documents as $doc)
                            <tr>
                                <td>{{ $doc->title }}</td>
                                <td><x-category-badge :category="$doc->category" /></td>
                                <td>{{ $doc->creator?->name }}</td>
                                <td>{{ $doc->visible_to_all ? __('Yes') : __('Restricted') }}</td>
                                <td>
                                    @if($doc->visible_to_all)
                                        <span class="text-muted">—</span>
                                    @else
                                        @php $users = $doc->users; @endphp
                                        @if($users->isEmpty())
                                            <span class="text-muted">{{ __('Aucun utilisateur assigné') }}</span>
                                        @else
                                            <ul class="list-disc list-inside text-xs text-gray-700">
                                                @foreach($users as $user)
                                                    <li>{{ $user->name }} &lt;{{ $user->email }}&gt;</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    @endif
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex items-center min-w-[70px] justify-start">
                                            @if($doc->visible_to_all)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ __('Public') }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-orange-50 text-orange-700 border border-orange-200">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                    {{ __('Privé') }}
                                                </span>
                                            @endif
                                        </span>
                                        <x-secondary-button href="{{ route('admin.documents.edit', $doc) }}">{{ __('Edit') }}</x-secondary-button>
                                        <form action="{{ route('admin.documents.destroy', $doc) }}" method="POST" onsubmit="return confirm('{{ __('Delete document?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button type="submit">{{ __('Delete') }}</x-danger-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($documents->hasPages())
                    <div class="mt-4">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
