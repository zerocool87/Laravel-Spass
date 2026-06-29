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

    <div class="py-8" x-data="{
        replyToPost: null,
        replyName: '',
        replyToBody: '',
        openMenu: null,
        editPost: null,
        editBody: '',
        threadId: {{ $thread->id }},
        init() {
            this._onClick = (event) => {
                if (this.openMenu !== null) {
                    const postEl = document.getElementById(`post-${this.openMenu}`);
                    if (!postEl || !postEl.contains(event.target)) {
                        this.openMenu = null;
                    }
                }
            };
            document.addEventListener('click', this._onClick);
        },
        destroy() {
            document.removeEventListener('click', this._onClick);
        }
    }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="widget-container">
                <x-widget-header title="💬 {{ $thread->title }}" />
                <div class="p-6 sm:p-8 flex flex-col gap-5">
                    @forelse($posts as $post)
                        <div id="post-{{ $post->id }}" class="flex gap-4 group">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 rounded-full bg-[#faa21b]/20 flex items-center justify-center text-[#faa21b] font-bold text-base">
                                    {{ strtoupper(substr($post->author->prenom ?? $post->author->name, 0, 1)) }}
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 max-w-[calc(100%-4rem)]">
                                <div class="flex items-center gap-2">
                                    <span class="text-base font-semibold text-gray-900">{{ $post->author->prenom ? $post->author->prenom.' '.$post->author->name : $post->author->name }}</span>
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

                                @if($post->replyTo)
                                    <a href="#post-{{ $post->reply_to_post_id }}"
                                       class="inline-flex items-center gap-1 text-xs text-[#faa21b] hover:text-[#d4880f] hover:underline mb-1">
                                        ↩ {{ __('En réponse à') }}
                                        <span class="font-semibold">{{ $post->replyTo->author->prenom ? $post->replyTo->author->prenom.' '.$post->replyTo->author->name : $post->replyTo->author->name }}</span>
                                    </a>
                                @endif

                                <div class="mt-2 rounded-xl px-5 py-4 {{ $post->user_id === $currentUser->id ? 'bg-[#faa21b]/20 text-gray-900 border-l-2 border-[#faa21b]' : 'bg-[#faa21b]/10 text-gray-800' }}">
                                    <p class="text-base leading-relaxed whitespace-pre-wrap">{{ $post->body }}</p>
                                </div>

                                <div class="mt-1 flex items-center gap-3">
                                    <span class="text-sm text-gray-500">
                                        {{ $post->created_at->format('d/m/Y H:i') }}
                                    </span>
                                        <button
                                            type="button"
                                            class="text-sm text-gray-400 hover:text-[#faa21b] transition opacity-0 group-hover:opacity-100 max-sm:opacity-100 inline-flex items-center gap-1"
                                            @click="replyToPost = {{ $post->id }}; replyName = @js($post->author->prenom ? $post->author->prenom.' '.$post->author->name : $post->author->name); replyToBody = @js(Str::limit($post->body, 120)); $nextTick(() => document.getElementById('forum-reply-form').scrollIntoView({ behavior: 'smooth' }))"
                                        >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                        </svg>
                                        <span class="hidden sm:inline">{{ __('Répondre') }}</span>
                                    </button>

                                    @if($post->user_id === $currentUser->id || $currentUser->isAdmin())
                                        <div class="relative">
                                            <button
                                                type="button"
                                                class="text-sm text-gray-400 hover:text-gray-600 transition opacity-0 group-hover:opacity-100 max-sm:opacity-100 inline-flex items-center px-0.5"
                                                @click="openMenu = openMenu === {{ $post->id }} ? null : {{ $post->id }}"
                                            >
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                                </svg>
                                            </button>

                                            <div x-show="openMenu === {{ $post->id }}"
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="opacity-0 scale-95"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 class="absolute right-0 top-full mt-1 z-30 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-1 overflow-hidden"
                                                 style="display: none;">
                                                @if($post->user_id === $currentUser->id)
                                                    <button
                                                        type="button"
                                                        class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-[#faa21b]/10 flex items-center gap-3 transition"
                                                        @click="editPost = {{ $post->id }}; editBody = @js($post->body); openMenu = null"
                                                    >
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        {{ __('Modifier') }}
                                                    </button>
                                                @endif

                                                @if($post->reply_to_post_id && ($post->user_id === $currentUser->id || $currentUser->isAdmin()))
                                                    <form method="POST" action="{{ route('elus.forum.posts.detach-reply', [$thread, $post]) }}">
                                                        @csrf
                                                        @method('PUT')
                                                        <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-[#faa21b]/10 flex items-center gap-3 transition">
                                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            {{ __('Détacher la réponse') }}
                                                        </button>
                                                    </form>
                                                @endif

                                                <hr class="my-1 border-gray-100">

                                                <form method="POST" x-bind:action="`/elus/forum/${threadId}/posts/${ {{ $post->id }} }`" onsubmit="return confirm('{{ __('Supprimer ce message ?') }}')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 flex items-center gap-3 transition">
                                                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        {{ __('Supprimer') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    @endif
                                </div>
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

                <div id="forum-reply-form" class="scroll-mt-24 border-t border-[#faa21b]/20 p-6 sm:p-8">
                    <form method="POST" action="{{ route('elus.forum.posts.store', $thread) }}" class="flex flex-col gap-4">
                        @csrf
                        <input type="hidden" name="reply_to_post_id" :value="replyToPost">

                        <template x-if="replyToPost">
                            <div class="flex items-start gap-3 p-3 bg-[#faa21b]/10 rounded-xl border border-[#faa21b]/20">
                                <div class="w-8 h-8 rounded-full bg-[#faa21b]/20 flex items-center justify-center text-[#faa21b] font-bold text-xs flex-shrink-0">
                                    <span x-text="replyName.charAt(0).toUpperCase()"></span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900" x-text="replyName"></p>
                                    <p class="text-sm text-gray-500 truncate" x-text="replyToBody"></p>
                                </div>
                                <button
                                    type="button"
                                    class="text-gray-400 hover:text-red-500 transition p-0.5 flex-shrink-0"
                                    @click="replyToPost = null; replyName = ''; replyToBody = ''"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        </template>

                        <textarea name="body" rows="4" class="w-full input-orange text-base" placeholder="{{ __('Écrire une réponse...') }}" required maxlength="5000">{{ old('body') }}</textarea>
                        <x-input-error :messages="$errors->get('body')" />
                        <x-input-error :messages="$errors->get('reply_to_post_id')" />
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

        {{-- Modal d'édition --}}
        <template x-if="editPost">
            <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4" @click="editPost = null; editBody = ''">
                <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl mx-auto p-6" @click.stop>
                    <h3 class="text-lg font-bold text-gray-900 mb-4">{{ __('Modifier le message') }}</h3>
                    <form method="POST" x-bind:action="`/elus/forum/${threadId}/posts/${editPost}`">
                        @csrf
                        @method('PUT')
                        <textarea name="body" rows="6" class="w-full input-orange text-base" x-model="editBody" required maxlength="5000"></textarea>
                        <x-input-error :messages="$errors->get('body')" />
                        <div class="flex items-center justify-between gap-4 mt-4">
                            <button
                                type="button"
                                class="text-base text-gray-500 hover:text-gray-700 transition"
                                @click="editPost = null; editBody = ''"
                            >
                                {{ __('Annuler') }}
                            </button>
                            <button type="submit" class="btn-primary-orange text-base px-6 py-3">
                                {{ __('Enregistrer') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>
    </div>
</x-app-layout>
