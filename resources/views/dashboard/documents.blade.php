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
                                <button type="button" onclick="window.openDocument({embed: {{ json_encode(route('documents.embed', $doc)) }}, info: {{ json_encode(route('documents.info', $doc)) }}, download: {{ json_encode(route('documents.download', $doc)) }}, title: {{ json_encode($doc->title) }}})" class="inline-flex px-3 py-1 neon-btn">{{ __('View') }}</button>
                                <a href="{{ route('documents.download', $doc) }}" class="inline-flex px-3 py-1 neon-btn">{{ __('Download') }}</a>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            @includeWhen(View::exists('documents._preview_modal'), 'documents._preview_modal')
        </div>
    </div>
</div>