<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Actualités') }}"
            subtitle="{{ __('Les dernières nouvelles du SDEEG') }}"
            icon="📰"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="actualites"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-4">
            @forelse($actualites as $actualite)
                <a href="{{ route('elus.actualites.show', $actualite) }}"
                   class="block bg-white rounded-2xl shadow border border-[#faa21b]/15 p-6 hover:shadow-md hover:border-[#faa21b]/40 transition group">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <h2 class="text-base font-semibold text-gray-900 group-hover:text-[#b36b00] transition truncate">
                                {{ $actualite->title }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-500 line-clamp-2">
                                {{ Str::limit(strip_tags($actualite->content), 160) }}
                            </p>
                        </div>
                        <div class="flex-shrink-0 text-right text-xs text-gray-400 whitespace-nowrap">
                            {{ $actualite->published_at?->diffForHumans() }}
                        </div>
                    </div>
                    @if($actualite->creator)
                        <p class="mt-3 text-xs text-gray-400">{{ __('Par') }} {{ $actualite->creator->name }}</p>
                    @endif
                </a>
            @empty
                <div class="bg-white rounded-2xl shadow border border-[#faa21b]/15 p-12 text-center">
                    <p class="text-4xl mb-3">📰</p>
                    <p class="text-gray-500 font-medium">{{ __('Aucune actualité pour le moment.') }}</p>
                    <p class="text-sm text-gray-400 mt-1">{{ __('Revenez bientôt pour les dernières nouvelles du SDEEG.') }}</p>
                </div>
            @endforelse

            <div class="mt-4">
                {{ $actualites->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
