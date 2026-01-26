<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl neon-h1">{{ __('Library') }}</h2>
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
                                    <a href="{{ route('documents.download', $doc) }}" class="inline-flex px-3 py-1 neon-btn">Download</a>
                                </div>
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-4">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>