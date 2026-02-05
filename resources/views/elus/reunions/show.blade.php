<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('elus.reunions.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $reunion->status_color }}">
                        {{ $reunion->status_label }}
                    </span>
                    <h2 class="font-semibold text-xl leading-tight">{{ $reunion->title }}</h2>
                </div>
            </div>
            @can('admin')
            <a href="{{ route('elus.reunions.edit', $reunion) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
                {{ __('Modifier') }}
            </a>
            @endcan
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
                {{-- Main Content --}}
                <div class="lg:col-span-2 space-y-6">
                    {{-- Description --}}
                    @if($reunion->description)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Description') }}</h3>
                        <p class="text-gray-600 whitespace-pre-line">{{ $reunion->description }}</p>
                    </div>
                    @endif

                    {{-- Ordre du jour --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Ordre du jour') }}</h3>
                        @if($reunion->ordre_du_jour)
                            <div class="text-gray-600 whitespace-pre-line">{{ $reunion->ordre_du_jour }}</div>
                        @else
                            <p class="text-gray-400 italic">{{ __('Aucun ordre du jour défini') }}</p>
                        @endif
                    </div>

                    {{-- Compte rendu --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Compte rendu') }}</h3>
                            @can('admin')
                            @if(!$reunion->compte_rendu)
                                <a href="{{ route('elus.reunions.edit', $reunion) }}" class="text-sm text-blue-600 hover:text-blue-800">+ {{ __('Ajouter') }}</a>
                            @endif
                            @endcan
                        </div>
                        @if($reunion->compte_rendu)
                            <div class="text-gray-600 whitespace-pre-line">{{ $reunion->compte_rendu }}</div>
                        @else
                            <p class="text-gray-400 italic">{{ __('Le compte rendu n\'a pas encore été rédigé') }}</p>
                        @endif
                    </div>

                    {{-- Documents --}}
                    @if($reunion->documents && count($reunion->documents) > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Documents') }}</h3>
                        <ul class="divide-y divide-gray-200">
                            @foreach($reunion->documents as $doc)
                                <li class="py-3 flex items-center">
                                    <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm text-gray-900">{{ $doc['name'] ?? $doc }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>

                {{-- Sidebar --}}
                <div class="space-y-6">
                    {{-- Info Card --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Informations') }}</h3>
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Instance') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">
                                    <a href="{{ route('elus.instances.show', $reunion->instance) }}" class="text-blue-600 hover:text-blue-800">
                                        {{ $reunion->instance->name }}
                                    </a>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Date') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $reunion->date->format('d/m/Y') }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Heure') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $reunion->date->format('H:i') }}</dd>
                            </div>
                            @if($reunion->location)
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Lieu') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">📍 {{ $reunion->location }}</dd>
                            </div>
                            @endif
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Statut') }}</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $reunion->status_color }}">
                                        {{ $reunion->status_label }}
                                    </span>
                                </dd>
                            </div>
                        </dl>
                    </div>

                    {{-- Participants --}}
                    @if($reunion->participants && count($reunion->participants) > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Participants') }}</h3>
                        <ul class="space-y-2">
                            @foreach($reunion->participants as $participant)
                                <li class="text-sm text-gray-600">{{ $participant }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Actions --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Actions') }}</h3>
                        <div class="space-y-2">
                            <a href="{{ route('elus.reunions.edit', $reunion) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                {{ __('Modifier la réunion') }}
                            </a>
                            <form method="POST" action="{{ route('elus.reunions.destroy', $reunion) }}" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer cette réunion ?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
                                    {{ __('Supprimer') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
