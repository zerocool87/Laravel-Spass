<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ $instance->name }}"
            subtitle="{{ $instance->type_label }}"
            icon="🏛️"
            :backRoute="route('admin.instances.index')"
            :backLabel="__('Retour aux instances')"
            activeSection="admin"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-sm flex items-start gap-3">
                    <div class="mt-0.5">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Informations principales -->
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                <div class="flex items-start justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 mb-2">{{ $instance->name }}</h2>
                        <div class="flex items-center gap-3">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[#faa21b]/10 text-[#faa21b]">
                                {{ $instance->type_label }}
                            </span>
                            @if($instance->territory)
                                <span class="inline-flex items-center text-sm text-gray-600">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $instance->territory }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <a
                        href="{{ route('admin.instances.edit', $instance) }}"
                        class="inline-flex items-center px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition"
                    >
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('Modifier') }}
                    </a>
                </div>

                @if($instance->description)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ __('Description') }}</h3>
                        <p class="text-gray-700">{{ $instance->description }}</p>
                    </div>
                @endif

                @if($instance->members && count($instance->members) > 0)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-3">{{ __('Membres') }} ({{ count($instance->members) }})</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach($instance->members as $member)
                                <div class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-700">{{ $member }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <div class="pt-6 border-t border-gray-200">
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">{{ __('Créée le') }}</span>
                            <p class="font-medium text-gray-900">{{ $instance->created_at->format('d/m/Y à H:i') }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">{{ __('Dernière mise à jour') }}</span>
                            <p class="font-medium text-gray-900">{{ $instance->updated_at->format('d/m/Y à H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Réunions à venir -->
            @if($upcomingReunions->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Réunions à venir') }}</h3>
                    <div class="space-y-3">
                        @foreach($upcomingReunions as $reunion)
                            <div class="flex items-center justify-between p-4 bg-blue-50 rounded-lg border border-blue-200">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $reunion->title }}</h4>
                                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $reunion->start_time->format('d/m/Y') }}
                                        </span>
                                        @if($reunion->location)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                </svg>
                                                {{ $reunion->location }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($reunion->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Réunions passées -->
            @if($pastReunions->count() > 0)
                <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                    <h3 class="text-xl font-semibold text-gray-900 mb-4">{{ __('Réunions passées') }}</h3>
                    <div class="space-y-3">
                        @foreach($pastReunions as $reunion)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-900">{{ $reunion->title }}</h4>
                                    <div class="flex items-center gap-4 mt-1 text-sm text-gray-600">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $reunion->start_time->format('d/m/Y') }}
                                        </span>
                                        @if($reunion->location)
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                </svg>
                                                {{ $reunion->location }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-200 text-gray-700">
                                    {{ ucfirst($reunion->status) }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if($upcomingReunions->count() === 0 && $pastReunions->count() === 0)
                <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                    <div class="text-center py-12">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucune réunion') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">{{ __('Cette instance n\'a pas encore de réunions planifiées.') }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
