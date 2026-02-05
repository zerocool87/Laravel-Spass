<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('elus.projects.index') }}" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </a>
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $project->status_color }}">
                        {{ $project->status_label }}
                    </span>
                    <h2 class="font-semibold text-xl leading-tight">{{ $project->title }}</h2>
                </div>
            </div>
            @can('admin')
            <a href="{{ route('elus.projects.edit', $project) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition">
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
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Description') }}</h3>
                        @if($project->description)
                            <p class="text-gray-600 whitespace-pre-line">{{ $project->description }}</p>
                        @else
                            <p class="text-gray-400 italic">{{ __('Aucune description') }}</p>
                        @endif
                    </div>

                    {{-- Territories --}}
                    @if($project->territories && count($project->territories) > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Territoires concernés') }}</h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach($project->territories as $territory)
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm bg-blue-100 text-blue-800">
                                    📍 {{ $territory }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Google Maps Placeholder --}}
                    <x-google-map-placeholder 
                        title="{{ __('Localisation du projet') }}" 
                        location="{{ $project->territories && count($project->territories) > 0 ? $project->territories[0] : 'Paris, France' }}"
                        height="350px"
                    />

                    {{-- Indicators --}}
                    @if($project->indicators && count($project->indicators) > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Indicateurs') }}</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @foreach($project->indicators as $key => $value)
                                <div class="bg-gray-50 rounded p-4">
                                    <p class="text-sm text-gray-500">{{ $key }}</p>
                                    <p class="text-lg font-semibold text-gray-900">{{ $value }}</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Documents --}}
                    @if($project->documents && count($project->documents) > 0)
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Documents associés') }}</h3>
                        <ul class="divide-y divide-gray-200">
                            @foreach($project->documents as $doc)
                                <li class="py-3 flex items-center justify-between">
                                    <div class="flex items-center">
                                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm text-gray-900">{{ $doc['name'] ?? $doc }}</span>
                                    </div>
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
                                <dt class="text-sm text-gray-500">{{ __('Type') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $project->type_label }}</dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Statut') }}</dt>
                                <dd>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $project->status_color }}">
                                        {{ $project->status_label }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Budget') }}</dt>
                                <dd class="text-lg font-semibold text-gray-900">{{ $project->formatted_budget }}</dd>
                            </div>
                            @if($project->start_date)
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Date de début') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $project->start_date->format('d/m/Y') }}</dd>
                            </div>
                            @endif
                            @if($project->end_date)
                            <div>
                                <dt class="text-sm text-gray-500">{{ __('Date de fin') }}</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ $project->end_date->format('d/m/Y') }}</dd>
                            </div>
                            @endif
                        </dl>
                    </div>

                    {{-- Actions --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Actions') }}</h3>
                        <div class="space-y-2">
                            <a href="{{ route('elus.projects.edit', $project) }}" class="block w-full text-center px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
                                {{ __('Modifier le projet') }}
                            </a>
                            <form method="POST" action="{{ route('elus.projects.destroy', $project) }}" onsubmit="return confirm('{{ __('Êtes-vous sûr de vouloir supprimer ce projet ?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-4 py-2 border border-red-300 text-red-600 rounded-md hover:bg-red-50 transition">
                                    {{ __('Supprimer') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Timeline --}}
                    <div class="bg-white rounded-lg shadow p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Historique') }}</h3>
                        <div class="text-sm text-gray-500">
                            <p>{{ __('Créé le') }}: {{ $project->created_at->format('d/m/Y H:i') }}</p>
                            <p>{{ __('Modifié le') }}: {{ $project->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
