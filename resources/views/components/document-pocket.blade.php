@props(['document'])

@php
    $categoryColors = config('documents.category_colors', []);
    $accentClass = $categoryColors[$document->category] ?? 'bg-[#faa21b]';

    $colorMap = [
        'bg-amber-600' => '217, 119, 6',
        'bg-amber-500' => '245, 158, 11',
        'bg-emerald-600' => '5, 150, 105',
        'bg-cyan-600' => '8, 145, 178',
        'bg-rose-600' => '225, 29, 72',
        'bg-sky-600' => '2, 132, 199',
        'bg-[#faa21b]' => '250, 162, 27',
    ];
    $rgb = $colorMap[$accentClass] ?? '250, 162, 27';

    $mime = $document->getMimeType();
    if ($mime) {
        if (str_starts_with($mime, 'application/pdf')) {
            $fileIcon = '📕';
        } elseif (str_starts_with($mime, 'image/')) {
            $fileIcon = '🖼️';
        } elseif (str_starts_with($mime, 'text/')) {
            $fileIcon = '📃';
        } elseif (str_contains($mime, 'spreadsheet') || str_contains($mime, 'excel')) {
            $fileIcon = '📊';
        } elseif (str_contains($mime, 'wordprocessing') || str_contains($mime, 'document')) {
            $fileIcon = '📝';
        } else {
            $fileIcon = '📄';
        }
    } else {
        $fileIcon = '📄';
    }
@endphp

<div class="pocket-card group relative bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-md hover:border-[#faa21b]/40 hover:-translate-y-0.5 transition-all duration-200 overflow-hidden">
    <div class="h-1 {{ $accentClass }}"></div>

    <div class="p-4 relative z-10">
        <div class="flex items-start gap-3 mb-3">
            <div
                class="w-10 h-10 rounded-lg flex items-center justify-center text-lg shrink-0"
                style="background-color: rgba({{ $rgb }}, 0.1); color: rgb({{ $rgb }})"
            >
                {{ $fileIcon }}
            </div>
            <div class="min-w-0 flex-1">
                <h4 class="font-semibold text-gray-900 truncate group-hover:text-[#faa21b] transition-colors">
                    {{ $document->title }}
                </h4>
                @if($document->description)
                    <p class="text-sm text-gray-500 line-clamp-2 mt-0.5">{{ $document->description }}</p>
                @endif
            </div>
        </div>

        <div class="flex items-center gap-2 text-xs text-gray-400 mb-3">
            <span>{{ $document->created_at->format('d/m/Y') }}</span>
            @if($document->creator)
                <span class="text-gray-300">·</span>
                <span>{{ $document->creator->name }}</span>
            @endif
        </div>

        <div class="flex items-center justify-between gap-2">
            @if($document->visible_to_all)
                <span class="text-xs font-medium text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                    {{ __('Public') }}
                </span>
            @else
                <span class="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full border border-orange-200">
                    {{ __('Privé') }}
                </span>
            @endif
            <div class="flex items-center gap-1.5">
                <button
                    type="button"
                    onclick="window.openDocument({
                        embed: {{ json_encode(route('documents.embed', $document)) }},
                        info: {{ json_encode(route('documents.info', $document)) }},
                        download: {{ json_encode(route('documents.download', $document)) }},
                        title: {{ json_encode($document->title) }}
                    })"
                    class="text-xs px-3 py-1.5 rounded-lg border border-[#faa21b]/30 text-[#faa21b] font-medium hover:bg-[#faa21b]/10 transition-colors"
                >
                    {{ __('Voir') }}
                </button>
                <a
                    href="{{ route('documents.download', $document) }}"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-1 text-xs px-3 py-1.5 rounded-lg bg-[#faa21b] text-white font-medium hover:bg-[#e89315] transition-colors shadow-sm"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    <span class="hidden sm:inline">{{ __('Télécharger') }}</span>
                </a>
            </div>
        </div>
    </div>
</div>
