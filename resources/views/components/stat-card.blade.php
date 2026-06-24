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

<div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-4 sm:p-5 flex items-center gap-4">
    <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-xl flex items-center justify-center shrink-0 {{ $iconClasses }}">
        {!! $icon !!}
    </div>
    <div class="min-w-0">
        <p class="text-lg sm:text-2xl font-bold text-gray-900 truncate">{{ $value }}</p>
        <p class="text-xs sm:text-sm text-gray-500 truncate">{{ $label }}</p>
    </div>
</div>
