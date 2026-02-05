<div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="glass p-4">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ __('Library') }}</h3>

            @if($documents->isEmpty())
                <p class="text-gray-500">{{ __('No documents available.') }}</p>
            @else
                <ul class="space-y-3">
                    @foreach($documents as $doc)
                        <li class="flex items-center justify-between">
                            <div>
                                <div class="font-semibold">{{ $doc->title }}</div>
                                <div class="text-sm text-cyan-300">{{ $doc->description }}</div>
                            </div>
                            <div class="flex items-center gap-2">
                                <x-secondary-button type="button" onclick="window.openDocument({embed: {{ json_encode(route('documents.embed', $doc)) }}, info: {{ json_encode(route('documents.info', $doc)) }}, download: {{ json_encode(route('documents.download', $doc)) }}, title: {{ json_encode($doc->title) }}})">{{ __('View') }}</x-secondary-button>
                                <x-primary-button href="{{ route('documents.download', $doc) }}" target="_blank" rel="noopener">{{ __('Download') }}</x-primary-button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif

            @includeWhen(View::exists('documents._preview_modal'), 'documents._preview_modal')
        </div>
    </div>
</div>
