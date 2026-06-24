<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ __('Instances') }}"
            subtitle="{{ __('Comités et commissions') }}"
            icon="🏛️"
            :backRoute="route('elus.dashboard')"
            :backLabel="__('Retour au tableau de bord')"
            activeSection="instances"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Instances Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($instances as $instance)
                    <div class="widget-container">
                        <div class="widget-content">
                            <h3 class="text-lg font-medium text-gray-900">{{ $instance->name }}</h3>
                            <div class="mt-4 flex items-center justify-between">
                                <span class="text-sm text-gray-500">{{ $instance->reunions_count }} {{ __('réunion(s)') }}</span>
                                <a href="{{ route('elus.instances.show', $instance) }}" class="text-sm text-[#faa21b] hover:text-[#e89315] font-semibold">{{ __('Voir détails') }} →</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 widget-container">
                        <div class="widget-empty">
                            <h3 class="widget-empty-title">{{ __('Aucune instance') }}</h3>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
