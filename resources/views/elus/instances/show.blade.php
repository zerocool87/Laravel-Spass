<x-app-layout>
    <x-slot name="header">
        <div class="bg-[#FFA500] -mx-8 -my-6 px-8 py-6 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="{{ route('elus.instances.index') }}" class="text-white/80 hover:text-white transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $instance->type_label }}
                        </span>
                        <h2 class="font-semibold text-xl text-white leading-tight">{{ $instance->name }}</h2>
                    </div>
                </div>
                <div class="flex space-x-2">
                    @can('admin')
                    <a href="{{ route('elus.reunions.create', ['instance_id' => $instance->id]) }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 transition">
                        + {{ __('Planifier une réunion') }}
                    </a>
                    <a href="{{ route('elus.instances.edit', $instance) }}" class="inline-flex items-center px-4 py-2 border border-white/30 rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-white/10 transition">
                        {{ __('Modifier') }}
                    </a>
                    @endcan
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Main Info --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Description --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informations') }}</h3>
                        @if($instance->description)
                            <p class="text-gray-600">{{ $instance->description }}</p>
                        @else
                            <p class="text-gray-400 italic">{{ __('Aucune description') }}</p>
                        @endif

                        @if($instance->territory)
                            <div class="mt-4 pt-4 border-t">
                                <p class="text-sm text-gray-500">📍 {{ __('Territoire') }}: <span class="text-gray-700">{{ $instance->territory }}</span></p>
                            </div>
                        @endif
                    </div>

                    {{-- Upcoming Reunions --}}
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Prochaines réunions') }}</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @forelse($upcomingReunions as $reunion)
                                <a href="{{ route('elus.reunions.show', $reunion) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $reunion->title }}</p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $reunion->status_color }}">
                                                {{ $reunion->status_label }}
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-medium text-gray-900">{{ $reunion->date->format('d/m/Y') }}</p>
                                            <p class="text-sm text-gray-500">{{ $reunion->date->format('H:i') }}</p>
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
                    <div class="bg-white rounded-lg shadow">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Réunions passées') }}</h3>
                        </div>
                        <div class="divide-y divide-gray-200">
                            @forelse($pastReunions as $reunion)
                                <a href="{{ route('elus.reunions.show', $reunion) }}" class="block px-6 py-4 hover:bg-gray-50 transition">
                                    <div class="flex items-center justify-between">
                                        <div>
                                            <p class="font-medium text-gray-900">{{ $reunion->title }}</p>
                                            @if($reunion->compte_rendu)
                                                <span class="text-xs text-green-600">✓ {{ __('Compte rendu disponible') }}</span>
                                            @endif
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm text-gray-500">{{ $reunion->date->format('d/m/Y') }}</p>
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
                    {{-- Members --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Membres') }}</h3>
                         @if($instance->members && is_array($instance->members) && count($instance->members) > 0)
                             <ul class="space-y-2">
                                 @foreach($instance->members as $member)
                                     <li class="text-sm text-gray-600">{{ $member }}</li>
                                 @endforeach
                             </ul>
                         @else
                             <p class="text-gray-400 italic text-sm">{{ __('Aucun membre défini') }}</p>
                         @endif
                    </div>

                    {{-- Actions --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Actions') }}</h3>
                        <div class="space-y-2">
                            @can('admin')
                            <a href="{{ route('elus.reunions.create', ['instance_id' => $instance->id]) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                {{ __('Planifier une réunion') }}
                            </a>
                            <a href="{{ route('elus.instances.edit', $instance) }}" class="block w-full text-center px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 transition">
                                {{ __('Modifier l\'instance') }}
                            </a>
                            <form method="POST" action="{{ route('elus.instances.destroy', $instance) }}" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette instance ?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
                                    {{ __('Supprimer') }}
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
