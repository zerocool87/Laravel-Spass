@php
    use App\Models\ForumThread;
    /** @var ForumThread $thread */
    /** @var \App\Models\User $currentUser */
@endphp

<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Forum') }}"
            subtitle="{{ __('Discussions entre élus') }}"
            icon="💬"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="forum"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Forum')]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-2">
                    @foreach($instances as $instance)
                        @php
                            $instanceThreads = $threads->where('instance_id', $instance->id);
                            $instanceUnread = $instanceThreads->filter(fn ($t) => ! $t->is_read)->count();
                        @endphp
                        @continue($instanceThreads->isEmpty())
                        <div class="widget-container mb-6" x-data="{ open: false }">
                            <button @click="open = ! open" type="button" class="w-full text-left">
                                <div class="px-4 sm:px-6 py-4 sm:py-5 bg-[#faa21b]/15 border-b-2 border-[#faa21b]/20 flex items-center justify-between gap-2 rounded-t-xl">
                                    <h3 class="text-lg sm:text-xl font-bold text-[#faa21b] flex items-center gap-2">
                                        <svg
                                            class="w-4 h-4 transition-transform duration-200"
                                            :class="{ 'rotate-90': open }"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                        >
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                        {{ $instance->name }}
                                    </h3>
                                    <div class="flex items-center gap-2">
                                        @if($instanceUnread > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-500 text-white">
                                                {{ $instanceUnread }} {{ __('non lu(s)') }}
                                            </span>
                                        @endif
                                        <span class="text-sm text-gray-500">{{ $instanceThreads->count() }} {{ __('sujets') }}</span>
                                    </div>
                                </div>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="divide-y divide-[#faa21b]/20">
                                @foreach($instanceThreads as $thread)
                                    <a href="{{ route('elus.forum.show', $thread) }}" class="block px-6 py-5 hover:bg-[#faa21b]/5 transition">
                                        <div class="flex items-start justify-between gap-4">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="font-bold text-gray-900 truncate text-base">
                                                        {{ $thread->is_pinned ? '📌 ' : '' }}{{ $thread->title }}
                                                    </p>
                                                    @php
                                                        $isUnread = ! $thread->is_read;
                                                    @endphp
                                                    @if($isUnread)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-500 text-white">
                                                            {{ __('Nouveau') }}
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="flex flex-wrap items-center gap-x-2 gap-y-0.5 mt-1 text-sm text-gray-600">
                                                    <span>{{ $thread->creator->name }}</span>
                                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">
                                                        <span>💬</span>
                                                        <span>{{ $thread->creator->forum_posts_count }}</span>
                                                    </span>
                                                    @if($thread->creator->commune)
                                                        <span class="text-gray-400">·</span>
                                                        <span>{{ $thread->creator->commune }}</span>
                                                    @endif
                                                    @if($thread->creator->titres)
                                                        @php
                                                            $titres = is_array($thread->creator->titres) ? $thread->creator->titres : json_decode($thread->creator->titres, true) ?? [];
                                                        @endphp
                                                        @foreach(array_slice($titres, 0, 2) as $titre)
                                                            <span class="text-gray-400">·</span>
                                                            <span>{{ $titre }}</span>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="flex flex-col items-end gap-1 flex-shrink-0 text-sm text-gray-500">
                                                <span>{{ $thread->posts_count }} {{ __('messages') }}</span>
                                                @if($thread->latestPost)
                                                    <span>{{ $thread->latestPost->created_at->diffForHumans() }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach

                    @if($threads->isEmpty())
                        <div class="widget-container">
                            <div class="widget-empty">
                                <svg class="widget-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <h3 class="widget-empty-title">{{ __('Aucune discussion') }}</h3>
                                <p class="widget-empty-description">{{ __('Soyez le premier à lancer un sujet dans une instance.') }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div>
                    <div class="widget-container">
                        <a href="{{ route('elus.forum.create') }}" class="block mx-6 my-6">
                            <button type="button" class="btn-primary-orange w-full">
                                {{ __('Créer un sujet') }}
                            </button>
                        </a>
                    </div>

                    <div class="widget-container mt-6">
                        <x-widget-header title="{{ __('Instances') }}" />
                        <div class="p-6 flex flex-col gap-2">
                            @foreach($instances as $instance)
                                <div class="flex items-center justify-between">
                                    <span class="text-sm text-gray-700">{{ $instance->name }}</span>
                                    <span class="text-xs text-gray-400">{{ $threads->where('instance_id', $instance->id)->count() }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
