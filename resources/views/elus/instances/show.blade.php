<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            :title="$instance->name"
            icon="🏛️"
            :backRoute="route('elus.instances.index')"
            :backLabel="__('Retour aux instances')"
            activeSection="instances"
        />
    </x-slot>

    <x-slot name="breadcrumbs">
        <x-breadcrumbs :items="[['label' => __('Accueil'), 'url' => route('elus.dashboard')], ['label' => __('Instances'), 'url' => route('elus.instances.index')], ['label' => $instance->name]]" />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Info --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Upcoming Reunions --}}
                    <div class="widget-container">
                        <div class="px-6 py-4 border-b border-[#faa21b]/20">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2"><span class="w-1 h-5 bg-[#faa21b] rounded-full inline-block"></span>{{ __('Prochaines réunions') }}</h3>
                        </div>
                        <div class="divide-y divide-[#faa21b]/10">
                            @forelse($upcomingReunions as $reunion)
                                <a href="{{ route('elus.reunions.show', $reunion) }}" class="block px-6 py-4 hover:bg-[#faa21b]/5 transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $reunion->title }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $reunion->status_color }}">
                                                {{ $reunion->status_label }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">{{ $reunion->start_time->format('d/m/Y') }}</p>
                                            <p class="text-sm text-gray-500">{{ $reunion->start_time->format('H:i') }}</p>
                                        </div>
                                    </div>
                                    @if($reunion->location)
                                        <p class="mt-1 text-sm text-gray-500">📍 {{ $reunion->location }}</p>
                                    @endif
                                </a>
                            @empty
                                <div class="px-6 py-8 text-center text-gray-500">
                                    {{ __('Aucune réunion à venir') }}
                                </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Past Reunions --}}
                    <div class="widget-container">
                        <div class="px-6 py-4 border-b border-[#faa21b]/20">
                            <h3 class="text-lg font-semibold text-gray-900 flex items-center gap-2"><span class="w-1 h-5 bg-[#faa21b] rounded-full inline-block"></span>{{ __('Réunions passées') }}</h3>
                        </div>
                        <div class="divide-y divide-[#faa21b]/10">
                            @forelse($pastReunions as $reunion)
                                <a href="{{ route('elus.reunions.show', $reunion) }}" class="block px-6 py-4 hover:bg-[#faa21b]/5 transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $reunion->title }}</p>
                                            @if($reunion->compte_rendu)
                                                <span class="text-xs text-green-600">✓ {{ __('Compte rendu disponible') }}</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">{{ $reunion->start_time->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-6 py-8 text-center text-gray-500">
                                    {{ __('Aucune réunion passée') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    <div class="widget-container p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2"><span class="w-1 h-5 bg-[#faa21b] rounded-full inline-block"></span>{{ __('Actions') }}</h3>
                        <div class="space-y-2">
                            @can('admin')
                            <a href="{{ route('elus.reunions.create', ['instance_id' => $instance->id]) }}" class="block w-full text-center px-4 py-2 bg-[#faa21b] text-white rounded-md hover:bg-[#e89315] transition">
                                {{ __('Planifier une réunion') }}
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
