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

            <div class="widget-container mb-6">
                <form method="GET" action="{{ route('elus.forum.index') }}" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-[150px] sm:min-w-[200px]">
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="{{ __('Rechercher un sujet...') }}" class="w-full input-orange" onchange="this.form.submit()">
                    </div>
                    <div>
                        <select name="thematique_id" class="select-orange" onchange="this.form.submit()">
                            <option value="">{{ __('Toutes les thématiques') }}</option>
                            @foreach($thematiques as $thematique)
                                <option value="{{ $thematique->id }}" {{ request('thematique_id') == $thematique->id ? 'selected' : '' }}>
                                    {{ $thematique->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <select name="sort" class="select-orange" onchange="this.form.submit()">
                            <option value="latest" {{ request('sort', 'latest') == 'latest' ? 'selected' : '' }}>{{ __('Plus récents (activité)') }}</option>
                            <option value="replies" {{ request('sort') == 'replies' ? 'selected' : '' }}>{{ __('Plus de réponses') }}</option>
                        </select>
                    </div>
                </form>
            </div>

            {{-- Threads table --}}
            <div class="widget-container">
                <x-widget-header
                    title="💬 {{ __('Discussions') }}"
                    :link="route('elus.forum.create')"
                    linkText="{{ __('Créer un sujet') }}"
                    linkIcon="➕"
                />
                <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-[#faa21b]/20">
                                <thead class="bg-[#faa21b]/5">
                                    <tr>
                                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Thématique') }}</th>
                                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Sujet') }}</th>
                                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-left text-xs font-bold text-[#faa21b] uppercase tracking-wider hidden sm:table-cell">{{ __('Auteur') }}</th>
                                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-center text-xs font-bold text-[#faa21b] uppercase tracking-wider">{{ __('Rép.') }}</th>
                                        <th class="px-3 sm:px-6 py-2 sm:py-3 text-right text-xs font-bold text-[#faa21b] uppercase tracking-wider hidden md:table-cell">{{ __('Dernière activité') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-[#faa21b]/20">
                                    @forelse($threads as $thread)
                                        <tr class="hover:bg-[#faa21b]/5 transition">
                                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-[#faa21b]/10 text-[#faa21b]">
                                                    {{ $thread->thematique->name }}
                                                </span>
                                            </td>
                                            <td class="px-3 sm:px-6 py-2 sm:py-4">
                                                <a href="{{ route('elus.forum.show', $thread) }}" class="text-sm font-semibold text-gray-900 hover:text-[#faa21b] flex items-center gap-2">
                                                    @if($thread->is_pinned)
                                                        <span class="flex-shrink-0">📌</span>
                                                    @endif
                                                    <span class="truncate max-w-[200px] sm:max-w-xs">{{ $thread->title }}</span>
                                                    @php $isUnread = ! $thread->is_read; @endphp
                                                    @if($isUnread)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-500 text-white flex-shrink-0">
                                                            {{ __('Nouveau') }}
                                                        </span>
                                                    @endif
                                                </a>
                                            </td>
                                            <td class="px-3 sm:px-6 py-2 sm:py-4 whitespace-nowrap hidden sm:table-cell">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-7 h-7 rounded-full bg-[#faa21b]/20 flex items-center justify-center text-[#faa21b] font-bold text-xs flex-shrink-0">
                                                        {{ strtoupper(substr($thread->creator->prenom ?? $thread->creator->name, 0, 1)) }}
                                                    </div>
                                                    <span class="text-sm text-gray-600">{{ $thread->creator->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 sm:px-6 py-2 sm:py-4 text-center text-sm text-gray-500">
                                                {{ $thread->posts_count }}
                                            </td>
                                            <td class="px-3 sm:px-6 py-2 sm:py-4 text-right text-sm text-gray-500 hidden md:table-cell whitespace-nowrap">
                                                @if($thread->latestPost)
                                                    <span title="{{ $thread->latestPost->created_at->format('d/m/Y H:i') }}">
                                                        {{ $thread->latestPost->created_at->diffForHumans() }}
                                                    </span>
                                                @else
                                                    <span title="{{ $thread->created_at->format('d/m/Y H:i') }}">
                                                        {{ $thread->created_at->diffForHumans() }}
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-4 sm:px-6 py-6 sm:py-8 text-center text-gray-500">
                                                <div class="widget-empty">
                                                    <svg class="widget-empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                                    </svg>
                                                    <h3 class="widget-empty-title">{{ __('Aucune discussion') }}</h3>
                                                    <p class="widget-empty-description">{{ __('Soyez le premier à lancer un sujet dans une thématique.') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

            {{-- Pagination --}}
            <div class="mt-6">
                {{ $threads->links('vendor.pagination.forum') }}
            </div>
        </div>
    </div>
</x-app-layout>
