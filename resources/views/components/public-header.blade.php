@props([
    'title',
    'subtitle' => null,
    'icon' => null,
    'backRoute' => null,
    'backLabel' => null,
    'actions' => null
])

<div class="bg-gradient-to-r from-blue-500 to-indigo-600 -mx-8 -my-6 px-8 py-6 shadow-lg">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            @if($backRoute)
                <a href="{{ $backRoute }}" class="text-white/80 hover:text-white transition" aria-label="{{ $backLabel ?? __('Retour') }}">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
            @endif
            <div>
                <h2 class="font-bold text-2xl text-white {{ $subtitle ? 'mb-1' : '' }}">
                    @if($icon) {{ $icon }} @endif {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="text-white/90 text-sm">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($actions)
            <div class="flex space-x-2 items-center">
                {{ $actions }}
            </div>
        @endif
    </div>
</div>
