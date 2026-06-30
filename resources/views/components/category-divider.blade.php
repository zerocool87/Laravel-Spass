@props(['category', 'count' => 0, 'color' => 'bg-amber-600', 'icon' => '📄'])

<div class="bg-white rounded-xl shadow border border-gray-200/80 overflow-hidden" x-data="{ open: true }">
    <div
        class="flex items-center justify-between gap-2 px-4 py-2.5 cursor-pointer select-none transition-opacity hover:opacity-90 text-white text-sm font-bold {{ $color }}"
        @click="open = !open"
        role="button"
        tabindex="0"
        :aria-expanded="open"
        @keydown.enter.prevent="open = !open"
        @keydown.space.prevent="open = !open"
    >
        <div class="flex items-center gap-2 min-w-0">
            <span>{{ $icon }}</span>
            <span class="truncate">{{ $category }}</span>
            @if($count > 0)
                <span class="text-white/70 text-xs font-medium shrink-0">({{ $count }})</span>
            @endif
        </div>
        <svg
            class="w-3.5 h-3.5 shrink-0 transition-transform duration-200"
            :class="{ 'rotate-180': open }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
        >
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
        </svg>
    </div>
    <div
        x-show="open"
        x-collapse.duration.300ms
        x-cloak
    >
        <div class="p-3 sm:p-4">
            {{ $slot }}
        </div>
    </div>
</div>
