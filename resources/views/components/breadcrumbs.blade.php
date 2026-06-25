@props(['items' => []])

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500">
        @foreach($items as $item)
            <li class="flex items-center gap-1.5">
                <svg class="w-3 h-3 text-gray-300 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                @if(isset($item['url']))
                    <a href="{{ $item['url'] }}" class="hover:text-[#faa21b] transition truncate max-w-[200px]">
                        {{ $item['label'] }}
                    </a>
                @else
                    <span class="text-gray-700 font-semibold truncate max-w-[200px]" aria-current="page">{{ $item['label'] }}</span>
                @endif
            </li>
        @endforeach
    </ol>
</nav>
