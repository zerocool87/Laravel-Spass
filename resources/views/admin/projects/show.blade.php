<x-app-layout>
    <x-slot name="header">
        <x-elus-header
            title="{{ $project->title }}"
            subtitle="{{ $project->type_label }}"
            icon="📋"
            :backRoute="route('admin.projects.index')"
            :backLabel="__('Retour aux projets')"
            activeSection="admin"
        />
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-800 px-6 py-4 rounded-2xl shadow-sm flex items-start gap-3">
                    <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-semibold">{{ session('success') }}</span>
                </div>
            @endif

            {{-- Main card --}}
            <div class="bg-white rounded-2xl shadow-lg border border-[#faa21b]/20 p-8">
                {{-- Header row --}}
                <div class="flex items-start justify-between mb-6">
                    <div class="flex-1">
                        <h2 class="text-2xl font-bold text-gray-900 mb-3">{{ $project->title }}</h2>
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-[#faa21b]/10 text-[#faa21b]">
                                {{ $project->type_label }}
                            </span>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold {{ $project->status_color }}">
                                {{ $project->status_label }}
                            </span>
                            @if($project->commune)
                                <span class="inline-flex items-center gap-1 text-sm text-gray-600">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $project->commune }}
                                </span>
                            @endif
                        </div>
                    </div>
                    <a
                        href="{{ route('admin.projects.edit', $project) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#faa21b] text-white rounded-xl font-semibold text-sm shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        {{ __('Modifier') }}
                    </a>
                </div>

                {{-- Description --}}
                @if($project->description)
                    <div class="mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-2">{{ __('Description') }}</h3>
                        <p class="text-gray-700 leading-relaxed">{{ $project->description }}</p>
                    </div>
                @endif

                {{-- Budget & Dates --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 py-6 border-t border-b border-gray-100 mb-6">
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Budget') }}</p>
                        <p class="text-lg font-bold text-gray-900">
                            @if($project->budget)
                                {{ $project->formatted_budget }}
                            @else
                                <span class="text-gray-400 font-normal text-sm">{{ __('Non renseigné') }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Date de début') }}</p>
                        <p class="text-base font-semibold text-gray-900">
                            @if($project->start_date)
                                {{ $project->start_date->format('d/m/Y') }}
                            @else
                                <span class="text-gray-400 font-normal text-sm">{{ __('Non renseignée') }}</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-1">{{ __('Date de fin') }}</p>
                        <p class="text-base font-semibold text-gray-900">
                            @if($project->end_date)
                                {{ $project->end_date->format('d/m/Y') }}
                            @else
                                <span class="text-gray-400 font-normal text-sm">{{ __('Non renseignée') }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Metadata --}}
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">{{ __('Créé le') }}</span>
                        <p class="font-medium text-gray-900">{{ $project->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                    <div>
                        <span class="text-gray-500">{{ __('Dernière mise à jour') }}</span>
                        <p class="font-medium text-gray-900">{{ $project->updated_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>

            {{-- Danger zone --}}
            <div class="bg-white rounded-2xl shadow-lg border border-red-100 p-6">
                <h3 class="text-sm font-bold text-red-800 uppercase tracking-wide mb-3">{{ __('Zone de danger') }}</h3>
                <p class="text-sm text-gray-600 mb-4">{{ __('La suppression du projet est irréversible. Toutes les données associées seront définitivement perdues.') }}</p>
                 <button
                     type="button"
                     class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-600 transition"
                     x-on:click="$dispatch('open-modal', 'confirm-delete-project-show')"
                 >
                     <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                     </svg>
                     {{ __('Supprimer ce projet') }}
                 </button>

                 <x-modal name="confirm-delete-project-show" :show="false">
                     <div class="p-6">
                         <div class="flex flex-col items-center text-center">
                             <svg class="w-10 h-10 text-red-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                             </svg>
                             <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ __('Supprimer le projet') }}</h3>
                             <p class="text-sm text-gray-600 mb-4">
                                 {{ __('Êtes-vous sûr de vouloir supprimer ce projet ?') }}<br>
                                 {{ __('Cette action est irréversible.') }}
                             </p>
                             <div class="flex items-center justify-center gap-3">
                                 <button type="button" class="px-4 py-2 text-sm font-medium text-gray-600 hover:bg-gray-100 rounded-lg transition" x-on:click="$dispatch('close-modal', 'confirm-delete-project-show')">
                                     {{ __('Annuler') }}
                                 </button>
                                 <form method="POST" action="{{ route('admin.projects.destroy', $project) }}">
                                     @csrf
                                     @method('DELETE')
                                     <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white rounded-lg font-semibold text-sm shadow hover:bg-red-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-red-600 transition">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                         </svg>
                                         {{ __('Supprimer') }}
                                     </button>
                                 </form>
                             </div>
                         </div>
                     </div>
                 </x-modal>
            </div>

        </div>
    </div>
</x-app-layout>
