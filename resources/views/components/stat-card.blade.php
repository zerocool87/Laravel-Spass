@props(['label', 'value', 'icon', 'color' => 'orange'])

@php
$colors = [
    'orange' => 'bg-[#faa21b]/10 text-[#b36b00]',
    'emerald' => 'bg-emerald-100 text-emerald-700',
    'blue' => 'bg-blue-100 text-blue-700',
    'purple' => 'bg-purple-100 text-purple-700',
];
$iconClasses = $colors[$color] ?? $colors['orange'];
@endphp

<div class="bg-white rounded-xl shadow border border-[#faa21b]/20 p-3 sm:p-4 flex items-center gap-3">
    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg flex items-center justify-center shrink-0 {{ $iconClasses }}">
        {!! $icon !!}
    </div>
    <div class="min-w-0">
        <p class="text-2xl sm:text-3xl font-bold text-gray-900 truncate leading-tight">{{ $value }}</p>
        <p class="text-sm sm:text-base text-gray-600 truncate font-medium leading-tight">{{ $label }}</p>
    </div>
</div>
