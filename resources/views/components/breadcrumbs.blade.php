@props(['items' => []])

<nav aria-label="Breadcrumb" class="mb-4">
    <ol class="flex flex-wrap items-center gap-1.5 text-sm text-gray-500">
        <li>
            <a href="{{ route('elus.dashboard') }}" class="hover:text-[#faa21b] transition flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                <span class="hidden sm:inline">{{ __('Accueil') }}</span>
            </a>
        </li>
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
