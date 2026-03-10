<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ $actualite->title }}"
            subtitle="{{ $actualite->published_at?->format('d/m/Y') }}"
            icon="📰"
            :backRoute="route('elus.actualites.index')"
            :backLabel="__('Retour aux actualités')"
            activeSection="actualites"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow border border-[#faa21b]/15 p-8">
                <div class="flex items-center gap-3 mb-6 text-sm text-gray-500">
                    <span>📅 {{ $actualite->published_at?->format('d/m/Y à H:i') }}</span>
                    @if($actualite->creator)
                        <span>·</span>
                        <span>{{ $actualite->creator->name }}</span>
                    @endif
                </div>

                <div class="prose max-w-none text-gray-800 leading-relaxed">
                    {!! nl2br(e($actualite->content)) !!}
                </div>

                <div class="mt-8 pt-6 border-t border-gray-100">
                    <a href="{{ route('elus.actualites.index') }}" class="inline-flex items-center gap-2 text-sm font-medium text-[#b36b00] hover:underline">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        {{ __('Retour aux actualités') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
