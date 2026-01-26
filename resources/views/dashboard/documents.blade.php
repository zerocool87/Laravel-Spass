<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="glass p-4">
            <h3 class="neon-h1 text-lg mb-4">{{ __('Library') }}</h3>

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
                                <a href="{{ route('documents.download', $doc) }}" class="inline-flex px-3 py-1 neon-btn">{{ __('Download') }}</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>
</div>