@php
    /** @var \App\Models\ForumThread $thread */
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
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Forum'), 'url' => route('elus.forum.index')], ['label' => $thread->title]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="widget-container">
                <x-widget-header title="💬 {{ $thread->title }}" />
                <div class="p-6 sm:p-8 flex flex-col gap-5">
                    @forelse($posts as $post)
                        <div class="flex gap-4 {{ $post->user_id === $currentUser->id ? 'flex-row-reverse' : '' }}">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-[#faa21b]/20 flex items-center justify-center text-[#faa21b] font-bold text-base">
                                    {{ strtoupper(substr($post->author->prenom ?? $post->author->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 {{ $post->user_id === $currentUser->id ? 'text-right' : '' }}">
                                <div class="flex items-center gap-2 {{ $post->user_id === $currentUser->id ? 'justify-end' : '' }}">
                                    <span class="text-base font-semibold text-gray-900">{{ $post->author->name }}</span>
                                    <span class="inline-flex items-center gap-1 text-xs font-medium text-gray-400 bg-gray-100 rounded-full px-2 py-0.5">
                                        <span>💬</span>
                                        <span>{{ $post->author->forum_posts_count }}</span>
                                    </span>
                                    @if($post->author->titres)
                                        @php($titres = is_array($post->author->titres) ? $post->author->titres : json_decode($post->author->titres, true) ?? [])
                                        @foreach(array_slice($titres, 0, 1) as $titre)
                                            <span class="text-sm text-gray-500">· {{ $titre }}</span>
                                        @endforeach
                                    @endif
                                    @if($post->author->commune)
                                        <span class="text-sm text-gray-500">· {{ $post->author->commune }}</span>
                                    @endif
                                </div>
                                <div class="mt-2 rounded-xl px-5 py-4 {{ $post->user_id === $currentUser->id ? 'bg-[#faa21b] text-white' : 'bg-[#faa21b]/10 text-gray-800' }}">
                                    <p class="text-base leading-relaxed whitespace-pre-wrap">{{ $post->body }}</p>
                                </div>
                                <p class="mt-1 text-sm text-gray-500 {{ $post->user_id === $currentUser->id ? 'text-right' : '' }}">
                                    {{ $post->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                    @empty
                        <div class="widget-empty">
                            <svg class="widget-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="widget-empty-title">{{ __('Aucun message') }}</h3>
                            <p class="widget-empty-description">{{ __('Soyez le premier à répondre à ce sujet.') }}</p>
                        </div>
                    @endforelse
                </div>

                <div class="px-6 sm:px-8 pb-6 flex items-center justify-between gap-4">
                    <div class="flex-1">
                        {{ $posts->links() }}
                    </div>
                    <a href="#" class="text-sm text-gray-500 hover:text-[#faa21b] flex-shrink-0 transition" title="{{ __('Retour en haut') }}">
                        ↑ {{ __('Haut') }}
                    </a>
                </div>

                <div id="repondre" class="scroll-mt-24 border-t border-[#faa21b]/20 p-6 sm:p-8">
                    <form method="POST" action="{{ route('elus.forum.posts.store', $thread) }}" class="flex flex-col gap-4">
                        @csrf
                        <textarea name="body" rows="4" class="w-full input-orange text-base" placeholder="{{ __('Écrire une réponse...') }}" required maxlength="5000">{{ old('body') }}</textarea>
                        <x-input-error :messages="$errors->get('body')" />
                        <div class="flex items-center justify-between gap-4">
                            <p class="text-sm text-gray-500">
                                {{ __('Réponse visible par tous les élus.') }}
                            </p>
                            <button type="submit" class="btn-primary-orange text-base px-6 py-3">
                                {{ __('Répondre') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
