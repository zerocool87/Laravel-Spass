@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'backRoute' => null,
    'backLabel' => null,
    'actions' => null
])

<div class="bg-[#faa21b] -mx-4 -my-3 sm:-mx-6 sm:-my-4 px-4 py-4 sm:px-6 sm:py-5 shadow-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3 sm:space-x-4">
            @if($backRoute)
                <a href="{{ $backRoute }}" class="text-white/80 hover:text-white transition flex-shrink-0" aria-label="{{ $backLabel ?? __('Retour') }}">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
            @endif
            <div class="min-w-0">
                <h2 class="font-bold text-xl sm:text-2xl text-white truncate {{ $subtitle ? 'mb-1' : '' }}">
                    @if($icon) {{ $icon }} @endif {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="text-white/90 text-xs sm:text-sm truncate">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($actions)
            <div class="flex space-x-2 items-center flex-shrink-0">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
