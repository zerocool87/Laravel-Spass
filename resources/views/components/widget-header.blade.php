@props([
    'title',
    'icon' => '',
    'link' => null,
    'linkText' => '',
    'linkIcon' => '→'
])

<div class="px-6 py-4 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between rounded-t-xl">
    <h3 class="text-lg font-bold text-[#faa21b]">
        @if($icon) {{ $icon }} @endif {{ $title }}
    </h3>

    {{-- Allow either a simple link or an actions slot for arbitrary controls (forms/buttons) --}}
    @if($link)
    <a href="{{ $link }}" class="text-sm text-[#faa21b] hover:text-[#e89315] font-semibold flex items-center">
        {{ $linkText }}
        @if($linkIcon)
        <span class="ml-1">{{ $linkIcon }}</span>
        @endif
    </a>
    @else
        @isset($actions)
            <div class="widget-header-actions">
                {{ $actions }}
            </div>
        @endisset
    @endif
</div>
