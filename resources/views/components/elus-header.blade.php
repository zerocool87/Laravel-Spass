@props([
    'title',
    'subtitle' => null,
    'icon' => '🏛️',
    'backRoute' => null,
    'backLabel' => null,
    'showNav' => true,
    'activeSection' => null,
    'badge' => null,
    'badgeColor' => null,
    'actions' => null,
    'weather' => null,
])

@php
    $navItems = [
        ['route' => 'elus.instances.index', 'label' => __('Instances'), 'key' => 'instances', 'desc' => __('Comités et commissions')],
        ['route' => 'elus.projects.index', 'label' => __('Projets'), 'key' => 'projects', 'desc' => __('Projets territoriaux en cours')],
        ['route' => 'elus.reunions.index', 'label' => __('Réunions'), 'key' => 'reunions', 'desc' => __('Calendrier des réunions')],
        ['route' => 'elus.documents.index', 'label' => __('Documents'), 'key' => 'documents', 'desc' => __('Bibliothèque de documents')],
        ['route' => 'elus.actualites.index', 'label' => __('Actualités'), 'key' => 'actualites', 'desc' => __('Actualités et communications')],
        ['route' => 'elus.forum.index', 'label' => __('Forum'), 'key' => 'forum', 'desc' => __('Discuter avec les autres élus'), 'badge' => $forumUnreadCount ?? 0],
    ];
    $isActive = fn (string $key): bool => $activeSection === $key;
    $linkClasses = fn (string $key, string $context = 'desktop'): string => $context === 'desktop'
        ? 'px-3 py-2 text-sm '.($isActive($key) ? 'bg-white text-[#faa21b] font-semibold shadow-sm' : 'text-white/90 hover:text-white hover:bg-white/15 font-medium').' rounded-lg transition'
        : 'block px-4 py-2.5 text-sm '.($isActive($key) ? 'bg-white text-[#faa21b] font-semibold shadow-sm' : 'text-white/90 hover:text-white hover:bg-white/15 font-medium').' rounded-lg transition';
@endphp

<div class="bg-[#faa21b] mx-auto px-4 py-4 sm:px-6 sm:py-5 lg:px-8 shadow max-w-7xl rounded-b-2xl" x-data="{ mobileOpen: false }">
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
                @if($badge)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                        {{ $badge }}
                    </span>
                @endif
                <h2 class="font-bold text-xl sm:text-2xl text-white truncate {{ $badge ? '' : 'mb-1' }}">
                    {!! $icon !!} {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="text-white/90 text-xs sm:text-sm truncate">{{ $subtitle }}</p>
                @endif
                @if($weather)
                    <p id="weather-info" class="text-white text-xs mt-0.5 font-semibold">
                        <span class="inline-flex items-center gap-1.5">
                            <span id="weather-icon" class="text-lg">{{ $weather['icon'] }}</span>
                            <span id="weather-temp">{{ $weather['temp'] }}</span>
                            <span class="text-white/60">•</span>
                            <span id="weather-city">{{ $weather['city'] ?? 'Limoges' }}</span>
                        </span>
                    </p>
                @endif
            </div>
        </div>

        @if($showNav)
            {{-- Desktop nav (hidden on mobile) --}}
            <nav class="hidden lg:flex space-x-1 items-center flex-shrink-0">
                @foreach($navItems as $item)
                    <a href="{{ route($item['route']) }}" class="{{ $linkClasses($item['key']) }}" title="{{ $item['desc'] ?? $item['label'] }}">
                        <span>{{ $item['label'] }}</span>
                        @if(($item['badge'] ?? 0) > 0)
                            <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-semibold bg-red-600 text-white ml-1">
                                {{ $item['badge'] }}
                            </span>
                        @endif
                    </a>
                @endforeach

                {{ $actions ?? '' }}

                @can('admin')
                    <a href="{{ route('elus.admin.index') }}" class="px-3 py-2 text-sm {{ $isActive('admin') ? 'bg-white text-[#faa21b] font-bold' : 'bg-white/10 text-white hover:bg-white/20 font-medium' }} rounded-lg transition" title="{{ __('Administration des élus et contenus') }}">{{ __('Administration') }}</a>
                @endcan

                <a href="{{ route('elus.dashboard') }}" class="px-3 py-2 text-sm bg-white text-[#faa21b] font-bold rounded-lg transition" title="{{ __('Retour au tableau de bord') }}">{{ __('TdB') }}</a>
                <div class="ml-1">
                    <x-elus-user-menu />
                </div>
            </nav>

            {{-- Mobile hamburger + user menu --}}
            <div class="flex lg:hidden items-center gap-2">
                <x-elus-user-menu />
                <button @click="mobileOpen = !mobileOpen" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:bg-white/20 focus:outline-none transition" aria-label="{{ __('Menu') }}">
                    <svg x-show="!mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                    <svg x-show="mobileOpen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        @else
            <div class="flex space-x-2 items-center">
                {{ $actions ?? '' }}
            </div>
        @endif
    </div>

    {{-- Mobile slide-out nav (visible when hamburger open) --}}
    @if($showNav)
        <nav x-show="mobileOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 -translate-y-2" class="lg:hidden mt-3 pb-1 space-y-1" style="display: none;">
            @foreach($navItems as $item)
                <a href="{{ route($item['route']) }}" class="{{ $linkClasses($item['key'], 'mobile') }}" title="{{ $item['desc'] ?? $item['label'] }}">
                    <span>{{ $item['label'] }}</span>
                    @if(($item['badge'] ?? 0) > 0)
                        <span class="inline-flex items-center justify-center min-w-[1.25rem] h-5 px-1.5 rounded-full text-xs font-semibold bg-red-600 text-white ml-2">
                            {{ $item['badge'] }}
                        </span>
                    @endif
                </a>
            @endforeach
            @can('admin')
                <a href="{{ route('elus.admin.index') }}" class="block px-4 py-2.5 text-sm {{ $isActive('admin') ? 'bg-white text-[#faa21b] font-bold' : 'bg-white/10 text-white hover:bg-white/20 font-medium' }} rounded-lg transition" title="{{ __('Administration des élus et contenus') }}">{{ __('Administration') }}</a>
            @endcan
            <a href="{{ route('elus.dashboard') }}" class="block px-4 py-2.5 text-sm bg-white text-[#faa21b] font-bold rounded-lg transition" title="{{ __('Retour au tableau de bord') }}">{{ __('Tableau de bord') }}</a>
        </nav>
    @endif
</div>
