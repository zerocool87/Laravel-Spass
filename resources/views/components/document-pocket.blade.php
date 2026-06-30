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

<div
    class="pocket-card group relative bg-white rounded-xl border-2 border-gray-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-200 overflow-hidden"
    style="--cat-rgb: {{ $rgb }}"
>
    <div class="absolute left-0 top-0 bottom-0 w-2 {{ $accentClass }} rounded-l-xl"></div>

    <div class="absolute inset-0 opacity-[0.03]" style="background-color: rgb({{ $rgb }})"></div>

    <div class="relative pl-6 p-5">
        <div class="flex items-start gap-4 mb-3">
            <div
                class="w-14 h-14 rounded-2xl flex items-center justify-center text-2xl shrink-0 shadow-sm"
                style="background-color: rgba({{ $rgb }}, 0.12); color: rgb({{ $rgb }})"
            >
                {{ $fileIcon }}
            </div>

            <div class="min-w-0 flex-1 pt-0.5">
                <h4 class="text-base font-bold text-gray-900 leading-snug group-hover:text-[rgb(var(--cat-rgb))] transition-colors line-clamp-2 mb-1.5">
                    {{ $document->title }}
                </h4>

                <div class="flex items-center gap-2.5 flex-wrap">
                    <span
                        class="text-xs font-semibold px-2.5 py-0.5 rounded-full border"
                        style="background-color: rgba({{ $rgb }}, 0.1); color: rgb({{ $rgb }}); border-color: rgba({{ $rgb }}, 0.2)"
                    >
                        {{ $document->category }}
                    </span>
                    <span class="text-xs text-gray-400">
                        📅 {{ $document->created_at->format('d/m/Y') }}
                    </span>
                    @if($document->creator)
                        <span class="text-xs text-gray-400">·</span>
                        <span class="text-xs text-gray-400">👤 {{ $document->creator->name }}</span>
                    @endif
                </div>
            </div>
        </div>

        @if($document->description)
            <p class="text-sm text-gray-500 leading-relaxed line-clamp-3 mb-3">
                {{ $document->description }}
            </p>
        @endif

        <div class="flex items-center justify-between gap-3 pt-3 border-t border-gray-100/80 mt-2">
            <div>
                @if($document->visible_to_all)
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200/60 shadow-sm">
                        ✅ {{ __('Public') }}
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 text-xs font-semibold text-orange-700 bg-orange-50 px-2.5 py-1 rounded-full border border-orange-200/60 shadow-sm">
                        🔒 {{ __('Privé') }}
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-2">
                <button
                    type="button"
                    onclick="window.openDocument({
                        embed: {{ json_encode(route('documents.embed', $document)) }},
                        info: {{ json_encode(route('documents.info', $document)) }},
                        download: {{ json_encode(route('documents.download', $document)) }},
                        title: {{ json_encode($document->title) }}
                    })"
                    class="inline-flex items-center gap-1.5 text-xs font-semibold px-3.5 py-2 rounded-lg border-2 border-gray-200 text-gray-600 bg-white hover:border-[rgb(var(--cat-rgb))]/40 hover:text-[rgb(var(--cat-rgb))] hover:bg-[rgb(var(--cat-rgb))]/5 transition-all shadow-sm"
                >
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    <span>{{ __('Voir') }}</span>
                </button>

                <a
                    href="{{ route('documents.download', $document) }}"
                    target="_blank"
                    rel="noopener"
                    class="inline-flex items-center gap-1.5 text-xs font-bold px-3.5 py-2 rounded-lg text-white shadow-md hover:shadow-lg hover:opacity-90 transition-all"
                    style="background-color: rgb({{ $rgb }})"
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
