@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}"
         class="inline-flex items-center gap-3 select-none">

        @if ($paginator->onFirstPage())
            <span class="text-gray-300 cursor-not-allowed text-[11px] uppercase tracking-wider font-semibold">{{ __('Réunions précédentes') }}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="text-[#b36b00] hover:text-[#faa21b] transition text-[11px] uppercase tracking-wider font-semibold">{{ __('Réunions précédentes') }}</a>
        @endif

        <span class="text-gray-300 mx-1">|</span>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="text-[#b36b00] hover:text-[#faa21b] transition text-[11px] uppercase tracking-wider font-semibold">{{ __('Réunions suivantes') }}</a>
        @else
            <span class="text-gray-300 cursor-not-allowed text-[11px] uppercase tracking-wider font-semibold">{{ __('Réunions suivantes') }}</span>
        @endif

    </nav>
@endif
