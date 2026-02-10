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
    'actions' => null
])

<div class="bg-[#faa21b] mx-auto px-8 py-6 shadow-lg max-w-7xl">
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
                @if($badge)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeColor }}">
                        {{ $badge }}
                    </span>
                @endif
                <h2 class="font-bold text-2xl text-white {{ $badge ? '' : 'mb-1' }}">
                    {{ $icon }} {{ $title }}
                </h2>
                @if($subtitle)
                    <p class="text-white/90 text-sm">{{ $subtitle }}</p>
                @endif
            </div>
        </div>

        @if($showNav)
            <nav class="flex space-x-2 items-center">
                <a href="{{ route('elus.instances.index') }}" class="px-4 py-2 text-sm {{ $activeSection === 'instances' ? 'bg-white text-[#faa21b] font-bold' : 'text-white hover:bg-white/20 font-medium' }} rounded-lg transition">{{ __('Instances') }}</a>
                <a href="{{ route('elus.projects.index') }}" class="px-4 py-2 text-sm {{ $activeSection === 'projects' ? 'bg-white text-[#faa21b] font-bold' : 'text-white hover:bg-white/20 font-medium' }} rounded-lg transition">{{ __('Projets') }}</a>
                <a href="{{ route('elus.reunions.index') }}" class="px-4 py-2 text-sm {{ $activeSection === 'reunions' ? 'bg-white text-[#faa21b] font-bold' : 'text-white hover:bg-white/20 font-medium' }} rounded-lg transition">{{ __('Réunions') }}</a>
                <a href="{{ route('elus.documents.index') }}" class="px-4 py-2 text-sm {{ $activeSection === 'documents' ? 'bg-white text-[#faa21b] font-bold' : 'text-white hover:bg-white/20 font-medium' }} rounded-lg transition">{{ __('Documents') }}</a>
                <a href="{{ route('elus.collab.index') }}" class="px-4 py-2 text-sm inline-flex items-center gap-2 {{ $activeSection === 'collab' ? 'bg-white text-[#faa21b] font-bold' : 'text-white hover:bg-white/20 font-medium' }} rounded-lg transition">
                    <span>{{ __('Collaboratif') }}</span>
                    @if(($collabUnreadCount ?? 0) > 0)
                        <span class="inline-flex items-center justify-center min-w-[1.5rem] h-6 px-2 rounded-full text-xs font-semibold bg-red-600 text-white">
                            {{ $collabUnreadCount }}
                        </span>
                    @endif
                </a>

                {{ $actions ?? '' }}

                @can('admin')
                    <a href="{{ route('elus.admin.index') }}" class="px-4 py-2 text-sm {{ $activeSection === 'admin' ? 'bg-white text-[#faa21b] font-bold' : 'bg-white/10 text-white hover:bg-white/20 font-medium' }} rounded-lg transition">{{ __('Administration') }}</a>
                @endcan

                <a href="{{ route('elus.dashboard') }}" class="px-4 py-2 text-sm bg-white text-[#faa21b] font-bold rounded-lg transition">{{ __('Tableau de bord') }}</a>
                <div class="ml-2">
                    <x-elus-user-menu />
                </div>
            </nav>
        @else
            <div class="flex space-x-2 items-center">
                {{ $actions ?? '' }}
            </div>
        @endif
    </div>
</div>
