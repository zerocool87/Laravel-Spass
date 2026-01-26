<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100">{{ __('Library') }}</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="glass p-4">
                @if($documents->isEmpty())
                    <p class="text-cyan-200">{{ __('No documents available.') }}</p>
                @else
                    <ul class="space-y-3">
                        @foreach($documents as $doc)
                            <li class="flex items-center justify-between">
                                <div>
                                    <div class="font-semibold">{{ $doc->title }}</div>
                                    <div class="text-sm text-cyan-300">{{ $doc->description }}</div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button type="button" onclick="window.openDocument({embed: {{ json_encode(route('documents.embed', $doc)) }}, info: {{ json_encode(route('documents.info', $doc)) }}, download: {{ json_encode(route('documents.download', $doc)) }}, title: {{ json_encode($doc->title) }}})" class="inline-flex px-3 py-1 bg-gray-800 text-white rounded-md">{{ __('View') }}</button>
                                    <a href="{{ route('documents.download', $doc) }}" class="inline-flex px-3 py-1 bg-gray-800 text-white rounded-md">{{ __('Download') }}</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4">
                        {{ $documents->links() }}
                    </div>
                @endif
                @include('documents._preview_modal')            </div>
        </div>
    </div>
</x-app-layout>