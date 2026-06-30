@props(['title', 'linkUrl', 'linkText'])

<div class="bg-white rounded-xl shadow-lg border-2 border-[#faa21b]/15 flex flex-col overflow-hidden hover:shadow-xl transition-shadow">
    <div class="px-4 py-3 bg-[#faa21b]/10 border-b-[1.5px] border-[#faa21b]/20 flex items-center justify-between gap-2">
        <h3 class="text-sm font-bold text-[#faa21b] flex items-center gap-2">
            {{ $icon }}
            {{ $title }}
        </h3>
        <a href="{{ $linkUrl }}" class="text-xs font-semibold text-[#faa21b] hover:text-[#e89315] flex items-center gap-1 transition">
            {{ $linkText }}
            <x-icon.chevron-right class="w-3 h-3" />
        </a>
    </div>
    <div class="divide-y divide-orange-50 overflow-y-auto flex-1">
        {{ $slot }}
    </div>
</div>
