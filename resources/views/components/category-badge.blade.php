@props(['category' => null])

@if(!$category)
    <span class="text-xs text-gray-400">—</span>
@else
    @php
        $colors = config('documents.category_colors', []);
        $icons = config('documents.category_icons', []);
        $infoColor = $colors[$category] ?? 'bg-gray-600';
        $icon = $icons[$category] ?? '';
    @endphp

    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $infoColor }} text-white" aria-label="{{ __($category) }}">
        @if(!empty($icon))
            {!! $icon !!}
        @endif
        <span class="ml-2">{{ __($category) }}</span>
    </span>
@endif
